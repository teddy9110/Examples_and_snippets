<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductContentToPromotedProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promoted_products', function (Blueprint $table) {
            $table->string('placement_slug')->nullable()->change();
            $table->string('video_url')->after('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promoted_products', function (Blueprint $table) {
            $table->dropColumn('video_url');
        });
    }
}
