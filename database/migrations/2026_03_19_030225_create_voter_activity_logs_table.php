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
    Schema::create('voter_activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('voter_id')->constrained('voters')->onDelete('cascade');
        $table->string('action'); // 'voted', 'password_changed', 'login', etc.
        $table->string('description')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('voter_activity_logs');
}
};
