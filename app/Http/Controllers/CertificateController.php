<?php

namespace App\Http\Controllers;

use DB;
use App\User;
use App\Result;
use App\Program;
use App\Certificate;
use App\Transaction;
use App\ScoreSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $userid = Auth::user()->id;
        $i = 1;
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            // $programs = Program::whereHas('certificates', function ($query) {
            //     return $query;
            // })->withCount('certificates')->orderby('created_at', 'DESC')->get();
            $programs = Program::withCount('certificates')->where('id', '<>', 1)->whereNULL('parent_id')->orderBy('created_at', 'desc')->get();
            return view('dashboard.admin.certificates.selecttraining', compact('programs', 'i'));
        }

        if (!empty(array_intersect(studentRoles(), Auth::user()->role()))) {
            $details = certificationStatus($request->p_id, auth()->user()->id);
            $program = $details['program'] ?? collect([]);

            // Checks
            if ($program->allow_payment_restrictions_for_certificates == 'yes') {
                $user_balance = Transaction::where('program_id',  $request->p_id)->where('user_id', auth()->user()->id)->first();
                if ($user_balance->balance > 0) {
                    return back()->with('error', 'Please Pay your balance of ' . $user_balance->currency_symbol . number_format($user_balance->balance) . ' in order to get view/download certificate');
                }
            }

            if ($program->only_certified_should_see_certificate == 'yes') {
                $details = $details['status'] ?? collect([]);
                
                if (!$details || $details == 'NOT CERTIFIED') {
                    return back()->with('error', 'You must be certified before you can view certificate');
                }
            }
            
            $certificate = Certificate::with(['user'])->where('user_id', Auth::user()->id)->whereProgramId($request->p_id)->first();

            if (!isset($certificate)) {
                return back()->with('error', 'Certificate for selected program is not ready at this time, please try again or consult admin');
            }

            if ($certificate->show_certificate() == 'Disabled') {
                return back()->with('error', 'Certificate Unavailable at the moment, please check back');
            }

            return view('dashboard.student.certificates.index', compact('certificate', 'program'));
        }
        return back();
    }

    public function adminCertificates($program_id)
    {
    }

    public function create()
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            $programs = Program::withCount('users')->where('id', '<>', 1)->orderBy('created_at', 'DESC')->get();

            return view('dashboard.admin.certificates.create', compact('programs'));
        }
    }

    public function certificateStatus($user_id, $program_id, $status, $certificate_id)
    {
        $transaction = Transaction::where(['user_id' => $user_id, 'program_id' => $program_id])->first();
        $transaction->update(['show_certificate' => $status]);

        return back()->with('message', 'Status updated successfully');
    }
    public function selectUser(Request $request, $program_id)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {

            $i = 1;
            $users = DB::table('program_user')->where('program_id', $request->program_id)->get();
            $certificates = Certificate::with(['user', 'program'])->where('program_id', $request->program_id)->orderBy('created_at', 'desc')->get();

            foreach ($users as $user) {
                $user->name = User::whereId($user->user_id)->value('name');
                $user->certificates_count = Certificate::whereUserId($user->user_id)->whereProgramId($request->program_id)->count();
            }

            $program = Program::find($program_id);
            $score_settings = ScoreSetting::whereProgramId($request->program_id)->first();

            $p_id = $program->id;
            $p_name = $program->p_name;
            $certificate_settings = $program->auto_certificate_settings;

            return view('dashboard.admin.certificates.createcert', compact('users', 'p_id', 'p_name', 'certificates', 'i', 'score_settings','certificate_settings'));
        }
        return back();
    }
    
    public function save(Request $request)
    {
        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
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

            Transaction::where(['user_id' => $request->user_id, 'program_id' => $request->p_id])->update(['show_certificate' => 0]);

            return back()->with('message', ' certificate succesfully added');
            // return redirect(route('certificates.create'))->with('message', ' certificate succesfully added'); 
        }

        return abort(404);
    }

    public function show(certificate $certificate)
    {
        return view('dashboard.admin.certificates.edit')->with('certificate', $certificate)->with('programs', Program::orderBy('created_at', 'desc')->get());
    }

    public function destroy(certificate $certificate)
    {

        $certificate_count = certificate::where('file', $certificate->file)->count();

        if ($certificate_count <= 1) {
            unlink(base_path() . '/uploads/certificates' . '/' . $certificate->file);
        }

        $certificate->delete();

        //delete certificate from storage           
        return redirect('certificates')->with('message', 'certificate succesfully deleted');
    }

    public function getfile($filename)
    {
        $realpath = base_path() . '/uploads/certificates' . '/' . $filename;
        return response()->download($realpath);
    }

    public function modify(Request $request){
        $action = $request->action == 'disable' ? 0 : 1;
        $transactions = Transaction::with('user')->whereIn('user_id', $request->data)->where('program_id', $request->program_id);
    
        if(in_array($request->action, ['enable','disable'])){
            $transactions->update(['show_certificate' => $action]);
        }

        if($request->action == 'regenerate-certificate'){
            if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
                foreach ($transactions->get() as $transaction) {
                    $location = base_path('uploads/certificates');

                    $name = generateCertificate($request, $request->program_id, $location, $transaction->user);
                    ;

                    $certificate = Certificate::updateOrCreate(['user_id' =>  $transaction->user_id, 'program_id' => $request->program_id],[
                        'user_id' => $transaction->user_id,
                        'file' => $name,
                        'program_id' => $request->program_id,
                    ]);
                    
                    $transaction->show_certificate = 0;
                    $transaction->save();
                }

                return back()->with('Certificate successfully autugenerated');
            }
        }
        
        return response()->json(['message' => 'success'], 200);
        
    }

    public function generateCertificates(Request $request, $program_id){

        set_time_limit(0);

        if (!empty(array_intersect(adminRoles(), Auth::user()->role())) || !empty(array_intersect(graderRoles(), Auth::user()->role()))) {
            $transactions = Transaction::with('user')->where('program_id', $program_id)->where('show_certificate', 0)->get();

            if ($transactions->isEmpty()) {
                return back()->with('error', 'No Participant found for selected progtam!');
            }

            foreach ($transactions as $transaction) {
                $location = base_path('uploads/certificates');
                $name = generateCertificate($request, $program_id, $location, $transaction->user);
                
                Certificate::create([
                    'user_id' =>  $transaction->user_id,
                    'file' => $name,
                    'program_id' => $program_id,
                ]);

                $transaction->show_certificate = 0;
                $transaction->save();
            }

            return back()->with('Certificate successfully autugenerated');
        }
    }

    public function generateCertificatePreview(Request $request, $program_id)
    {
        $location = 'certificate_previews';
        $name = generateCertificate($request->all(), $program_id, $location);

        return response()->json([
            'preview_image_path' => '/certificate_previews/' . $name,
        ]);
        
    }


    public function clearAllPreviews(){

    }
}
