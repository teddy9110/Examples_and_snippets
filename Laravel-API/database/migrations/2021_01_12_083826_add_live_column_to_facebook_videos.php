<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLiveColumnToFacebookVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'facebook_videos',
            function (Blueprint $table) {
                $table->boolean('live')->default('0');
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
        Schema::table(
            'facebook_videos',
            function (Blueprint $table) {
                $table->dropColumn('live');
            }
        );
    }
}
