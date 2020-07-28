<?php

namespace App\Http\Controllers\API;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function show(Request $request)
    { 
        if($request->ref == md5('PASSWACSP_#12345')){

            $user =  User::with('program', 'certificate')->select('id','name', 'email', 'program_id', 't_amount', 'created_at')->where('email', $request->email)->where('role_id', 'Student')->first();

            $certificate = $user->certificate;
            if(!empty($certificate)){
                $message = 'CERTIFIED on '. config('app.name');
            }else $message = 'NOT CERTIFIED on '. config('app.name');
            
            $participant['id'] = $user->id;
            $participant['name'] = $user->name;
            $participant['email'] = $user->email;
            $participant['training'] = $user->program->p_name;
            $participant['training fee'] = $user->program->p_amount;
            $participant['amount paid'] = $user->t_amount;
            $participant['registered'] = $user->created_at;

            
            $response = ['success' => true, 'data' => $participant,'message' => $message,];
            
            return response()->json($response, 200);
        
        }return response()->json(['success' => false,'message' => 'Unauthorized Access',], 401);
        
    }

}
