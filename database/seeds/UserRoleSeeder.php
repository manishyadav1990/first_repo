<?php

use Illuminate\Database\Seeder;
use App\Models\Users\UserRole;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        //id, role_name, created_at, updated_at

        DB::table('userrole')->truncate();
        $users_role = array(
            [

                 'role_name' =>'Administrator',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [

                'role_name' =>'Accountant',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        );
        DB::table('userrole')->insert($users_role);
    }



}
