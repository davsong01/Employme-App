<?php

namespace App\Http\Controllers\Admin;

use App\Program;
use App\Material;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\facades\DB;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Program $program)
    {
        $i = 1;
        if(Auth::user()->role_id == "Admin"){
            $programs = Program::with('users')->where('id', '<>', 1)->orderBy('created_at', 'desc')->get();

            foreach($programs as $program){
                $program['fully_paid'] = 0;
                $program['part_paid'] = 0;

                foreach($program->users as $users){
                    if($users->balance <= 0){
                        $program['fully_paid'] = $program['fully_paid'] + 1;
                    }

                    if($users->balance > 0){
                        $program['part_paid'] = $program['part_paid'] + 1;
                    }
                    // print_r();
                }
            }

            $users = User::where('role_id', '<>', 'Admin')->get();
             return view('dashboard.admin.programs.index', compact('programs', 'i'));
        }
            else  return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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


    public function store()
    {  
        $program = Program::create($this->validateRequest());
        $this->storeImage($program);
        
        return redirect('programs')->with('message', 'Program added succesfully');
    }


    public function edit($id)
    {
        $i = 1;
        $program = Program::find($id);
        
        return view('dashboard.admin.programs.edit', compact('program'));
    }

    public function update(Program $program)
    {
        $program->update($this->validateRequest());
        $this->storeImage($program);
        //I used return redirect so as to avoid creating new instances of the user and program class
        return redirect('programs')->with('message', 'training updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
        $programs = Program::with('users')->onlyTrashed()->get();

        foreach($programs as $program){
            $program['fully_paid'] = 0;
            $program['part_paid'] = 0;

            foreach($program->users as $users){
                if($users->balance <= 0){
                    $program['fully_paid'] = $program['fully_paid'] + 1;
                }

                if($users->balance > 0){
                    $program['part_paid'] = $program['part_paid'] + 1;
                }
                // print_r();
            }
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

        private function validateRequest(){
            return tap(request()->validate([
            'p_name' => 'required',
            'p_abbr' => 'required',
            'p_amount' => 'required',
            'e_amount' => 'required',
            'p_start' => 'required',
            'p_end' => 'required',
            ]), function (){
                if (request()->hasFile('booking_form')){
                    request()->validate([
                        'booking_form' =>'file|mimes:pdf|max:10000',
                    ]);
                }
            });
    }           
        private function storeImage($program){
            if(request()->has('booking_form')){ 
                $program->update([
                    'booking_form' => request()->booking_form->storeAs('bookingforms', request()->booking_form->getClientOriginalName(), 'uploads'),
                ]); 
        }
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
}
