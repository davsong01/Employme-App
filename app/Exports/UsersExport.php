<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
   
    public function collection()
    {
        // $users = User::with('program')->where('roles', 'Student')->get();
        $users = User::select('updated_at', 'name', 'email', 'phone', 'amount', 'balance', 'bank', 'roles', 'program_id')->with('program')->where('roles', 'Student')->orderBy('program_id', 'DESC')->get();

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
