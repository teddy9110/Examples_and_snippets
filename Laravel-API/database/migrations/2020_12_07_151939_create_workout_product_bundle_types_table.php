<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkoutProductBundleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'workout_product_bundle_types',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('exercise_location_id')->nullable();
                $table->integer('exercise_level_id')->nullable();
                $table->integer('exercise_frequency_id')->nullable();
                $table->integer('bundle_id');
                $table->timestamps();
                $table->softDeletes();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workout_product_bundle_types');
    }
}
