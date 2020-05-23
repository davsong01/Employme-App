<?php


namespace App\Http\Controllers\Admin;

use App\Module;
use App\Program;
use App\Complain;
use App\Question;
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
        } return back();
    }
    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->get();
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
            'time' => 'required|numeric|min:5',
            'type' => 'required|numeric',
            'noofquestions' => 'required|numeric|min:5|max:20'
        ]);

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
        //
    }

    public function enablemodule($id){
        $module = Module::findOrFail($id);
        if($module->questions->count() <= 0){
            return back()->with('error', 'You cannot enable a module with empty questions, Please add questions to this module');
        }
        $module->status = 1;
        $module->save();

        // Program::where('id',$module->program->id)->where('hasmodule', 0)->first()->update(array('hasmodule' => 1));
        //  = Program::update([

        // ]);

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
        return back();
    }

    public function update(Request $request, Module $module)
    {
        $module->update($request->all());
        return redirect('modules')->with('message', 'Module has been succesfully updated');
    }

    public function destroy(Module $module)
    {
        //
    }
}
