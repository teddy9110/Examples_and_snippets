<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class WorkoutPromotedProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exercise_category', function (Blueprint $table) {
            $table->unsignedInteger('promoted_product_id')->nullable();
            $table->foreign('promoted_product_id')->references('id')->on('promoted_products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exercise_category', function (Blueprint $table) {
            $table->dropForeign('exercise_category_promoted_product_id_foreign');
            $table->dropColumn('promoted_product_id');
        });
    }
}
