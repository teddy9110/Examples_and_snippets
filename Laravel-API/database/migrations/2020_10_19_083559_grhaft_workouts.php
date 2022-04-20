<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore
class GrhaftWorkouts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_category', function (Blueprint $table) {
            $table->softDeletes();
            $table->integer('exercise_frequency_id')->nullable();
            $table->unsignedTinyInteger('order')->nullable();
            $table->text('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('content_thumbnail')->nullable();
            $table->string('video')->nullable();
        });

        Schema::create('workout_rounds', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('workout_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->unsignedTinyInteger('order');
            $table->unsignedTinyInteger('repeat')->default(1);
            $table->string('thumbnail')->nullable();
            $table->string('content_thumbnail')->nullable();
            $table->string('video')->nullable();
            $table->string('content_video')->nullable();

            $table->foreign('workout_id')->references('id')->on('exercise_category')->onDelete('cascade');
        });

        Schema::create('workout_round_exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quantity')->nullable();
            $table->unsignedInteger('exercise_id');
            $table->unsignedInteger('round_id');
            $table->unsignedTinyInteger('order');
            $table->unsignedTinyInteger('repeat')->default(1);

            $table->foreign('exercise_id')->references('id')->on('exercise')->onDelete('cascade');
            $table->foreign('round_id')->references('id')->on('workout_rounds')->onDelete('cascade');
        });

        Schema::table('exercise', function (Blueprint $table) {
            $table->integer('exercise_category_id')->nullable()->change();
            $table->integer('sort_order')->nullable()->change();
            $table->string('thumbnail')->nullable();
            $table->string('content_thumbnail')->nullable();
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
            $table->dropColumn([
                'deleted_at', 'exercise_frequency_id', 'order', 'content', 'thumbnail', 'content_thumbnail', 'video',
            ]);
        });

        Schema::dropIfExists('workout_round_exercises');

        Schema::dropIfExists('workout_rounds');


        Schema::table('exercise', function (Blueprint $table) {
            $table->integer('sort_order')->nullable(false)->change();
            $table->dropColumn('thumbnail', 'content_thumbnail');
        });
    }
}
