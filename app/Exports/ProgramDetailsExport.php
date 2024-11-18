<?php

namespace App\Exports;

use DB;
use App\Models\User;
use App\Models\Program;
use App\Models\Transaction;
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
        $participants = DB::table('program_user')->select(['id','created_at', 'program_id','user_id'])->whereProgramId($this->id)->orderBy('created_at', 'DESC')->get();
        // $participants = Transaction::orderBy('program_user.created_at', 'DESC')
        // ->whereProgramId($this->id)
        //     ->join("users", "program_user.user_id", "=", "users.id")
        //         ->join("programs", "program_user.program_id", "=", "programs.id")
        //         ->join("certificates", "certificates.program_id", "=", "programs.id" AND certificates.user_id = "users.id")
        //             ->select(['program_user.created_at AS date', 'programs.p_name AS program', 'users.name','users.email','users.t_phone AS phone','program_user.t_amount as paid', 'program_user.balance as outstanding', 'program_user.t_type as paymentmode', 'program_user.invoice_id AS invoice', 'program_user.t_location as venue','certificates.certificate_number'])
        //             ->get();
        $participants = Transaction::orderBy('program_user.created_at', 'DESC')
        ->where('program_user.program_id', $this->id)
        ->join("users", "program_user.user_id", "=", "users.id")
        ->join("programs", "program_user.program_id", "=", "programs.id")
        ->join("certificates", function ($join) {
            $join->on("certificates.program_id", "=", "programs.id")
            ->on("certificates.user_id", "=", "users.id");
        })
        ->select([
            'program_user.created_at AS date',
            // 'programs.p_name AS program',
            'users.staffID',
            'users.name',
            'certificates.certificate_number',
            'users.email',
            'users.t_phone AS phone',
            'program_user.t_amount as paid',
            'program_user.balance as outstanding',
            'program_user.t_type as paymentmode',
            'program_user.invoice_id AS invoice',
            'program_user.t_location as venue'
        ])
        ->get();


        return $participants;
    }

    public function headings(): array
    {
        return [
            'Date Created',
            // 'Training',
            'Staff ID',
            'Name',
            'Certificate No',
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
