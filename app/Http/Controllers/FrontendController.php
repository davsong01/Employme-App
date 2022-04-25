<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\Location;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FrontendController extends Controller
{
    public function index(Request $request)
    {
        // $search = $request->query('search');
        // if($search){
            //     $trainings = Program::where('p_name', 'LIKE', "%{$search}%")->where('id', '<>', 1)->whereStatus(1)->whereIn('id',$programs)->ORDERBY('created_at', 'DESC')->get();
            // }else{
            //     $trainings = Program::where('id', '<>', 1)->whereStatus(1)->whereIn('id',$programs)->ORDERBY('created_at', 'DESC')->get();
            // }

        
        

        if(Session::get('facilitator')){
            $programs = FacilitatorTraining::whereUserId(Session::get('facilitator_id'))->pluck('program_id')->toArray();
            $trainings = Program::where('id', '<>', 1)->whereStatus(1)->whereIn('id',$programs)->ORDERBY('created_at', 'DESC')->get();
            $discounts = Program::where('e_amount', '!=', 0)->where('close_earlybird', 0)->where('id', '<>', 1)->whereIn('id',$programs)->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();

        }else{
            $trainings = Program::where('id', '<>', 1)->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();
            $discounts = Program::where('e_amount', '!=', 0)->where('close_earlybird', 0)->where('id', '<>', 1)->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();
        }
        
        return view('welcome', compact('trainings', 'discounts'));
    }

    public function show($id)
    {
        $training = Program::findOrFail($id);

        if($training->p_end < date('Y-m-d') || $training->close_registration == 1){
            return redirect(route('welcome'));
        }

        $locations = Location::select('title')->distinct()->whereProgramId($training->id)->get();

        return view('single_training', compact('training', 'locations'));
    }

    public function getfile($filename){
        $realpath = base_path() . '/uploads/trainings'. '/' .$filename;
        return response()->download($realpath);
    }  

    public function thankyou(Request $request){
         dd($request->all());
        return view('emails.thankyou');
    }
}
