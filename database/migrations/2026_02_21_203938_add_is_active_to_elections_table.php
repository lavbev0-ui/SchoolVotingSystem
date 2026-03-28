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
    Schema::table('elections', function (Blueprint $table) {
        // Magdadagdag ng is_active column, default ay 1 (Active)
        $table->boolean('is_active')->default(1)->after('title'); 
    });
}

public function down(): void
{
    Schema::table('elections', function (Blueprint $table) {
        $table->dropColumn('is_active');
    });
}
};
