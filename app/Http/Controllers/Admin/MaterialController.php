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

        if (checkRoleHas(['Admin','Facilitator'])){
            if(checkRoleHas(['Admin'])) {
                $programs = Program::withCount('materials')->orderBy('created_at', 'desc')->get();
            }
    
            if(checkRoleHas(['Facilitator','Grader'])) {
                $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
                $programs = Program::withCount('materials')->orderBy('created_at', 'desc')->whereIn('id', $trainings)->get();
            }

            return view('dashboard.admin.materials.selecttraining', compact('programs'));
        } 

        if (checkRoleHas(['Student'])){
            $i = 1;
            $program = Program::find($request->p_id);
            $aiSetting = $program->ai_settings;
            
            $useAi = getProgramModuleAvailability($program, 'materials');

            if ($program->allow_payment_restrictions_for_materials == 'yes') {
                $transaction = getTransactionFromProgramIds($request->p_id);
                
                $balance = $transaction?->balance ?? 0;
                $currency = $transaction?->currency_symbol;
                
                if ($transaction->balance > 0) {
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
            if (checkRoleHas(['Admin'])) {
                $training = Program::withCount('materials')->where('id', $training->id)->first();
            }

            if (checkRoleHas(['Facilitator', 'Grader'])) {
                $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
                $training = Program::withCount('materials')->where('id', $training->id)->whereIn('id', $trainings)->first();
            } 
        }else {
            return back();
        }
        
        $materials = Material::with('program')->where('program_id', $training->id)->orderBy('created_at', 'desc')->get();
        
        return view('dashboard.admin.materials.index', compact('i', 'materials','training')); 
    }
    
    public function create()
    {

    }

    public function store(Request $request)
    {
        if (request()->has('p_id')) {
            foreach (request()->file('file') as $file) {
                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($fileName);
                $path = base64_encode(request()->p_id . '/' . $filename . '.' . $file->getClientOriginalExtension());

                $fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $filename = Str::slug($fileName);
                $preferredName = request()->p_id . '/' . $filename . '.' . $file->getClientOriginalExtension();
                
                $path = $this->storeFileInUploadsDiskAndEncodeInDb($file, 'materials', $preferredName);
                
                Material::create([
                    'title' => $file->getClientOriginalName(),
                    'program_id' =>  request()->p_id,
                    'file' => $path,
                ]);
            }
            
            return back()->with('message', 'Study material succesfully added');
        } 
    }

    public function show(Material $material)
    {

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
        $material_count = Material::where('file', $material->file)->count();
        
        if ($material_count <= 1) {
            $file = base64_decode($material->file);

            if (file_exists(base_path() . '/uploads/materials' . '/' . $file)) {
                unlink(base_path() . '/uploads/materials' . '/' . $file);
            }
        }

        $material->delete();
        if (checkRoleHas(['Facilitator'])) {
            return back()->with('message', 'Material has been deleted forever');
        }
        return redirect('materials')->with('message', 'Study material succesfully deleted');
    }

    public function clone(Material $material, Request $request)
    {
        //create new material with existing material information except program id
        Material::create([
            'title' => $request->title,
            'program_id' =>  $request->program_id,
            'file' => $request->file,
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
}
