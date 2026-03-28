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
        // Gagamit tayo ng hasTable check para hindi mag-fail kung nandoon na ang table
        if (!Schema::hasTable('positions')) {
            Schema::create('positions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('election_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('description')->nullable();
                
                // Mahalaga ito para sa logic ng ElectionController
                $table->integer('max_selection')->default(1); 
                
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};