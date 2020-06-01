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
        $user = User::findorFail($id);
        $programs = Program::all();
        if(Auth::user()->role_id == "Admin" && $id == Auth::user()->id){
        return view('dashboard.admin.profiles.edit', compact('programs','user'));
        }elseif(Auth::user()->role_id == "Teacher" && $id == Auth::user()->id){
            return view('dashboard.teacher.profiles.edit', compact('programs','user'));
        }elseif($id == Auth::user()->id){
            return view('dashboard.student.profiles.edit', compact('programs','user'));
        }
        return back();
    }
   
    public function update(Request $request, $id)
    {   
        $user = User::findorFail(Auth::user()->id);
    
            if($request['password']){
                $user-> password = bcrypt($request['password']);
            };

            if(request()->has('file')){ 
               // dd($request->file);

                $imgName = $request->file->getClientOriginalName();
                
                $picture = Image::make($request->file)->resize(100, 100);
                
                $picture->save('profiles/'.'/'.$imgName);

                $user->update([
                    'profile_picture' => $imgName,
                ]);
            }
    

        //check amount against payment
        // $user->update($this->validateRequest());
        // $this->storeImage($user);
       
        return back()->with('message', 'Profile update successful');
    
    }
    private function validateRequest(){
        return tap(request()->validate([
        'name' => 'required',
        't_phone' => '',
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
