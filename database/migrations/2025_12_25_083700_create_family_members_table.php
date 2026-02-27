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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('national_id')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('relationship')->nullable(); // علاقة برب الأسرة (زوج/زوجة، ابن/ابنة، أب، أم، إلخ)
            $table->integer('order')->default(0); // ترتيب في الأسرة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
