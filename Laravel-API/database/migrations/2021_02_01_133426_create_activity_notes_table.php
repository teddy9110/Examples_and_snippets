<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('activity_details')){
            if(Schema::hasColumn('activity_details', 'period')) {
                Schema::table('activity_details', function(Blueprint $table) {
                    $table->dropColumn('period');
                    $table->enum('period', ['true', 'false', 'unknown'])->default('unknown');
                });
            }

            if(Schema::hasColumn('activity_details','activity')) {
                Schema::table('activity_details', function(Blueprint $table) {
                    $table->dropColumn('activity');
                    $table->integer('activity_id')->after('user_id')->unsigned()->index();
                });
            }

            Schema::table('activity_details', function(Blueprint $table){
                $table->foreign('user_id')->references('id')->on('users')
                    ->onUpdate('cascade')->onDelete('cascade');

                $table->foreign('activity_id')->references('id')->on('activity')
                    ->onUpdate('cascade')->onDelete('cascade');
            });
        }
        else {
            Schema::create(
                'activity_details',
                function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->integer('user_id')->unsigned()->index();
                    $table->integer('activity_id')->unsigned()->index();
                    $table->string('note', 255)->nullable();
                    $table->enum('period', ['true', 'false', 'unknown'])->default('unknown');
                    $table->date('date');
                    $table->timestamps();

                    $table->foreign('user_id')->references('id')->on('users')
                        ->onUpdate('cascade')->onDelete('cascade');

                    $table->foreign('activity_id')->references('id')->on('activity')
                        ->onUpdate('cascade')->onDelete('cascade');
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_notes');
    }
}
