<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\Location;
use App\Settings;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FrontendController extends Controller
{
    public function reset(){
        Session::flush();
        $setting = Settings::first();
        $currency = $setting->CURR_ABBREVIATION;
        $currency_symbol = $setting->DEFAULT_CURRENCY;

        Session::put('currency_symbol', $currency_symbol);
        Session::put('currency', $currency);
        Session::put('exchange_rate', 1);

        return redirect(url('/'));
    }

    public function index(Request $request)
    {

        if($request->has('search')){
            if (Session::get('facilitator')) {
                $programs = FacilitatorTraining::whereUserId(Session::get('facilitator_id'))->pluck('program_id')->toArray();
                $trainings = Program::where('id', '<>', 1)->whereStatus(1)->whereIn('id', $programs)->where('p_name', 'LIKE', '%' . $request->search . '%')->ORDERBY('created_at', 'DESC')->paginate(12);
            } else {
                $trainings = Program::where('id', '<>', 1)->whereStatus(1)->where('p_name', 'LIKE', '%' . $request->search . '%')->ORDERBY('created_at', 'DESC')->paginate(12);
                // dd($trainings->count());
            }
            $search = $request->search;
            return view('search_results', compact('trainings', 'search'));
        }

        if(Session::get('facilitator')){
            $programs = FacilitatorTraining::whereUserId(Session::get('facilitator_id'))->pluck('program_id')->toArray();
            $trainings = Program::where('id', '<>', 1)->whereStatus(1)->whereIn('id',$programs)->ORDERBY('created_at', 'DESC')->paginate(12);
            $discounts = Program::where('e_amount', '!=', 0)->where('close_earlybird', 0)->where('id', '<>', 1)->whereIn('id',$programs)->where('p_end', '>=', now())->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();
        }else{
            $trainings = Program::where('id', '<>', 1)->whereStatus(1)->ORDERBY('created_at', 'DESC')->ORDERBY('p_start', 'ASC')->paginate(12);
            $discounts = Program::where('e_amount', '!=', 0)->where('close_earlybird', 0)->where('id', '<>', 1)->where('p_end','>=', now())->whereStatus(1)->ORDERBY('created_at', 'DESC')->get();
        }
      
        return view('welcome', compact('trainings', 'discounts'));
    }


    public function show($id)
    {
        $training = Program::findOrFail($id);

        if($training->p_end < date('Y-m-d') || $training->close_registration == 1){
            return redirect(route('welcome'));
        }
        
        $locations = (!is_null($training->locations) && $training->show_locations == 'yes') ? json_decode($training->locations, true) : null;
        $modes = (!is_null($training->modes) && $training->show_modes == 'yes') ? json_decode($training->modes, true) : null;
        
        return view('single_training', compact('training', 'locations','modes'));
    }

    public function getfile($filename){
        $realpath = base_path() . '/uploads/trainings'. '/' .$filename;

        return response()->download($realpath);
    }  

    public function thankyou(Request $request){
      
        return view('thankyou');
    }
}
