<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransformationStoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transformation_stories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->string('email');
            $table->decimal('weight_loss', 5, 2);
            $table->decimal('start_weight', 6, 2);
            $table->decimal('current_weight', 6, 2);
            $table->string('story');
            $table->string('before_photo')->nullable();
            $table->string('after_photo')->nullable();
            $table->boolean('marketing_accepted')->default(false);
            $table->boolean('remain_anonymous')->default(true);
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
        Schema::dropIfExists('transformation_stories');
    }
}
