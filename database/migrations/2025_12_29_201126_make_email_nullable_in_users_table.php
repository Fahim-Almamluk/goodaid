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
        Schema::table('users', function (Blueprint $table) {
            // Remove unique constraint first
            $table->dropUnique(['email']);
            // Make email nullable
            $table->string('email')->nullable()->change();
            // Add unique constraint back (allows multiple nulls)
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove unique constraint
            $table->dropUnique(['email']);
            // Make email not nullable
            $table->string('email')->nullable(false)->change();
            // Add unique constraint back
            $table->unique('email');
        });
    }
};
