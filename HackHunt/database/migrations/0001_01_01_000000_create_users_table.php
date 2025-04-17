<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('about_me')->nullable();
            $table->string('nickname')->unique();
            $table->string('profile_picture')->default('default.png');
            $table->string('background_picture')->default('default_background.png');
            $table->integer('role_id')->default(1);
            $table->integer('rank');
            $table->string('country');
            $table->boolean('active')->default(true);
            $table->bigInteger('total_points')->default(0);
            $table->float('accuracy')->default(0.0);
            $table->json('links')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number', 20)->unique();
            $table->date('birthday');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('verified')->default(false);
            $table->integer('vulnerabilities_count')->default(0);
            $table->integer('engagement_count')->default(0);
            $table->boolean('authenticated')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->boolean('active')->default(true);
            $table->string('ip')->nullable();
            $table->string('device_id')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index('email');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 255)->primary();
            $table->uuid('user_id')->nullable();
            $table->foreign('user_id')->references('uuid')->on('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload')->nullable();
            $table->integer('last_activity')->index()->nullable();
            $table->text('remember_token')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
