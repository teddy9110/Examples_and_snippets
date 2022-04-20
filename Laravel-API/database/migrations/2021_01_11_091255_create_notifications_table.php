<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'notifications',
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('title', 50);
                $table->string('content', 255);
                $table->string('image')->nullable();
                $table->string('link')->nullable();
                $table->string('data')->nullable();
                $table->dateTime('send_at')->nullable();
                $table->unsignedBigInteger('topic_id');
                $table->timestamps();

                $table->foreign('topic_id')->references('id')->on('topics')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('notifications');
    }
}
