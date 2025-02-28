<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('password');
            $table->string('email');
            $table->string('remember_token', 512)->nullable();
            $table->string('image')->nullable();
            $table->string('background_image')->nullable();
            $table->integer('role_id')->default(1);
            $table->timestamps();

            $table->index('role_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
