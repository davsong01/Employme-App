<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ProgramDetailsExport;
use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Material;
use App\Models\Module;
use App\Models\Program;
use App\Models\Question;
use App\Models\ScoreSetting;
use App\Models\TempTransaction;
use App\Models\Transaction;
use App\Models\User;
use DavidOghi\CertificateGeneration\Services\CertificateManager as PackageCertificateManager;
use App\Services\ExcelService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;

class ProgramController extends Controller
{
    public function index(Program $program)
    {
        $i = 1;

        if (checkRoleHas(['Admin','Grader','Facilitator'])) {
            if(checkRoleHas(['Admin'])){
                $programs = Program::withPaymentStats()
                ->with(['users:id','subPrograms'])
                ->where('id','<>',1)
                ->latest()
                ->get();
            }else{
                $programs = resolveAuthUser()->userTrainings()->get();
            }

            return view('dashboard.admin.programs.index', compact('programs', 'i'));
        }
        
        return redirect('/');
    }

    public function exportdetails($id)
    {
        $programname = Program::whereId($id)->value('p_name');
        
        $programname = preg_replace('/[^A-Za-z0-9\-]/', '', $programname);
        return Excel::download(new ProgramDetailsExport($id), $programname . ' participants.xlsx');
    }

    public function processExportParticipantsDataFromTraining(Request $request)
    {
        $programIds = $request->program_ids ?? [];
        $explicitProgramId = $request->filled('explicit_program_id') ? (int) $request->explicit_program_id : null;
        $removeDuplicates = $request->filled('remove_duplicate') && $request->remove_duplicate === 'yes';
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : null;
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : null;

        // Preload program names
        $programMap = Program::pluck('p_name', 'id')->toArray();

        // Base query
        $baseQuery = TempTransaction::with(['user'])
            ->where('status', 'complete');

        // Either explicit program OR multiple programs
        if ($explicitProgramId) {
            $baseQuery->where('program_id', $explicitProgramId);
        } elseif (!empty($programIds)) {
            $baseQuery->where(function($q) use ($programIds) {
                foreach ($programIds as $programId) {
                    $q->orWhereJsonContains('program_ids', (int) $programId);
                }
            });
        }

        // Date filter
        if ($from && $to) {
            $baseQuery->whereBetween('created_at', [$from, $to]);
        } elseif ($from) {
            $baseQuery->where('created_at', '>=', $from);
        } elseif ($to) {
            $baseQuery->where('created_at', '<=', $to);
        }

        // Payment type filter
        if ($request->filled('payment_type')) {
            $baseQuery->where('type', $request->payment_type);
        }

        // Fetch transactions after all filters applied
        $transactions = $baseQuery->get();
        
        // Map transactions to export rows
        $rows = $transactions->map(function ($tx) use ($programMap) {
            $user = $tx->user;
            $payment = $tx->paymentLog;

            $programIds = $tx->program_ids ?? [];
            $programNames = collect($programIds)
                ->map(fn($id) => $programMap[$id] ?? null)
                ->filter()
                ->map(fn($name) => preg_replace('/[^A-Za-z0-9\- ]/', '', $name))
                ->implode(', ');

            return [
                'Date Created'     => $user?->created_at?->format('Y-m-d H:i'),
                'Staff ID'         => $user?->staffId,
                'Name'             => $user?->name,
                'Email'            => $user?->email,
                'Phone'            => $user?->phone,
                'Payment Type'     => $payment?->type,
                'Expected Amount'  => $payment?->expected_amount,
                'Amount Paid'      => $payment?->amount,
                'Balance'          => $payment?->balance,
                'Payment Mode'     => $payment?->payment_mode,
                'Transaction ID'   => $payment?->transid,
                'Location'         => $user?->location,
                'Program(s)'       => $programNames,
                'user_id'          => $user?->id, // for duplicate removal
            ];
        });

        // Remove duplicates if requested
        if ($removeDuplicates) {
            $rows = $rows->groupBy('user_id')->map(fn($group) => $group->first())->values();
        }

        // Remove helper column
        $rows = $rows->map(fn($r) => Arr::except($r, ['user_id']));

        $excelService = new ExcelService();
        return $excelService->fastExport($rows->toArray(), 'participants.xlsx');
    }

    public function create()
    {
        if(checkRoleHas(['Admin'])) {
            $program = new Program();
            $programs = collect();
            $modes = ['Online', 'Offline'];
            $currencies = Currency::where('status', 1)->orderBy('name')->get();

            return view('dashboard.admin.programs.edit', [
                'program' => $program,
                'programs' => $programs,
                'modes' => $modes,
                'currencies' => $currencies,
                'certificateSettings' => [],
                'legacyCertificateSettings' => false,
                'useExistingSettings' => 'no',
                'inheritedProgramId' => null,
                'isCreate' => true,
            ]);
        } else {
            return redirect('/programs');
        }
    }

    public function exportParticipantsDataFromTraining()
    {
        if (checkRoleHas(['Admin'])) {
            $programs = Program::where('id', '<>', 1)->orderBy('id','desc')->get();
            return view('dashboard.admin.programs.export', compact('programs'));
        } else {
            return redirect('/programs');
        }
    }


    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'p_name' => 'required',
            'p_abbr' => 'required',
            'p_amount' => 'required',
            'e_amount' => 'required',
            'p_start' => 'required',
            'p_end' => 'required',
            'hasmock' => 'required',
            'is_closed' => 'nullable',
            'booking_form' => 'file|mimes:pdf|max:10000',
            'image' => 'required|image |max:10000',
            'haspartpayment' => 'required',
            'status' => 'required',
            'off_season' => 'required',
            'show_catalogue_popup' => 'required',
            'show_locations' => 'required',
            'locations' => 'nullable',
            'show_modes' => 'required',
            'allow_payment_restrictions_for_materials' => 'required',
            'allow_payment_restrictions_for_pre_class_tests' => 'required',
            'allow_payment_restrictions_for_post_class_tests' => 'required',
            'allow_payment_restrictions_for_results' => 'required',
            'allow_payment_restrictions_for_certificates' => 'required',
            'allow_payment_restrictions_for_completed_tests' => 'required',
            'allow_preferred_timing' => 'required',
            'allow_flexible_payment' => 'required',
        ]);

        //Save booking form
        if ($request->file('booking_form')) {
            $filePath = $this->uploadFileToUploads($request->file('booking_form'), 'booking_form', 'bookingforms');
            $filePath = 'bookingforms/' . $filePath;
        }

        // uploads file to the desired folder in uploads directory
        $file = $this->uploadFileToUploads($request->file('image'), 'image', 'trainings', 533, 533);
        $data['image'] = 'trainingimage/' . $file;

        if ($request->has('show_locations') && $request->show_locations == 'yes') {
            for ($i = 0; $i < count($request->location_name); $i++) {
                $l[] = array_column($request->only(['location_name', 'location_address']), $i);
            }

            foreach ($l as $test) {
                $locations[$test[0]] = $test[1];
            }
            $locations = json_encode($locations);
        }

        if ($request->has('show_modes') && $request->show_modes == 'yes') {
            for ($i = 0; $i < count($request->mode_name); $i++) {
                $m[] = array_column($request->only(['mode_name', 'mode_amount']), $i);
            }

            foreach ($m as $test) {
                $modes[$test[0]] = $test[1];
            }
            $modes = json_encode($modes);
        }

        $program = Program::Create([
            'p_name' => $data['p_name'],
            'p_abbr' => $data['p_abbr'],
            'p_amount' => $data['p_amount'],
            'e_amount' => $data['e_amount'],
            'p_start' => $data['p_start'],
            'p_end' => $data['p_end'],
            'hasmock' => $data['hasmock'],
            'haspartpayment' => $data['haspartpayment'],
            'status' => $data['status'],
            'off_season' => $data['off_season'],
            'is_closed' => $data['is_closed'],
            'booking_form' => $filePath ?? null,
            'show_locations' => $data['show_locations'],
            'show_modes' => $data['show_modes'],
            'modes' => $modes ?? null,
            'locations' => $locations ?? null,
            'show_catalogue_popup' => $data['show_catalogue_popup'],
            'image' => 'trainingimage/' . $file,
            'allow_payment_restrictions_for_materials' => $data['allow_payment_restrictions_for_materials'],
            'allow_payment_restrictions_for_pre_class_tests' => $data['allow_payment_restrictions_for_pre_class_tests'],
            'allow_payment_restrictions_for_post_class_tests' => $data['allow_payment_restrictions_for_post_class_tests'],
            'allow_payment_restrictions_for_results' => $data['allow_payment_restrictions_for_results'],
            'allow_payment_restrictions_for_certificates' => $data['allow_payment_restrictions_for_certificates'],
            'allow_payment_restrictions_for_completed_tests' => $data['allow_payment_restrictions_for_completed_tests'],
            'allow_preferred_timing' => $data['allow_preferred_timing'],
            'allow_flexible_payment' => $data['allow_flexible_payment'],
        ]);

        if ($request->has('sub_name') && $request->show_sub == 'yes') {
            for ($i = 0; $i < count($request->sub_name); $i++) {
                $l[] = array_column($request->only(['sub_name', 'sub_amount']), $i);
            }

            foreach ($l as $test) {
                $subs[$test[0]] = $test[1];
            }

            foreach ($subs as $name => $amount) {
                $sub_data = $program->toArray();
                $sub_data['p_name'] = $name;
                $sub_data['parent_id'] = $sub_data['id'];
                $sub_data['p_amount'] = $amount;
                unset($sub_data['id']);
                Program::Create($sub_data);
            }
        }


        return redirect('programs')->with('message', 'Program added succesfully');
    }

    public function edit($id)
    {
        // dd(phpinfo());
        $i = 1;
        $program = Program::with('certificateTemplate')->findOrFail($id);
        $modes = [
            'Online',
            'Offline'
        ];

        $currencies = Currency::where('status', 1)->orderBy('name')->get();
        $certificateSettings = $program->auto_certificate_settings ?? [];
        $inheritedProgramId = old('existing_program_id', data_get($certificateSettings, 'inherited_from'));
        $useExistingSettings = old('use_existing_settings', !empty($inheritedProgramId) ? 'yes' : 'no');
        $legacyCertificateSettings = $this->programUsesLegacyCertificateSettings($program);

        $programs = Program::select('id', 'p_name', 'certificate_template_id', 'auto_certificate_settings')
            ->whereNotIn('id', [$program->id, 1])
            ->orderBy('p_name')
            ->get()
            ->filter(function (Program $candidate) use ($program) {
                return $this->programUsesLegacyCertificateSettings($candidate)
                    || !empty($candidate->certificate_template_id)
                    || data_get($candidate->auto_certificate_settings, 'auto_certificate_status') === 'yes';
            })
            ->values();

        if (!empty($inheritedProgramId) && ! $programs->contains('id', (int) $inheritedProgramId)) {
            $inheritedProgram = Program::select('id', 'p_name', 'certificate_template_id', 'auto_certificate_settings')
                ->find($inheritedProgramId);

            if ($inheritedProgram) {
                $programs->push($inheritedProgram);
            }
        }

        $programs = $programs->sortBy('p_name')->values();

        return view('dashboard.admin.programs.edit', compact(
            'program',
            'modes',
            'currencies',
            'programs',
            'certificateSettings',
            'inheritedProgramId',
            'useExistingSettings',
            'legacyCertificateSettings'
        ));
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->only(['show_sub', 'p_name', 'p_abbr', 'p_amount', 'e_amount', 'p_start', 'status', 'p_end', 'hasmock', 'off_season', 'is_closed','haspartpayment', 'show_modes', 'show_locations', 'allow_payment_restrictions', 'allow_payment_restrictions_for_materials', 'allow_payment_restrictions_for_pre_class_tests', 'allow_payment_restrictions_for_post_class_tests', 'allow_payment_restrictions_for_results', 'allow_payment_restrictions_for_certificates', 'allow_payment_restrictions_for_completed_tests', 'allow_preferred_timing', 'allow_flexible_payment', 'only_certified_should_see_certificate', 'program_lock', 'login_without_password','currencies', 'currency_values', 'early_bird_status','ai_settings']);

        $this->deleteAllFilesInAPublicFolder('certificate_previews');

        $hasLegacyInputs = $request->hasFile('auto_certificate_template')
            || $request->filled('existing_auto_certificate_template')
            || $request->filled('existing_program_id')
            || is_array($request->text_type);

        if ($request->use_existing_settings == 'yes' && !empty($request->existing_program_id)) {
            $sourceProgram = Program::with('certificateTemplate')->find($request->existing_program_id);

            if ($sourceProgram) {
                if (!empty($sourceProgram->certificate_template_id)) {
                    $data['certificate_template_id'] = $sourceProgram->certificate_template_id;
                    $data['auto_certificate_settings'] = [
                        'auto_certificate_status' => $request->auto_certificate_status,
                        'inherited_from' => $sourceProgram->id,
                    ];
                } else {
                    $sourceSettings = $sourceProgram->auto_certificate_settings ?? [];
                    $sourceSettings['auto_certificate_status'] = $request->auto_certificate_status;
                    $sourceSettings['inherited_from'] = $sourceProgram->id;
                    $data['certificate_template_id'] = null;
                    $data['auto_certificate_settings'] = $sourceSettings;
                }
            }
        } elseif ($hasLegacyInputs) {
            // Manual Build Logic
            $templatePath = $program->auto_certificate_settings['auto_certificate_template'] ?? null;

            if ($request->hasFile('auto_certificate_template')) {
                $name = uniqid(9) . '.' . $request->auto_certificate_template->getClientOriginalExtension();
                $request->auto_certificate_template->storeAs('certificate_templates', $name, 'uploads');
                $templatePath = 'certificate_templates/' . $name;
            }

            $data['certificate_template_id'] = null;
            // Pass templatePath explicitly instead of attaching to $request
            $data['auto_certificate_settings'] = $this->buildCertificateSettings($request, $templatePath);
        } else {
            $existingSettings = is_array($program->auto_certificate_settings) ? $program->auto_certificate_settings : [];
            $existingSettings['auto_certificate_status'] = $request->auto_certificate_status ?? data_get($program, 'auto_certificate_settings.auto_certificate_status', 'no');
            $data['auto_certificate_settings'] = $existingSettings;
        }


        if(!empty($data['currencies']) && !empty($data['currency_values'])){
            $selectedCurrencyIds = $request->input('currencies', []);
            $currencyValues = $request->input('currency_values', []);

            $currencyData = [];

            foreach ($selectedCurrencyIds as $currencyId) {
                $currencyData[] = [
                    'id' => (int) $currencyId,
                    'amount' => isset($currencyValues[$currencyId]) && $currencyValues[$currencyId] !== ''
                        ? (float) $currencyValues[$currencyId]
                        : null,
                ];
            }

            $data['currencies'] = $currencyData;
        }else{
            $data['currencies'] = $program->currencies;
        }
        unset($data['currency_values']);

    
        if ($request->hasFile('image')) {

            // Dont delete old files, another progeam may be using it
            // uploads file to the desired folder in uploads directory
            $file = $this->uploadFileToUploads($request->file('image'), 'image', 'trainings', 533, 533);

            $data['image'] = 'trainingimage/' . $file;
        }

        if ($request->hasFile('booking_form')) {
            //Save booking form
            $filePath = $this->uploadFileToUploads($request->file('booking_form'), 'booking_form', 'bookingforms');

            $data['booking_form'] = 'bookingforms/' . $filePath;
        }
        
        if (!empty($request->show_locations) && $request->show_locations == 'yes') {
            if(isset($request->location_name) && count($request->location_name) > 0){
                for ($i = 0; $i < count($request->location_name); $i++) {
                    $l[] = array_column($request->only(['location_name', 'location_address']), $i);
                }
    
                foreach ($l as $test) {
                    $locations[$test[0]] = $test[1];
                }
                $data['locations'] = json_encode($locations);
            }
        }

        if (!empty($request->show_modes) && $request->show_modes == 'yes') {
            for ($i = 0; $i < count($request->mode_name); $i++) {
                $m[] = array_column($request->only(['mode_name', 'mode_amount']), $i);
            }

            foreach ($m as $test) {
                $modes[$test[0]] = $test[1];
            }
            $data['modes'] = json_encode($modes);
        }

        $program->update($data);
        
        if ($request->sub_name && $request->show_sub == 'yes') {

            for ($i = 0; $i < count($request->sub_name); $i++) {
                $l[] = array_column($request->only(['sub_name', 'sub_amount', 'sub_status', 'sub_program_id']), $i);
            }

            foreach ($l as $test) {
                $subs[] = [
                    'p_name' => $test[0],
                    'p_amount' => $test[1],
                    'status' => $test[2],
                    'id' => $test[3] ?? null,
                ];
            }
            
            if (isset($subs) && !empty($subs)) {
                $sub_programs = $subs;
                $new_sub_data = $program->toArray();
                $new_sub_data = array_diff_key($new_sub_data, array_flip(["status", "id", "created_at", "updated_at", "sub_programs", "deleted_at", "p_name", "p_amount", "show_sub"]));
                
                foreach ($sub_programs as $key => $sub) {
                    $new_sub_data['p_name'] = $sub['p_name'];
                    $new_sub_data['p_amount'] = $sub['p_amount'];
                    $new_sub_data['status'] = $sub['status'];
                    $new_sub_data['parent_id'] = $program->id;
                    unset($new_sub_data['slug']);
                    
                    if (isset($sub['id'])) {
                        $subProgram = Program::where('id', $sub['id'])->first();
                        $subProgram->update($new_sub_data);
                    } else {
                        $new = Program::Create($new_sub_data);
                    }
                }
            }
        }

        return redirect()->back()->with('message', 'Training updated successfully');
    }

    public function buildCertificateSettings($request, $templatePath)
    {
        $auto_certificate_settings = [
            "auto_certificate_name_font_size"   => $request->auto_certificate_name_font_size,
            "auto_certificate_name_font_weight" => $request->auto_certificate_name_font_weight,
            "auto_certificate_color"           => $request->auto_certificate_color,
            "auto_certificate_top_offset"      => $request->auto_certificate_top_offset,
            "auto_certificate_left_offset"     => $request->auto_certificate_left_offset,
            "text_type"                        => $request->text_type,
            "text_type_face"                   => $request->text_type_face,
        ];

        $final_array = [];

        // Only process rows if status is yes and we actually have array data
        if ($request->auto_certificate_status == 'yes' && is_array($request->text_type)) {
            foreach ($request->text_type as $index => $type) {
                $final_array[] = [
                    "text_type"                         => $type,
                    "text_type_face"                    => $request->text_type_face[$index] ?? 'Pesaro-Bold.ttf',
                    "auto_certificate_name_font_size"   => $request->auto_certificate_name_font_size[$index] ?? null,
                    "auto_certificate_name_font_weight" => $request->auto_certificate_name_font_weight[$index] ?? null,
                    "auto_certificate_color"           => $request->auto_certificate_color[$index] ?? '#000000',
                    "auto_certificate_top_offset"      => $request->auto_certificate_top_offset[$index] ?? 0,
                    "auto_certificate_left_offset"     => $request->auto_certificate_left_offset[$index] ?? 0,
                ];
            }
        }

        return [
            "auto_certificate_status"   => $request->auto_certificate_status,
            "auto_certificate_template" => $templatePath,
            "settings"                  => $final_array
        ];
    }

    private function programUsesLegacyCertificateSettings(Program $program): bool
    {
        if (! empty($program->certificate_template_id)) {
            return false;
        }

        $settings = $program->auto_certificate_settings;

        if (! is_array($settings)) {
            return false;
        }

        return ! empty($settings['auto_certificate_template'])
            || ! empty($settings['settings'])
            || ! empty($settings['inherited_from']);
    }

    private function programUsesDesignerCertificateSettings(Program $program): bool
    {
        return ! empty($program->certificate_template_id);
    }

    public function migrateCertificateDesigner(Program $program, PackageCertificateManager $certificates)
    {
        if ($this->programUsesDesignerCertificateSettings($program)) {
            return back()->with('message', 'This program is already using the new certificate designer.');
        }

        if (! $this->programUsesLegacyCertificateSettings($program)) {
            return back()->with('error', 'No legacy certificate settings found for this program.');
        }

        $legacySettings = $program->auto_certificate_settings ?? [];
        $legacyTemplatePath = data_get($legacySettings, 'auto_certificate_template');

        if (blank($legacyTemplatePath)) {
            return back()->with('error', 'Legacy certificate template is missing.');
        }

        $legacyAbsolutePath = base_path('uploads/' . $legacyTemplatePath);
        if (! file_exists($legacyAbsolutePath)) {
            return back()->with('error', 'Legacy certificate template file could not be found.');
        }

        $templateModel = config('certificates.models.template', \App\Models\CertificateTemplate::class);
        $existingTemplate = $templateModel::query()
            ->where('description', 'Migrated from legacy program ID ' . $program->id)
            ->first();

        if ($existingTemplate) {
            $program->update([
                'certificate_template_id' => $existingTemplate->id,
                'auto_certificate_settings' => [
                    'auto_certificate_status' => data_get($legacySettings, 'auto_certificate_status', 'no'),
                    'migrated_from_legacy' => true,
                ],
            ]);

            return back()->with('message', 'Program migrated to the new certificate designer successfully.');
        }

        $storedTemplatePath = Storage::disk(config('certificates.storage.disk', 'local'))->putFileAs(
            trim(config('certificates.storage.template_directory', 'certificates/templates'), '/'),
            new File($legacyAbsolutePath),
            Str::slug($program->p_name . ' certificate') . '-' . Str::random(8) . '.' . pathinfo($legacyAbsolutePath, PATHINFO_EXTENSION)
        );

        $template = $certificates->create([
            'name' => $program->p_name . ' Certificate',
            'description' => 'Migrated from legacy program ID ' . $program->id,
            'certificate_template' => $storedTemplatePath,
            'settings' => certificatePackageSettingsFromLegacy($legacySettings, $legacyAbsolutePath),
            'status' => true,
        ], resolveAuthUser());

        $program->update([
            'certificate_template_id' => $template->id,
            'auto_certificate_settings' => [
                'auto_certificate_status' => data_get($legacySettings, 'auto_certificate_status', 'no'),
                'migrated_from_legacy' => true,
            ],
        ]);

        return back()->with('message', 'Program migrated to the new certificate designer successfully.');
    }

    public function removeSubProgram($id)
    {
        $check = DB::table('program_user')->where('program_id', $id)->count();
        if ($check <= 0) {
            Program::find($id)->forceDelete();
            return response()->json(['status' => 'success', 'message' => 'Removed successfully!'], 200);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Cannot remove program with one or more participants!'], 200);
        }
    }

    public function destroy($id)
    {
        $program = program::withTrashed()->where('id', $id)->firstOrFail();

        if ($program->trashed()) {
            $program->forceDelete();

            return redirect('programs')->with('message', 'Training has been deleted forever');
        } else {

            $program->users()->detach();

            $program->delete();

            return redirect('programs')->with('message', 'Training has been trashed');
        }
    }

    public function trashed()
    {
        $i = 1;
        //Get all programs
        $programs = Program::with('users')->onlyTrashed()->get();

        //Get all students
        $users = User::where('roles', 'Student')->get();

        //Get Users payment status
        foreach ($programs as $program) {
            $program['part_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '>', 0)->count();
            $program['fully_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '<=', 0)->count();
        }

        // dd($programs);
        return view('dashboard.admin.programs.trash', compact('programs', 'i'));
    }

    public function restore($id)
    {
        $program = program::withTrashed()->where('id', $id)->whereNULL('parent_id')->firstOrFail();

        $program->restore();

        return redirect(route('programs.index'))->with('message', 'Program has been restored');
    }

    public function showcrm($id)
    {
        $program = Program::find($id);
        $programName = $program->p_name;
        $program->hascrm = 1;
        $program->save();

        return back()->with('message', 'CRM has been succesfully enabled for ' . $programName);
    }

    public function hidecrm($id)
    {
        $program = Program::find($id);
        $programName = $program->p_name;
        $program->hascrm = 0;
        $program->save();

        return back()->with('message', 'CRM has been succesfully disabled for ' . $programName);
    }

    public function closeRegistration($id)
    {
        $program = Program::findorfail($id);
        $programName = $program->p_name;
        $program->close_registration = 1;
        $program->save();

        return back()->with('message', 'Registration is now closed for ' . $programName);
    }

    public function openRegistration($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->close_registration = 0;
        $program->save();

        return back()->with('message', 'Registration is now extended for ' . $programName);
    }

    public function openEarlyBird($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->early_bird_status = 1;
        $program->save();

        return back()->with('message', 'Early is now extended for ' . $programName);
    }

    public function closeEarlyBird($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->early_bird_status = 0;
        $program->save();

        return back()->with('message', 'EarlyBird payment is now closed for ' . $programName);
    }

    public function cloneTraining(Request $request, Program $training)
    {
        $scoresettings = $training->scoresettings;
        $materials = $training->materials;
        $modules = $training->modules;
        $questions = $training->questions;
        $cloneCertificateSettings = array_intersect(['certificate_settings', 'all'], $request->clone_options);
        $trainingUsesDesignerCertificates = $this->programUsesDesignerCertificateSettings($training);

        $training->parent_id = null;

        // Create new program
        $newT = Arr::except($training->toArray(), ['id','created_at','updated_at','deleted_at', 'scoresettings', 'materials', 'modules', 'questions']);

        if ($trainingUsesDesignerCertificates && ! empty($cloneCertificateSettings)) {
            $newT['certificate_template_id'] = $training->certificate_template_id;
            $newT['auto_certificate_settings'] = [
                'auto_certificate_status' => data_get($training, 'auto_certificate_settings.auto_certificate_status', 'no'),
            ];
        } else {
            // Legacy programs should never carry old certificate configuration into a clone.
            unset($newT['auto_certificate_settings'], $newT['certificate_template_id']);
        }

        try {
            DB::beginTransaction();
            $newT['status'] = 0;
            $newT['is_closed'] = 'no';
            $newT['p_name'] = 'copy_' . $training->p_name;
            $newT['hascrm'] = 0;
            $newT['hasresult'] = 0;
            $newT['show_certificate'] = 0;
            unset($newT['slug']);
            $new = Program::create($newT);

            if (array_intersect(['score_settings', 'all'], $request->clone_options)){
                // Create scoresettings
                if (isset($training->scoresettings) && !empty($training->scoresettings)) {
                    $score = ScoreSetting::create([
                        'program_id' => $new->id,
                        'certification' => $training->scoresettings->certification,
                        'class_test' => $training->scoresettings->class_test,
                        'role_play' => $training->scoresettings->role_play,
                        'crm_test' => $training->scoresettings->crm_test,
                        'email' => $training->scoresettings->email,
                        'passmark' => $training->scoresettings->passmark,
                        'total' => $training->scoresettings->total,
                    ]);
                }
            }

            if (array_intersect(['training_materials', 'all'], $request->clone_options)) {
                // Material
                if (isset($training->materials) && !empty($training->materials)) {
                    foreach ($training->materials as $material) {
                        $file = base64_decode($material->file);
                        Material::create([
                            "program_id" => $new->id,
                            "title" => $material->title,
                            "file" => $material->file,
                        ]);
                    }
                }
            }

            if (array_intersect(['modules', 'all'], $request->clone_options)) {
                // Modules
                if (isset($training->modules) && !empty($training->modules)) {
                    foreach ($training->modules as $module) {
                        $new_module =  Module::create([
                            "program_id" => $new->id,
                            "title" => $module->title,
                            "time" => $module->time,
                            "noofquestions" => $module->noofquestions,
                            "status" => 0,
                            "type" => $module->type == 'Class Test' ? 0 : 1,
                        ]);

                        //Get Module questions 
                        $module_questions = Question::whereModuleId($module->id)->get();

                        //Duplicate module questions for newly created module       
                        foreach ($module_questions as $question) {
                            Question::create([
                                'title' => $question->title,
                                'optionA' => $question->optionA,
                                'optionB' => $question->optionB,
                                'optionC' => $question->optionC,
                                'optionD' => $question->optionD,
                                'correct' => $question->correct,
                                'module_id' => $new_module->id,
                            ]);
                        }
                    }
                }
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
        
        return back()->with('message', 'Training cloned successfully');
    }
    
    public function importDataFromTraining(Request $request, Program $training)
    {
        $import_options = $request->import_options;
        $import_from_training = Program::find($request->import_from);
        $import_into_training = $training;
        $importCertificateSettings = array_intersect(['certificate_settings', 'all'], $import_options);
        $sourceUsesDesignerCertificates = $this->programUsesDesignerCertificateSettings($import_from_training);

        try {
            DB::beginTransaction();

            if ($sourceUsesDesignerCertificates && ! empty($importCertificateSettings)) {
                $import_into_training->certificate_template_id = $import_from_training->certificate_template_id;
                $import_into_training->auto_certificate_settings = [
                    'auto_certificate_status' => data_get($import_from_training, 'auto_certificate_settings.auto_certificate_status', 'no'),
                ];
                $import_into_training->save();
            }

            if (array_intersect(['score_settings', 'all'], $import_options)) {
                // Create scoresettings
                if (isset($import_from_training->scoresettings) && !empty($import_from_training->scoresettings)) {
                    ScoreSetting::create([
                        'program_id' => $import_into_training->id,
                        'certification' => $import_from_training->scoresettings->certification,
                        'class_test' => $import_from_training->scoresettings->class_test,
                        'role_play' => $import_from_training->scoresettings->role_play,
                        'crm_test' => $import_from_training->scoresettings->crm_test,
                        'email' => $import_from_training->scoresettings->email,
                        'passmark' => $import_from_training->scoresettings->passmark,
                        'total' => $import_from_training->scoresettings->total,
                    ]);
                }
            }

            if (array_intersect(['training_materials', 'all'], $import_options)) {
                // Material
                if (isset($import_from_training->materials) && !empty($import_from_training->materials)) {
                    foreach ($import_from_training->materials as $material) {
                        $file = base64_decode($material->file);
                        Material::create([
                            "program_id" => $import_into_training->id,
                            "title" => $material->title,
                            "file" => $material->file,
                        ]);
                    }
                }
            }

            if (array_intersect(['modules', 'all'], $import_options)) {
                // Modules
                if (isset($import_from_training->modules) && !empty($import_from_training->modules)) {
                    foreach ($import_from_training->modules as $module) {
                        $new_module =  Module::create([
                            "program_id" => $import_into_training->id,
                            "title" => $module->title,
                            "time" => $module->time,
                            "noofquestions" => $module->noofquestions,
                            "status" => 0,
                            "type" => $module->type == 'Class Test' ? 0 : 1,
                        ]);

                        //Get Module questions 
                        $module_questions = Question::whereModuleId($module->id)->get();

                        //Duplicate module questions for newly created module       
                        foreach ($module_questions as $question) {
                            Question::create([
                                'title' => $question->title,
                                'optionA' => $question->optionA,
                                'optionB' => $question->optionB,
                                'optionC' => $question->optionC,
                                'optionD' => $question->optionD,
                                'correct' => $question->correct,
                                'module_id' => $new_module->id,
                            ]);
                        }
                    }
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }

        return back()->with('message', 'Training Data successfully');
    }

    public function passwordReset($id)
    {
        $transactions = Transaction::with('user')->where('program_id', $id)->get();

        $plainPassword = '11111';
        $hashedPassword = Hash::make($plainPassword);

        $counter = 0;

        DB::transaction(function () use ($transactions, $hashedPassword, &$counter) {
            foreach ($transactions as $transaction) {
                if ($transaction->user) {
                    $transaction->user->update([
                        'password' => $hashedPassword
                    ]);
                    $counter++;
                }
            }
        });
        
        return back()->with('message', "Password for $counter participants successfully reset to: $plainPassword");
    }

}
