<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('program_user', function (Blueprint $table) {
            $table->id();

            $table->uuid('program_id');
            $table->foreign('program_id')
                ->references('program_id')->on('programs')
                ->onDelete('cascade');

            $table->uuid('user_id');
            $table->foreign('user_id')
                ->references('uuid')->on('users')
                ->onDelete('cascade');


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_user');
    }
};
