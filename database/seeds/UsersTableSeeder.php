<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin = User::create([
            'name'=>'Admin',
            'email'=>'davsong01@gmail.com',
            'password' => bcrypt('admin')
        ]);

        $aauthor = User::create([
            'name'=>'Author',
            'email'=>'davsong02@gmail.com',
            'password' => bcrypt('Author')
        ]);

        $user = User::create([
            'name'=>'User',
            'email'=>'davsong16@gmail.com',
            'password' => bcrypt('User')
        ]);

        //attach the roles to the user created
        // $admin->roles->attach($adminRole);
        // $author->roles->attach($authorRole);
        // $user->roles->attach($auserRole);
        
    }
}
