<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. ELECTIONS TABLE
        Schema::create('elections', function (Blueprint $table) {
            $table->id();
            // User ID ng admin na gumawa ng election
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->string('title');
            $table->text('description')->nullable(); // Pinalitan ang bio ng description para sa general info
            $table->string('status')->default('active'); // Idinagdag ang status
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable()->index();
            $table->string('eligibility_type')->default('all'); 
            $table->json('eligibility_metadata')->nullable(); 
            $table->timestamps();
        });

        // 2. POSITIONS TABLE 
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('elections')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('max_votes')->default(1); // Ginamit ang max_votes para tugma sa seeder error
            $table->timestamps();
        });

        // 3. CANDIDATES TABLE
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            // Voter link: Mahalaga ito para sa profile sync ng kandidato
            $table->foreignId('voter_id')->nullable()->constrained('voters')->onDelete('cascade'); 
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->string('first_name'); // Pinaghiwalay base sa Voter profile mo
            $table->string('last_name');
            $table->foreignId('grade_level_id')->nullable()->constrained('grade_levels')->nullOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('sections')->nullOnDelete();
            $table->text('bio')->nullable();
            $table->string('party')->nullable();
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