<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExerciseLevelToExerciseCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
/*
        Schema::create('exercise_level_to_exercise_category', function (Blueprint $table) {
            $table->integer('exercise_level_id');
            $table->integer('exercise_category_id');
        });
*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('exercise_level_to_exercise_category');
    }
}
