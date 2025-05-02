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
        Schema::create('program_invites', function (Blueprint $table) {
            $table->id();
            $table->uuid('program_id');
            $table->uuid('user_id');
            $table->enum('status',['Accepted','Rejected','Expired','Pending'])->default('Pending');
            $table->string('invited_by');
            $table->timestamp('expire_at');
            $table->timestamps();

            
            $table->foreign('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_invites');
    }
};
