<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAppStoreFeedback extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_app_store_review_feedbacks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('score');
            $table->string('comments');
            $table->unsignedInteger('review_id');
            $table->foreign('review_id')->references('id')->on('user_app_store_reviews')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_app_store_review_feedback');
    }
}
