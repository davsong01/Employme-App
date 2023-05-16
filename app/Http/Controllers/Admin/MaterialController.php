<?php

namespace App\Http\Controllers\Admin;
use App\Role;
use App\User;
use App\Mocks;
use App\Module;
use App\Program;
use App\Material;
use App\FacilitatorTraining;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $userid = Auth::user()->id;
        $i = 1;
        
        if(Auth::user()->role_id == "Admin"){
            
            $materials = Material::with('program')->orderBy('created_at', 'desc')->get();

            return view('dashboard.admin.materials.index', compact('i', 'materials'));
        }

        if(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
            
            $facilitator_programs = FacilitatorTraining::whereUser_id(auth()->user()->id)->get();

            $materialCount = 0;
            
            if($facilitator_programs->count() > 0){
                foreach($facilitator_programs as $trainings){
                    $trainings['p_name'] = Program::whereId($trainings->program_id)->value('p_name');
                    $trainings['materialCount'] = Material::whereProgramId($trainings->program_id)->count();
                } 
            }

            $programs = Program::where('id', '<>', 1)->get();
            
            return view('dashboard.teacher.materials.show', compact( 'i', 'facilitator_programs'));
        }

        if(Auth::user()->role_id == "Student"){       
            $i = 1;   

            $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
            if($user_balance->balance > 0){
                return back()->with('error', 'Please Pay your balance of '. $user_balance->currency_symbol . number_format($user_balance->balance). ' in order to get access to materials');
            }

            $program = Program::find($request->p_id);  
            
                if($program->hasmock == 1){
                    
                    //Check if user has taken pre tests and return back if otherwise
                    $expected_pre_class_tests = Module::ClassTests($program->id)->count();
                    
                    $completed_pre_class_tests = Mocks::where('program_id', $program->id)->where('user_id', auth()->user()->id)->count();
 
                    if($completed_pre_class_tests < $expected_pre_class_tests ){
                        return Redirect::to('mocks?p_id='.$program->id)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Training materials');
                    }
                                        
                }
                
                $materials = Material::where('program_id', $program->id)->orderBy('created_at', 'DESC')->get();
                $show_catalogue = $this->showCatalogue($program);
                
                return view('dashboard.student.materials.index', compact('i', 'materials', 'program','show_catalogue'));
        }
        
    }
    
        
    public function all($p_id){
        if(Auth::user()->role_id == "Grader" || Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Admin"){
            $i = 1;

            $materials = Material::with('program')->where('program_id', $p_id)->orderBy('created_at', 'DESC')->get();

            return view('dashboard.teacher.materials.index', compact( 'i', 'materials', 'p_id'));
        }return abort(404);
    }

    public function create()
    {

    if(Auth::user()->role_id == "Admin"){
        $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
        // $materials = Material::with('program')->all();
    
        return view('dashboard.admin.materials.create', compact('programs'));
    }
        return back();
    }

    public function add($p_id){
        if(Auth::user()->role_id == "Facilitator"){

            $program = Program::select('id', 'p_name')->whereId($p_id)->first();

            return view('dashboard.teacher.materials.create', compact('p_id', 'program'));
        
        }

    }
    
    public function store(Request $request)
    {
        if($request->has('p_id')){
            //$imagePath = request('booking_form')->store('/uploads', 'public');
            foreach($request->file('file') as $file){
          
                $imagePath = $file->storeAs('materials', $file->getClientOriginalName(), 'uploads');  
            
                Material::create([
                    'title' =>$file->getClientOriginalName(),
                    'program_id' =>  $request->p_id,
                    'file' => $file->getClientOriginalName(),
                ]);
            }
            
            return redirect(url('/facilitatormaterials/'.$request->p_id))->with('message', 'Study material succesfully added');
        }
        else{
            $data = request()->validate([
            'program_id' => 'nullable',
            'file' => 'required',
            'file.*' => 'mimes:doc,pdf,docx',
            ]);

            //get id of selected program
            $program_id = $data['program_id'];
            //$imagePath = request('booking_form')->store('/uploads', 'public');
            foreach($request->file('file') as $file){
            
            $imagePath = $file->storeAs('materials', $file->getClientOriginalName(), 'uploads');  
           
            Material::create([
                'title' =>$file->getClientOriginalName(),
                'program_id' =>  $program_id,
                'file' => $file->getClientOriginalName(),
            ]);
        }
        

    }  
        // return response()->json(['success'=>'Study Material Uploaded Successfully']);
        return redirect('materials')->with('message', 'Study material succesfully added');
    }

    public function show(Material $material)
    {

        $programs = Program::orderBy('created_at', 'desc')->where('id', '<>', $material->program_id)->where('id', '<>', 1)->get();

        return view('dashboard.admin.materials.edit')->with('material', $material)->with('programs', $programs);
    }

    public function edit($id)
    {
        
    }

    public function update(Request $request, $id)
    {
    
    }

    public function destroy(Material $material)
    {
        $material_count = Material::where('file', $material->file)->count();

        if($material_count <= 1){
            unlink( base_path() . '/uploads/materials'.'/'. $material->file);
        }
       
        $material->delete();
        if(auth()->user()->role_id == 'Facilitator'){
            return back()->with('message', 'Material has been deleted forever');
        }
        return redirect('materials')->with('message','Study material succesfully deleted');


    }

    public function clone(Material $material, Request $request)
    {
        //create new material with existing material information except program id
        Material::create([
            'title' =>$request->title,
            'program_id' =>  $request->program_id,
            'file' => $request->file,
        ]);

        return redirect('materials')->with('message','Study material succesfully cloned');
    } 

    public function getfile($filename){
       
        $realpath = base_path() . '/uploads/materials'. '/' .$filename;
        
        return response()->download($realpath);
    }  

    private function checkMock($expected, $completed){
        if($expected < $completed){
            dd($expected, $completed);
            return Redirect::to('mocks?p_id='. 21)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Training materials');
        }
            else {
                return Redirect::to('mocks?p_id='. 21)->with('error', 'Sorry, you have to take all Pre Class Tests for this Training before you can access Training materials');
            }
            // return 1;                
        }
    }
