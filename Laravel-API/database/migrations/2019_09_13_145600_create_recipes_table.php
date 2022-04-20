<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('serves');
            $table->string('prep_time');
            $table->string('total_time');
            $table->string('image_uri');

            $table->float('macro_calories');
            $table->float('macro_protein');
            $table->float('macro_carbs');
            $table->float('macro_fats');
            $table->float('macro_fibre');

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
        Schema::dropIfExists('recipes');
    }
}
