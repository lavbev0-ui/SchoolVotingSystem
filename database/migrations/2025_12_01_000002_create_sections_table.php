<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedBigInteger('grade_level_id');
    $table->string('year_level'); 
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};