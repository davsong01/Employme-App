<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Program;
use App\Models\Complain;
use App\Models\Material;
use App\Models\Question;
use App\Models\FacilitatorTraining;
use Illuminate\Http\Request;
use App\Imports\QuestionsImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{

    public function importExport($p_id)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {

            return view('dashboard.admin.questions.import', compact('p_id'));
        }
        return abort(404);
    }

    public function import(Request $request)
    {

        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {

            $this->validate(request(), [
                'file' => 'required|
				mimetypes:xlsv,xlsx,xls,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
				application/excel,application/x-excel,application/x-msexcel,text/comma-seperated-values, text/csv'
            ], [
                'file.mimetypes' => 'The file must be a file of type: xlsx'
            ]);

            try {
                Excel::import(new QuestionsImport($request->p_id), request()->file('file'));
            } catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();
                return back()->with('error', $error);
            }
            return redirect(route('questions.index'))->with('message', 'Data has been imported succesfully');
        }
        return abort(404);
    }

    public function index()
    {
        $i = 1;

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {

            $programs_with_questions = Program::withCount('questions')->orderBy('id', 'desc')->get();

            return view('dashboard.admin.questions.index', compact('programs_with_questions', 'i'));
        }


        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {

            $programs_with_questions = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();

            if ($programs_with_questions->count() > 0) {
                foreach ($programs_with_questions as $modules) {
                    $modules['p_name'] = Program::whereId($modules->program_id)->value('p_name');
                    $modules['program_id'] = Program::whereId($modules->program_id)->value('id');
                    $modules['questions_count'] = Module::withCount('questions')->whereProgramId($modules->program_id)->get()->sum('questions_count');
                }
            }

            // dd($programs_with_modules);
            return view('dashboard.teacher.questions.index', compact('i', 'programs_with_questions'));
        }
        return back();
    }

    public function create(Request $request)
    {
    }


    public function add($p_id)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $modules = Module::withCount('questions')->whereProgramId($p_id)->get();

            return view('dashboard.admin.questions.create', compact('modules'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            $modules = Module::withCount('questions')->whereProgramId($p_id)->get();
            return view('dashboard.admin.questions.create', compact('modules'));
        }

        return back();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'module' => 'required|numeric',
            'title' => 'required',
            'optionA' => 'sometimes',
            'optionB' => 'sometimes',
            'optionC' => 'sometimes',
            'optionD' => 'sometimes',
            'correct' => 'sometimes',
        ]);


        //get module
        $module = Module::findorFail($data['module']);

        //Check if options are needed or not for this question
        if ($this->checkOptions($data, $module) == 0) {
            return back()->with('error', 'No options needed for the associated module, please remove all options and try again');
        }

        if ($this->checkOptions($data, $module) == 2) {
            return back()->with('error', 'The associated module needs options, please enter all options and try again');
        }

        //check if module already has maximum number of questions
        if ($module->questions->count() >= $module->noofquestions) {
            return back()->with('error', 'The associated module already has maximum number of questions');
        }
        $question = Question::create([
            'title' => $request->title,
            'optionA' => $request->optionA,
            'optionB' => $request->optionB,
            'optionC' => $request->optionC,
            'optionD' => $request->optionD,
            'correct' => $request->correct,
            'module_id' => $request->module
        ]);

        return redirect('questions')->with('message', 'Question succesfully added');
    }
    public function show($p_id)
    {

        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            $i = 1;

            $questions = Question::whereHas('module', function ($query) use ($p_id) {
                $query->whereProgramId($p_id);
            })->get();

            $p_name = Program::whereId($p_id)->value('p_name');

            return view('dashboard.admin.questions.show', compact('questions', 'i', 'p_name', 'p_id'));
        }
    }

    public function edit(Question $question)
    {
        $modules = Module::orderBy('created_at', 'DESC')->get();
        return view('dashboard.admin.questions.edit', compact('question', 'modules'));
    }

    public function update(Request $request, Question $question)
    {
        $question->update($request->all());
        return back()->with('message', 'Question has been succesfully updated');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('message', 'Question has been deleted successfully');
    }

    private function checkOptions($values, $module)
    {
        if ($module->type == 'Certification Test') {
            if (!empty($values['optionA']) || !empty($values['optionB']) && !empty($values['optionC']) || !empty($values['optionD']) || !empty($values['correct'])) {
                return 0; //No options needed for the associated module
            } else {
                return 1; //Everything is fine, continue
            }
        }

        if ($module->type == 'Class Test' && (empty($values['optionA']) || empty($values['optionB']) && empty($values['optionC']) || empty($values['optionD']) || empty($values['correct']))) {
            return 2; //The associated module needs options
        } else {
            return 1; //Everything is fine, continue
        }
    }
}
