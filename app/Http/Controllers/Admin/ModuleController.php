<?php


namespace App\Http\Controllers\Admin;

use App\Module;
use App\Program;
use App\Complain;
use App\Question;
use App\ScoreSetting;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ModuleController extends Controller
{

    public function index()
    {
        if(Auth::user()->role_id == "Admin"){
            $i = 1;  
            //my first use of Eager loading
            $modules = Module::with( ['program', 'questions'] )->orderBy('id', 'DECS')->get();          
            $questions_count = Question::all()->count();
          
            return view('dashboard.admin.modules.index', compact('modules', 'i', 'questions_count'));
        }
        
        if(Auth::user()->role_id == "Facilitator"){
            
            $facilitator_programs = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();

            $materialCount = 0;
            $i = 1;
            if($facilitator_programs->count() > 0){
                foreach($facilitator_programs as $modules){
                    $modules['p_name'] = Program::whereId($modules->program_id)->value('p_name');
                    $modules['materialCount'] = Module::whereProgramId($modules->program_id)->count();
                } 
            }
            
            return view('dashboard.teacher.modules.show', compact( 'i', 'facilitator_programs'));
        } return back();
    }

    public function all($p_id){
        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            $i = 1;  
            $modules = Module::with( ['program', 'questions'] )->whereProgramId($p_id)->orderBy('id', 'DECS')->get();          
            $questions_count = Question::all()->count();
          
            return view('dashboard.teacher.modules.show', compact('modules', 'i', 'questions_count'));
        }
    }
    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->orderBy('id', 'DESC')->get();
            return view('dashboard.admin.modules.create', compact('programs'));
        }

        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            $programs = Program::where('id', '<>', 1)->where('id', auth()->user()->program->id)->orderBy('id', 'DESC')->get();
            return view('dashboard.admin.modules.create', compact('programs'));
        }
        return back();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|min:5',
            'program' => 'required',
            'status' => 'required|numeric',
            'time' => 'required|numeric|min:2',
            'type' => 'required|numeric',
            'noofquestions' => 'required|numeric'
        ]);

        //check if scoresettings exist for this program
        $score_settings_check = ScoreSetting::where('program_id', $data['program'])->get();

        if($score_settings_check->count() < 1){
            return redirect('scoreSettings')->with('error', 'No score settings defined! Please define Score settings for this program below');
        }
        
        $module = Module::create([
            'title' => $request->title,
            'program_id' => $request->program,
            'status' => $request->status,
            'noofquestions' => $request->noofquestions,
            'time' => $request->time,
            'type' => $request->type,
        ]);

        return redirect('modules')->with('message', 'Module succesfully added');
    }

    public function show(Module $module)
    {
        
    }

    public function enablemodule($id){
        $module = Module::findOrFail($id);
        if($module->questions->count() <= 0 || $module->questions->count() < $module->noofquestions){
            return back()->with('error', 'You cannot enable a module with empty questions or less than expected questions, Please add questions to this module');
        }
        $module->status = 1;
        $module->save();

        return back()->with('message', 'This Module and its questions have been enabled Successfully ');
    }

    public function disablemodule($id){
        $module = Module::findOrFail($id);
        $module->status = 0;
        $module->save();
        return back()->with('message', 'This Module and its questions have been disabled uccessfully ');
    }

    public function edit(Module $module)
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
            return view('dashboard.admin.modules.edit', compact('module', 'programs'));
        }

        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            $programs = Program::where('id', '<>', 1)->where('id', auth()->user()->program->id)->orderBy('created_at', 'DESC')->get();
            return view('dashboard.admin.modules.edit', compact('module', 'programs'));
        }
        return back();
    }

    public function update(Request $request, Module $module)
    {
            $module->update([
                'title' => $request->title,
                'program_id' => $request->program_id,
                'status' => $request->status,
                'noofquestions' => $request->noofquestions,
                'time' => $request->time,
                'type' => $request->type,
            ]);
        return redirect('modules')->with('message', 'Module has been succesfully updated');
    }

    public function destroy(Module $module)
    {
        //
    }
}
