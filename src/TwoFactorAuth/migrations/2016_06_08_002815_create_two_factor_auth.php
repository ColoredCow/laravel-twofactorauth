<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwoFactorAuth extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('two_factor_auth', function (Blueprint $table) {
            $table->integer('user_id')
                ->unsigned();
            $table->string('otp');
            $table->timestamps();
        });
        
        Schema::table('two_factor_auth', function (Blueprint $table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('two_factor_auth')
                ->unsigned();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('two_factor_auth');
    }
}
