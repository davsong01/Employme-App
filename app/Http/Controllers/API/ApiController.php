<?php

namespace App\Http\Controllers\API;

use App\Certificate;
use App\User;
use App\Module;
use App\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function show(Request $request)
    { 
        if($request->ref == md5('PASSWACSP_#12345')){

            $users = User::with('program', 'certificate', 'results')->select('id','name', 'email', 'program_id', 'balance', 'created_at')->where('program_id', '<>', 3)->where('email', $request->email)->where('role_id', 'Student')->get();
        
            foreach($users as $user){
            
                $user['total_ct_score'] = 0;
                $user['total_cert_score'] = 0;
                $user['program_score_settings'] = 0;
                $user['total_email_test_score'] = 0;
                
                $user['class_test_module_count'] = Module::where('program_id', $user->program->id)->where('type', 'Class Test')->count();  
                $user['total_role_play_score'] = 0;
                if(isset($user->results)){
                    foreach($user->results as $results){
                        $user['marked_by'] = $results->marked_by; 
                        $user['grader'] = $results->grader;
                        $user['total_role_play_score'] = $results->role_play_score + $user['total_role_play_score']; 
                        $user['total_email_test_score'] = $results->email_test_score + $user['total_email_test_score']; 

                    $user['module'] = $results->module->id;
                        if($results->module->type == 'Class Test'){
                            $user['total_ct_score'] = $results->class_test_score + $user['total_ct_score']; 
                            //$user['modules'] = $results->module->count();
                            $user['program_score_settings'] = $results->program->scoresettings->class_test;
                            //print_r( $results->module->questions->count());
                            $user['program_score_settings'] = $results->program->scoresettings->class_test;
                           
                            $u =  Module::where('type', 0)->get();
                            $obtainable = array();
                            foreach($u as $t){
                                $questions = array_push($obtainable, $t->questions->count());
                            }
                            $obtainable = array_sum($obtainable);
                            
                            $user['test_score'] = ($user['total_ct_score'] * $user['program_score_settings'] ) / $obtainable;

                            $user['test_score'] = round($user['test_score'] , 0);
                   
                        }
                        
                        if($results->module->type == 'Certification Test'){
                            $user['result_id'] = $results->id;
                        }
                        
                        $user['total_cert_score'] = $results->certification_test_score + $user['total_cert_score']; 
                        
                        $user['total_scores'] = $user['total_cert_score'] + $user['test_score'] + $user['total_email_test_score'] + $user['total_role_play_score'];  
                    } 
                }
        
            }
            
            $participant['score'] = $user['total_scores'];
            $participant['rating'] = $this->calculateRating($user['total_scores'], $user->program->id);
            $participant['id'] = $user->id;
            $participant['name'] = $user->name;
            $participant['email'] = $user->email;
            $participant['training'] = $user->program->p_name;
            $participant['balance'] = $user->balance;
            $participant['registered'] = $user->certificate->created_at;
            
            // dd($participant['score']);
            if( $participant['score'] < 75){
                $message = "<strong style='color:red'>NOT CERTIFIED on ". config('app.name')."</strong>";
            }else if( $participant['score'] >= 75){
                $message = "<strong style='color:green'>CERTIFIED on ". config('app.name')."</strong>";
            }
            

            $response = ['success' => true, 'data' => $participant,'message' => $message,];
            
            return response()->json($response, 200);
        
        }return response()->json(['success' => false,'message' => 'Unauthorized Access',], 401);  
    }

    private function calculateRating($score){

            if($score < 74 ){
                $rating = 'NULL';
            }

            if($score >= 75 && $score <= 85 ){
                $rating = 1;
            }

            if($score >= 86 && $score <= 94 ){
                $rating = 2;
            }

            if($score >= 86 && $score <= 94 ){
                $rating = 3;
            }

            if($score >= 95 && $score <= 99 ){
                $rating = 4;
            }

            if($score == 100){
                $rating = 5;
            }
           
           return $rating;
    }

    public function verifyCertificateNumber(Request $request){
        $certificate_number = request()->get('certificate_number');

        if(!$certificate_number){
            return response()->json(['status' => false, 'message' => 'Certificate number is required!',], 422);
        }

        $certificate = Certificate::where('certificate_number', $certificate_number)->first();
        
        if(!$certificate){
            return response()->json(['status' => false,'message' => 'Certificate not found!','data' => null], 401);
        }
        
        if ($certificate->show_certificate() == 'Disabled') {
            return response()->json(['status' => false, 'message' => 'Certificate not found!', 'data' => null], 401);
        }
        
        $details = [
            'certificate_number' => $certificate->certificate_number,
            'training' => $certificate->program->p_name,
            'participant' => $certificate->user->name,
        ];
        $response = ['success' => true,'message' => 'Success', 'data' => $details,];

        return response()->json($response, 200);
    }
}
