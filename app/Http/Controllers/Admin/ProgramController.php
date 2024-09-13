<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\User;
use App\Module;
use App\Program;
use App\Material;
use App\Question;
use App\ScoreSetting;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgramDetailsExport;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    public function index(Program $program)
    {
        $i = 1;
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || in_array(4, Auth::user()->permissions())) {
            //Get all programs
            $programs = Program::with(['users:id','subPrograms'])->where('id', '<>', 1)->orderBy('created_at', 'desc')->get();
            
            //Get all students
            $users = User::where('role_id', 'Student')->get();

            //Get Users payment status
            foreach ($programs as $program) {
                $program['part_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '>', 0)->count();
                $program['fully_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '<=', 0)->count();
            }

            return view('dashboard.admin.programs.index', compact('programs', 'i'));
        } else  return redirect('/');
    }

    public function exportdetails($id)
    {
        $programname = Program::whereId($id)->value('p_name');
        $programname = preg_replace('/[^A-Za-z0-9\-]/', '', $programname);
        return Excel::download(new ProgramDetailsExport($id), $programname . ' participants.xlsx');
    }

    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $programs = Program::where('id', '<>', 1)->get();
            $materials = Material::all();
            return view('dashboard.admin.programs.create', compact('programs'));
        } else {
            return redirect('/programs');
        }
        // return view('dashboard.admin.programs.create', compact('programs', 'program'));
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
        $i = 1;
        $program = Program::find($id);
        $modes = [
            'Online',
            'Offline'
        ];
        return view('dashboard.admin.programs.edit', compact('program', 'modes'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->only(['show_sub', 'p_name', 'p_abbr', 'p_amount', 'e_amount', 'p_start', 'status', 'p_end', 'hasmock', 'off_season', 'is_closed','haspartpayment', 'show_modes', 'show_locations', 'allow_payment_restrictions', 'allow_payment_restrictions_for_materials', 'allow_payment_restrictions_for_pre_class_tests', 'allow_payment_restrictions_for_post_class_tests', 'allow_payment_restrictions_for_results', 'allow_payment_restrictions_for_certificates', 'allow_payment_restrictions_for_completed_tests', 'allow_preferred_timing', 'allow_flexible_payment' ]);

        // Clear all certificate previews
        $this->deleteAllFilesInAPublicFolder('certificate_previews');
        //check if new featured image
        if(!empty($request->auto_certificate_template)){
            $name = uniqid(9) . '.' . $request->auto_certificate_template->getClientOriginalExtension();
            $request->auto_certificate_template->storeAs('certificate_templates', $name, 'uploads');
            
            $request['path'] = 'certificate_templates/'.$name;
            
        }else{
            $request['path'] = $program->auto_certificate_settings['auto_certificate_template'];
        }

        $data['auto_certificate_settings'] = $this->buildCertificateSettings($request);
        
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
            for ($i = 0; $i < count($request->location_name); $i++) {
                $l[] = array_column($request->only(['location_name', 'location_address']), $i);
            }

            foreach ($l as $test) {
                $locations[$test[0]] = $test[1];
            }
            $data['locations'] = json_encode($locations);
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

    public function buildCertificateSettings($request){
        $auto_certificate_settings = [
            "auto_certificate_name_font_size" => $request->auto_certificate_name_font_size,
            "auto_certificate_name_font_weight" => $request->auto_certificate_name_font_weight,
            "auto_certificate_color" => $request->auto_certificate_color,
            "auto_certificate_top_offset" => $request->auto_certificate_top_offset,
            "auto_certificate_left_offset" => $request->auto_certificate_left_offset,
            "text_type" => $request->text_type,
        ];

        $final_array = [];

        foreach ($auto_certificate_settings as $key => $req) {
            foreach ($req as $index => $value) {
                $final_array[$index][$key] = $value;
            }
        }

        $data['auto_certificate_settings'] = [
            "auto_certificate_status" => $request->auto_certificate_status,
            "auto_certificate_template" => $request->path,
            "settings" => $final_array
        ];

        return $data['auto_certificate_settings'];
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
        $users = User::where('role_id', 'Student')->get();

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
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->hascrm = 1;
        $program->save();

        return redirect('programs')->with('message', 'CRM has been succesfully enabled for ' . $programName);
    }

    public function hidecrm($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->hascrm = 0;
        $program->save();

        return redirect('programs')->with('message', 'CRM has been succesfully disabled for ' . $programName);
    }

    public function closeRegistration($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->close_registration = 1;
        $program->save();

        return redirect('programs')->with('message', 'Registration is now closed for ' . $programName);
    }

    public function openRegistration($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->close_registration = 0;
        $program->save();

        return redirect('programs')->with('message', 'Registration is now extended for ' . $programName);
    }

    public function openEarlyBird($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->close_earlybird = 1;
        $program->save();

        return redirect('programs')->with('message', 'Early is now extended for ' . $programName);
    }

    public function closeEarlyBird($id)
    {
        $program = Program::findorfail($id);
        $programName = Program::where('id', $id)->pluck('p_name');
        $program->close_earlybird = 0;
        $program->save();

        return redirect('programs')->with('message', 'EarlyBird payment is now closed for ' . $programName);
    }

    public function cloneTraining(Program $training)
    {
        $scoresettings = $training->scoresettings;
        $materials = $training->materials;
        $modules = $training->modules;
        $questions = $training->questions;

        $training->p_name = 'copy_' . $training->p_name;
        // Create new program
       
        $newT = Arr::except($training->toArray(), ['id','created_at','updated_at','deleted_at', 'scoresettings', 'materials', 'modules', 'questions']);
        $new = Program::create($newT);
       
      
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

        // Material
        if (isset($training->materials) && !empty($training->materials)) {
            foreach ($training->materials as $material) {
                Material::create([
                    "program_id" => $new->id,
                    "title" => $material->title,
                    "file" => $material->file,
                ]);
            }
        }

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

        return back()->with('message', 'Training cloned successfully');
    }
}
