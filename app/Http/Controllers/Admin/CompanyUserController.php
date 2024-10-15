<?php

namespace App\Http\Controllers\Admin;

use App\Models\Program;
use App\Models\CompanyUser;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\CompanyUserTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CompanyUserController extends Controller
{
    public function index()
    {
        $i = 1;

        $users = CompanyUser::select('id', 'name', 'email', 'created_at', 'phone', 'permissions', 'status')
            ->distinct()->with('trainings')
            ->orderBy('created_at', 'DESC')->get();

        foreach($users as $user){
            $trainings = $user->trainings->pluck('program_id')->toArray();
            $user->p_names = Program::select(['id','p_name'])->whereIn('id', $trainings)->orderBy('created_at','DESC')->get();
        }
        // $users->map(function ($users) {
        //     $details = CompanyUserTraining::where('company_user_id', $users->id);
        //     $users->program_count = $details->distinct()->count();
        //     $transactions = DB::table('program_user')->where('facilitator_id', $users->id);
        //     $users->students_count = $transactions->count();
        //     $users->earnings = $transactions->sum('facilitator_earning');
        //     $users->image = (filter_var($users->profile_picture, FILTER_VALIDATE_URL) !== false) ? $users->profile_picture : url('/') . '/avatars' . '/' . $users->profile_picture;

        //     return $users;
        // });
        
        // foreach ($users as $user) {
        //     $names = [];
        //     foreach ($user->trainings as $trainings) {
        //         if (isset($trainings)) {
        //             $trainingp_name = Program::whereId($trainings->program_id)->value('p_name');
        //             array_push($names, $trainingp_name);
        //         } else;
        //     }

        //     $user->p_names =  $names;
        // }

        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            return view('dashboard.admin.company.index', compact('users', 'i'));
        }
    }

    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role()))) {
            $programs = Program::where('id', '<>', 1)->orderby('created_at', 'DESC')->get();
            $menus = app('app\Http\Controllers\Controller')->companyMenus();

            $menuStructure = [];
            foreach ($menus as $menu) {
                if ($menu['isParent'] == 'yes') {
                    $menuStructure[$menu['id']] = $menu;
                    $menuStructure[$menu['id']]['children'] = [];
                } else {
                    $menuStructure[$menu['parentId']]['children'][] = $menu;
                }
            }
            
            return view('dashboard.admin.company.create', compact('programs', 'menuStructure'));
        }
        return back();
    }

    public function store(Request $request)
    {
        $data = request()->validate([
            'name' => 'required | min:5',
            'gender' => 'sometimes',
            'job_title' => 'sometimes',
            'profile_picture' => 'sometimes',
            'phone' => 'sometimes',
            'email' => 'required|unique:company_users,email',
            'trainings' => 'nullable',
            'status' => 'required',
            'permissions' => 'required',
        ]);
        
        $selectedPrograms = [];
        
        foreach ($request->trainings as $programId => $programData) {
            if (isset($programData['selected'])) {
                $selectedPrograms[$programId] = $programData['access'];
            }
        }

        $data['password'] = Hash::make(generateRandomNumber(6));
        
        $admin = CompanyUser::create(Arr::except($data, 'trainings'));
        
        if (!empty($selectedPrograms)) {
            foreach ($selectedPrograms as $key=>$value) {

                CompanyUserTraining::updateOrCreate(
                [
                    'company_user_id' => $admin->id,
                    'program_id' => $key,
                ],[
                    'company_user_id' => $admin->id,
                    'program_id' => $key,
                    'access' => $value
                ]);
            }
        }

        return redirect(route('companyuser.index'))->with('message', 'Company Admin added succesfully');
    }

    public function edit(CompanyUser $companyuser){
        $programs = Program::where('id', '<>', 1)->orderby('created_at', 'DESC')->get();
        $menus = app('app\Http\Controllers\Controller')->companyMenus();

        $menuStructure = [];
        foreach ($menus as $menu) {
            if ($menu['isParent'] == 'yes') {
                $menuStructure[$menu['id']] = $menu;
                $menuStructure[$menu['id']]['children'] = [];
            } else {
                $menuStructure[$menu['parentId']]['children'][] = $menu;
            }
        }

        $user = $companyuser;
        $user->trainings = $user->trainings->mapWithKeys(function ($training) {
            return [$training['program_id'] => $training['access']];
        })->toArray();

        
        return view('dashboard.admin.company.edit', compact('programs', 'menuStructure','user'));
    }

    public function update(Request $request, CompanyUser $companyuser)
    {
        $data = request()->validate([
            'name' => 'required | min:5',
            'phone' => 'sometimes',
            'gender' => 'sometimes',
            'job_title' => 'sometimes',
            'profile_picture' => 'sometimes',
            'email' => [
                'required',
                Rule::unique('company_users')->ignore($companyuser->id)
            ],
            'trainings' => 'nullable',
            'status' => 'required',
            'permissions' => 'required',
        ]);
        
        $selectedPrograms = [];

        foreach ($request->trainings as $programId => $programData) {
            if (isset($programData['selected'])) {
                $selectedPrograms[$programId] = $programData['access'];
            }
        }

        if(!empty($request->password)){
            $data['password'] = Hash::make($request->password);
        }
        
        $companyuser->update(Arr::except($data, 'trainings'));
        
        if (!empty($selectedPrograms)) {
            CompanyUserTraining::where('company_user_id', $companyuser->id)->delete();
            foreach ($selectedPrograms as $key => $value) {

                CompanyUserTraining::updateOrCreate(
                    [
                        'company_user_id' => $companyuser->id,
                        'program_id' => $key,
                    ],
                    [
                        'company_user_id' => $companyuser->id,
                        'program_id' => $key,
                        'access' => $value
                    ]
                );
            }
        }

        return back()->with('message', 'Update Successful');
    }

    public function destroy(CompanyUser $companyuser){
        CompanyUserTraining::where('company_user_id', $companyuser->id)->delete();
        $companyuser->delete();

        return back()->with('message','Delete Successful');
    }
}