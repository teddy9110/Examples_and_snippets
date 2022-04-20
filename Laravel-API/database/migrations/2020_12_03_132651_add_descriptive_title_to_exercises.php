<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDescriptiveTitleToExercises extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise', function (Blueprint $table) {
            Schema::table('exercise', function (Blueprint $table) {
                $table->string('descriptive_title');
            });

            DB::statement('update exercise set descriptive_title = title;');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exercise', function (Blueprint $table) {
            $table->dropColumn('descriptive_title');
        });
    }
}
