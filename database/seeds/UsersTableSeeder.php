<?php

use App\Role;
use App\User;
use App\Program;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $default_program = Program::create([
            'p_name' => 'Default Program',
            'p_abbr' => 'DFTLP',
            'p_amount' => '40000',
            'e_amount' => '35000',
            'p_start' => '2019-11-12',
            'p_end' => '2019-11-12',
        ]);

        $admin = User::create([
            'name'=>'Admin',
            'email'=>'davsong01@gmail.com',
            'role_id' => 'Admin',
            'password' => bcrypt('123456'),
            'program_id' => $default_program->id,
        ]);

        $user = User::create([
            'name'=>'User',
            'email'=>'davsong16@gmail.com',
            'role_id' => 'Student',
            'program_id' => $default_program->id,
            'password' => bcrypt('User')
        ]);

        //attach the roles to the user created
        // $admin->roles->attach($adminRole);
        // $author->roles->attach($authorRole);
        // $user->roles->attach($auserRole);
        
    }
}
