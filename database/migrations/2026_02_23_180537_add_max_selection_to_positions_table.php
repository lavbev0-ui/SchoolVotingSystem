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
    Schema::table('positions', function (Blueprint $table) {
        // Idadagdag ang max_selection column
        $table->integer('max_selection')->default(1)->after('title');
    });
}

public function down()
{
    Schema::table('positions', function (Blueprint $table) {
        $table->dropColumn('max_selection');
    });
}
};
