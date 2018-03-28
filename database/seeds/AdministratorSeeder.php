<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('administrator')->truncate();
        $administrator = array(
            [
                'id'=>700001,
                'login_id'=>1,
                'first_name'=>'Demo',
                'last_name' =>'User',
                'gender'=>'Male',
                'address'=>'Malad',
                'city'=>'Mumbai',
                'state'=>'Maharashtra',
                'country'=>'India',
                'email'=>'deepika@nitaipartners.com',
                'phone_number'=>'8652426752',
                'zip_code'=>'4000095',
                'dob'=>'05-06-1990',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'id'=>700002,
                'login_id'=>2,
                'first_name'=>'accounts@nymdx.com',
                'last_name' =>'',
                'gender'=>'Male',
                'address'=>'Malad',
                'city'=>'Mumbai',
                'state'=>'Maharashtra',
                'country'=>'India',
                'email'=>'accounts@nymdx.com',
                'phone_number'=>'8652426752',
                'zip_code'=>'4000095',
                'dob'=>'05-06-1990',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
            'id'=>700003,
            'login_id'=>3,
            'first_name'=>'Niket',
            'last_name' =>'Naik',
            'gender'=>'Male',
            'address'=>'Goregaon',
            'city'=>'Mumbai',
            'state'=>'Maharashtra',
            'country'=>'India',
            'email'=>'niket@nitaipartners.com',
            'phone_number'=>'8652426734',
            'zip_code'=>'4000095',
            'dob'=>'05-06-1990',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
            'id'=>700004,
            'login_id'=>4,
            'first_name'=>'Aditya',
            'last_name' =>'Satsangi',
            'gender'=>'Male',
            'address'=>'Goregaon',
            'city'=>'Mumbai',
            'state'=>'Maharashtra',
            'country'=>'India',
            'email'=>'aditya@nitaipartners.com',
            'phone_number'=>'8652426723',
            'zip_code'=>'4000095',
            'dob'=>'05-06-1990',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
          ],
            [
            'id'=>700005,
            'login_id'=>5,
            'first_name'=>'admin@nitaipartners.com',
            'last_name' =>'',
            'gender'=>'Male',
            'address'=>'Goregaon',
            'city'=>'Mumbai',
            'state'=>'Maharashtra',
            'country'=>'India',
            'email'=>'admin@nitaipartners.com',
            'phone_number'=>'8652426723',
            'zip_code'=>'4000095',
            'dob'=>'05-06-1990',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]
        );
        DB::table('administrator')->insert($administrator);
    }
}
