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
        Schema::table('batch_recipients', function (Blueprint $table) {
            $table->boolean('received')->default(false)->after('beneficiary_id');
            $table->timestamp('received_at')->nullable()->after('received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_recipients', function (Blueprint $table) {
            $table->dropColumn(['received', 'received_at']);
        });
    }
};
