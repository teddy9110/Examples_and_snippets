<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Rhf\Modules\Workout\Models\Workout;

class AddHiddenColumnToPivotRelatedVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pivot_related_videos', function (Blueprint $table) {
            $table->boolean('active')->default(true);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pivot_related_videos', function (Blueprint $table) {
            $table->dropColumn('active');
        });
    }
}
