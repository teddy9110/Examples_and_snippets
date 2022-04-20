<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBodyFatPercentageToActivityDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_details', function (Blueprint $table) {
            $table->string('body_fat_percentage')->nullable()->default(null)->after('period');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_details', function (Blueprint $table) {
            $table->dropColumn('body_fat_percentage');
        });
    }
}
