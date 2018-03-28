<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $tables =[
        'users',
        'password_resets',
        'oauth_scopes',
        'oauth_grants',
        'oauth_grant_scopes',
        'oauth_clients',
        'oauth_client_endpoints',
        'oauth_client_scopes',
        'oauth_client_grants',
        'oauth_sessions',
        'oauth_session_scopes',
        'oauth_auth_codes',
        'oauth_auth_code_scopes',
        'oauth_access_tokens',
        'oauth_access_token_scopes',
        'oauth_refresh_tokens',
        'administrator',
        'system_users_role',
        'userrole',
        'countries',
        'languages',
        'time_zones'
    ];
    public function run()
    {
        Model::unguard();
        if (env('APP_ENV') != 'production') {
            // only run if environment is not production
            switch (env('APP_ENV')) {
                case 'testing':
                    DB::statement('PRAGMA foreign_keys = OFF;');
                    break;
                default:
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                    break;
            }
            // truncate all tables in tables list
            foreach ($this->tables as $table) {
                DB::table($table)->truncate();
            }
        }

        $this->call(Oauth2SecuritySeeder::class);
        $this->call('Oauth2Seeder');
        $this->call('AdministratorSeeder');
        $this->call('SystemUserSeeder');
        $this->call('UserSeeder');
        $this->call('UserRoleSeeder');
        $this->call('CountriesTableSeeder');
        $this->call('TimeZonesTableSeeder');
        $this->call('LanguagesTableSeeder');
        $this->call('CountryDatabaseTableSeeder');
        // $this->call(UserTableSeeder::class);

        Model::reguard();
    }
}
