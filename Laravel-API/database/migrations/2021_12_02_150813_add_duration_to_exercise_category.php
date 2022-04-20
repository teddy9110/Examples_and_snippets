<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDurationToExerciseCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_category', function (Blueprint $table) {
            $table->string('duration', 50)->after('standard_flow_thumbnail')->nullable();
            $table->string('workout_flow')->after('duration')->default('standard');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exercise_category', function (Blueprint $table) {
            $table->dropColumn(['duration', 'workout_flow']);
        });
    }
}
