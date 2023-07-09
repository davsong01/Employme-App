<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\User;
use App\Module;
use App\Program;
use App\Material;
use App\Question;
use App\ScoreSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgramDetailsExport;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


class ProgramController extends Controller
{
    public function index(Program $program)
    {
        $i = 1;
        if(Auth::user()->role_id == "Admin"){
            //Get all programs
            $programs = Program::with('users')->where('id', '<>', 1)->orderBy('created_at', 'desc')->get();
           
            //Get all students
            $users = User::where('role_id', 'Student')->get();

            //Get Users payment status
            foreach($programs as $program){
                $program['part_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '>', 0)->count();
                $program['fully_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '<=', 0)->count();        
            }

            return view('dashboard.admin.programs.index', compact('programs', 'i'));
        } else  return redirect('/');
    }

     public function exportdetails($id) 
    {   
        $programname = Program::whereId($id)->value('p_name');
        $programname = preg_replace('/[^A-Za-z0-9\-]/', '', $programname);
        return Excel::download(new ProgramDetailsExport($id), $programname.' participants.xlsx');
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::where('id', '<>', 1)->get();
            $materials = Material::all();
                return view('dashboard.admin.programs.create', compact('programs'));
                }
            else{
                return redirect('/programs');
            }
       // return view('dashboard.admin.programs.create', compact('programs', 'program'));
    }


    public function store(Request $request)
    {  
       $data = $this->validate($request, [
            'p_name' => 'required',
            'p_abbr' => 'required',
            'p_amount' => 'required',
            'e_amount' => 'required',
            'p_start' => 'required',
            'p_end' => 'required',
            'hasmock' => 'required',
            'booking_form' =>'file|mimes:pdf|max:10000',
            'image' =>'required|image |max:10000',
            'haspartpayment' => 'required',
            'allow_payment_restrictions' => 'required',
            'status' => 'required',
            'off_season' => 'required',
            'show_catalogue_popup'=>'required',
            'show_locations' => 'required',
            'locations'=>'nullable',
            'show_modes' => 'required',
            'allow_payment_restrictions_for_materials' => 'required',
            'allow_payment_restrictions_for_pre_class_tests' => 'required',
            'allow_payment_restrictions_for_post_class_tests' => 'required',
            'allow_payment_restrictions_for_results' => 'required',
            'allow_payment_restrictions_for_certificates' => 'required',
            'allow_payment_restrictions_for_completed_tests' => 'required'
        ]);

        
        //Save booking form
        if($request->file('booking_form')){
            $filePath = $this->uploadFileToUploads($request->file('booking_form'), 'booking_form', 'bookingforms');
            $filePath = 'bookingforms/'.$filePath;
        }

        // uploads file to the desired folder in uploads directory
        $file = $this->uploadFileToUploads($request->file('image'), 'image', 'trainings', 533, 533);
        $data['image'] = 'trainingimage/' . $file;
      
        if($request->has('show_locations') && $request->show_locations == 'yes'){
            for ($i = 0; $i < count($request->location_name); $i++) {
                $l[] = array_column($request->only(['location_name','location_address']), $i);
            }
            
            foreach ($l as $test) {
                $locations[$test[0]] = $test[1];
            }
            $locations = json_encode($locations);
          
        }
        if($request->has('show_modes') && $request->show_modes == 'yes'){
            for ($i = 0; $i < count($request->mode_name); $i++) {
                $m[] = array_column($request->only(['mode_name','mode_amount']), $i);
            }
            
            foreach ($m as $test) {
                $modes[$test[0]] = $test[1];
            }
            $modes = json_encode($modes);
            
        }
     
        $program = Program::Create([
            'p_name' => $data['p_name'],
            'p_abbr' => $data['p_abbr'],
            'p_amount' => $data['p_amount'],
            'e_amount' => $data['e_amount'],
            'p_start' => $data['p_start'],
            'p_end' => $data['p_end'],
            'hasmock' => $data['hasmock'],
            'haspartpayment' => $data['haspartpayment'],
            'status' => $data['status'],
            'off_season' => $data['off_season'],
            'booking_form' => $filePath ?? null,
            'show_locations' => $data['show_locations'],
            'show_modes' => $data['show_modes'],
            'modes' => $modes ?? null,
            'locations' => $locations ?? null,
            'show_catalogue_popup'=>$data['show_catalogue_popup'],
            'image' => 'trainingimage/'.$file,
            'allow_payment_restrictions' => $data['allow_payment_restrictions']
        ]);

        
        return redirect('programs')->with('message', 'Program added succesfully');
    }

    public function edit($id)
    {
        $i = 1;
        $program = Program::find($id);
        $modes = [
            'Online',
            'Offline'
        ];
        return view('dashboard.admin.programs.edit', compact('program','modes'));
    }

    public function update(Request $request, Program $program)
    {
        $data = $request->only(['p_name', 'p_abbr', 'p_amount', 'e_amount', 'p_start', 'status', 'p_end', 'hasmock', 'off_season', 'haspartpayment','show_modes','show_locations', 'allow_payment_restrictions','allow_payment_restrictions_for_materials','allow_payment_restrictions_for_pre_class_tests','allow_payment_restrictions_for_post_class_tests' ,'allow_payment_restrictions_for_results','allow_payment_restrictions_for_certificates' ,'allow_payment_restrictions_for_completed_tests']);
        // dd($request->all());
        //check if new featured image
       
        if($request->hasFile('image')){

            // Dont delete old files, another progeam may be using it
            // uploads file to the desired folder in uploads directory
            $file = $this->uploadFileToUploads($request->file('image'),'image','trainings', 533, 533);
            
            $data['image'] = 'trainingimage/' . $file;

        }
       
        if($request->hasFile('booking_form')){
            //Save booking form
            $filePath = $this->uploadFileToUploads($request->file('booking_form'), 'booking_form', 'bookingforms');
            
            $data['booking_form'] = 'bookingforms/' . $filePath;
        }

        if ($request->has('show_locations') && $request->show_locations == 'yes') {
            for ($i = 0; $i < count($request->location_name); $i++) {
                $l[] = array_column($request->only(['location_name', 'location_address']), $i);
            }

            foreach ($l as $test) {
                $locations[$test[0]] = $test[1];
            }
            $data['locations'] = json_encode($locations);

        }
        if ($request->has('show_modes') && $request->show_modes == 'yes') {
            for ($i = 0; $i < count($request->mode_name); $i++) {
                $m[] = array_column($request->only(['mode_name', 'mode_amount']), $i);
            }

            foreach ($m as $test) {
                $modes[$test[0]] = $test[1];
            }
            $data['modes'] = json_encode($modes);

        }
        
        $program->update($data);

        return back()->with('message', 'Training updated successfully');
    }


    public function destroy($id)
    {
        $program = program::withTrashed()->where('id', $id)->firstOrFail();

        if($program->trashed()){
            $program->forceDelete();

            return redirect('programs')->with('message', 'Training has been deleted forever');
        
        } else {

            $program->users()->detach();
            
            $program->delete();

            return redirect('programs')->with('message', 'Training has been trashed');
        }       
    }

    public function trashed()
    {
        $i = 1;
        //Get all programs
        $programs = Program::with('users')->onlyTrashed()->get();
        
        //Get all students
        $users = User::where('role_id', 'Student')->get();

        //Get Users payment status
        foreach($programs as $program){
            $program['part_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '>', 0)->count();
            $program['fully_paid'] = DB::table('program_user')->where('program_id', $program->id)->where('balance', '<=', 0)->count();        
        }

        // dd($programs);
       return view('dashboard.admin.programs.trash', compact('programs', 'i'));
    }

    public function restore($id)
    {
        $program = program::withTrashed()->where('id', $id)->firstOrFail();

        $program->restore();

       return redirect(route('programs.index'))->with('message', 'Program has been restored');
    }

        public function showcrm($id){
            $program = Program::findorfail($id);
            $programName = Program::where('id', $id)->pluck('p_name');
            $program->hascrm = 1;
            $program->save();
    
            return redirect('programs')->with('message', 'CRM has been succesfully enabled for '.$programName);
        }
    
        public function hidecrm($id){
            $program = Program::findorfail($id);
            $programName = Program::where('id', $id)->pluck('p_name');
            $program->hascrm = 0;
            $program->save();
    
            return redirect('programs')->with('message', 'CRM has been succesfully disabled for '.$programName);
        }

        public function closeRegistration($id){
            $program = Program::findorfail($id);
            $programName = Program::where('id', $id)->pluck('p_name');
            $program->close_registration = 1;
            $program->save();

            return redirect('programs')->with('message', 'Registration is now closed for '.$programName);
        }

        public function openRegistration($id){
            $program = Program::findorfail($id);
            $programName = Program::where('id', $id)->pluck('p_name');
            $program->close_registration = 0;
            $program->save();

            return redirect('programs')->with('message', 'Registration is now extended for '.$programName);
        }

        public function openEarlyBird($id){
            $program = Program::findorfail($id);
            $programName = Program::where('id', $id)->pluck('p_name');
            $program->close_earlybird = 1;
            $program->save();

            return redirect('programs')->with('message', 'Early is now extended for '.$programName);
        }

        public function closeEarlyBird($id){
            $program = Program::findorfail($id);
            $programName = Program::where('id', $id)->pluck('p_name');
            $program->close_earlybird = 0;
            $program->save();

            return redirect('programs')->with('message', 'EarlyBird payment is now closed for '.$programName);
        }

        public function cloneTraining(Program $training){
            $scoresettings = $training->scoresettings;
            $materials = $training->materials;
            $modules = $training->modules;
            $questions = $training->questions;
            
            // Create new program
            $new = Program::create([
                "p_name" => 'copy_'.$training->p_name,
                "p_abbr" => $training->p_abbr,
                "description" => $training->description,
                "p_amount" => $training->p_amount,
                "e_amount" => $training->e_amount,
                "close_earlybird" => $training->close_earlybird,
                "p_start" => $training->p_start,
                "p_end" => $training->p_end,
                "image" => $training->image,
                "booking_form" => $training->booking_form,
                "hascrm" => $training->hascrm,
                "hasmock" => $training->hasmock,
                "haspartpayment" => $training->haspartpayment,
                "status" => 0,
                "off_season" => $training->off_season,
                "verification" => $training->verification,
                "hasresult" => $training->hasresult,
                "close_registration" => $training->close_registration
            ]);

            // Create scoresettings
            if(isset($training->scoresettings) && !empty($training->scoresettings)){
                $score = ScoreSetting::create([
                    'program_id' => $new->id,
                    'certification' => $training->scoresettings->certification,
                    'class_test' => $training->scoresettings->class_test,
                    'role_play' => $training->scoresettings->role_play,
                    'email' => $training->scoresettings->email,
                    'passmark' => $training->scoresettings->passmark,
                    'total' => $training->scoresettings->total,
                ]);
            }

            // Material
            if (isset($training->materials) && !empty($training->materials)) {
                foreach($training->materials as $material){
                    Material::create([
                        "program_id" => $new->id,
                        "title" => $material->title,
                        "file" => $material->file,
                    ]);
                }
            }

            // Modules
            if (isset($training->modules) && !empty($training->modules)) {
                foreach ($training->modules as $module) {
                $new_module =  Module::create([
                        "program_id" => $new->id,
                        "title" => $module->title,
                        "time" => $module->time,
                        "noofquestions" => $module->noofquestions,
                        "status" => 0,
                        "type" => $module->type == 'Class Test' ? 0 : 1,
                    ]);

                    //Get Module questions 
                    $module_questions = Question::whereModuleId($module->id)->get();

                    //Duplicate module questions for newly created module       
                    foreach ($module_questions as $question) {
                        Question::create([
                            'title' => $question->title,
                            'optionA' => $question->optionA,
                            'optionB' => $question->optionB,
                            'optionC' => $question->optionC,
                            'optionD' => $question->optionD,
                            'correct' => $question->correct,
                            'module_id' => $new_module->id,
                        ]);
                    }

                }
            }

            return back()->with('message', 'Training cloned successfully');
        }
}
