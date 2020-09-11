<?php

namespace App\Http\Controllers\Admin;
use App\Role;
use App\User;
use App\Material;
use App\Program;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
      $userid = Auth::user()->id;
        $i = 1;
        if(Auth::user()->role_id == "Admin"){
            $materials = Material::orderBy('created_at', 'desc')->get();
            // $materials= $image= str_replace(' ', '%20', $materials);
            $programs = Program::where('id', '<>', 1)->get();
            return view('dashboard.admin.materials.index', compact('programs', 'i', 'materials'));
        }elseif(Auth::user()->role_id == "Facilitator" || Auth::user()->role_id == "Grader"){
                //show only trainings attached to this user
                $materials = Material::where('program_id', '=', Auth::user()->program_id)->orderBy('created_at', 'DESC')->get();
                   return view('dashboard.admin.materials.index', compact('materials','i') );

                } elseif(Auth::user()->role_id == "Student"){       
                $i = 1;   
                $program = Program::find($request->p_id);         
                $materials = Material::where('program_id', $program->id)->orderBy('created_at', 'DESC')->get();
   
                return view('dashboard.student.materials.index', compact('i', 'materials', 'program'));
        }
            else  return redirect('/');
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
        $programs = Program::where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();
        $materials = Material::all();
            return view('dashboard.admin.materials.create', compact('programs'));
            }
        elseif(Auth::user()->role_id == "Facilitator"){
            $programs = Program::where('id', '=', Auth::user()->program_id)->orderBy('created_at', 'DESC')->get();
            $materials = Material::where('program_id', '=', Auth::user()->program_id)->orderBy('created_at', 'DESC')->get();
            return view('dashboard.admin.materials.create', compact('programs'));
        }
           else return redirect('/materials');
        }

    public function store(Request $request)
    {
        $data = request()->validate([
            'program_id' => 'required',
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
     
        return redirect('materials')->with('message', 'Study material succesfully added');
    }

    public function show(Material $material)
    {
        return view('dashboard.admin.materials.edit')->with('material', $material)->with('programs', Program::orderBy('created_at', 'desc')->get());
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
    
}