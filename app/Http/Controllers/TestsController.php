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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TestsController extends Controller
{
    public function index(Request $request)
    {
        $transaction = Transaction::where('program_id',  $request->p_id)->where('user_id', resolveAuthUser()->id)->first();
       
        if (checkRoleHas(['Student'])){
            $program = Program::find($request->p_id);

            if ($program->allow_payment_restrictions_for_post_class_tests == 'yes') {
                if ($transaction->balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $transaction->currency_symbol . number_format($transaction->balance) . ' in order to get access to tests');
                }
            }

            $i = 1;

            $modules = Module::with('questions')->where('program_id', $program->id)
            // ->where('status', 1)
            ->where('computation_status', 1)
            ->get();

            if (resolveAuthUser()->redotest == $program->id) {
                $modules = Module::with('questions')->where('program_id', $program->id)->get();
            }

            //Check if user has taken pre tests and bounce back if otherwise
            if ($program->hasmock == 1) {
                $expected_pre_class_tests = Module::ClassTests($program->id)->count();

                $completed_pre_class_tests = Mocks::where('program_id', $program->id)->where('user_id', resolveAuthUser()->id)->count();
                if ($completed_pre_class_tests < $expected_pre_class_tests) {
                    return Redirect::to('mocks?p_id=' . $program->id)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Post Class Tests');
                }
            }
            
            foreach ($modules as $module) {
                $module_check = Result::where('module_id', $module->id)->where('user_id', resolveAuthUser()->id)->get();
                $resitStatus = $this->getResitTrainingStatus($module, $transaction);
                $expiry = $resitStatus['expiry'];
                
                if ($resitStatus['status'] == 1 && $expiry > now()) {
                    $module['redo'] = 1;
                    $module['completed'] = 0;
                    $module['expiry'] = $expiry;
                    $module['status'] = 1;
                } else {
                    $module['redo'] = 0;
                    $module['completed'] = 1;
                    $module['expiry'] = $expiry;
                }
            }

            // dd($modules);
            return view('dashboard.student.tests.index', compact('modules', 'i', 'program'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $program = Program::find($request->p_id);
        
        $class_test_details = $request->except(['_token', 'mod_id', 'id', 'prefix__']);

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

        $resitStatus = $this->getResitTrainingStatus($module, $transaction);
        
        // Get Resit Status
        if($check && ($resitStatus['status'] == 0 || $resitStatus['status'] == 2)){
            return back()->with('error', 'You have already taken this test, Please click "Post Class Tests" on the left navigation bar to take an available test!');
        }
        if ($resitStatus['status'] == 1 ) {
            if(isset($resitStatus['expiry'])){
                $parsedDate = \Carbon\Carbon::parse($resitStatus['expiry']);

                if(now() > $parsedDate){
                    return back()->with('error', 'the resit period for this test elapsed on: ' .$parsedDate.'. Please contact an administrator!');
                }
            }
            
            if($module->type == 'Class Test'){
                $check->redo_test = 2; // signifying that the test has been retaken
                $this->markClassTests($module, $check);                
            }

            if($module->type == 'Certification Test'){
                $check->certification_test_details = json_encode($certification_test_details);
                $check->save();

                // End redo test
                $data["certification_test_resit_status"] = 2;
                $data["certification_test_resit_expiry"] = NULL;
                $data["last_updated_at"] = now();

                if(!empty($transaction->training_result->certification_test_resit_enabled_by_id)){
                    $user = User::select('id','name','email')->where('id', $transaction->training_result->certification_test_resit_enabled_by_id )->first();
                    
                    if($user){
                        $email = $user->email;
                    }
                }

                $email = Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL');

                udateTrainingResult($transaction->program_id, $transaction->user_id, $data);

                $details['subject'] = 'Test Re-write successful';
                $details['email'] = $email;
                $details['content'] = 'Hello, <br><br>' . resolveAuthUser()->name . '(' . resolveAuthUser()->email . ') has completed a rewrite of certification test for <strong>' . $program->p_name . '</strong> training. <br><br>Please proceed to grade accordingly. <br><br>Thanks';
                $details['type'] = 'bulk';

                $this->sendGenericEmail($details);
            }
            
        } else {
            if ($module->type == 'Certification Test') {
                try {
                    Result::create([
                        'program_id' => $module->program->id,
                        'user_id' => resolveAuthUser()->id,
                        'module_id' => $module->id,
                        'certification_test_details' => json_encode($certification_test_details),
                    ]);
                } catch (\Illuminate\Database\QueryException $ex) {
                    $ex->getMessage();
                    return back()->with('error',  'Something went wrong, please try again');
                }
            } elseif ($module->type == 'Class Test') {
                $this->markClassTests($module);
            }
        }
        
        // Update training result
        $data["last_updated_at"] = now();
        udateTrainingResult($request->p_id, resolveAuthUser()->id);
        
        return Redirect::to('userresults?p_id=' . $program->id);
    }
    
    public function markClassTests($module, $result=null){
        $questions = $module->questions->toarray();
        // $no_of_questions = count($questions);
        $score = 0;
        $request = request()->all();
        $class_test_details = request()->except(['_token', 'mod_id', 'id', 'prefix__']);

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
                if(empty($result)){
                    Result::create([
                        'program_id' => $module->program->id,
                        'user_id' => resolveAuthUser()->id,
                        'module_id' => $module->id,
                        'class_test_score' => $score,
                        'class_test_details' => json_encode($class_test_details),
                    ]);
                }else{
                    $result->update([
                        'program_id' => $module->program->id,
                        'user_id' => resolveAuthUser()->id,
                        'module_id' => $module->id,
                        'class_test_score' => $score,
                        'class_test_details' => json_encode($class_test_details),
                    ]);
                }
            }

            return [
                'status' => true,
                'message' => 'Result created!',
            ];
        } catch (\Illuminate\Database\QueryException $ex) {
            $ex->getMessage();
            return [
                'status' => false,
                'message' => 'Something went wrong, please take test again!',
            ];
        }
    }

    public function getResitTrainingStatus($module, $transaction){
        $status = 0;
        $expiry = NULL;
        $module_type = $module->type;
        
        $result = Result::where(['module_id' => $module->id,'user_id' => $transaction->user_id,'program_id' => $transaction->program_id])->first();
        
        if($module_type == 'Class Test'){
            $status = ($transaction->training_result->class_test_resit_status && $result->redo_test == 0) ? $transaction->training_result->class_test_resit_status : $result->redo_test;

            if(!empty($status) && $status == 1 && $result->redo_test == 0){
                $expiry = $transaction->training_result->class_test_resit_expiry;
            }
        }

        if($module_type == 'Certification Test'){
            $status = ($transaction->training_result->certification_test_resit_status && $result->redo_test == 0) ? $transaction->training_result->certification_test_resit_status : $result->redo_test;

            // $status =  $transaction->training_result->certification_test_resit_status ?? null;
            
            if(!empty($status) && $status == 1){
                $expiry = $transaction->training_result->certification_test_resit_expiry;
            }
        }
        // dd($expiry, $transaction->training_result->certification_test_resit_status);
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
            $hasmock = $program->hasmock;

            if ($program->allow_payment_restrictions_for_completed_tests == 'yes') {
                $user_balance = DB::table('program_user')->where('program_id',  $program->id)->where('user_id', resolveAuthUser()->id)->first();
                if ($user_balance->balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $user_balance->currency_symbol . number_format($user_balance->balance) . ' in order to access tests');
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

        foreach ($questions as $question) {
            $program_name = $question->module->program->p_name;
            $program = $question->module->program()->first();
            $time = $question->module->time;
            $module_title = $question->module->title;
        }

        if ($module_type == 'Class Test') {
            return view('dashboard.student.tests.quizz', compact('questions', 'i', 'program', 'program_name', 'module_title', 'time'));
        }
        if ($module_type == 'Certification Test') {
            return view('dashboard.student.tests.certification', compact('questions', 'i', 'program', 'program_name', 'module_title', 'time'));
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
