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
        // 1. SETTINGS TABLE: Para sa system configuration
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('setting_key')->unique(); 
                $table->text('value')->nullable();
                $table->string('label')->nullable();
                $table->string('type')->default('text');
                $table->timestamps();
            });
        }

        // 2. CANDIDATES TABLE UPDATE: Pagdagdag ng Bio at Credibility fields
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                // Bio para sa mahabang talambuhay
                if (!Schema::hasColumn('candidates', 'bio')) {
                    $table->text('bio')->nullable()->after('campaign_platform');
                }
                
                // Achievements para sa listahan ng kredibilidad
                if (!Schema::hasColumn('candidates', 'achievements')) {
                    $table->text('achievements')->nullable()->after('bio');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tatanggalin ang mga columns kung i-ro-rollback ang migration
        if (Schema::hasTable('candidates')) {
            Schema::table('candidates', function (Blueprint $table) {
                $columnsToDrop = [];

                if (Schema::hasColumn('candidates', 'bio')) {
                    $columnsToDrop[] = 'bio';
                }
                if (Schema::hasColumn('candidates', 'achievements')) {
                    $columnsToDrop[] = 'achievements';
                }

                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }

        Schema::dropIfExists('settings');
    }
};