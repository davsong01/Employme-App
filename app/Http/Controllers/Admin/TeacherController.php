<?php

namespace App\Http\Controllers\Admin;

use PDF;
use App\Models\Role;
use App\Models\User;
use App\Models\Admin;
use App\Models\Program;
use App\Models\Material;
use App\Mail\Welcomemail;
use App\Models\PaymentMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\FacilitatorTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class TeacherController extends Controller
{
    public function index()
    {
        $i = 1;

        $users = Admin::select('id', 'last_login','off_season_availability', 'name', 'earnings', 'email', 'profile_picture', 'roles', 'created_at', 'phone', 'license', 'status')
            ->distinct()->with('trainings');

        if(checkRoleHas(['Facilitator', 'Grader'])){
            $users = $users->whereNotIn('id', [1, resolveAuthUser()->id]);
        }

        $users = $users->orderBy('created_at', 'DESC')->get();

        
        $users->map(function ($user) {
            $details = DB::table('facilitator_trainings')->where('user_id', $user->id);
            $user->program_count = $details->distinct()->count();
            $transactions = DB::table('program_user')->where('facilitator_id', $user->id);
            $user->students_count = $transactions->count();
            $user->earnings = $transactions->sum('facilitator_earning');
            $user->image = (filter_var($user->profile_picture, FILTER_VALIDATE_URL) !== false) ? $user->profile_picture : url('/') . '/avatars' . '/' . $user->profile_picture;

            return $user;
        });
        
        foreach ($users as $user) {
            $names = [];
            foreach ($user->trainings as $trainings) {
                if (isset($trainings)) {
                    $trainingp_name = Program::whereId($trainings->program_id)->value('p_name');
                    array_push($names, $trainingp_name);
                } else;
            }

            $user->p_names =  $names;
        }
        
        return view('dashboard.admin.teachers.index', compact('users', 'i'));
    }

    public function showStudents($id)
    {
        $users = DB::table('program_user')->where('facilitator_id', $id)
            ->join('users', 'program_user.user_id', '=', 'users.id')
            ->join('programs', 'programs.id', '=', 'program_user.program_id')
            ->orderBy('program_user.created_at')
            ->get();

        $i = 1;

        return view('dashboard.admin.teachers.my_students', compact('users', 'i'));
    }

    public function showPrograms($id)
    {
        $users = DB::table('facilitator_trainings')->where(['user_id' => $id, 'status' => 1])
            ->join('programs', 'programs.id', '=', 'facilitator_trainings.program_id')
            ->select('programs.id', 'programs.p_name', 'facilitator_trainings.created_at')
            ->orderBy('facilitator_trainings.created_at')
            ->get();

        $i = 1;

        return view('dashboard.admin.teachers.my_programs', compact('users', 'i'));
    }

    public function showEarnings($id)
    {
        $earnings = DB::table('program_user')->where('facilitator_id', $id)->where('facilitator_earning', '>', 0)
            ->join('users', 'users.id', '=', 'program_user.user_id')
            ->join('programs', 'programs.id', '=', 'program_user.program_id')
            ->select('p_name', 'name', 'program_user.id', 'program_user.facilitator_id', 'currency', 'paymenttype', 'amount', 'invoice_id', 'p_abbr', 'coupon_amount', 'coupon_id', 'coupon_code', 'facilitator_earning', 'profile_picture', 'program_user.created_at', 'currency_symbol')
            ->orderBy('program_user.created_at')
            ->get();

        $i = 1;
        $currency = Admin::whereId($id)->first()->payment_modes->currency_symbol;

        return view('dashboard.admin.teachers.my_student_earnings', compact('earnings', 'i', 'id', 'currency'));
    }

    public function create()
    {
        if(checkRoleHas(['Admin'])) {
            $programs = Program::where('id', '<>', 1)->orderby('created_at', 'DESC')->get();
            $payment_modes = PaymentMode::whereStatus('active')->get();
            return view('dashboard.admin.teachers.create', compact('programs', 'payment_modes'));
        }
        return back();
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'payment_mode' => 'required',
            'license' => 'nullable',
            'picture' => 'nullable',
            'name' => 'required | min:5',
            'profile' => 'sometimes',
            'phone' => 'sometimes',
            'file' => 'nullable',
            'email' => 'required|unique:users,email',
            'password' => 'sometimes',
            'role' => 'required',
            'training' => 'nullable',
            'off_season_availability' => 'nullable',
            'status' => 'required',
        ]);

        if (isset($request->password) && !empty($request->password)) {
            $password = $data['password'];
        } else {
            $password = 12345;
        }

        if ($request->has('file')) {
            $imgName = $request->file->getClientOriginalName();
            $picture = Image::make($request->file)->resize(400, 400);
            $picture->save('profiles/' . '/' . $imgName);
        } else {
            $imgName = null;
        }

        $data['menu_permissions'] = implode(',', $request->menu_permissions);

        // try {
            if (!empty(array_intersect($data['role'], ["Facilitator", "Grader"]))) {
                $facilitator = Admin::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'profile' => $data['profile'],
                    'phone' => $data['phone'],
                    'off_season_availability' => $data['off_season_availability'],
                    'profile_picture' => $data['picture'] ?? $imgName,
                    'license' => $data['license'],
                    'password' => bcrypt($password),
                    'roles' => implode(',', $data['role']),
                    'status' => $data['status'],
                    'payment_mode' => $data['payment_mode'],
                    'waacsp_url' => $data['waacsp_url'] ?? null,
                    'menu_permissions' => $data['menu_permissions'],

                ]);

                if ($request->has('training')) {
                    //attach facilitator to program
                    foreach ($data['training'] as $training) {
                        FacilitatorTraining::create([
                            'user_id' => $facilitator->id,
                            'program_id' => $training
                        ]);
                    }
                }

                return redirect(route('teachers.index'))->with('message', 'Facilitator added succesfully');
            }

            if (!empty(array_intersect($data['role'], ["Admin"]))) {
                $datax = request()->validate([
                    'menu_permissions' => 'required',
                ]);

                Admin::create([
                    'name' => $data['name'],
                    'profile' => $data['profile'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => bcrypt($data['password']),
                    'roles' => implode(',', $data['role']),
                    'menu_permissions' => $data['menu_permissions'],
                ]);

                return redirect(route('teachers.index'))->with('message', 'Admin added succesfully');
            }
        // } catch (\Throwable $th) {
        //     dd($th->getMessage(), $th->getLine());
        //     return back()->with('error', $th->getMessage());
        // }
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
        $user = Admin::with('trainings')->where('id', $id)->first();
        
        $allprograms = Program::where('id', '<>', 1)
            ->select('id', 'p_name', 'created_at')->orderBy('created_at', 'DESC')->get();

        $other_details = DB::table('program_user')->where('facilitator_id', $user->id);
        $user->students_count = $other_details->count();
        $user->earnings = $other_details->sum('facilitator_earning');

        $user->image = (filter_var($user->profile_picture, FILTER_VALIDATE_URL) !== false) ? $user->profile_picture : url('/') . '/profiles/' . $user->profile_picture;

        $payment_modes = PaymentMode::whereStatus('active')->get();
        
        foreach($user->trainings as $training){
            $program = Program::select('id','p_name')->where('programs.id', $training->program_id)->first();
            $training->p_name = $program->p_name;
        }
        
        return view('dashboard.admin.teachers.edit', compact( 'user', 'allprograms', 'payment_modes'));
    }
    
    public function update(Request $request, $id)
    {
        $user = Admin::findorFail($id);
        $check = [
            'teachers.update.menu',
            'teachers.update.training.access',
            'teachers.role.status'
        ];

        $allpermissions = canUserAccessPermission($check);
        
        if ($request['password']) {
            $user->password = bcrypt($request['password']);
        };

        if (request()->has('file')) {
            $imgName = $request->file->getClientOriginalName();
            $picture = Image::make($request->file)->resize(400, 400);
            $picture->save('profiles/' . '/' . $imgName);
        }
        
        
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];

        if ($allpermissions['teachers.role.status']) {
            $user->roles = $request['role'];
        }

        $user->profile = $request['profile'];
        $user->status = $request['status'];
        $user->profile_picture = $imgName ?? $user->profile_picture;
        $user->status = $request['status'];

        $user->payment_mode = $request['payment_mode'];
        $user->off_season_availability = $request['off_season_availability'];
        $user->waacsp_url = $request['waacsp_url'];

        if($allpermissions['teachers.update.menu']){
            $user->menu_permissions = $request->menu_permissions;
        }
        
        //Delete corresponding Facilitator Program details
        if ($allpermissions['teachers.update.training.access']) {
            if (!empty($request['training'])) {
                foreach ($request['training'] as $training) {
                    if(isset($request->training_permissions[$training])){
                        FacilitatorTraining::updateOrCreate([
                            'user_id' => $user->id,
                            'program_id' => $training],[
                            'user_id' => $user->id,
                            'program_id' => $training,
                            'training_permissions' => $request->training_permissions[$training]
                        ]);
                    }
                }
                
                FacilitatorTraining::where('user_id', $user->id)
                    ->whereNotIn('program_id', $request->training)
                    ->delete();
            }
        }
        
        $user->save();
        return back()->with('message', 'Operation Successful');
    }

    public function destroy($id)
    {
        $user = Admin::findOrFail($id);
        //Delete corresponding programs
        FacilitatorTraining::whereUserId($user->id)->delete();

        $user->delete();
        return redirect('teachers')->with('message', 'Facilitator deleted successfully');
    }
}
