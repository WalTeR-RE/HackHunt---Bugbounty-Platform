<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('report_id');
            $table->uuid('performed_by');
            $table->string('action');     
            $table->json('details')->nullable();
            $table->timestamps();

            $table->foreign('report_id')->references('uuid')->on('reports')->onDelete('cascade');
            $table->foreign('performed_by')->references('uuid')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_logs');
    }
};
