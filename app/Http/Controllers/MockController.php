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
use Illuminate\Http\Request;
use App\Services\ExcelService;
use App\Models\TempTransaction;
use App\Models\FacilitatorTraining;
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

    
    public function pretest(Request $request)
    {
        if (! checkRoleHas(['Admin', 'Facilitator'])) {
            return back();
        }

        $allowedProgramIds = checkRoleHas(['Admin'])
            ? Program::query()->pluck('id')->all()
            : resolveAuthUser()->trainings()->pluck('program_id')->all();

        $programs = Program::query()
            ->whereIn('id', $allowedProgramIds)
            ->whereHas('mocks')
            ->orderByDesc('created_at')
            ->get(['id', 'p_name']);
        $visibleProgramIds = $programs->pluck('id')->all();
        $programRecordCounts = Transaction::query()
            ->whereIn('program_id', $visibleProgramIds)
            ->whereHas('user')
            ->selectRaw('program_id, COUNT(*) as record_count')
            ->groupBy('program_id')
            ->pluck('record_count', 'program_id');
        $programs->each(fn ($training) => $training->record_count = $programRecordCounts[$training->id] ?? 0);

        $usersQuery = \App\Models\Transaction::query()
            ->with([
                'user',
                'program.scoresettings',
                'mocks' => fn ($query) => $query->whereIn('program_id', $visibleProgramIds)->latest(),
                'mocks.module',
            ])
            ->whereIn('program_id', $visibleProgramIds)
            ->whereHas('user')
            ->when($request->filled('program_id'), fn ($query) => $query->where('program_id', $request->integer('program_id')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->value();
                $query->whereHas('user', fn ($userQuery) => $userQuery
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('staffID', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%"));
            })
            ->when($request->filled('name'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', '%' . $request->input('name') . '%')))
            ->when($request->filled('email'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('email', $request->input('email'))))
            ->when($request->filled('phone'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('phone', $request->input('phone'))))
            ->when($request->filled('staffID'), fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('staffID', $request->input('staffID'))))
            ->when($request->input('status') === 'yes', fn ($query) => $query->whereHas('mocks', fn ($mockQuery) => $mockQuery->whereColumn('mocks.program_id', 'program_user.program_id')))
            ->when($request->input('status') === 'no', fn ($query) => $query->whereDoesntHave('mocks', fn ($mockQuery) => $mockQuery->whereColumn('mocks.program_id', 'program_user.program_id')))
            ->orderByDesc(Program::query()->select('created_at')->whereColumn('programs.id', 'program_user.program_id'))
            ->orderByDesc('program_user.created_at');

        $isExport = $request->filled('columns');
        $users = $isExport ? $usersQuery->get() : $usersQuery->paginate(adminPaginationRecords())->withQueryString();

        $permissionKeys = ['view-certification-score', 'view-roleplay-score', 'view-email-score', 'view-crm-score', 'view-class-score', 'result.export', 'stopredotest', 'results.destroy', 'results.add', 'view-total-score', 'mocks.add', 'mocks.destroy'];
        $permissions = array_fill_keys($permissionKeys, false);
        foreach ($programs as $training) {
            $trainingPermissions = checkTrainingHasPermissions($training->id, $permissionKeys);
            foreach ($permissionKeys as $key) {
                $permissions[$key] = $permissions[$key] || !empty($trainingPermissions[$key]);
            }
        }

        $userCollection = $users instanceof \Illuminate\Pagination\AbstractPaginator ? $users->getCollection() : $users;
        $userCollection->each(function ($record) use ($permissionKeys) {
            $programId = $record->program_id;
            $settings = $record->program?->scoresettings ?? new ScoreSetting();
            $record->total_cert_score = 0;
            $record->total_class_test_score = 0;
            $record->total_email_test_score = 0;
            $record->total_role_play_score = 0;
            $record->total_crm_test_score = 0;
            $record->program_ct_score_settings = $settings->class_test ?? 0;
            $record->passmark = $settings->passmark ?? 0;
            $record->created_at = null;
            $record->class_test_module_count = Module::where('program_id', $programId)->where('status', 1)->where('type', 'Class Test')->count();
            $record->marked_by = '';
            $record->grader = '';
            $record->result_id = 0;
            $record->final_ct_score = 0;
            $record->obtainable = 0;
            $record->name = $record->user->name;
            $record->email = $record->user->email;
            $record->staffID = $record->user->staffID;
            $record->phone = $record->user->phone;
            $record->metadata = $record->user->metadata;
            $record->gender = $record->user->gender;

            $programMocks = $record->mocks->where('program_id', $programId);
            foreach ($programMocks as $attempt) {
                $record->total_role_play_score += $attempt->role_play_score;
                $record->total_email_test_score += $attempt->email_test_score;
                $record->created_at = $attempt->created_at;
                if ($attempt->module?->type === 'Class Test') {
                    $this->calculateClassTestScore($attempt, $record, $programId);
                }
                if ($attempt->module?->type === 'Certification Test') {
                    $record->total_cert_score += $attempt->certification_test_score;
                    $record->result_id = $attempt->id;
                    $record->certification_test_details = 1;
                    $record->marked_by = $attempt->marked_by;
                    $record->grader = $attempt->grader;
                }
                $record->final_ct_score = $this->calculateFinalCtScore($record);
            }

            $record->row_permissions = checkTrainingHasPermissions($programId, $permissionKeys);
            $record->row_score_settings = $settings;
            $record->setRelation('mocks', $programMocks->values());
        });

        $program = $programs->firstWhere('id', $request->integer('program_id')) ?? $programs->first();
        $program ??= new Program(['id' => null, 'p_name' => 'All Programs']);
        $score_settings = $program->scoresettings;
        if ($isExport) {
            $data = in_array('all', $request->columns, true)
                ? ['staffID', 'name', 'email', 'phone', 'gender', 'metadata']
                : $request->columns;
            return (new FastExcel(buildResultExport($users, $data, $score_settings ?? new ScoreSetting())))
                ->download('Pretest Report for selected filters.xlsx');
        }

        $records = $users->total();
        $i = $users->firstItem() ?? 1;
        $page = 'mocks';
        $title = '<b>Pre Test Results</b>';
        $isGlobalResultIndex = true;

        return view('dashboard.admin.mocks.index', compact('users', 'i', 'program', 'programs', 'records', 'score_settings', 'page', 'title', 'permissions', 'isGlobalResultIndex'));
    }

    public function index(Request $request)
    {
        $programId = $request->p_id;

        $transaction = Transaction::where('program_id', $programId)
            ->where('user_id', resolveAuthUser()->id)
            ->first();

        if (checkRoleHas(['Student'])) {

            $program = Program::find($programId);
            $payment = getTransactionFromProgramIds($programId);
            $balance = $payment?->balance ?? 0;
            $currency = $payment?->currency_symbol ?? '₦';

            // Safety: Ensure program exists
            if (!$program) {
                return back()->with('error', 'Program not found.');
            }

            // -------------------------------
            // Payment Restriction Check
            // -------------------------------
            $specialCandidates = [
                // 20250
            ];

            if ($program->allow_payment_restrictions_for_pre_class_tests === 'yes') {
                $payment = getTransactionFromProgramIds($programId);
                $balance = $payment?->balance ?? 0;
                $currency = $payment?->currency_symbol ?? '₦';
                
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
                            ' in order to access this resource'
                    );
                }
                
            }

            $modules = Module::with('questions')
                ->where('program_id', $programId)
                ->whereType(0)
                ->get();

            foreach ($modules as $module) {
                $hasCompleted = Mocks::where('module_id', $module->id)
                    ->where('user_id', resolveAuthUser()->id)
                    ->exists();

                $module->completed = $hasCompleted ? 1 : 0;
            }

            $i = 1;

            return view('dashboard.student.pretests.index', compact('modules', 'i', 'program'));
        }

        return back()->with('error', 'Unauthorized access.');
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
        // The route ID is retained for old bookmarks; without an explicit program
        // filter the results index is global across the user's visible programs.
        if (! $request->filled('p_id') || $request->filled('program_id')) {
            return $this->pretest($request);
        }

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
                $query->where('phone', $request->phone);
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
            
            $isAdmin = checkRoleHas(['Admin']);
            $isFacilitatorOrGrader = checkRoleHas(['Facilitator', 'Grader']);
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
                $user->phone = $user->user->phone;
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
                return view('dashboard.company.posttests.index', compact('users', 'i', 'program', 'records', 'score_settings', 'page', 'title'));
            }

            return view('dashboard.admin.mocks.index', compact('users', 'i', 'program','records', 'score_settings','page','title'));
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

        $class_test_details = $request->except(['_token', 'mod_id', 'id', 'prefix__']);

        if (sizeof($class_test_details) < 2) {
            return back()->with('error', 'You must answer at least 1 question');
        };

        $certification_test_details = $request->except(['_token', 'mod_id', 'id', 'p_id', 'prefix__']);

        foreach ($certification_test_details as $key => $value) {
            if ((!isset($certification_test_details[$key]))) {
                return back()->with('error', 'You must answer at least 1 question');
            };
            // print_r(str_word_count($certification_test_details[$key]);
            if (str_word_count($certification_test_details[$key]) > 500) {
                return back()->with('error', 'Maximum number of words allowed for each question is 500, please try again');
            };
        }

        $check = Mocks::where('user_id', resolveAuthUser()->id)->where('module_id', $request->mod_id)->count();

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
                    'user_id' => resolveAuthUser()->id,
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
                        'user_id' => resolveAuthUser()->id,
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
        
        if (empty($questionsarray) || $questionsarray[0]['module']['program_id'] <> $request->p_id) {
            return back()->with('error', 'No questions avaliable at the moment, please contact admin');
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
            
            if (checkRoleHas(['Facilitator'])) {
                $marked_by = resolveAuthUser()->name;
                $roleplayscore = $request->roleplayscore;
                $grader = $mock->grader;
                $email_test_score = $mock->email_test_score;
                $certification_score = $mock->certification_test_score;
            }

            if(checkRoleHas(['Admin'])) {
                $marked_by = $mock->marked_by;
                $roleplayscore = $request->roleplayscore;
                $grader = 'Admin';
                $email_test_score = $request->emailscore;
                $certification_score = $request->certification_score;
            }

            if (checkRoleHas(['Grader'])) {
                $marked_by = $mock->marked_by;
                $roleplayscore = $mock->role_play_score;
                $grader = resolveAuthUser()->name;
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
        if(checkRoleHas(['Admin'])) {
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
