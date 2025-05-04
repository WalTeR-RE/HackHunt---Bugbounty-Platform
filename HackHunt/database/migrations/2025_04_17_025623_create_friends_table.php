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
        Schema::create('friends', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_one_id');
            $table->uuid('user_two_id');
            $table->enum('status', ['pending', 'accepted', 'blocked','rejected'])->default('pending');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_one_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('user_two_id')->references('uuid')->on('users')->onDelete('cascade');

            $table->unique(['user_one_id', 'user_two_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('friends');
    }
};
