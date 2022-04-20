<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExerciseFrequencyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('exercise_frequency')) {
            Schema::create('exercise_frequency', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('amount');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exercise_frequency_to_exercise_category')) {
            Schema::create('exercise_frequency_to_exercise_category', function (Blueprint $table) {
                $table->integer('exercise_frequency_id');
                $table->integer('exercise_category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exercise_frequency');
        Schema::dropIfExists('exercise_level_to_exercise_category');
    }
}
