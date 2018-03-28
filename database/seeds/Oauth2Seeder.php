<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class Oauth2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->delete();

        DB::table('oauth_clients')->insert([
            'id'  => env('OAUTH2_CLIENT_ID'),
            'name' => 'api-server',
            'secret'   => env('OAUTH2_CLIENT_SECRET'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
