<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePivotUserAppReviewTopics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_user_app_review_feedback_topics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('feedback_id');
            $table->unsignedInteger('topic_id');
            $table->timestamps();
            $table->foreign('topic_id')->references('id')->on('app_review_topics')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')
                ->on('user_app_store_review_feedbacks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_user_app_review_feedback_topics');
    }
}
