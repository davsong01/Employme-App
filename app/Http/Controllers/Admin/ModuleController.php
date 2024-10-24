<?php


namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Program;
use App\Models\Complain;
use App\Models\Question;
use App\Models\ScoreSetting;
use App\Models\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ModuleController extends Controller
{

    public function index()
    {
        $i = 1;

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $modules = Module::with(['program', 'questions'])->orderBy('created_at', 'desc')->get();
            $questions_count = Question::all()->count();

            // $programs_with_modules = Program::whereHas('modules', function ($query) {
            //     return $query;
            // })->orderby('created_at', 'DESC')->get();
            $programs_with_modules = Program::orderby('created_at', 'DESC')->get();

            return view('dashboard.admin.modules.index', compact('programs_with_modules', 'modules', 'i', 'questions_count'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {

            $programs_with_modules = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();
            if ($programs_with_modules->count() > 0) {
                foreach ($programs_with_modules as $modules) {
                    $modules['p_name'] = Program::whereId($modules->program_id)->value('p_name');
                    $modules['modules_count'] = Program::withCount('modules')->whereId($modules->program_id)->get()->sum('modules_count');
                    $modules['questions_count'] = Module::withCount('questions')->whereProgramId($modules->program_id)->get()->sum('questions_count');
                }
            }

            return view('dashboard.teacher.modules.index', compact('i', 'programs_with_modules'));
        }
        return back();
    }

    public function all($p_id)
    {
        $i = 1;

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $program_name = Program::select('p_name', 'id')->whereId($p_id)->first();
            $modules = Module::with(['program', 'questions'])->whereProgramId($p_id)->orderBy('created_at', 'desc')->get();
            $questions_count = Module::withCount('questions')->whereProgramId($p_id)->get()->sum('questions_count');

            return view('dashboard.admin.modules.show', compact('program_name', 'p_id', 'modules', 'i', 'questions_count'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {

            $program_name = Program::select('p_name', 'id')->whereId($p_id)->first();
            $modules = Module::with(['program', 'questions'])->whereProgramId($p_id)->orderBy('created_at', 'desc')->get();
            $questions_count = Module::withCount('questions')->whereProgramId($p_id)->get()->sum('questions_count');

            return view('dashboard.teacher.modules.show', compact('program_name', 'modules', 'i', 'questions_count'));
        }
    }
    public function create(Request $request)
    {
        $program = Program::select('id', 'p_name')->whereId($request->p_id)->first();

        return view('dashboard.admin.modules.create', compact('program'));
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
        }

        // if(!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))|| !empty(array_intersect(graderRoles(), Auth::user()->role()))){

        //     return view('dashboard.admin.modules.create', compact('programs'));
        // }
        // return back();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|min:5',
            'program' => 'required',
            'status' => 'required|numeric',
            'time' => 'required|numeric|min:2',
            'type' => 'required|numeric',
            'noofquestions' => 'required|numeric',
            'allow_test_retake' => 'required|numeric'

        ]);

        if ($data['type'] == 1 && $data['noofquestions'] > 1) {
            return back()->with('error', 'Module of type certification can only accomodate 1 question per module');
        }
        //check if scoresettings exist for this program
        $score_settings_check = ScoreSetting::where('program_id', $data['program'])->get();

        if ($score_settings_check->count() < 1) {
            return redirect('scoreSettings')->with('error', 'No score settings defined! Please define Score settings for this program below');
        }

        $module = Module::create([
            'title' => $request->title,
            'program_id' => $request->program,
            'status' => $request->status,
            'noofquestions' => $request->noofquestions,
            'time' => $request->time,
            'type' => $request->type,
            'allow_test_retake' => $request->allow_test_retake
        ]);

        return redirect(route('facilitatormodules', $request->program))->with('message', 'Module succesfully added');
    }

    public function clone(Request $request)
    {
        //find module
        $module = Module::findOrFail($request->id);

        //convert module type to integer
        if ($module->type == 'Class Test') {
            $type = 0;
        }

        if ($module->type == 'Certification Test') {
            $type = 1;
        }

        //Get Module questions 
        $module_questions = Question::whereModuleId($request->id)->get();

        //Create new module
        $new_module = Module::create([
            'title' => $module->title,
            'program_id' => $request->program_id,
            'status' => $module->status,
            'noofquestions' => $module->noofquestions,
            'time' => $module->time,
            'allow_test_retake' => $module->allow_test_retake,
            'type' => $type,
        ]);

        //Duplicate module questions for newly created module       
        foreach ($module_questions as $question) {
            $new = Question::create([
                'title' => $question->title,
                'optionA' => $question->optionA,
                'optionB' => $question->optionB,
                'optionC' => $question->optionC,
                'optionD' => $question->optionD,
                'correct' => $question->correct,
                'module_id' => $new_module->id,
            ]);
        }

        return redirect(route('facilitatormodules', $request->program_id))->with('message', 'Module and associated questions succesfully cloned');
    }

    public function show(Module $module)
    {
        $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'desc')->get();
        return view('dashboard.teacher.modules.showclone', compact('module', 'programs'));
    }

    public function enablemodule($id)
    {
        $module = Module::findOrFail($id);
        if ($module->questions->count() <= 0 || $module->questions->count() < $module->noofquestions) {
            return back()->with('error', 'You cannot enable a module with empty questions or less than expected questions, Please add questions to this module');
        }
        $module->status = 1;
        $module->save();

        return back()->with('message', 'This Module and its questions have been enabled Successfully ');
    }

    public function disablemodule($id)
    {
        $module = Module::findOrFail($id);
        $module->status = 0;
        $module->save();
        return back()->with('message', 'This Module and its questions have been disabled uccessfully ');
    }

    public function edit(Module $module)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $program = Program::whereId($module->program_id)->first();

            return view('dashboard.admin.modules.edit', compact('module', 'program'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {

            $program = Program::whereId($module->program_id)->first();

            return view('dashboard.teacher.modules.edit', compact('module', 'program'));
        }
        return back();
    }

    public function update(Request $request, Module $module)
    {

        if ($request->type == 0) {
            $type = 0;
        }
        $module->update([
            'title' => $request->title,
            'program_id' => $request->program_id,
            'status' => $request->status,
            'noofquestions' => $request->noofquestions,
            'time' => $request->time,
            'type' => $request->type,
            'allow_test_retake' => $request->allow_test_retake
        ]);
        return redirect(route('facilitatormodules', $module->program))->with('message', 'Module succesfully updated');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return back()->with('message', 'Module deleted successfully');
    }
}
