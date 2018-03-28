<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemusersroleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('system_users_role'))
        {
            Schema::create('system_users_role', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('role_id')->unsigned()->default(0);
                $table->foreign('role_id')->references('id')->on('userrole')->onUpdate('cascade')->onDelete('cascade');
                $table->unsignedInteger('login_id')->unsigned()->default(0);
                $table->foreign('login_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
                $table->unsignedInteger('userid')->unsigned()->default(0);
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
        Schema::drop('system_users_role');
    }
}
