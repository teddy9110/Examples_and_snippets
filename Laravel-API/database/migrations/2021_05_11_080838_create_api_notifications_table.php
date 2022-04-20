<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('app_version')->nullable(true);
            $table->enum('platform', ['android', 'ios', 'all']);
            $table->string('platform_version')->nullable(true);
            $table->string('title');
            $table->text('content');
            $table->string('action_text');
            $table->string('action_callback')->nullable();
            $table->string('type')->default('information');
            $table->timestamp('not_before')->nullable();
            $table->timestamp('not_after')->nullable();
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
        Schema::dropIfExists('api_notifications');
    }
}
