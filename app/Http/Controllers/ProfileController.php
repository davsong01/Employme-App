<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    
    public function edit($id)
    {
        $user = User::select('id', 'name', 'email', 'password', 't_phone', 'profile_picture', 'gender')->whereId($id)->first();

        if(Auth::user()->role_id == "Admin" && $id == Auth::user()->id){
        return view('dashboard.admin.profiles.edit', compact('user'));
        }
        elseif(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            return view('dashboard.admin.profiles.edit', compact('user'));
        }
        elseif(Auth::user()->role_id == "Student" && $id == Auth::user()->id){
            return view('dashboard.student.profiles.edit', compact('user'));
        }
        return back();
    }
   
    public function update(Request $request, $id)
    {   

        $user = User::findorFail(Auth::user()->id);
       
        $user->name = $request->name;
        $user->t_phone = $request->t_phone;
        $user->gender = $request->gender;

        if($request['password']){
            $user->password = bcrypt($request['password']);
        };

        if(request()->has('file')){ 
           
            $imgName = $request->file->getClientOriginalName();
            
            $picture = Image::make($request->file)->resize(100, 100);
            
            $picture->save('profiles/'.'/'.$imgName);

            $user->update([
                'profile_picture' => $imgName,
            ]);
        }
        
        $user->save();
       
        return back()->with('message', 'Profile update successful');
    
    }
    private function validateRequest(){
        return tap(request()->validate([
        'name' => 'required',
        't_phone' =>'required | numeric | min:9',
        'gender' => 'required',
        ]), function (){
            if (request()->hasFile('file')){
                request()->validate([
                    'file' =>'mimes:jpeg,png,jpg|max:1000',
                ]);
            }
        });
}           
    private function storeImage($user){

    }
}
