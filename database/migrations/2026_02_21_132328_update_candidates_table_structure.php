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
            // 1. Alisin ang lumang 'name' column kung nandoon pa
            if (Schema::hasColumn('candidates', 'name')) {
                $table->dropColumn('name');
            }

            // 2. Idagdag ang mga bagong columns nang may "Safety Check"
            // Ginagawa ito para iwas sa error na "Duplicate column name"
            if (!Schema::hasColumn('candidates', 'first_name')) {
                $table->string('first_name')->after('position_id');
            }

            if (!Schema::hasColumn('candidates', 'middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }

            if (!Schema::hasColumn('candidates', 'last_name')) {
                $table->string('last_name')->after('middle_name');
            }

            if (!Schema::hasColumn('candidates', 'bio')) {
                $table->text('bio')->nullable()->after('party');
            }
            
            // Siguraduhin din na ang voter_id ay nandoon para sa relationship
            if (!Schema::hasColumn('candidates', 'voter_id')) {
                $table->foreignId('voter_id')->nullable()->after('id')->constrained('voters')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'middle_name', 'last_name', 'bio', 'voter_id']);
            
            // Ibalik ang 'name' column kung i-rollback ang migration
            if (!Schema::hasColumn('candidates', 'name')) {
                $table->string('name')->after('position_id');
            }
        });
    }
};