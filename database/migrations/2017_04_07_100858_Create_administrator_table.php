<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministratorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('administrator')) {
            Schema::create('administrator', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('login_id')->unsigned()->default(0);
                $table->foreign('login_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('gender');
                $table->string('address');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->string('email');
                $table->string('phone_number');
                $table->string('zip_code');
                $table->string('dob');
                $table->timestamps();
            });

            $statement = "
                        ALTER TABLE administrator AUTO_INCREMENT = 700001;
                    ";

            DB::unprepared($statement);
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('administrator');
    }
}
