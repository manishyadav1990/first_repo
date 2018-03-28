<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SystemUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   //id, role_id, login_id, userid, created_at, updated_at
        DB::table('system_users_role')->truncate();
        $system_users_role = array(
            [

                'login_id'=>1,
                'role_id'=>'2',
                'userid' =>'700001',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [

                'login_id'=>2,
                'role_id'=>'1',
                'userid' =>'700002',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [

                'login_id'=>3,
                'role_id'=>'2',
                'userid' =>'700003',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [

                'login_id'=>4,
                'role_id'=>'2',
                'userid' =>'700004',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'login_id'=>5,
                'role_id'=>'2',
                'userid' =>'700005',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        );
        DB::table('system_users_role')->insert($system_users_role);
    }
}
