<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Mocks;
use App\Models\Module;
use App\Models\Result;
use App\Models\Program;
use App\Models\Question;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\ResultThread;
use Illuminate\Http\Request;
use App\Models\TempTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TestsController extends Controller
{
    public function index(Request $request)
    {
        $programId = $request->p_id;
        $userId = resolveAuthUser()->id;

        $transaction = Transaction::where('program_id', $programId)
            ->where('user_id', $userId)
            ->first();

        if (checkRoleHas(['Student'])) {

            $program = Program::find($programId);

            if (!$program) {
                return back()->with('error', 'Program not found.');
            }

            // ------------------------------------
            //  PAYMENT CHECK (SAFE VERSION)
            // ------------------------------------
            if ($program->allow_payment_restrictions_for_post_class_tests === 'yes') {
                $payment = getTransactionFromProgramIds($request->p_id);
                $balance = $payment->balance ?? 0;
                $currency = $payment->currency_symbol;
                
                $specialCandidates = [
                    // 20250
                ];
                
                if (
                    $payment &&
                    $balance > 0 &&
                    !in_array($payment->user_id, $specialCandidates)
                ) {
                    return back()->with(
                        'error',
                        'Please Pay your balance of ' .
                            ($currency ?? '') .
                            number_format($balance) .
                            ' in order to get access to tests'
                    );
                }
            }

            $i = 1;

            // ------------------------------------
            //  REDO OVERRIDE
            // ------------------------------------
            if (resolveAuthUser()->redotest == 1) {
                $modules = Module::with('questions')
                    ->where('program_id', $program->id)
                    ->get();
            }
            // ------------------------------------
            //  ENSURE PRE-TESTS ARE COMPLETED
            // ------------------------------------
            if ($program->hasmock == 1) {
                $expected = Module::ClassTests($program->id)->count();

                $completed = Mocks::where('program_id', $program->id)
                    ->where('user_id', $userId)
                    ->count();

                if ($completed < $expected) {
                    return redirect('mocks?p_id=' . $program->id)
                        ->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Post Class Tests');
                }
            }

            // ------------------------------------
            //  ACTUAL MODULES LOAD
            // ------------------------------------
            $modules = Module::with('questions')
                ->where('program_id', $program->id)
                ->where('computation_status', 1)
                ->get()
                ->reject(function ($module) use ($transaction) {
                    $resitStatus = $this->getResitTrainingStatus($module->type, $transaction);
                    
                    // if resit not allowed and module inactive → hide
                    return $resitStatus['status'] != 1 && $module->status == 0;
                });
            // ------------------------------------
            //  DECORATE MODULES
            // ------------------------------------

            foreach ($modules as $module) {
                $moduleCheck = Result::where('module_id', $module->id)
                    ->where('user_id', $userId)
                    ->where('program_id', $program->id)
                    ->first();

                $resitStatus = $this->getResitTrainingStatus($module->type, $transaction);

                $expiry = $resitStatus['expiry'];
        
                // Completed?
                $module->completed = $moduleCheck ? 1 : 0;

                // Redo allowed?
                $module->redo = ($moduleCheck &&
                    $resitStatus['status'] == 1 &&
                    $expiry > now())
                    ? ($moduleCheck->redo_test ?? 0)
                    : 0;

                // Expiry date
                $module->expiry = $expiry;
            }

            return view('dashboard.student.tests.index', compact('modules', 'i', 'program'));
        }

        return back()->with('error', 'Unauthorized');
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $program = Program::find($request->p_id);
        
        $class_test_details = $request->except(['_token', 'mod_id', 'id', 'prefix__']);
        $resit = false;

        if (sizeof($class_test_details) < 2) {
            return back()->with('error', 'You must answer at least 1 question');
        };

        $certification_test_details = $request->except(['_token', 'mod_id', 'id', 'p_id', 'prefix__']);
        
        foreach ($certification_test_details as $key => $value) {
            if ((!isset($certification_test_details[$key]))) {
                return back()->with('error', 'You must answer at least 1 question');
            };

            if (str_word_count($certification_test_details[$key]) > 500) {
                return back()->with('error', 'Maximum number of words allowed for each question is 500, please try again');
            };
        }

        $check = Result::where('user_id', resolveAuthUser()->id)->where('module_id', $request->mod_id)->first();
        $module = Module::findOrFail($request->mod_id);
        
        $transaction = Transaction::select('id', 'training_result', 'training_result_histories', 'user_id', 'program_id')
        ->where('program_id', $request->p_id)
            ->where('user_id', resolveAuthUser()->id)
            ->first();
        
        $resitStatus = $this->getResitTrainingStatus($module->type, $transaction);
        
        // Get Resit Status
        if($check && ($resitStatus['status'] == 0 || $resitStatus['status'] == 2)){
            return back()->with('error', 'You have already taken this test, Please click "Post Class Tests" on the left navigation bar to take an available test!');
        }
        
        if ($resitStatus['status'] == 1 && $module->type == 'Certification Test') {
            if(isset($resitStatus['expiry'])){
                $parsedDate = \Carbon\Carbon::parse($resitStatus['expiry']);

                if(now() > $parsedDate){
                    return back()->with('error', 'The resit period for this test elapsed on: ' .$parsedDate.'. Please contact an administrator!');
                }
            }

            if(!$check){
                $check = $this->createResult($module, $certification_test_details);
                $check->update([
                    'certification_test_details' => json_encode($certification_test_details),
                    'redo_test' => 2
                ]);
            }
            
            // End redo test
            $data["certification_test_resit_status"] = 2;
            $data["certification_test_resit_expiry"] = NULL;
            $data["last_updated_at"] = now();

            $email = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');

            if(!empty($transaction->training_result->certification_test_resit_enabled_by_id)){
                $user = User::select('id','name','email')->where('id', $transaction->training_result->certification_test_resit_enabled_by_id )->first();
                
                if($user){
                    $email = $user->email;
                }
            }
            
            udateTrainingResult($transaction->program_id, $transaction->user_id, $data);

            $details['subject'] = 'Test Re-write successful';
            $details['email'] = $email;
            $details['content'] = 'Hello, <br><br>'.resolveAuthUser()->name.'('.resolveAuthUser()->email.') has completed a rewrite of certification test for <strong>'.$program->p_name.'</strong> training. <br><br>Please proceed to grade accordingly. <br><br>Thanks' ;
            $details['type'] = 'bulk';
            
            $this->sendGenericEmail($details);
        } else {
            if($resitStatus['status'] == 1 && $module->type == 'Class Test'){
                if (isset($resitStatus['expiry'])) {
                    $parsedDate = \Carbon\Carbon::parse($resitStatus['expiry']);
                    
                    if (now() > $parsedDate) {
                        return back()->with('error', 'the resit period for this test elapsed on: ' . $parsedDate . '. Please contact an administrator!');
                    }else{
                        if ($check) {
                            return back()->with('error', 'You have already taken this test, Please click "Post Class Tests" on the left navigation bar to take an available test!');
                        }
                    }

                    $resit = true;
                }
            }

            $questions = $module->questions->toArray();
            $no_of_questions = count($questions);
            $score = 0;

            if ($module->type == 'Certification Test') {
                try {
                    $result = $this->createResult($module, $certification_test_details);
                } catch (\Illuminate\Database\QueryException $ex) {
                    if ($ex->errorInfo[1] == 1062) { // MySQL duplicate entry code
                        return back()->with(
                            'error',
                            'You have already taken this test. Please click "Post Class Tests" on the left navigation bar to take an available test.'
                        );
                    }
                    return back()->with('error',  'Something went wrong, please try again');
                }
            } elseif ($module->type == 'Class Test') {
                foreach ($questions as $question) {
                    $question_id = $question['id'];
                    if ($request[$question_id] == $question['correct']) {
                        $score = $score + 1;
                    } else {
                        $score;
                    }
                }

                try {
                    if ($module->type == 'Class Test') {
                        $result = $this->createResult($module, $class_test_details, $score);
                    }
                } catch (\Illuminate\Database\QueryException $ex) {
                    if ($ex->errorInfo[1] == 1062) { // MySQL duplicate entry code
                        return back()->with(
                            'error',
                            'You have already taken this test. Please click "Post Class Tests" on the left navigation bar to take an available test.'
                        );
                    }

                    return back()->with('error', 'Something went wrong, please try again.');
                }
            }
        }

        // Update training result
        $data["last_updated_at"] = now();

        if($resit){
            $expectedResultCount = Module::where('program_id', $transaction->program_id)->where('computation_status', 1)->where('type',0)->count();

            $resultCount = Result::where('program_id', $transaction->program_id)
            ->where('user_id', $transaction->user_id)
            ->whereNotNull('class_test_details')
            ->distinct()
            ->count('module_id');

            if ($expectedResultCount == $resultCount) {
                $data["class_test_resit_status"] = 2;
                $data["class_test_resit_expiry"] = NULL;
            }

            if($result){
                $result->update(['redo_test' => 2]);
            }
        }
        
        udateTrainingResult($transaction->program_id, $transaction->user_id, $data);
        session()->forget('exam_timer:' . resolveAuthUser()->id . ':' . $request->p_id . ':' . $request->mod_id);

        return Redirect::to('userresults?p_id=' . $program->id);
    }

    public function createResult($module, $details, $score = 0){
        $type = $module->type;

        if($type == 'Certification Test'){
            return Result::create([
                'program_id' => $module->program->id,
                'user_id' => resolveAuthUser()->id,
                'module_id' => $module->id,
                'certification_test_details' => json_encode($details),
                'duplicate_key' => $module->id . '-' . resolveAuthUser()->id . '-' . $module->program->id
            ]);
        }

        if($type == 'Class Test'){
            return Result::create([
                'program_id' => $module->program->id,
                'user_id' => resolveAuthUser()->id,
                'module_id' => $module->id,
                'class_test_score' => $score,
                'class_test_details' => json_encode($details),
                'duplicate_key' => $module->id . '-' . resolveAuthUser()->id . '-' . $module->program->id
            ]);
        }
    }

    public function getResitTrainingStatus($module_type, $transaction){
        $status = 0;
        $expiry = NULL;
       
        if($module_type == 'Class Test'){
            $status =  $transaction->training_result->class_test_resit_status ?? null;
            if(!empty($status) && $status == 1){
                $expiry = $transaction->training_result->class_test_resit_expiry;
            }
        }

        if($module_type == 'Certification Test'){
            $status =  $transaction->training_result->certification_test_resit_status ?? null;
            if(!empty($status) && $status == 1){
                $expiry = $transaction->training_result->certification_test_resit_expiry;
            }
        }

        return [
            'status' => $status,
            'expiry' => $expiry,
        ];
    }

    public function userresults(Request $request)
    {

        if (checkRoleHas(['Student'])){

            $i = 1;
            $program = Program::find($request->p_id);

            if (!$program->hasresult) {
                return back()->with('error', 'Results for this program have not been enabled, Please check back!');
            }
            $hasmock = $program->hasmock;

            if ($program->allow_payment_restrictions_for_completed_tests == 'yes') {
                $transaction = getTransactionFromProgramIds($request->p_id);
                $balance = $transaction->balance ?? 0;
                $currency = $transaction->currency_symbol;

                if ($balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $currency.number_format($balance) . ' in order to access tests');
                }
            }

            //Check if user has taken pre tests and return back if otherwise
            if ($program->hasmock == 1) {
                $expected_pre_class_tests = Module::ClassTests($program->id)->count();
                $completed_pre_class_tests = Mocks::where('program_id', $program->id)->where('user_id', resolveAuthUser()->id)->count();

                if ($completed_pre_class_tests < $expected_pre_class_tests) {
                    return Redirect::to('mocks?p_id=' . $program->id)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Post Class Tests');
                }
            }

            $results = Result::with('module')->where('user_id', resolveAuthUser()->id)->whereProgramId($program->id)->orderBy('module_id', 'DESC')->get();

            $cert_score = Result::where('user_id', resolveAuthUser()->id)->whereProgramId($program->id)->sum('certification_test_score');

            //If cert score is 0 or less, it is recorded as processing

            foreach ($results as $result) {
                $result['certification_test_score'] =  $cert_score;
            }

            $mock_results = Mocks::with('module')->where('user_id', resolveAuthUser()->id)->whereProgramId($program->id)->orderBy('module_id', 'DESC')->get();

            return view('dashboard.student.tests.result', compact('results', 'i', 'program', 'mock_results', 'hasmock'));
        }
    }


    public function show($id, Request $request)
    {
        $questions = Question::with('module')->where('module_id', $id)->get();
        $i = 1;
        
        //check if registered module
        $questionsarray = $questions->toArray();
        if ($questionsarray[0]['module']['program_id'] <> $request->p_id) {
            return abort(404);
        };

        $module_type = Module::where('id', $id)->value('type');
        $timerKey = 'exam_timer:' . resolveAuthUser()->id . ':' . $request->p_id . ':' . $id;
        $timerState = session($timerKey, []);
        $startedAt = data_get($timerState, 'started_at');
        $expiresAt = data_get($timerState, 'expires_at');
        $now = now()->timestamp;

        foreach ($questions as $question) {
            $program_name = $question->module->program->p_name;
            $program = $question->module->program()->first();
            $time = $question->module->time;
            $module_title = $question->module->title;
        }

        if (empty($startedAt) || empty($expiresAt)) {
            $startedAt = $now;
            $expiresAt = $now + ((int) $time * 60);

            session()->put($timerKey, [
                'started_at' => $startedAt,
                'expires_at' => $expiresAt,
            ]);
        }

        $remainingSeconds = max(0, $expiresAt - $now);

        if ($module_type == 'Class Test') {
            return view('dashboard.student.tests.quizz', compact('questions', 'i', 'program', 'program_name', 'module_title', 'time', 'remainingSeconds'));
        }
        if ($module_type == 'Certification Test') {
            return view('dashboard.student.tests.certification', compact('questions', 'i', 'program', 'program_name', 'module_title', 'time', 'remainingSeconds'));
        }
    }

    public function userResultComments(Request $request, $id)
    {
        $comments = Result::where('id', $id)->first();
        $program = Program::find($request->p_id);
        return view('dashboard.student.tests.result_comments', compact('comments', 'id', 'program'));
    }

    public function retakeTest(Request $request, Module $module){
        // $results = Result::where('module_id', $module->id)->whereProgramId(request()->get('p_id'))->where('user_id', resolveAuthUser()->id)->get();
        $details = certificationStatus(request()->get('p_id'), resolveAuthUser()->id);
        $results = $details['results'];
        
        if (!$details || $details['status'] == 'CERTIFIED') {
            return back()->with('error', 'You are already certified for this training');
        }
        
        foreach($results as $result){
            $history = app('App\Http\Controllers\Admin\ResultController')->createResultThread($result);
            $result->delete();
            
            return Redirect::to('tests?p_id=' . request()->get('p_id'));
        }

    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
