<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserQuestionnaireAdditionalQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_questionnaires', function (Blueprint $table) {
            $table->boolean('processed_food')->after('step_goal_increased_days')->nullable();
            $table->string('workouts_in_weeks')->after('workout_with_rh_app')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_questionnaires', function (Blueprint $table) {
            $table->dropColumn(['processed_food', 'workouts_in_weeks']);
        });
    }
}
