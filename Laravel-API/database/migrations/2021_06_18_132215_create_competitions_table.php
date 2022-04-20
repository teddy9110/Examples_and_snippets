<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('subtitle');
            $table->string('slug')->unique();
            $table->string('description', 5000);
            $table->string('desktop_image')->nullable();
            $table->string('mobile_image')->nullable();
            $table->string('app_image')->nullable();
            $table->text('rules');
            $table->string('prize');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('active')->default(0);
            $table->boolean('closed')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitions');
    }
}
