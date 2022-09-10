<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
   
    public function collection()
    {
        // $users = User::with('program')->where('role_id', 'Student')->get();
        $users = User::select('updated_at', 'name', 'email', 't_phone', 't_amount', 'balance', 'bank', 'role_id', 'program_id')->with('program')->where('role_id', 'Student')->orderBy('program_id', 'DESC')->get();

        foreach($users as $user){
            $user['program'] =  $user->program->p_name;
        }
        
        return $users;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Name',
            'Email',
            'Phone',
            'Amount Paid',
            'Balance',
            'Bank',
            'Type',
            'Program ID',
            'Program Name',
        ];
    }
}
