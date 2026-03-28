<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->text('biography')->nullable(); // Para sa mahabang talambuhay
        $table->text('credibility')->nullable(); // Para sa mga achievements o credentials
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            //
        });
    }
};
