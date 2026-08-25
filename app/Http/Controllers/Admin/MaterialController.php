<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\User;
use App\Models\Mocks;
use App\Models\Module;
use App\Models\Program;
use App\Models\Material;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FacilitatorTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class MaterialController extends Controller
{
    public function decode(){

    }

    public function index(Request $request)
    {
        $userid = resolveAuthUser()->id;
        $useAi = false;

        if (checkRoleHas(['Admin','Facilitator','Grader'])){
            $allowedProgramIds = $this->allowedProgramIds();
            $programQuery = Program::query()->withCount('materials')->orderBy('created_at', 'desc');

            if(! checkRoleHas(['Admin'])) {
                $programQuery->whereIn('id', $allowedProgramIds);
            }

            $programs = $programQuery->get();

            $materialsQuery = Material::query()
                ->with(['program', 'uploader'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $search = $request->string('search')->value();

                    $query->where(function ($builder) use ($search) {
                        $builder->where('title', 'like', "%{$search}%")
                            ->orWhereHas('program', fn ($programQuery) => $programQuery->where('p_name', 'like', "%{$search}%"));
                    });
                })
                ->when($request->filled('program_id'), fn ($query) => $query->where('program_id', $request->integer('program_id')))
                ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->input('date_from')))
                ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->input('date_to')));

            if(! checkRoleHas(['Admin'])) {
                $materialsQuery->whereIn('program_id', $allowedProgramIds);
            }

            $materials = $materialsQuery
                ->latest()
                ->paginate(adminPaginationRecords())
                ->withQueryString();

            return view('dashboard.admin.materials.index', compact('materials', 'programs'));
        }

        if (checkRoleHas(['Student'])){
            $i = 1;
            $program = Program::find($request->p_id);
            $aiSetting = $program->ai_settings;
            
            $useAi = getProgramModuleAvailability($program, 'materials');

            if ($program->allow_payment_restrictions_for_materials == 'yes') {
                $transaction = getTransactionFromProgramIds($request->p_id);
                
                $balance = $transaction?->balance ?? 0;
                $currency = $transaction?->currency_symbol ?? '₦';
                
                if (($transaction?->balance ?? 0) > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $currency.number_format($balance) . ' in order to get access to materials');
                }
            }
            
            if ($program->hasmock == 1) {

                //Check if user has taken pre tests and return back if otherwise
                $expected_pre_class_tests = Module::ClassTests($program->id)->count();

                $completed_pre_class_tests = Mocks::where('program_id', $program->id)->where('user_id', resolveAuthUser()->id)->count();

                if ($completed_pre_class_tests < $expected_pre_class_tests) {
                    return Redirect::to('mocks?p_id=' . $program->id)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Training materials');
                }
            }

            $materials = Material::where('program_id', $program->id)->orderBy('created_at', 'DESC')->get();
            $show_catalogue = $this->showCatalogue($program);
            
            return view('dashboard.student.materials.index', compact('materials', 'program', 'show_catalogue','useAi'));
        }
    }

    public function getTrainingMaterials(Program $training){
        $i = 1;

        if (checkRoleHas(['Admin','Facilitator', 'Grader'])) {
            if (! checkRoleHas(['Admin']) && ! in_array($training->id, $this->allowedProgramIds(), true)) {
                abort(403);
            }

            $training = Program::withCount('materials')->where('id', $training->id)->firstOrFail();
        }else {
            return back();
        }
        
        $materials = Material::with(['program', 'uploader'])->where('program_id', $training->id)->orderBy('created_at', 'desc')->get();
        
        return view('dashboard.admin.materials.index', compact('i', 'materials','training')); 
    }
    
    public function create()
    {

    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'file' => ['required', 'array', 'min:1'],
            'file.*' => ['file'],
        ]);

        if (! checkRoleHas(['Admin']) && ! in_array((int) $data['program_id'], $this->allowedProgramIds(), true)) {
            abort(403);
        }

        foreach ($request->file('file') as $file) {
            $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($fileName);
            $preferredName = $data['program_id'] . '/' . $filename . '.' . $file->getClientOriginalExtension();

            $path = $this->storeFileInUploadsDiskAndEncodeInDb($file, 'materials', $preferredName);

            Material::create([
                'title' => $file->getClientOriginalName(),
                'program_id' =>  $data['program_id'],
                'file' => $path,
                'uploaded_by' => resolveAuthUser()->id,
                'uploaded_at' => now(),
            ]);
        }

        return back()->with('message', 'Study material succesfully added');
    }

    public function show(Material $material)
    {
        if (! checkRoleHas(['Admin']) && ! in_array((int) $material->program_id, $this->allowedProgramIds(), true)) {
            abort(403);
        }

        $programs = Program::orderBy('created_at', 'desc')->where('id', '<>', $material->program_id)->where('id', '<>', 1)->get();
        return view('dashboard.admin.materials.edit')->with('material', $material)->with('programs', $programs);
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy(Material $material)
    {
        if (! checkRoleHas(['Admin']) && ! in_array((int) $material->program_id, $this->allowedProgramIds(), true)) {
            abort(403);
        }

        $this->deleteMaterialFileIfUnused($material);

        $material->delete();
        if (checkRoleHas(['Facilitator'])) {
            return back()->with('message', 'Material has been deleted forever');
        }
        return redirect('materials')->with('message', 'Study material succesfully deleted');
    }

    public function bulkDestroy(Request $request)
    {
        $data = $request->validate([
            'material_ids' => ['required', 'array', 'min:1'],
            'material_ids.*' => ['integer', 'exists:materials,id'],
        ]);

        $materials = Material::with('program')
            ->whereIn('id', $data['material_ids'])
            ->get();

        if (! checkRoleHas(['Admin'])) {
            $allowedProgramIds = $this->allowedProgramIds();
            $materials = $materials->filter(fn (Material $material) => in_array((int) $material->program_id, $allowedProgramIds, true));
        }

        if ($materials->isEmpty()) {
            return back()->with('error', 'No selected materials could be deleted with your current permissions');
        }

        DB::transaction(function () use ($materials) {
            foreach ($materials as $material) {
                $this->deleteMaterialFileIfUnused($material);
                $material->delete();
            }
        });

        return back()->with('message', 'Selected study materials were deleted permanently');
    }

    public function bulkClone(Request $request)
    {
        $data = $request->validate([
            'material_ids' => ['required', 'array', 'min:1'],
            'material_ids.*' => ['integer', 'exists:materials,id'],
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        if (! checkRoleHas(['Admin']) && ! in_array((int) $data['program_id'], $this->allowedProgramIds(), true)) {
            abort(403);
        }

        $materials = Material::with('program')
            ->whereIn('id', $data['material_ids'])
            ->get();

        if (! checkRoleHas(['Admin'])) {
            $allowedProgramIds = $this->allowedProgramIds();
            $materials = $materials->filter(fn (Material $material) => in_array((int) $material->program_id, $allowedProgramIds, true));
        }

        if ($materials->isEmpty()) {
            return back()->with('error', 'No selected materials could be cloned with your current permissions');
        }

        DB::transaction(function () use ($materials, $data) {
            foreach ($materials as $material) {
                Material::create([
                    'title' => $material->title,
                    'program_id' => $data['program_id'],
                    'file' => $material->file,
                    'uploaded_by' => resolveAuthUser()->id,
                    'uploaded_at' => now(),
                ]);
            }
        });

        return back()->with('message', 'Selected study materials were cloned successfully');
    }

    public function clone($material_id, Request $request)
    {
        $material = Material::findOrFail($material_id);

        $data = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
        ]);

        if (! checkRoleHas(['Admin']) && ! in_array((int) $material->program_id, $this->allowedProgramIds(), true)) {
            abort(403);
        }

        if (! checkRoleHas(['Admin']) && ! in_array((int) $data['program_id'], $this->allowedProgramIds(), true)) {
            abort(403);
        }

        // Clone the source material into the selected program.
        Material::create([
            'title' => $material->title,
            'program_id' =>  $data['program_id'],
            'file' => $material->file,
            'uploaded_by' => resolveAuthUser()->id,
            'uploaded_at' => now(),
        ]);

        return redirect('materials')->with('message', 'Study material succesfully cloned');
    }

    public function getfile($filename)
    {
        $filename = base64_decode($filename);
        $realpath = base_path() . '/uploads/materials' . '/' . $filename;
        return response()->download($realpath);
    }

    private function checkMock($expected, $completed)
    {
        if ($expected < $completed) {
            dd($expected, $completed);
            return Redirect::to('mocks?p_id=' . 21)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Training materials');
        } else {
            return Redirect::to('mocks?p_id=' . 21)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Training materials');
        }
        // return 1;                
    }

    private function allowedProgramIds(): array
    {
        if (checkRoleHas(['Admin'])) {
            return Program::pluck('id')->all();
        }

        return resolveAuthUser()
            ?->trainings
            ?->pluck('program_id')
            ?->map(fn ($id) => (int) $id)
            ->all() ?? [];
    }

    private function deleteMaterialFileIfUnused(Material $material): void
    {
        $materialCount = Material::where('file', $material->file)->count();

        if ($materialCount <= 1) {
            $file = base64_decode($material->file);

            if ($file && file_exists(base_path() . '/uploads/materials' . '/' . $file)) {
                unlink(base_path() . '/uploads/materials' . '/' . $file);
            }
        }
    }
}
