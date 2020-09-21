<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Program;
use App\Material;
use Illuminate\Http\Request;
use Illuminate\Support\facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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
        ]);

        //Save booking form
        $file = $request->file('booking_form')->getClientOriginalName();
        $filePath = $request->file('booking_form')->storeAs('bookingforms', $file,'uploads');

        //Resize and save program banner
        $file = $request->file('image')->getClientOriginalName();
            
        $image = Image::make($request->image)->resize(533, 533);
 
        Storage::disk('uploads')->put('trainings/'.$file, (string) $image->encode());

        $data['image'] = $file;

        Program::Create([
            'p_name' => $data['p_name'],
            'p_abbr' => $data['p_abbr'],
            'p_amount' => $data['p_amount'],
            'e_amount' => $data['e_amount'],
            'p_start' => $data['p_start'],
            'p_end' => $data['p_end'],
            'hasmock' => $data['hasmock'],
            'booking_form' => $filePath,
            'image' => 'trainingimage/'.$file,
        ]); 
        
        return redirect('programs')->with('message', 'Program added succesfully');
    }

    public function edit($id)
    {
        $i = 1;
        $program = Program::find($id);
        
        return view('dashboard.admin.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program)
    {

        $data = $request->only(['p_name', 'p_abbr', 'p_amount', 'e_amount', 'p_start', 'p_end', 'hasmock']);

        //check if new featured image
        if($request->hasFile('image')){
           
            //Resize and save program banner
            $file = $request->file('image')->getClientOriginalName();
                
            $image = Image::make($request->image)->resize(533, 533);
    
            Storage::disk('uploads')->put('trainings/'.$file, (string) $image->encode());

            //delete old one
            if($program->image != 'trainingimage/default.jpg'){
                unlink( base_path() . '/uploads/trainings/'. substr($program->image, 14));
            }
           
            $data['image'] = 'trainingimage/'.$file;

        }

        if($request->hasFile('booking_form')){
            //Save booking form
            $file = $request->file('booking_form')->getClientOriginalName();
            $filePath = $request->file('booking_form')->storeAs('bookingforms', $file,'uploads');

            //delete old one
            unlink( base_path() . '/uploads/'.$program->booking_form);

            //update attribute
            $data['booking_form'] = $filePath;
        }

        $program->update($data);

        return redirect('programs')->with('message', 'training updated successfully');
    }


    public function destroy($id)
    {
        $program = program::withTrashed()->where('id', $id)->firstOrFail();

        if($program->trashed()){
            $program->forceDelete();

            return redirect('programs')->with('message', 'Training has been deleted forever');
        
        } else {
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
}
