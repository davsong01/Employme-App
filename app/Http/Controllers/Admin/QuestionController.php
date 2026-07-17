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
        if (checkRoleHas(['Admin','Facilitator'])) {

            return view('dashboard.admin.questions.import', compact('p_id'));
        }
        return abort(404);
    }

    public function import(Request $request)
    {

        if (checkRoleHas(['Admin','Facilitator'])) {
            
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
            return back()->with('message', 'Data has been imported succesfully');
        }
        return abort(404);
    }

    public function index()
    {
        $i = 1;

        if (checkRoleHas(['Admin', 'Facilitator','Grader'])) {

            if (checkRoleHas(['Admin'])) {
                $programs_with_questions = Program::withCount('questions')->orderBy('id', 'desc')->get();
                return view('dashboard.admin.questions.index', compact('programs_with_questions', 'i'));
            }else if(checkRoleHas(['Facilitator', 'Grader'])){
                $trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();
                $programs_with_questions = Program::withCount('questions')->whereIn('id', $trainings)->orderBy('id', 'desc')->get();
                return view('dashboard.admin.questions.index', compact('programs_with_questions', 'i'));
            }else{

            }
        }
        
        return back();
    }

    public function create(Request $request)
    {
    }


    public function add($p_id)
    {
        if(checkRoleHas(['Admin'])) {
            $modules = Module::withCount('questions')->whereProgramId($p_id)->get();
            $selectedModuleId = request('module_id');
            $selectedModuleType = optional($modules->firstWhere('id', $selectedModuleId))->type;

            return view('dashboard.admin.questions.create', compact('modules', 'selectedModuleId', 'selectedModuleType'));
        }

         if(checkRoleHas(['Facilitator','Grader'])) {
            $modules = Module::withCount('questions')->whereProgramId($p_id)->get();
            $selectedModuleId = request('module_id');
            $selectedModuleType = optional($modules->firstWhere('id', $selectedModuleId))->type;

            return view('dashboard.admin.questions.create', compact('modules', 'selectedModuleId', 'selectedModuleType'));
        }

        return back();
    }

    public function store(Request $request)
    {
        $moduleId = $request->input('module');
        $questionRows = $request->input('questions');
        $legacyQuestion = $request->only(['module', 'title', 'optionA', 'optionB', 'optionC', 'optionD', 'correct']);

        if (is_array($questionRows)) {
            $questionRows = collect($questionRows)->filter(function ($question) {
                return !empty(data_get($question, 'title')) || !empty(data_get($question, 'module'));
            })->values()->all();
        } else {
            $moduleId = $moduleId ?? data_get($legacyQuestion, 'module');
            $questionRows = [$legacyQuestion];
        }

        if (empty($questionRows)) {
            return back()->with('error', 'Please add at least one question');
        }

        $preparedQuestions = [];
        $modulePlan = [];
        $moduleCache = [];

        foreach ($questionRows as $index => $questionData) {
            $title = trim((string) data_get($questionData, 'title', ''));
            $optionA = data_get($questionData, 'optionA');
            $optionB = data_get($questionData, 'optionB');
            $optionC = data_get($questionData, 'optionC');
            $optionD = data_get($questionData, 'optionD');
            $correct = data_get($questionData, 'correct');

            if (empty($moduleId) || empty($title)) {
                return back()->with('error', 'Each question needs a module and a title');
            }

            $module = $moduleCache[$moduleId] ??= Module::findOrFail($moduleId);
            $isCertification = $module->type === 'Certification Test';

            if ($isCertification) {
                $optionA = null;
                $optionB = null;
                $optionC = null;
                $optionD = null;
                $correct = null;
            }

            $payload = [
                'module' => $module,
                'data' => [
                    'title' => $title,
                    'optionA' => $optionA,
                    'optionB' => $optionB,
                    'optionC' => $optionC,
                    'optionD' => $optionD,
                    'correct' => $correct,
                    'module_id' => $module->id,
                ],
            ];

            $optionCheck = $this->checkOptions($payload['data'], $module);
            if ($optionCheck == 0) {
                return back()->with('error', 'No options needed for the associated module, please remove all options and try again');
            }

            if ($optionCheck == 2) {
                return back()->with('error', 'The associated module needs options, please enter all options and try again');
            }

            $preparedQuestions[] = $payload;
            $modulePlan[$module->id] = ($modulePlan[$module->id] ?? 0) + 1;
        }

        foreach ($modulePlan as $moduleId => $plannedCount) {
            $module = Module::findOrFail($moduleId);
            if ($module->questions()->count() + $plannedCount > $module->noofquestions) {
                return back()->with('error', 'One of the selected modules already has maximum number of questions');
            }
        }

        $createdQuestion = null;

        foreach ($preparedQuestions as $questionData) {
            $createdQuestion = Question::create($questionData['data']);
        }

        if (!$createdQuestion) {
            return back()->with('error', 'Unable to add the questions right now');
        }

        return redirect(route('modules.edit', ['p_id' => $createdQuestion->module->program_id, 'module' => $createdQuestion->module_id]) . '#questions')
            ->with('message', 'Questions successfully added');
    }
    public function show($p_id)
    {

        if (checkRoleHas(['Admin']) || checkRoleHas(['Facilitator','Grader'])) {
            return redirect(route('facilitatormodules', $p_id))
                ->with('message', 'Questions are now managed from each module.');
        }
    }

    public function edit(Question $question)
    {
        $modules = Module::orderBy('created_at', 'DESC')->get();
        
        return view('dashboard.admin.questions.edit', compact('question', 'modules'));
    }

    public function update(Request $request, Question $question)
    {
        $module = Module::findOrFail($request->input('module_id', $question->module_id));

        $data = $request->only(
            [
                'module_id',
                'title',
                'optionA',
                'optionB',
                'optionC',
                'optionD',
                'correct'
            ]);

        if ($module->type === 'Certification Test') {
            $data['optionA'] = null;
            $data['optionB'] = null;
            $data['optionC'] = null;
            $data['optionD'] = null;
            $data['correct'] = null;
        }

        $question->update($data);

        return redirect(route('modules.edit', ['p_id' => $question->module->program_id, 'module' => $question->module_id]) . '#questions')
            ->with('message', 'Question has been succesfully updated');
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return back()->with('message', 'Question has been deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected_questions', []);
        
        if (!empty($ids)) {
            Question::whereIn('id', $ids)->delete();
            return back()->with('success', 'Selected questions deleted successfully.');
        }
        return back()->with('error', 'No questions selected.');
    }


    private function checkOptions($values, $module)
    {
        if ($module->type == 'Certification Test') {
            if (!empty($values['optionA']) || !empty($values['optionB']) || !empty($values['optionC']) || !empty($values['optionD']) || !empty($values['correct'])) {
                return 0; //No options needed for the associated module
            } else {
                return 1; //Everything is fine, continue
            }
        }

        if ($module->type == 'Class Test' && (empty($values['optionA']) || empty($values['optionB']) || empty($values['optionC']) || empty($values['optionD']) || empty($values['correct']))) {
            return 2; //The associated module needs options
        } else {
            return 1; //Everything is fine, continue
        }
    }
}
