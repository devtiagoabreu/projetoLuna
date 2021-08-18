<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->string('email');
            $table->string('password');
        });
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_professional');
            
        });
        Schema::create('user_appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user');
            $table->integer('id_professional');
            $table->datetime('ap_datetime');
        });
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar')->default('default.png');
            $table->float('stars')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('email');
            $table->string('password');
        });
        Schema::create('professional_photos', function (Blueprint $table) {
            $table->id();
            $table->integer('id_professional');
            $table->string('url');
        });
        Schema::create('professional_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('id_professional');
            $table->float('rate');
        });
        Schema::create('professional_services', function (Blueprint $table) {
            $table->id();
            $table->integer('id_professional');
            $table->string('name');
            $table->float('price');
        });
        Schema::create('professional_testimonials', function (Blueprint $table) {
            $table->id();
            $table->integer('id_professional');
            $table->string('name');
            $table->float('rate');
            $table->string('body');
        });
        Schema::create('professional_availability', function (Blueprint $table) {
            $table->id();
            $table->integer('id_professional');
            $table->integer('weekday');
            $table->text('hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_favorites');
        Schema::dropIfExists('user_appointments');
        Schema::dropIfExists('professionals');
        Schema::dropIfExists('professional_photos');
        Schema::dropIfExists('professional_reviews');
        Schema::dropIfExists('professional_services');
        Schema::dropIfExists('professional_testimonials');
        Schema::dropIfExists('professional_availability');
    }
}
