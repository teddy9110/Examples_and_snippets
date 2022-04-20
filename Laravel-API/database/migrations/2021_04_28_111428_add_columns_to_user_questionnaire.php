<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToUserQuestionnaire extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_questionnaires', function (Blueprint $table) {
            $table->string('own_workouts', 255)->after('workout_with_rh_app')->nullable()->default(null);
            $table->string('issues_preventing_workouts', 255)->after('own_workouts')->nullable()->default(null);
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
            $table->dropColumn(['own_workouts', 'issues_preventing_workouts']);
        });
    }
}
