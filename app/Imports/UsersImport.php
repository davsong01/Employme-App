<?php

namespace App\Imports;

// use App\User;
use App\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
// use Illuminate\Support\Facades\DB;
// use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class UsersImport implements ToCollection, WithHeadingRow
{
    use Importable;

    protected $p_id;

    function __construct($p_id) {
        $this->p_id = $p_id;
    }


    public function collection(Collection $rows)
    {
        $program = Program::where('id', $this->p_id)->first();

        foreach ($rows as $row) {
            
            Validator::make($row->toArray(),
                [
                    'email'=> 'required',
                    // 'email'=> 'required|unique:users,email',
                    'name' => 'required',
                    'phone' => 'nullable',
                    'gender' => 'nullable',
                    'location' => 'nullable',
                ],
                [
                    'email.required' => 'One or more users do not have an email, please check and try again',
                    'email.unique' => 'One or more email already exists in database, please check and try again',
                    'name.required' => 'One or more rows require name, Please check and try again',
                    // 'phone.required' => 'One or more rows require phone number, Please check and try again',
                    // 'gender.required' => 'One or more rows require gender, Please check and try again',
                ]
                )->validate();
                $row['phone'] = $row['phone'] ?? null;
                $row['email'] = strtolower($row['email']);

                $data = app('App\Http\Controllers\Controller')->prepareFreeTrainingDetails($program, $row, true);
                    
                $data['payment_type'] = 'Full';
                $data['message'] = 'Full payment';
                $data['paymentStatus'] = 1;
                $data['currency_symbol'] = '&#x20A6;';
                $data['balance'] = 0;
                
                // $c = $c ?? NULL; // Coupon
                $data['new'] = 'yes';
                $data = app('App\Http\Controllers\Controller')->createUserAndAttachProgramAndUpdateEarnings($data, [], null, );
            
        }
    }

    public function headings(): array
    {
        return [
            'email',
        ];
    }

}
