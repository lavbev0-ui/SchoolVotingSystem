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
        // Idadagdag ang mga nawawalang columns base sa error
        if (!Schema::hasColumn('candidates', 'manifesto')) {
            $table->text('manifesto')->nullable()->after('party');
        }
        if (!Schema::hasColumn('candidates', 'bio')) {
            $table->text('bio')->nullable()->after('manifesto');
        }
    });
}

public function down()
{
    Schema::table('candidates', function (Blueprint $table) {
        $table->dropColumn(['manifesto', 'bio']);
    });
}
};
