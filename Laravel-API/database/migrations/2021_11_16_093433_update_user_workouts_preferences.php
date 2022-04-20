<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserWorkoutsPreferences extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_workout_preferences', function (Blueprint $table) {
            $table->integer('exercise_level_id')->after('schedule')->nullable();
            $table->integer('exercise_location_id')->after('exercise_level_id')->nullable();
            $table->integer('exercise_frequency_id')->after('exercise_location_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_workout_preferences', function (Blueprint $table) {
            $table->dropColumn(['exercise_level_id', 'exercise_location_id', 'exercise_frequency_id']);
        });
    }
}
