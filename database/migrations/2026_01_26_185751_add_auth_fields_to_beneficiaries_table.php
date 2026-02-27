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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('national_id');
            $table->string('password')->nullable()->after('username');
            $table->timestamp('password_set_at')->nullable()->after('password');
            $table->boolean('has_set_password')->default(false)->after('password_set_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['username', 'password', 'password_set_at', 'has_set_password']);
        });
    }
};
