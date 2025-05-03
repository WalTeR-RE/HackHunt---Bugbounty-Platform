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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->uuid('program_id')->unique();
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('bounty_range')->nullable();
            $table->boolean('is_private')->default(0);
            $table->integer('number_of_reports')->nullable();
            $table->integer('avg_bounty')->nullable();
            $table->integer('validation_time')->nullable();
            $table->integer('vulnerabilities_rewarded')->default(0);
            $table->timestamp('started_at');
            $table->text('fast_description')->nullable();
            $table->json('rewards')->nullable();
            $table->text('target_description')->nullable();
            $table->json('scope')->nullable();
            $table->text('description_rules')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
