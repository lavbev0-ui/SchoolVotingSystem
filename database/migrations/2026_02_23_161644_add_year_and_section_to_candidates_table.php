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
    Schema::table('candidates', function (Blueprint $table) {
        // Ibinabalik ang Year Level at Section fields
        if (!Schema::hasColumn('candidates', 'year_level')) {
            $table->string('year_level')->nullable()->after('middle_name');
        }
        
        if (!Schema::hasColumn('candidates', 'section')) {
            $table->string('section')->nullable()->after('year_level');
        }
    });
}

public function down(): void
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn(['year_level', 'section']);
    });
}
};
