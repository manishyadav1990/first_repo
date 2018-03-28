<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('country_database')) {
            Schema::create('country_database', function (Blueprint $table) {
                $table->increments('id');
                $table->string('databasefor');
                $table->string('api_url');
                $table->string('ui_url');
                $table->string('database_details');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('country_database');
    }
}
