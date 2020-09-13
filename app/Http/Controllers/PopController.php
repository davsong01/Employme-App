<?php

namespace App\Http\Controllers;

use App\Pop;
use App\Program;
use App\Mail\POPemail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;

class PopController extends Controller
{
    public function index(){

    }

    public function create(){
        $trainings = Program::select('id', 'p_end', 'p_name', 'close_registration')->where('id', '<>', 1)->where('close_registration', 0)->where('p_end', '>', date('Y-m-d'))->ORDERBY('created_at', 'DESC')->get();

        return view('pop')->with('trainings', $trainings);
    }

    public function store(Request $request){
        
        $data = $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required | numeric',
            'bank' => 'required',
            'amount' => 'required | numeric',
            'training' => 'required | numeric',
            'location' => 'nullable',
            'file' => 'required | max:2048 | mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        
        //handle file
        $file = $data['name'].'-'.date('D-s');
        $extension = $request->file('file')->getClientOriginalExtension();
        $filePath = $request->file('file')->storeAs('pop', $file.'.'.$extension  ,'uploads');

        //Store new pop
        $pop = Pop::create([
            'name' => $data['name'],
            'email' =>  $data['email'],
            'phone' =>  $data['phone'],
            'bank' =>  $data['bank'],
            'amount' =>  $data['amount'],
            'program_id' =>  $data['training'],
            'location' =>  $data['location'],
            'file' => $filePath,
        ]);
        
        //Prepare Attachment
        $data['pop'] = base_path() . '/uploads'.'/'. $filePath;
        $data['training'] = Program::where('id', $data['training'])->value('p_name');

        //Send mail to admin
        Mail::to(config('custom.official_email'))->send(new POPemail($data));
        return back()->with('message', 'Your proof of payment has been received,  we will confirm  and issue you an E-receipt ASAP, Thank you');
    }

    public function approve(){

    }
}
