<?php

use Illuminate\Database\Seeder;
use Illuminate\Contracts\Database;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userData=array(

            [
                'id'=> 1,
                'name' => 'Demo User',
                'email'   => 'deepika@nitaipartners.com',
                'password'   => bcrypt('deepika@nitaipartners.com'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'=> 2,
                'name' => 'accounts@nymdx.com',
                'email'   => 'accounts@nymdx.com',
                'password'   => bcrypt('4273J@gannath'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
            'id'=> 3,
            'name' => 'Niket',
            'email'   => 'niket@nitaipartners.com',
            'password'   => bcrypt('4273N1t@iP@rtner$'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
                'id'=> 4,
                'name' => 'Aditya',
                'email'   => 'aditya@nitaipartners.com',
                'password'   => bcrypt('4273N1t@iP@rtner$'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
            'id'=> 5,
            'name' => 'admin@nitaipartners.com',
            'email'   => 'admin@nitaipartners.com',
            'password'   => bcrypt('4273N1t@iP@rtner$'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]
        );
        DB::table('users')->insert($userData);
     }
}
