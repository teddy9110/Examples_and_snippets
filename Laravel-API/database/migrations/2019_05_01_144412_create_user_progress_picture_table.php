<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProgressPictureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_progress_pictures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('user_progress_id');

            $table->uuid('uuid');
            $table->string('type');
            $table->string('original_name');
            $table->string('path');
            $table->boolean('public')->default(true);

            $table->timestamps();

            $table->foreign('user_progress_id')->references('id')->on('user_progress')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_progress_pictures');
    }
}
