<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // University-specific ID
            $table->string('sr_code')->unique();

            // Full name
            $table->string('name');

            // Role (student, faculty, admin)
            $table->string('role');

            // Campus reference
            $table->unsignedBigInteger('campus_id');

            // Authentication fields
            $table->string('email')->nullable()->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);

            $table->rememberToken(); // Laravel uses this for login sessions
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
