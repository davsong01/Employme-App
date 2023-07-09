<?php

namespace App\Http\Controllers;

use App\User;
use App\Program;
use App\Certificate;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $userid = Auth::user()->id;
        $i = 1;
        if(Auth::user()->role_id == "Admin"){

            $certificates = Certificate::with(['user', 'program'])->orderBy('created_at', 'desc')->get();
            
            return view('dashboard.admin.certificates.index', compact('i', 'certificates'));

        }
        
        if(Auth::user()->role_id == "Student"){    
            
            $program = Program::find($request->p_id);

            if ($program->allow_payment_restrictions_for_certificates == 'yes') {
                $user_balance = DB::table('program_user')->where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
                if($user_balance->balance > 0){
                    return back()->with('error', 'Please Pay your balance of '. $user_balance->currency_symbol . number_format($user_balance->balance). ' in order to get view/download certificate');
                }
            }

            $certificate = Certificate::with(['user'])->where('user_id', Auth::user()->id)->whereProgramId($request->p_id)->first();
            
            if(!isset($certificate)){
                return back()->with('error', 'Certificate for selected program is not ready at this time, please try again or consult admin');
            }

            return view('dashboard.student.certificates.index', compact('certificate', 'program'));

        }return back();
    }

    public function create()
    {
        if(Auth::user()->role_id == "Admin"){
            
            $programs = Program::withCount('users')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();

            return view('dashboard.admin.certificates.create', compact('programs'));
        }
    }

    public function selectUser(Request $request)
    {
        if(Auth::user()->role_id == "Admin"){
            
            $users = DB::table('program_user')->where('program_id', $request->program_id)->get();
            foreach($users as $user){
                $user->name = User::whereId($user->user_id)->value('name');
                $user->certificates_count = Certificate::whereUserId($user->user_id)->whereProgramId($request->program_id)->count();
            }

            $p_id = $request->program_id;

            return view('dashboard.admin.certificates.createcert', compact('users', 'p_id'));

            }return back();
    }

    public function save(Request $request)
    {
        if(Auth::user()->role_id == "Admin"){

            $data = $this->validate($request, [
                'user_id' => 'required',
                'certificate' => 'required | max:3048 | mimes:pdf,doc,docx,jpg,jpeg,png',
                'p_id' => 'required'
            ]);
            
            $file = $data['certificate'];

            $imagePath = $file->storeAs('certificates', $file->getClientOriginalName(), 'uploads');  
        
            certificate::create([
                'user_id' =>  $request->user_id,
                'file' => $file->getClientOriginalName(),
                'program_id' => $request->p_id,
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
