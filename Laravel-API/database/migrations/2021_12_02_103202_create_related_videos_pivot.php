<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedVideosPivot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pivot_related_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('related_video_id');
            $table->unsignedInteger('related_id');
            $table->string('related_type');

            $table->foreign('related_video_id')->references('id')->on('related_videos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pivot_related_videos');
    }
}
