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
        Schema::create(
            'user',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('username')->unique();
                // $table->string('email')->unique(); // If you need email
                // $table->timestamp('email_verified_at')->nullable(); // If you need email verification
                $table->string('password_hash'); // Store the hashed password
                $table->string('role')->default('customer'); // 'admin', 'customer'
                $table->string('tel')->nullable();
                // $table->rememberToken(); // If using Laravel's built-in auth
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
