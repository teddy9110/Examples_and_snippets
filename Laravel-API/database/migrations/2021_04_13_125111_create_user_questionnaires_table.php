<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserQuestionnairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_questionnaires', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id');
            $table->integer('workouts_per_week')->nullable()->default(null);
            $table->boolean('max_weights')->default(false);
            $table->boolean('workout_with_rh_app')->default(false);
            $table->boolean('tracking_progress')->default(false);
            $table->string('achieve_steps', 255)->nullable()->default(null);
            $table->integer('step_goal_increased_days')->nullable()->default(null);
            $table->string('hunger_level', 255)->nullable()->default(null);
            $table->integer('period_due_in_days')->nullable()->default(null);
            $table->boolean('started_medication')->default(false);
            $table->date('questionnaire_date')->default(now());
            $table->integer('zendesk_ticket_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_questionnaires');
    }
}
