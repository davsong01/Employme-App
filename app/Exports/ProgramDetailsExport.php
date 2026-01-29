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
        // $participants = DB::table('program_user')->select(['id','created_at', 'program_id','user_id'])->whereProgramId($this->id)->orderBy('created_at', 'DESC')->get();
        
        $participants = Transaction::orderBy('program_user.created_at', 'DESC')
            ->where('program_user.program_id', $this->id)
            ->leftjoin("temp_transactions", "program_user.transid", "=", "temp_transactions.transid")
            ->join("users", "program_user.user_id", "=", "users.id")

            ->select([
                'program_user.created_at AS date',
                'users.staffID',
                'users.name',
                'users.email',
                'users.phone AS phone',
                'temp_transactions.amount as paid',
                'temp_transactions.balance as outstanding',
                'temp_transactions.t_type as paymentmode',
                'temp_transactions.invoice_id AS invoice',
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
            // 'Certificate No',
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
