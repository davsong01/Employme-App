<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FacilitatorTraining;
use App\Models\Module;
use App\Models\Program;
use App\Models\Question;
use App\Models\Result;
use App\Models\ResultThread;
use App\Models\ScoreSetting;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use intervention\Image\Facades\Image;
use Rap2hpoutre\FastExcel\FastExcel;

class ResultController extends Controller
{
    public function index() {}

    public function posttest()
    {
        $user = resolveAuthUser();

        // Abort if user does not have accepted roles
        if (! checkRoleHas(['Admin', 'Facilitator', 'Grader'])) {
            return back();
        }

        // Base query
        $query = Program::orderBy('created_at', 'DESC');

        // Admin sees all programs
        if (checkRoleHas(['Admin'])) {
            $trainings = $query->get();
        }
        // Facilitator or Grader sees only their trainings
        else {
            $userTrainings = $user->trainings->pluck('program_id')->toArray();
            $trainings = $query->whereIn('id', $userTrainings)->get();
        }

        // Preload result counts efficiently
        $resultCounts = Result::select('program_id', DB::raw('COUNT(DISTINCT user_id) as total'))
            ->whereIn('program_id', $trainings->pluck('id'))
            ->groupBy('program_id')
            ->pluck('total', 'program_id');


        // Attach to each training (no extra queries)
        foreach ($trainings as $training) {
            $training->result_count = $resultCounts[$training->id] ?? 0;
        }

        return view('dashboard.admin.results.selecttraining', compact('trainings'));
    }


    public function getgrades(Request $request, $id, $internal = false)
    {
        $request->p_id = $request->p_id ?? $id;
        $users = Transaction::where('program_id', $request->p_id)
            ->with(['user', 'results' => function ($query) use ($request) {
                $query->where('program_id', $request->p_id);
            }]);
        
        if (!empty($request->status)) {
            if ($request->status == 'yes') {
                $users = $users->has('results');
            } else {
                $users = $users->doesntHave('results');
            }
        }

        if (!empty($request->email)) {
            $users = $users->whereHas('user', function ($query) use ($request) {
                $query->where('email', $request->email);
            });
        }

        if (!empty($request->name)) {
            $users = $users->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->name}%");
            });
        }

        if (!empty($request->phone)) {
            $users = $users->whereHas('user', function ($query) use ($request) {
                $query->where('phone', $request->phone);
            });
        }

        if (!empty($request->staffID)) {
            $users = $users->whereHas('user', function ($query) use ($request) {
                $query->where('staffID', $request->staffID);
            });
        }

        $records = $users->count();

        if ($internal) {
            $isAdmin = true;
        } else {
            $isAdmin = checkRoleHas(['Admin']);
            $isFacilitatorOrGrader = checkRoleHas(['Facilitator', 'Grader']);
        }

        $score_settings = ScoreSetting::select(['class_test', 'passmark', 'certification', 'role_play', 'crm_test', 'email'])
            ->where('program_id', $request->p_id)
            ->first();

        // Execute query
        if (empty($request->columns)) {
            $users = $users->paginate(30);
        } else {
            $users = $users->get();
        }

        if ($isAdmin || $isFacilitatorOrGrader) {
            $i = 1;

            $program = Program::whereId($request->p_id)->first();

            if (!empty($request->columns)) {
                if (in_array('all', $request->columns)) {
                    $data = [
                        'staffID',
                        'name',
                        'email',
                        'phone',
                        'gender',
                        'metadata'
                    ];
                } else {
                    $data = $request->columns;
                }
                
                $finalBuild = buildResultExport($users, $data, $score_settings);
                
                return (new FastExcel($finalBuild))->download('Post-test Report for ' . $program->p_name . '.xlsx');
            }

            $page = 'results';

            $title = '<b>Post Test Results for: </b>' . $program->p_name;

            if ($internal) {
                return view('dashboard.company.posttests.index', compact('users', 'i', 'program', 'records', 'score_settings', 'page', 'title'));
            }
            
            return view('dashboard.admin.results.index', compact('users', 'i', 'program', 'records', 'score_settings', 'page', 'title'));
        }
    }


    private function calculateClassTestScore($result, &$user, $programId)
    {
        $modules = Module::where('type', 0)->where('program_id', $programId)->where('computation_status', 1)->get();
        $obtainable = array();

        foreach ($modules as $module) {
            array_push($obtainable, $module->questions->count());
        }

        $user->obtainable = array_sum($obtainable);

        if ($modules->count() > 0) {
            $user->total_class_test_score += $result->class_test_score;
        }
    }

    private function calculateFinalCtScore($user)
    {
        return $user->obtainable > 0
            ? round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0)
            : 0;
    }

    public function create()
    {
        if (checkRoleHas(['Admin'])) {
            $programs = Program::where('id', '<>', 1)->get();
            $users = User::where('roles', '<>', "Admin")->where('roles', '<>', "Teacher")->where('roles', '<>', "Grader")->where('hasResult', '<>', 1)->orderBy('created_at', 'DESC')->get();
            return view('dashboard.admin.results.create', compact('users', 'programs'));
        } elseif (checkRoleHas(['Facilitator'])) {
            $programs = Program::where('id', '=', resolveAuthUser()->program_id)->get();
            $users = User::where('roles', '=', "Student")->where('hasResult', '<>', 1)->where('program_id', '=', resolveAuthUser()->program_id)->orderBy('created_at', 'DESC')->get();
            //return view('dashboard.admin.results.create', compact('users', 'programs'));
        } else
            return redirect('/dashboard');
    }

    public function store(Request $request) {}

    public function add($id)
    {
        $transaction = Transaction::with('program:id,p_name')->with('program.scoresettings')->where('id', $id)->first();
        $user_results = Result::with(['user', 'module', 'threads'])->where('user_id', $transaction->user_id)->whereProgramId($transaction->program_id)->where('certification_test_details', '<>', NULL)->get();
        
        $program = $transaction->program;
        $i = 1;

        $trainingResults = $transaction->training_result;

        $details['certification_score'] = $trainingResults->certification_test_score;
        $details['email_test_score'] = $trainingResults->email_test_score;
        $details['role_play_score'] = $trainingResults->roleplay_test_score;
        $details['crm_test_score'] = $trainingResults->crm_test_score;
        $details['user_name'] = $transaction->user->name;
        $details['grader_comment'] = $trainingResults->certification_grader_comment;
        $details['facilitator_comment'] = $trainingResults->certification_facilitator_comment;


        $results = [];

        foreach ($user_results as $result) {
            // If module type is 1, store separately
            if ($result->module->type == 1) {
                $details['c_result'] = $result;
            }

            $moduleTitle = $result->module->title;
            $questions = json_decode($result->certification_test_details, true);

            $moduleQuestionNumber = 1; // reset per module

            if (!$questions) {
                // No questions, user is re-writing test
                $results[] = [
                    'question_number' => $moduleQuestionNumber,
                    'module_title' => $moduleTitle,
                    'title' => 'User is re-writing test',
                    'answer' => 'User is re-writing test',
                ];
            } else {
                // Loop through each question
                foreach ($questions as $questionId => $answer) {
                    $results[] = [
                        'question_number' => $moduleQuestionNumber++,
                        'module_title' => $moduleTitle,
                        'title' => Question::whereId($questionId)->value('title'),
                        'answer' => $answer,
                    ];
                }
            }
        }

        $result_id = $transaction->id;
        $real_result_id = request()->r_id;

        return view('dashboard.admin.results.partial_edit', compact('user_results', 'i', 'result_id', 'program', 'details', 'results', 'real_result_id'));
    }

    // public function add(Request $request, $uid, $modid)
    // {
    //     $result_id = $modid;
    //     $program = Program::select('id', 'p_name')->with('scoresettings')->whereId($request->pid)->first();

    //     $user_results = Result::with(['user', 'module', 'threads'])->where('user_id', $uid)->whereProgramId($modid)->where('certification_test_details', '<>', NULL)->where('redo_test', 0)->get();
    //     $i = 1;
    //     $details['certification_score'] = 0;
    //     $details['email_test_score'] = 0;
    //     $details['role_play_score'] = 0;
    //     $details['crm_test_score'] = 0;
    //     $details['user_name'] = "";
    //     $details['allow_editing'] = 0;

    //     if (!$user_results) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Participant has not taken certification test',
    //             'uid' => $uid,
    //         ]);
    //         // return back()->with('error', 'Participant has not taken certification test');
    //     }
    //     $results = [];

    //     $history = ResultThread::with(['user', 'module'])->where('user_id', $uid)->whereProgramId($program->id)->where('certification_test_details', '<>', NULL)->get();
    //     if ($history) {
    //         $h_details['certification_score'] = 0;
    //         $h_details['email_test_score'] = 0;
    //         $h_details['role_play_score'] = 0;
    //         $h_details['crm_test_score'] = 0;
    //         $h_details['user_name'] = "";
    //         $h_details['allow_editing'] = 0;
    //         foreach ($history as $results) {

    //             if ($results->module->type == 1) {
    //                 $h_details['c_result'] = $results;
    //             }
    //             $h_details['certification_score'] = $results->certification_test_score + $h_details['certification_score'];
    //             $h_details['email_test_score'] = $results->email_test_score +  $h_details['email_test_score'];
    //             $h_details['role_play_score'] = $results->role_play_score +  $h_details['role_play_score'];
    //             $h_details['crm_test_score'] = $results->crm_test_score +  $h_details['crm_test_score'];
    //             $results['module_title'] = $results->module->title;
    //             $h_details['user_name'] = $results->user->name;
    //             $h_details['grader_comment'] = $results->grader_comment;
    //             $h_details['facilitator_comment'] = $results->facilitator_comment;
    //             $h_details['allow_editing'] = 1;

    //             $questions = json_decode($results->certification_test_details, true);

    //             if (!$questions) {
    //                 $results['title'] = 'User is re-writing test';
    //                 $results['answer'] = 'User is re-writing test';
    //             } else {
    //                 foreach ($questions as $key => $value) {
    //                     $results['title'] = Question::whereId($key)->value('title');
    //                     $results['answer'] = $value;
    //                 }
    //             }

    //             unset($results['certification_test_details']);
    //             // unset($results['certification_test_score']);
    //             unset($results['role_play_score']);
    //             unset($results['crm_test_score']);
    //             unset($results['email_test_score']);
    //         }
    //     }

    //     foreach ($user_results as $results) {
    //         if ($results->module->type == 1) {
    //             $details['c_result'] = $results;
    //         }
    //         $details['certification_score'] = $results->certification_test_score + $details['certification_score'];
    //         $details['email_test_score'] = $results->email_test_score +  $details['email_test_score'];
    //         $details['role_play_score'] = $results->role_play_score +  $details['role_play_score'];
    //         $details['crm_test_score'] = $results->crm_test_score +  $details['crm_test_score'];
    //         $results['module_title'] = $results->module->title;
    //         $details['user_name'] = $results->user->name;
    //         $details['grader_comment'] = $results->grader_comment;
    //         $details['facilitator_comment'] = $results->facilitator_comment;
    //         $details['allow_editing'] = 1;

    //         $questions = json_decode($results->certification_test_details, true);

    //         if (!$questions) {
    //             $results['title'] = 'User is re-writing test';
    //             $results['answer'] = 'User is re-writing test';
    //         } else {
    //             foreach ($questions as $key => $value) {
    //                 $results['title'] = Question::whereId($key)->value('title');
    //                 $results['answer'] = $value;
    //             }
    //         }

    //         unset($results['certification_test_details']);
    //         unset($results['certification_test_score']);
    //         unset($results['role_play_score']);
    //         unset($results['crm_test_score']);
    //         unset($results['email_test_score']);
    //     }

    //     return view('dashboard.admin.results.partial_edit', compact('user_results', 'i', 'result_id', 'program', 'details', 'results', 'history'));
    // }

    public function enable($id)
    {

        if (checkRoleHas(['Admin'])) {

            $program = Program::findorfail($id);

            $program->hasresult = 1;

            $program->save();

            return back()->with('message', 'Participants of this program can now print their statement of result');
        }
        return back();
    }

    public function disable($id)
    {
        if (checkRoleHas(['Admin'])) {

            $program = Program::findorfail($id);

            $program->hasresult = 0;

            $program->save();

            return back()->with('message', 'Participants of this program can no longer print their statement of result');
        }
        return back();
    }

    // public function show($id, Request $request)
    // {
    //     if (checkRoleHas(['Student']) || resolveAuthUser()->id == $id) {
    //         $transaction = Transaction::select('id', 'training_result', 'balance', 'user_id', 'program_id', 'currency_symbol')->where('program_id',  $request->p_id)->where('user_id', resolveAuthUser()->id)->first();
    //         $program = Program::select('id', 'allow_payment_restrictions_for_results', 'p_name', 'hasresult', 'only_certified_should_see_certificate')->with('scoresettings')->find($transaction->program_id);
            
    //         $details = certificationStatusNew($transaction->training_result, $program, resolveAuthUser());
            
    //         if ($program->allow_payment_restrictions_for_results == 'yes') {
    //             if ($transaction->balance > 0) {
    //                 return back()->with('error', 'Please Pay your balance of ' . $transaction->currency_symbol . number_format($transaction->balance) . ' in order to get access to view results');
    //             }
    //         }

    //         if($details)
    //         if ($program->hasresult == 0) {
    //             return back()->with('error', 'Results for this program have not been enabled, Please check back!');
    //         }

    //         return view('dashboard.admin.results.show', compact('details', 'program'));
    //     }

    //     return redirect('/');
    // }
    public function show($id, Request $request)
{
    if (checkRoleHas(['Student']) || resolveAuthUser()->id == $id) {
        $transaction = Transaction::select('id', 'training_result', 'balance', 'user_id', 'program_id', 'currency_symbol')
            ->where('program_id', $request->p_id)
            ->where('user_id', resolveAuthUser()->id)
            ->first();

        // Safety 1: Check if transaction exists
        if (!$transaction) {
            return back()->with('error', 'Transaction record not found.');
        }

        $program = Program::select('id', 'allow_payment_restrictions_for_results', 'p_name', 'hasresult', 'only_certified_should_see_certificate')
            ->with('scoresettings')
            ->find($transaction->program_id);

        // Safety 2: Check if program exists
        if (!$program) {
            return back()->with('error', 'Program details not found.');
        }
        
        $details = certificationStatusNew($transaction->training_result, $program, resolveAuthUser());

        // Payment restriction check
        if ($program->allow_payment_restrictions_for_results == 'yes') {
            if ($transaction->balance > 0) {
                return back()->with('error', 'Please Pay your balance of ' . $transaction->currency_symbol . number_format($transaction->balance) . ' in order to get access to view results');
            }
        }

        if(empty($details)) {
            return back()->with('error', 'Your certification status could not be determined. Please ensure you have taken the certification tests or contact support.');
        }

        // Result availability check
        if ($program->hasresult == 0) {
            return back()->with('error', 'Results for this program have not been enabled, Please check back!');
        }

        return view('dashboard.admin.results.show', compact('details', 'program'));
    }

    return redirect('/');
}

    public function update($id, Request $request)
    {
        
        try {
            //code...
            $result = Transaction::with('program', 'program.scoresettings')->where('id', $id)->first();
            
            $realResult = Result::where('id', $request->real_result_id)->first();
            
            $request["email_test_score"] = $request->emailscore;
            $request["roleplay_test_score"] = $request->roleplayscore;
            $request["crm_test_score"] = $request->crm_score;
            $request["certification_test_score"] = $request->certification_score;
    
            $request["certification_facilitator_comment"] = $request->facilitator_comment;
            
            $request["certification_grader"] = ($request->certification_score <> $result->training_result->certification_test_score) ? resolveAuthUser()->name : $result->training_result->certification_grader;
    
            $request["certification_facilitator"] = ($request->roleplayscore <> $result->training_result->roleplay_test_score) ? resolveAuthUser()->name : $result->training_result->certification_facilitator;
    
            $request["certification_grader_comment"] = $request->grader_comment;
    
            $transaction = udateTrainingResult($result->program_id, $result->user_id, $request->all());
            
            $realResult->update([
                "email_test_score" => $request->emailscore,
                "role_play_score" => $request->roleplayscore,
                "certification_test_score" => $request->certification_score,
                "crm_test_score" => $request->crm_score,
                "grader_comment" => $request->grader_comment,
                "facilitator_comment" => $request->facilitator_comment,
                "grader" => resolveAuthUser()->name,
                "marked_by" => resolveAuthUser()->name,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Test Scores Updated Successfully',
                'id' => $transaction->id,
                'certification_test_score' => $transaction->training_result?->certification_test_score ?? 0,
                'passmark' => $transaction->program->scoresettings->passmark,
                'role_play_score' => $transaction->training_result?->roleplay_test_score ?? 0,
                'email_test_score' => $transaction->training_result?->email_test_score ?? 0,
                'crm_test_score' => $transaction->training_result?->crm_test_score ?? 0,
                'certification_facilitator' => $transaction->training_result?->certification_facilitator ?? '',
                'certification_grader' => $transaction->training_result?->certification_grader ?? '',
                'total_score' => $transaction->training_result?->total_score ?? 0,
                'updated_at' => $result->updated_at ? \Carbon\Carbon::parse($result->updated_at)->format('jS F, Y, h:iA') : ''
            ]);
        } catch (\Throwable $th) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'message: ' .$th->getMessage().' Line:'.$th->getLine().' File: '.$th->getFile(),
                ]
            );
        }

        // return Redirect::to(route('results.getgrades', $result->program_id))->with('message', 'User Scores have been updated successfully');
    }


    public function destroy(Request $request, $id)
    {
        // Clear certification Tests
        $transaction = Transaction::with('program', 'user')->where('id', $id)->first();

        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            $results = Result::with('program', 'module')->where('program_id', $transaction->program_id)
                ->whereHas('module', function ($query) {
                    $query->where('computation_status', 1)
                        ->where('type', 1);
                })
                ->where('user_id', $transaction->user_id)
                ->first();

            $data["certification_test_resit_status"] = 1;
            $data["certification_test_resit_expiry"] = now()->addHours(env('CERTIFICATION_TEST_RESIT_EXIPIRY'));
            $data["certification_test_resit_enabled_by_id"] = resolveAuthUser()->id;

            udateTrainingResult($transaction->program_id, $transaction->user_id, $data);
            
            if($results){
                $results->update(['redo_test' => 1]);
            }

            // Save result thread
            if (!empty($results->certification_test_details)) {
                $this->createResultThread($results);
            }

            // $results->delete();
            // Send resit email
            $expiryHours = env('CERTIFICATION_TEST_RESIT_EXIPIRY');
            $expiryDate = now()->addHours($expiryHours)->format('M d, Y h:i A');

            $programName = $transaction->program->p_name ?? 'the program';
            $userName = $transaction->user->name ?? 'Participant';

            // Optional (only if exists)
            $moduleTitle = $results->module->title ?? null;

            $details['subject'] = 'Test Retake Activated';
            $details['email'] = $transaction->user->email;

            $details['content'] = "
            Hello {$userName}, <br><br>

            You are now eligible to retake your test for {$programName}.<br><br>
            " . ($moduleTitle ? "Test: <strong>{$moduleTitle}</strong><br><br>" : "") . "
            You have a {$expiryHours}-hour window to complete your retake before access closes.<br><br>
            <strong>Expiry Time:</strong> {$expiryDate}<br><br>
            Please ensure you complete and submit your test within this period.<br><br>
            If required, you may notify the admin after completion.<br><br>
            Best regards,<br>
            Program Team.
            ";

            $details['type'] = 'bulk';
            
            $this->sendGenericEmail($details);
            return back()->with('message', 'All Post Test Certification Test details for this user have been deleted successfully');
        }

        return back()->with('error', 'You are not allowed to perform this action');
    }

    public function destroyClassTests(Request $request, $id)
    {
        // Clear certification Tests
        $transaction = Transaction::with('program', 'user')->where('id', $id)->first();
        $modulesCount = Module::where('program_id', $transaction->program_id)->where('computation_status', 1)->where('type', 0)->count();

        if (checkRoleHas(['Admin', 'Grader', 'Facilitator'])) {
            // all class tests
            $results = Result::with('program', 'module')->where('program_id', $transaction->program_id)
                ->whereHas('module', function ($query) {
                    $query->where('computation_status', 1)
                        ->where('type', 0);
                })
                ->where('user_id', $transaction->user_id)
                ->whereNotNull('class_test_details')
                ->get();
            $data["class_test_resit_status"] = 1;
            $data["class_test_resit_expiry"] = now()->addHours(env('CERTIFICATION_TEST_RESIT_EXIPIRY'));
            $data["class_test_resit_enabled_by_id"] = resolveAuthUser()->id;

            $this->createClassTestsResultThread($results);

            udateTrainingResult($transaction->program_id, $transaction->user_id, $data);
            // Save result thread

            // Send resit email
            $details['subject'] = 'Test Re-write successful';
            $details['email'] = $transaction->user->email;
            $details['content'] = 'Hello ' . $transaction->user->name . ', <br><br>
            This is to inform you that you are now cleared to Re-sit Class Tests at the training: ' . $transaction->program->p_name . '. You now have a ' . env('CERTIFICATION_TEST_RESIT_EXIPIRY') . 'hour window to retake and submit for grading after which the portal will close for you to Resit.<br><br>The Re-sit window will expire on: ' . now()->addHours(env('CERTIFICATION_TEST_RESIT_EXIPIRY')) . '<br><br>Once you complete the Resit, kindly chat the school WhatsApp admin on 07038378085 to inform about your completion.<br><br>Thanks. <br>Program Admin.';
            $details['type'] = 'bulk';
            $this->sendGenericEmail($details);

            return back()->with('message', 'All Post Test Certification Test details for this user have been deleted successfully');
        }
        return back()->with('error', 'You are not allowed to perform this action');
    }

    public function destroyCrmTests(Request $request, $id) {}

    public function destroyRoleplayTests(Request $request, $id) {}

    public function getResitStatus($transaction)
    {
        if (isset($transaction->training_result->certification_test_resit_expiry))
            dd($transaction);
    }

    public function createResultThread($results)
    {
        $thread = ResultThread::create([
            'result_id' => $results->id,
            'submitted_on' => $results->created_at,
            "program_id" => $results->program_id,
            "module_id" => $results->module_id,
            "user_id" => $results->user_id,
            "marked_by" => $results->marked_by,
            "grader" => $results->grader,
            "class_test_score" => $results->class_test_score,
            "class_test_details" => $results->class_test_details,
            "certification_test_score" => $results->certification_test_score,
            "certification_test_details" => $results->certification_test_details,
            "role_play_score" => $results->role_play_score,
            "crm_test_score" => $results->crm_test_score,
            "email_test_score" => $results->email_test_score,
            "facilitator_comment" => $results->facilitator_comment,
            "grader_comment" => $results->grader_comment,
        ]);

        return $thread;
    }

    public function createClassTestsResultThread($results)
    {
        $metaData = [];

        if (!$results->isEmpty()) {
            $firstResult = $results->first();

            $metaData = [
                'program_id' => $firstResult->program_id,
                'user_id' => $firstResult->user_id,
                'result_id' => $firstResult->id,
                'module_id' => $firstResult->module_id,
                'submitted_on' => $firstResult->created_at,
                'class_test_details' => json_encode($results->map(function ($result) {
                    return [
                        'module_id' => $result->module_id,
                        'submitted_on' => $result->created_at,
                        'marked_by' => $result->marked_by,
                        'class_test_score' => $result->class_test_score,
                        'class_test_details' => $result->class_test_details,
                    ];
                })->toArray()),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $thread = ResultThread::create($metaData);
            Result::whereIn('id', $results->pluck('id'))->delete();

            return $thread;
        }

        return false;
    }

    public function verify(Request $request)
    {

        //Check if Parter has filled WAACSP details
        if (Settings::select('OFFICIAL_EMAIL')->first()->value('OFFICIAL_EMAIL') == NULL || Settings::select('token')->first()->value('token') == NULL) {
            return back()->with('fill', 'You must fill your official email and WAASP token. <a target="_blank" href="/settings/1/edit"><strong> CLICK HERE TO FILL</strong></a>');
        }
        $client = new Client();
        $res = $client->request('POST', 'http://127.0.0.1:8000/api/verify', [
            'form_params' => [
                'participants' => $request->participants,
                'passmark' => $request->passmark,
                'training' => $request->training,
                'token' => $request->token,
                'email' => $request->email,
            ]
        ]);

        $res = json_decode($res->getBody()->getContents());

        return back()->with($res->status, $res->message);
    }

    //return certification status
    private function certification($total, $passmark)
    {
        if ($total >= $passmark) {
            return 'CERTIFIED';
        }
        return 'NOT CERTIFIED';
    }

    public function clearDuplicates($program_id)
    {
        $programs = Program::select('id')->get();

        $count = 0;
        foreach ($programs as $program) {
            $duplicates = DB::table('results')
                ->select('module_id', 'user_id', 'program_id', DB::raw('COUNT(*) as count'))
                ->where('program_id', 32)
                ->groupBy('module_id', 'user_id', 'program_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            // Loop through duplicates and delete the second/latest one (keep the first occurrence)
            if (empty($duplicates)) {
                continue;
            }
            foreach ($duplicates as $duplicate) {
                // Find all the rows with the same `module_id`, `user_id`, and `program_id`, ordered by `created_at`
                $duplicat = [
                    'email' => User::where('id', $duplicate->user_id)->first()->email,
                    'program' => Program::where('id', $duplicate->program_id)->first()->p_name,
                    'module' => Module::where('id', $duplicate->module_id)->first()->title,
                    'count' => $duplicate->count
                ];

                \Log::info(['duplicates' => $duplicat]);

                $entries = DB::table('results')
                    ->where('program_id', $program_id)
                    ->where('module_id', $duplicate->module_id)
                    ->where('user_id', $duplicate->user_id)
                    ->orderBy('created_at')
                    ->get();

                if ($entries->count() > 1) {
                    $latest = $entries->last();
                    $count = $count + 1;
                    DB::table('results')->where('id', $latest->id)->delete();
                }

                $newResult = udateTrainingResult($duplicate->program_id, $duplicate->user_id);
                \Log::info(['new result' => $newResult->training_result]);
            }
        }



        return back()->with('message', $duplicates->count() . ' Duplicates removed successfully');
    }
}
