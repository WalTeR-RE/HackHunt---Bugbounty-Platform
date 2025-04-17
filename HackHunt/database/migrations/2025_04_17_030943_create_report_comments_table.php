<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_comments', function (Blueprint $table) {
            $table->id();
            $table->uuid('report_id');
            $table->uuid('user_id');
            $table->text('comment');
            $table->boolean('is_internal')->default(false); 
            $table->timestamps();

            $table->foreign('report_id')->references('uuid')->on('reports')->onDelete('cascade');
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_comments');
    }
};
