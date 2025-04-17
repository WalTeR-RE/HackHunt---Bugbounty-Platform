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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('reporter');
            $table->uuid('program_id');
            $table->enum('severity',['P1','P2','P3','P4','P5'])->nullable();
            $table->enum('status',['New', 'Triaged', 'Duplicate', 'Informative', 'Resolved', 'N/A'])->default('New');
            $table->integer('bounty')->nullable();
            $table->boolean('rewarded')->default(false);
            $table->integer('points')->nullable();
            $table->string('title');
            $table->string('type');
            $table->text('description');
            $table->json('attachments')->nullable();
            $table->timestamp('triaged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
