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
            $table->boolean('has_pregnant')->default(false)->after('residence_status');
            $table->boolean('has_nursing')->default(false)->after('has_pregnant');
            $table->boolean('has_children')->default(false)->after('has_nursing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['has_pregnant', 'has_nursing', 'has_children']);
        });
    }
};
