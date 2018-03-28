<?php

use Illuminate\Database\Seeder;

class CountryDatabaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $details = array(
            [
                'databasefor'=>'Mexico',
                'api_url'=>'http://mx-nymdx-api.nymdx.com/',
                'ui_url'=>'http://mx.nymdx.com/',
                'database_details'=>'localhost|root|root|nimdb_mx',
            ],
            [
                'databasefor'=>'US',
                'api_url'=>'http://us-nymdx-api.nymdx.com/',
                'ui_url'=>'http://us.nymdx.com/',
                'database_details'=>'localhost|root|root|nimdb_us',

            ],
            [
                'databasefor'=>'India',
                'api_url'=>'http://nymdx-api.npi-tech.com/ ',
                'ui_url'=>'http://nymdx.in/',
                'database_details'=>'localhost|root|root|nimdb',

            ],
            [
                'databasefor'=>'Development',
                'api_url'=>'http://nymdx-api.npi-tech.com/',
                'ui_url'=>'http://nymdx.npi-tech.com/',
                'database_details'=>'localhost|root|root|nimdb_dev',

            ],
            [
                'databasefor'=>'local',
                'api_url'=>' http://localhost:8000/',
                'ui_url'=>' http://localhost:8000/',
                'database_details'=>'localhost|root|root|nimdb',

            ],
        );
        DB::table('country_database')->insert($details);
    }
}
