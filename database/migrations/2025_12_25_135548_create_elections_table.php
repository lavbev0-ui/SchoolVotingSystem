<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ELECTIONS TABLE
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('title');
            $table->text('bio')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            // Stores: 'all', 'grade-level', 'section', or 'custom'
            $table->string('eligibility_type')->default('all'); 
            // Stores the array of selected grades, sections, or student IDs
            // Example: {"grades": ["11", "12"]} or {"sections": ["A", "B"]}
            $table->json('eligibility_metadata')->nullable(); 
            $table->enum('status', ['upcoming', 'active', 'completed', 'archived'])->default('upcoming');
            $table->timestamps();
        });

        // POSITIONS TABLE 
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('max_selection')->default(1); 
            $table->timestamps();
        });

        // CANDIDATES TABLE
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->string('name');
            $table->foreignId('grade_level_id')->nullable()->constrained('grade_levels')->nullOnDelete();
            $table->string('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->text('bio')->nullable();
            $table->text('party')->nullable();
            $table->text('platform')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('elections');
    }
};