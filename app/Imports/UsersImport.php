<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Program;
use App\Models\Transaction;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
        set_time_limit(3600);
        $program = Program::find($this->p_id);

        foreach ($rows as $row) {
            if (empty($row['email'])) {
                // Generate fake email if missing
                $faker = Faker::create();
                $programId = $program->id;
                $row['email'] = $faker->unique()->userName . "{$programId}@" . $faker->safeEmailDomain;
            } else {
                // Check if email already exists
                $user = User::select('id', 'email')->where('email', $row['email'])->first();

                if ($user) {
                    $check = Transaction::where('user_id', $user->id)
                        ->where('program_id', $program->id)
                        ->first();

                    if ($check) {
                        continue; // Skip if transaction exists for this user and program
                    }
                }

                // Normalize email
                $row['email'] = strtolower($row['email']);
            }

            // Staff ID check
            $staffID = $row['staffID'] ?? ($row['staffid'] ?? null);
            if (!empty($staffID)) {
                $user = User::select('id', 'staffID')->where('staffID', $staffID)->first();

                if ($user) {
                    $check = Transaction::where('user_id', $user->id)
                        ->where('program_id', $program->id)
                        ->first();

                    if ($check) {
                        continue; // Skip if transaction exists for this user and program
                    }
                }
            }

            // Validate the row data
            Validator::make($row->toArray(), [
                'email' => 'required|unique:users,email',
                'name' => 'required',
                'phone' => 'nullable',
                'gender' => 'nullable',
                'location' => 'nullable',
            ], [
                'email.required' => 'One or more users do not have an email, please check and try again.',
                'email.unique' => 'One or more emails already exist in the database, please check and try again.',
                'name.required' => 'One or more rows require a name, please check and try again.',
            ])->validate();

            // Normalize data before processing
            $row['staffID'] = $row['staffID'] ?? ($row['staffid'] ?? null);
            $row['phone'] = $row['phone'] ?? null;
            $row->forget(['staffid']);

            // Exclude unnecessary metadata
            $row['metadata'] = Arr::except($row->toArray(), ['name', 'staffID', 'phone', 'gender', 'location', 'email', 'staffid', 't_phone']);

            // Prepare training details and attach program
            $data = app('App\Http\Controllers\Controller')->prepareFreeTrainingDetails($program, $row, true);
            $data['payment_type'] = 'Full';
            $data['message'] = 'Full payment';
            $data['paymentStatus'] = 1;
            $data['currency_symbol'] = '&#x20A6;';
            $data['balance'] = 0;
            $data['new'] = 'yes';

            // Create user, attach program, and update earnings
            app('App\Http\Controllers\Controller')->createUserAndAttachProgramAndUpdateEarnings($data, [], null);
        }
    }

    public function headings(): array
    {
        return [
            'email',
        ];
    }

}
