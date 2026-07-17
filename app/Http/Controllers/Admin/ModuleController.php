<?php


namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Models\Program;
use App\Models\Complain;
use App\Models\Question;
use App\Models\ScoreSetting;
use App\Models\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class ModuleController extends Controller
{

    public function index()
    {
        $i = 1;
        
        if (checkRoleHas(['Admin'])) {
            $modules = Module::with(['program', 'questions'])->orderBy('created_at', 'desc')->get();
            $questions_count = Question::all()->count();
            $programs_with_modules = Program::orderby('created_at', 'DESC')->get();
        }elseif (checkRoleHas(['Facilitator','Grader'])){
            $user_trainings = resolveAuthUser()->trainings->pluck('program_id')->toArray();

            $modules = Module::with(['program', 'questions'])
            ->orderBy('created_at', 'desc')
            ->whereHas('program', function ($query) use ($user_trainings) {
                $query->whereIn('id', $user_trainings);
            })->get();

            $programs_with_modules = Program::orderby('created_at', 'DESC')->whereIn('id', $user_trainings)->get();
            $questions_count = Question::whereIn('id', $modules->pluck('id')->toArray())->count();

        } else{
            return back();
        }

        return view('dashboard.admin.modules.index', compact('programs_with_modules', 'modules', 'i', 'questions_count'));
    }

    public function all($p_id)
    {
        $i = 1;

        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return route('home');
        }

        $program_name = Program::select('p_name', 'id')->whereId($p_id)->first();
        $modules = Module::with(['program', 'questions'])->whereProgramId($p_id)->orderBy('created_at', 'desc')->get();
        $questions_count = Module::withCount('questions')->whereProgramId($p_id)->get()->sum('questions_count');
        
        return view('dashboard.admin.modules.show', compact('program_name', 'p_id', 'modules', 'i', 'questions_count'));

    }
    public function create(Request $request)
    {
        $program = Program::select('id', 'p_name')->whereId($request->p_id)->first();

        return view('dashboard.admin.modules.create', compact('program'));
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'title' => 'required|min:5',
            'program' => 'required',
            'status' => 'required|numeric',
            'time' => 'nullable|numeric|min:0',
            'type' => 'required|numeric',
            'noofquestions' => 'required|numeric',
            'allow_test_retake' => 'required|numeric'

        ]);
        
        // if ($data['type'] == 1 && $data['noofquestions'] > 1) {
        //     return back()->with('error', 'Module of type certification can only accomodate 1 question per module');
        // }
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
            'time' => $request->time ?? 0,
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
       
        if(checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            $program = Program::whereId($module->program_id)->first();
            $questions = $module->questions()->latest()->get();
            $questionImportProgramId = $module->program_id;
            $selectedModuleId = $module->id;
            $selectedModuleType = $module->type;

            return view('dashboard.admin.modules.edit', compact(
                'module',
                'program',
                'questions',
                'questionImportProgramId',
                'selectedModuleId',
                'selectedModuleType'
            ));
        }else{
            return back();
        }
    }

    public function syncQuestions(Request $request, Module $module)
    {
        if (!checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return back();
        }

        $validated = $this->validate($request, [
            'title' => 'required|min:5',
            'program_id' => 'required|numeric',
            'status' => 'required|numeric',
            'computation_status' => 'nullable|numeric',
            'noofquestions' => 'required|numeric|min:1',
            'time' => 'nullable|numeric|min:0',
            'type' => 'required',
            'allow_test_retake' => 'required|numeric',
            'questions' => 'required|array',
        ]);

        $questionRows = collect($request->input('questions'))
            ->filter(function ($question) {
                return trim((string) data_get($question, 'title', '')) !== '';
            })
            ->values()
            ->all();

        if (empty($questionRows)) {
            return back()->withInput()->with('error', 'Please add at least one question');
        }

        $questionLimit = (int) $validated['noofquestions'];
        if (count($questionRows) > $questionLimit) {
            return back()->withInput()->with('error', 'This module can only hold ' . $questionLimit . ' question(s)');
        }

        $isCertification = in_array((string) $validated['type'], ['1', 'Certification Test'], true);
        $existingQuestions = $module->questions()->get()->keyBy('id');
        $preparedRows = [];

        foreach ($questionRows as $row) {
            $title = trim((string) data_get($row, 'title', ''));
            $questionId = data_get($row, 'id');
            $optionA = data_get($row, 'optionA');
            $optionB = data_get($row, 'optionB');
            $optionC = data_get($row, 'optionC');
            $optionD = data_get($row, 'optionD');
            $correct = data_get($row, 'correct');

            if ($isCertification) {
                $optionA = null;
                $optionB = null;
                $optionC = null;
                $optionD = null;
                $correct = null;
            } elseif (empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || empty($correct)) {
                return back()->withInput()->with('error', 'Each class test question needs all options and a correct answer');
            }

            if ($questionId && !$existingQuestions->has((int) $questionId)) {
                return back()->withInput()->with('error', 'One of the questions could not be found. Please reload and try again');
            }

            $preparedRows[] = [
                'id' => $questionId ? (int) $questionId : null,
                'payload' => [
                    'title' => $title,
                    'optionA' => $optionA,
                    'optionB' => $optionB,
                    'optionC' => $optionC,
                    'optionD' => $optionD,
                    'correct' => $correct,
                    'module_id' => $module->id,
                ],
            ];
        }

        DB::transaction(function () use ($module, $validated, $preparedRows, $existingQuestions) {
            $module->update([
                'title' => $validated['title'],
                'program_id' => $validated['program_id'],
                'status' => $validated['status'],
                'computation_status' => $validated['computation_status'] ?? $module->computation_status,
                'noofquestions' => $validated['noofquestions'],
                'time' => $validated['time'] ?? 0,
                'type' => $validated['type'],
                'allow_test_retake' => $validated['allow_test_retake'],
            ]);

            $submittedIds = [];

            foreach ($preparedRows as $preparedRow) {
                if ($preparedRow['id']) {
                    $question = $existingQuestions->get($preparedRow['id']);
                    $question->update($preparedRow['payload']);
                    $submittedIds[] = $question->id;
                    continue;
                }

                $question = Question::create($preparedRow['payload']);
                $submittedIds[] = $question->id;
            }

            $module->questions()->whereNotIn('id', $submittedIds)->delete();
        });

        return redirect(route('modules.edit', ['p_id' => $module->program_id, 'module' => $module->id]) . '#questions')
            ->with('message', 'Module and questions updated successfully');
    }

    public function update(Request $request, Module $module)
    {

        // if ($request->type == 0) {
        //     $type = 0;
        // }
        $module->update([
            'title' => $request->title,
            'program_id' => $request->program_id,
            'status' => $request->status,
            'computation_status' => $request->computation_status,
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
