<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateTestUserRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_user_roles', function ($table) {
            $table->increments('id');
            $table->uuid('test_user_id');
            $table->foreign('test_user_id')->references('id')->on('test_users');
            $table->string('role');

            $table->timestamps();
        });

        $now = Carbon::now();

        DB::table('test_user_roles')->insert([
            'test_user_id' => 1,
            'role' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('test_user_roles');
    }
}