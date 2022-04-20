<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompetitionEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('description', 5000);
            $table->string('image')->nullable();
            $table->integer('votes')->default(0);
            $table->string('url')->nullable();
            $table->unsignedBigInteger('competition_id');
            $table->unsignedInteger('user_id');
            $table->integer('reports')->default(0);
            $table->integer('suspended')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('competition_id')->on('competitions')
                ->references('id')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('user_id')
                ->on('users')
                ->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competition_entries');
    }
}
