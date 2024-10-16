<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Mocks;
use App\Models\Module;
use App\Models\Result;
use App\Models\Program;
use App\Models\Question;
use App\Models\Transaction;
use App\Models\ScoreSetting;
use App\Models\FacilitatorTraining;
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Redirect;

class MockController extends Controller
{

    protected $excelService;

    public function __construct()
    {
        $this->excelService = new ExcelService();
    }

    
    public function pretest()
    {
        if (!empty(array_intersect(adminRoles(), auth()->user()->role()))) {
            //Select only programs that have results
            $programs = Program::whereHas('mocks', function ($query) {
                return $query->orderby('created_at', 'DESC');
            })->orderby('created_at', 'DESC')->get();

            $i = 1;
            return view('dashboard.admin.mocks.selecttraining', compact('programs', 'i'));
        }

        if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            //select all programs for this user
            $teacher_programs = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();

            //Select only programs that have results
            foreach ($teacher_programs as $programs) {
                $programs['p_name'] = Program::whereId($programs->program_id)->wherehasmock(1)->value('p_name');
                //  = Program::whereId($programs->program_id)->value('p_name');
            }

            $i = 1;
            return view('dashboard.teacher.mocks.selecttraining', compact('teacher_programs', 'i'));
        }
    }

    public function index(Request $request)
    {
        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {

            $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
            $program = Program::find($request->p_id);

            if ($program->allow_payment_restrictions_for_pre_class_tests == 'yes') {
                if ($user_balance->balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $user_balance->currency_symbol . number_format($user_balance->balance) . ' in order to access pre class tests');
                }
            }

            $i = 1;

            $modules = Module::with('questions')->where('program_id', $request->p_id)->whereType(0)->get();

            foreach ($modules as $module) {
                $module_check = Mocks::where('module_id', $module->id)->where('user_id', auth()->user()->id)->get();

                if ($module_check->count() > 0) {
                    $module['completed'] = 1;
                } else {
                    $module['completed'] = 0;
                }
            }
            //  dd($modules);

            return view('dashboard.student.pretests.index', compact('modules', 'i', 'program'));
        }
    }

    public function create()
    {
        //
    }

    public function grade($uid, $modid)
    {

        $user_results = Mocks::with(['program', 'user'])->where('id', $modid)->where('user_id', $uid)->first();

        $i = 1;
        $array = json_decode($user_results->certification_test_details, true);

        foreach ($array as $key => $value) {
            $array[Question::where('id', $key)->value('title')] = $value;
            unset($array[$key]);
        }

        return view('dashboard.admin.mocks.edit', compact('user_results', 'array', 'i'));
    }

    public function getgrades(Request $request, $id, $internal=false)
    {
        $request->pid = $id;
        
        $users = Transaction::where('program_id', $request->pid)
            ->with(['user', 'mocks' => function ($query) use ($request) {
                $query->where('program_id', $request->pid);
            }]);

        // Filter based on mock status if provided
        if (!empty($request->status)) {
            if ($request->status == 'yes') {
                $users = $users->has('mocks');
            } else {
                $users = $users->doesntHave('mocks');
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
                $query->where('t_phone', $request->phone);
            });
        }
        
        if (!empty($request->staffID)) {
            $users = $users->whereHas('user', function ($query) use ($request) {
                $query->where('staffID',$request->staffID); 
            });
        }

        $records = $users->count();
        // Roles determination
        if($internal){
            $isAdmin = true;
        }else{
            $isAdmin = !empty(array_intersect(adminRoles(), Auth::user()->role()));
            $isFacilitatorOrGrader = !empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()));
        }
        
        $score_settings = ScoreSetting::select(['class_test', 'passmark', 'certification', 'role_play','crm_test', 'email'])
        ->where('program_id', $request->pid)
            ->first();

        // Execute query
        if (empty($request->columns)) {
            $users = $users->paginate(50);
        } else {
            $users = $users->get();
        }
        // Process users if Admin or Facilitator/Grader
        if ($isAdmin || $isFacilitatorOrGrader) {
            $i = 1;
            // Check if $users is paginated
            $isPaginated = $users instanceof \Illuminate\Pagination\LengthAwarePaginator || $users instanceof \Illuminate\Pagination\Paginator;

            // Apply mapping logic to modify each user
            $modifiedUsers = $users->map(function ($user) use ($request, $score_settings) {
                $results = $user->mocks;

                // Initialize user scores and attributes
                $user->total_cert_score = 0;
                $user->total_class_test_score = 0;
                $user->total_email_test_score = 0;
                $user->total_role_play_score = 0;
                $user->program_id = $request->pid;
                $user->program_ct_score_settings = 0;
                $user->passmark = 0;
                $user->created_at = NULL;
                $user->class_test_module_count = Module::where('program_id', $request->pid)
                    ->where('status', 1)
                    ->where('type', 'Class Test')
                    ->count();
                $user->marked_by = '';
                $user->grader = '';
                $user->result_id = 0;
                $user->final_ct_score = 0;
                $user->obtainable = 0;

                $user->name = $user->user->name;
                $user->email = $user->user->email;
                $user->staffID = $user->user->staffID;
                $user->phone = $user->user->t_phone;
                $user->metadata = $user->user->metadata;
                $user->gender = $user->user->gender;

                // Fetch score settings
                $user->program_ct_score_settings = $score_settings->class_test;
                $user->passmark = $score_settings->passmark;

                // Process results for each user
                foreach ($results as $result) {
                    $user->total_role_play_score += $result->role_play_score;
                    $user->total_email_test_score += $result->email_test_score;
                    $user->created_at = $result->created_at;
    
                    if($result->module){
                        if ($result->module->type === 'Class Test') {
                            $this->calculateClassTestScore($result, $user, $request->pid);
                        }
    
                        if ($result->module->type === 'Certification Test') {
                            $user->total_cert_score += $result->certification_test_score;
                            $user->result_id = $result->id;
                            $user->certification_test_details = 1;
    
                            $user->marked_by = $result->marked_by;
                            $user->grader = $result->grader;
                        }
                    }
                    // Calculate final class test score
                    $user->final_ct_score = $this->calculateFinalCtScore($user);
                }

                return $user;
            });

            // If paginated, restore pagination structure
            if ($isPaginated) {
                $users = new \Illuminate\Pagination\LengthAwarePaginator(
                    $modifiedUsers,
                    $users->total(),
                    $users->perPage(),
                    $users->currentPage(), 
                    ['path' => $users->path()]
                );
            } else {
                $users = $modifiedUsers; 
            }

            $program = Program::whereId($request->pid)->first();

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
                return (new FastExcel($finalBuild))->download('Pretest Report for '.$program->p_name . '.xlsx');
            }

            $page = 'mocks';
            $title = '<b>Pre Test Results for: </b>'.$program->p_name;

            if ($internal) {
                return view('dashboard.company.pretests.index', compact('users', 'i', 'program', 'records', 'score_settings', 'page', 'title'));
            }

            return view('dashboard.admin.results.index', compact('users', 'i', 'program','records', 'score_settings','page','title'));
        }

    }

    private function calculateClassTestScore($result, &$user, $programId)
    {
        $modules = Module::where('type', 0)->where('program_id', $programId)->get();
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


    // public function getgrades(Request $request, $id)
    // {
    //     $request->pid = $id;
    //     $users = Transaction::select('user_id')->distinct()->whereProgramId($request->pid)->get();

    //     if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
    //         $i = 1;


    //         foreach ($users as $user) {
    //             $results = Mocks::where('user_id', $user->user_id)->where('program_id', $request->pid)->get();

    //             $user->total_cert_score = 0;
    //             $user->total_class_test_score = 0;
    //             $user->total_email_test_score = 0;
    //             $user->total_role_play_score = 0;

    //             $user->program_id = $request->pid;
    //             $user->program_ct_score_settings = 0;
    //             $user->passmark = 0;
    //             $user->created_at = NULL;
    //             $user->class_test_module_count = Module::where('program_id', $request->pid)->where('status', 1)->where('type', 'Class Test')->count();
    //             $user->marked_by = '';
    //             $user->grader = '';

    //             $score_settings = ScoreSetting::select(['class_test', 'passmark'])->whereProgramId($request->pid)->first();
    //             $user->program_ct_score_settings = $score_settings->class_test;
    //             $user->passmark = $score_settings->passmark;
    //             $user->result_id = 0;
    //             $user->name =  User::where('id', $user->user_id)->value('name');
    //             $user->final_ct_score = 0;
    //             $user->total_class_test_score = 0;
    //             $user->obtainable = 0;

    //             foreach ($results as $result) {
    //                 $user->total_role_play_score = $result->role_play_score + $user->total_role_play_score;
    //                 $user->created_at = $result->created_at;
    //                 $user->marked_by = $result->marked_by;
    //                 $user->grader = $result->grader;
    //                 $user->result_id = NULL;
    //                 $user->total_email_test_score = $result->email_test_score + $user->total_email_test_score;


    //                 if ($result->module->type == 'Class Test') {

    //                     $u =  Module::where('type', 0)->where('program_id', $request->pid)->where('status', 1)->get();

    //                     $obtainable = array();

    //                     foreach ($u as $t) {
    //                         $questions = array_push($obtainable, $t->questions->count());
    //                     }

    //                     $user->obtainable = array_sum($obtainable);

    //                     if ($u->count() > 0) {

    //                         $user->total_class_test_score = $result->class_test_score + $user->total_class_test_score;
    //                     }
    //                 }

    //                 if ($result->module->type == 'Certification Test') {

    //                     $user->total_cert_score = $result->certification_test_score +  $user->total_cert_score;

    //                     $user->result_id = $result->id;
    //                     $user->certification_test_details = 1;
    //                 }
    //                 $user->final_ct_score = round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0);

    //                 $program_name = Program::whereId($request->pid)->value('p_name');
    //             }
    //         }

    //         return view('dashboard.admin.mocks.index', compact('users', 'i', 'program_name'));
    //     }

    //     if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
    //         $i = 1;

    //         foreach ($users as $user) {
    //             $results = Mocks::where('user_id', $user->user_id)->where('program_id', $request->pid)->get();

    //             $user->total_cert_score = 0;
    //             $user->total_class_test_score = 0;
    //             $user->total_email_test_score = 0;
    //             $user->total_role_play_score = 0;

    //             $user->program_id = $request->pid;
    //             $user->program_ct_score_settings = 0;
    //             $user->passmark = 0;
    //             $user->created_at = NULL;
    //             $user->class_test_module_count = Module::where('program_id', $request->pid)->where('type', 'Class Test')->where('status', 1)->count();
    //             $user->marked_by = '';
    //             $user->grader = '';

    //             $score_settings = ScoreSetting::select(['class_test', 'passmark'])->whereProgramId($request->pid)->first();
    //             $user->program_ct_score_settings = $score_settings->class_test;
    //             $user->passmark = $score_settings->passmark;
    //             $user->result_id = 0;
    //             $user->name =  User::where('id', $user->user_id)->value('name');
    //             $user->final_ct_score = 0;
    //             $user->total_class_test_score = 0;
    //             $user->obtainable = 0;

    //             foreach ($results as $result) {
    //                 $user->total_role_play_score = $result->role_play_score + $user->total_role_play_score;
    //                 $user->created_at = $result->created_at;
    //                 $user->marked_by = $result->marked_by;
    //                 $user->grader = $result->grader;
    //                 $user->result_id = NULL;
    //                 $user->total_email_test_score = $result->email_test_score + $user->total_email_test_score;


    //                 if ($result->module->type == 'Class Test') {

    //                     $u =  Module::where('type', 0)->where('program_id', $request->pid)->where('status', 1)->get();

    //                     $obtainable = array();

    //                     foreach ($u as $t) {
    //                         $questions = array_push($obtainable, $t->questions->count());
    //                     }

    //                     $user->obtainable = array_sum($obtainable);

    //                     if ($u->count() > 0) {

    //                         $user->total_class_test_score = $result->class_test_score + $user->total_class_test_score;
    //                     }
    //                 }

    //                 if ($result->module->type == 'Certification Test') {

    //                     $user->total_cert_score = $result->certification_test_score +  $user->total_cert_score;

    //                     $user->result_id = $result->id;
    //                     $user->certification_test_details = 1;
    //                 }
    //                 $user->final_ct_score = round(($user->total_class_test_score * $user->program_ct_score_settings) / $user->obtainable, 0);
    //             }
    //         }

    //         $program_name = Program::whereId($request->pid)->value('p_name');
    //         return view('dashboard.admin.mocks.index', compact('users', 'i', 'program_name'));
    //     }
    // }

    public function add($uid, $modid)
    {
        $user_results = Result::with(['program', 'user'])->where('id', $modid)->where('user_id', $uid)->first();

        $i = 1;
        $array = json_decode($user_results->certification_test_details, true);

        foreach ($array as $key => $value) {
            $array[Question::where('id', $key)->value('title')] = $value;
            unset($array[$key]);
        }

        return view('dashboard.admin.mocks.edit', compact('user_results', 'array', 'i'));
    }

    public function store(Request $request)
    {
        $program = Program::find($request->p_id);

        $class_test_details = $request->except(['_token', 'mod_id', 'id']);

        if (sizeof($class_test_details) < 2) {
            return back()->with('error', 'You must answer at least 1 question');
        };

        $certification_test_details = $request->except(['_token', 'mod_id', 'id', 'p_id']);

        foreach ($certification_test_details as $key => $value) {
            if ((!isset($certification_test_details[$key]))) {
                return back()->with('error', 'You must answer at least 1 question');
            };
            // print_r(str_word_count($certification_test_details[$key]);
            if (str_word_count($certification_test_details[$key]) > 500) {
                return back()->with('error', 'Maximum number of words allowed for each question is 500, please try again');
            };
        }

        $check = Mocks::where('user_id', auth()->user()->id)->where('module_id', $request->mod_id)->count();

        if ($check > 0) {
            return back()->with('error', 'You have already taken this test, Please click "Pre Class Tests" on the left navigation bar to take an available test!');
        };

        $module = Module::findOrFail($request->mod_id);

        $questions = $module->questions->toarray();
        $no_of_questions = count($questions);
        $score = 0;

        if ($module->type == 'Certification Test') {
            try {
                $results = Mocks::create([
                    'program_id' => $module->program->id,
                    'user_id' => Auth::user()->id,
                    'module_id' => $module->id,
                    'certification_test_details' => json_encode($certification_test_details),
                ]);
            } catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();
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
                    $results = Mocks::create([
                        'program_id' => $module->program->id,
                        'user_id' => Auth::user()->id,
                        'module_id' => $module->id,
                        'class_test_score' => $score,
                        'class_test_details' =>  json_encode($class_test_details),
                    ]);
                }
            } catch (\Illuminate\Database\QueryException $ex) {
                $error = $ex->getMessage();
                return back()->with('error', 'something went wrong, please take test again');
            }
        }

        return Redirect::to('mocks?p_id=' . $program->id)->with('message', 'Pre class Test results have been recorded, you will be able to view results after post class tests');
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
            return view('dashboard.student.pretests.quizz', compact('questions', 'i', 'program', 'program_name', 'module_title', 'time'));
        }
        if ($module_type == 'Certification Test') {
            return view('dashboard.student.pretests.certification', compact('questions', 'i', 'program', 'program_name', 'module_title', 'time'));
        }
    }


    public function edit($id)
    {
        //
    }


    public function update(Mocks $mock, Request $request)
    {

        try {
            if (!empty(array_intersect(facilitatorRoles(), Auth::user()->role()))) {
                $marked_by = Auth::user()->name;
                $roleplayscore = $request->roleplayscore;
                $grader = $mock->grader;
                $email_test_score = $mock->email_test_score;
                $certification_score = $mock->certification_test_score;
            }

            if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
                $marked_by = $mock->marked_by;
                $roleplayscore = $request->roleplayscore;
                $grader = 'Admin';
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }

            if (!empty(array_intersect(graderRoles(), Auth::user()->role()))) {
                $marked_by = $mock->marked_by;
                $roleplayscore = $mock->role_play_score;
                $grader = Auth::user()->name;
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }

            $mock->marked_by = $marked_by;
            $mock->grader = $grader;
            $mock->certification_test_score = $certification_score;
            $mock->role_play_score = $roleplayscore;
            $mock->email_test_score = $email_test_score;

            // dd($mock->id);
            $mock->save();
        } catch (PDOException $ex) {
            return back()->with('error', $ex->getMessage());
        }

        return Redirect::to(route('mocks.getgrades', $mock->program_id))->with('message', 'User Scores have been updated successfully');
    }

    public function destroy(Request $request)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $users_results = Mocks::where('user_id', $request->id)->get();

            //delete all user results
            foreach ($users_results as $results) {
                $results->delete();
            };


            return redirect('mockresults')->with('message', 'All Pre Test Results for this user have been deleted successfully');
        }
        return back();
    }
}
