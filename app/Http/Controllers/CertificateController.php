<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\Certificate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
      $userid = Auth::user()->id;
        $i = 1;
        if(Auth::user()->role_id == "Admin"){

            $certificates = Certificate::with('user')->orderBy('created_at', 'desc')->get();

            $programs = Program::where('id', '<>', 1)->get();
            
            return view('dashboard.admin.certificates.index', compact('programs', 'i', 'certificates'));

        }
        
        if(Auth::user()->role_id == "Student"){       

                $certificates = Certificate::with('user')->where('user_id', Auth::user()->id)->first();

                return view('dashboard.student.certificates.index', compact('certificates'));

        }return back();
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            
            $programs = Program::select('id', 'p_name')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();

            return view('dashboard.admin.certificates.create', compact('programs'));
        }
    }

    public function selectUser(Request $request)
    {
        if(Auth::user()->role_id == "Admin"){
      
            $users = User::where('program_id', $request->program_id)->where('role_id', 'Student')->get();
            
                return view('dashboard.admin.certificates.createcert', compact('users'));
    
                }return back();
    }

    public function save(Request $request)
    {
        if(Auth::user()->role_id == "Admin"){
      
            $data = $this->validate($request, [
                'user_id' => 'required',
                'certificate' => 'required | max:3048 | mimes:pdf,doc,docx,jpg,jpeg,png',
            ]);
            
            $file = $data['certificate'];

            $imagePath = $file->storeAs('certificates', $file->getClientOriginalName(), 'uploads');  
        
            certificate::create([
                'user_id' =>  $request->user_id,
                'file' => $file->getClientOriginalName(),
            ]);
            return redirect(route('certificates.create'))->with('message', ' certificate succesfully added'); 
        } return abort(404);

        
    }

    public function show(certificate $certificate)
    {
        return view('dashboard.admin.certificates.edit')->with('certificate', $certificate)->with('programs', Program::orderBy('created_at', 'desc')->get());
    }

    public function destroy(certificate $certificate)
    {
        
        $certificate_count = certificate::where('file', $certificate->file)->count();

        if($certificate_count <= 1){
            unlink( base_path() . '/uploads/certificates'.'/'. $certificate->file);
        }
       
        $certificate->delete();

        //delete certificate from storage           
        return redirect('certificates')->with('message','certificate succesfully deleted');


    }

    public function getfile($filename){
        $realpath = base_path() . '/uploads/certificates'. '/' .$filename;
        return response()->download($realpath);
    }
    
}