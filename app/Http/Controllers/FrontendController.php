<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use App\Models\Program;
use App\Models\Location;
use App\Models\Settings;
use Illuminate\Http\Request;
use App\Models\FacilitatorTraining;
use Illuminate\Support\Facades\Session;
use App\Services\CurrencyAmountFormatter;

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
        $isFacilitator = Session::get('facilitator');
        $facilitatorProgramIds = $isFacilitator
            ? FacilitatorTraining::whereUserId(Session::get('facilitator_id'))->pluck('program_id')->toArray()
            : null;

        $programQuery = Program::allMainPrograms()
            ->where('id', '<>', 1)
            ->whereStatus(1);

        if ($isFacilitator && $facilitatorProgramIds) {
            $programQuery->whereIn('id', $facilitatorProgramIds);
        }

        $today = date('Y-m-d');

        // Upcoming
        $upcomingTrainings = (clone $programQuery)
            ->where('p_start', '>', $today)
            ->orderByDesc('created_at')
            ->paginate(8, ['*'], 'upcoming_page');

        // Ongoing
        $ongoingTrainings = (clone $programQuery)
            ->where('p_start', '<=', $today)
            ->where('p_end', '>=', $today)
            ->orderByDesc('created_at')
            ->paginate(8, ['*'], 'ongoing_page');

        // Past
        $pastTrainings = (clone $programQuery)
            ->where('p_end', '<', $today)
            ->orderByDesc('created_at')
            ->paginate(8, ['*'], 'past_page');

        // Discounts
        $discountsQuery = Program::where('e_amount', '!=', 0)
            ->where('early_bird_status', 0)
            ->where('id', '<>', 1)
            ->where('p_end', '>=', now())
            ->whereStatus(1);

        if ($isFacilitator && $facilitatorProgramIds) {
            $discountsQuery->whereIn('id', $facilitatorProgramIds);
        }

        $discounts = $discountsQuery->orderByDesc('created_at')->get();

        return view('welcome', compact(
            'discounts',
            'upcomingTrainings',
            'ongoingTrainings',
            'pastTrainings'
        ));
    }

    public function packages(Request $request)
    {
        if ($request->filled('search')) {
            $keyword   = $request->search;

            $packages = Group::with(['programs' => function ($q) {
                $q->mainActivePrograms();
            }])// eager-load child programs
            ->isActive()                       // scope: status = 1
            ->where('p_name', 'LIKE', "%{$keyword}%")
            ->latest()
            ->simplePaginate(16);

            return view('search_results', [
                'trainings' => $packages,          // keeps view variable name
                'search'    => $keyword,
            ]);
        }

        if (Session::get('facilitator')) {
            // pull program IDs linked to this facilitator
            $programIds = FacilitatorTraining::whereUserId(Session::get('facilitator_id'))
                ->pluck('program_id')
                ->toArray();

            // packages that contain at least one of the facilitator’s programs
            $packages = Group::with(['programs' => function ($q) {
                $q->mainActivePrograms();
            }])
            ->isActive()
            ->whereHas('programs', fn($q) => $q->whereIn('programs.id', $programIds))
            ->latest()
            ->simplePaginate(16);

            // discounted packages (early-bird price set & still valid)
            $discounts = Group::with(['programs' => function ($q) {
                    $q->mainActivePrograms();
                }])
                ->isActive()
                ->where('e_amount', '!=', 0)
                ->where('early_bird_status', 1)
                ->whereDate('p_end', '>=', now())
                ->whereHas('programs', fn($q) => $q->whereIn('programs.id', $programIds))
                ->latest()
                ->get();
        } else {
            // all active packages
            $packages = Group::with(['programs' => function ($q) {
                $q->mainActivePrograms(); 
            }])
            ->isActive()
            ->latest()
            ->simplePaginate(16);
            
            // global discounts
            $discounts = Group::with(['programs' => function ($q) {
                $q->mainActivePrograms();
            }])
            ->isActive()
            ->where('e_amount', '!=', 0)
            ->where('early_bird_status', 1)
            ->latest()
            ->get();
        }
        
        return view('packages', [
            'trainings' => $packages,  // keeps existing view variable names
            'discounts' => $discounts,
        ]);
    }

    public function earlyBird($id = null)
    {
        $id = \Request::get('training') ?? $id;
        $training = Program::with('subPrograms')->where('id', $id)->orWhere('slug', $id)->first();
        
        if ($training->p_end < date('Y-m-d') || $training->close_registration == 1) {
            return redirect(route('welcome'));
        }

        if($training->early_bird_status != 1 && $training->e_amount < 1){
            return redirect(route('welcome'));
        }

        $locations = (!is_null($training->locations) && $training->show_locations == 'yes') ? json_decode($training->locations, true) : null;
        $modes = (!is_null($training->modes) && $training->show_modes == 'yes') ? json_decode($training->modes, true) : null;
        
        if (isset($training->subPrograms) && $training->subPrograms->count() > 0) {
            return view('early_bird_single_training_with_children', compact('training', 'locations', 'modes'));
        }
        
        return view('early_bird_single_training', compact('training', 'locations', 'modes'));
    }

    public function groupEarlyBird($id = null)
    {
        $id = \Request::get('training') ?? $id;

        $training = Group::with(['programs' => function ($q) {
            $q->mainActivePrograms();
        }])
            ->isActive()
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();
        
        if ($training->p_end < date('Y-m-d') || $training->status != 1 || $training->early_bird_status != 1) {
            return redirect(route('packages'));
        }
        
        $locations = (!is_null($training->locations) && $training->show_locations == 'yes') ? json_decode($training->locations, true) : null;
        $modes = (!is_null($training->modes) && $training->show_modes == 'yes') ? json_decode($training->modes, true) : null;
        $training->is_closed = 'no';
        $is_package = true;
        return view('early_bird_single_training', compact('training', 'locations', 'modes', 'is_package'));
    }


    public function show($id = null)
    {
        $id = \Request::get('training') ?? $id ;
        $training = Program::with('subPrograms')->where('id', $id)->orWhere('slug', $id)->first();
        
        if(!$training){
            return back();
        }
        if($training->p_end < date('Y-m-d') || $training->close_registration == 1){
            return redirect(route('welcome'));
        }
        $locations = (!is_null($training->locations) && $training->show_locations == 'yes') ? json_decode($training->locations, true) : null;
        $modes = (!is_null($training->modes) && $training->show_modes == 'yes') ? json_decode($training->modes, true) : null;
        
        if(isset($training->subPrograms) && $training->subPrograms->count() > 0){
            return view('single_training_with_children', compact('training', 'locations', 'modes'));
        }
        
        return view('single_training', compact('training', 'locations','modes'));
    }

    public function showPackages($id = null)
    {
        $id = \Request::get('group') ?? $id;
        
        $group = Group::with(['programs' => function ($q) {
                $q->mainActivePrograms();
            }])
            ->isActive()
            ->where('id', $id)
            ->orWhere('slug', $id)
            ->firstOrFail();

        if ($group->p_end < date('Y-m-d') || $group->status != 1) {
            return redirect(route('packages'));
        }

        $locations = (!is_null($group->locations) && $group->show_locations == 'yes') ? json_decode($group->locations, true) : null;
        $modes = (!is_null($group->modes) && $group->show_modes == 'yes') ? json_decode($group->modes, true) : null;
        
        return view('single_group', compact('group', 'locations', 'modes'));
        
    }

    public function getModePaymentTypes(Request $request){
        // Check if mode exist for that training, if not return
        $options = "";
        $program = Program::where('id', $request->training)->first();
        if($program->show_modes == 'yes' && !empty($program->modes)){
            $modes = json_decode($program->modes, true);
            $mode_amount = $modes [$request->payment_mode];
            $options .= "<option value='full'>Full Payment (". $request->currency_symbol.number_format($mode_amount) .")</option>";

            if($program->haspartpayment == 1){
                $options .= "<option value='part'>Part Payment (". $request->currency_symbol.number_format($mode_amount/2) .")</option>";
            }
        }else{
            $options .= "<option value='full'>Full Payment (".$request->currency_symbol.number_format($program->p_amount).")</option>";

            if(($program->e_amount > 0 ) && $program->early_bird_status == 0 || $program->e_amount > 0){
                $options .= "<option value='earlybird'>Earlybird (".$request->currency_symbol.number_format($program->e_amount).")</option>";
            }
          
            if($program->haspartpayment == 1){
                $options .= "<option value='part'>Part Payment (". $request->currency_symbol.number_format($program->p_amount/2) .")</option>";
            }
                                            
        }
       
        return response()->json(['status'=>'success', 'data'=>$options]);
    }

    public function getfile($filename){
        $realpath = base_path() . '/uploads/trainings'. '/' .$filename;

        return response()->download($realpath);
    }

    public function thankyou(Request $request){
        return view('thankyou');
    }
}
