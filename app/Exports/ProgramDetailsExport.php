<?php

namespace App\Exports;

use DB;
use App\User;
use App\Program;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;


class ProgramDetailsExport implements FromCollection, WithHeadings
{
   
    protected $id;

    function __construct($id) {
        $this->id = $id;
    }

    public function collection()
    {
       
        // $users = User::with('program')->where('role_id', 'Student')->get();
        $participants = DB::table('program_user')->whereProgramId($this->id)->orderBy('created_at', 'DESC')->get();

       
        foreach($participants as $participant){
            $participant->date = $participant->created_at;
            $participant->program = Program::whereId($participant->program_id)->value('p_name');
            $participant->name = User::whereId($participant->user_id)->value('name');
            $participant->email = User::whereId($participant->user_id)->value('email');
            $participant->phone = User::whereId($participant->user_id)->value('t_phone');
            $participant->paid = $participant->t_amount;
            $participant->outstanding = $participant->balance;
            $participant->paymentmode = $participant->t_type;
            $participant->invoice = $participant->invoice_id;
            $participant->venue = $participant->t_location;

           
            unset($participant->id, $participant->t_amount, 
                $participant->created_at, 
                $participant->user_id, 
                $participant->program_id, 
                $participant->balance, 
                $participant->t_type, 
                $participant->t_location,
                $participant->transid, 
                $participant->paymenttype, 
                $participant->paymentStatus, 
                $participant->updated_at, 
                $participant->created_at,
                $participant->invoice_id);
        
        }

        return $participants;
    }

    public function headings(): array
    {
        return [
            'Date Created',
            'Training',
            'Name',
            'Email',
            'Phone',
            'Amount Paid',
            'Balance',
            'Payment Mode',
            'Invoice Id',
            'Location'         
        ];
    }
}
