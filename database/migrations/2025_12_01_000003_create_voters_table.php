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
    Schema::create('voters', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->string('suffix')->nullable(); 
        $table->string('photo_path', 2048)->nullable();
        $table->foreignId('grade_level_id')->nullable()->constrained('grade_levels')->nullOnDelete();
        $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
        $table->string('email')->unique()->nullable(); // Ginawang nullable
        $table->string('student_id')->unique(); // Ginamit ang final name na 'student_id'
        $table->boolean('is_active')->default(true);
        $table->string('password');
        $table->timestamps(); 
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};