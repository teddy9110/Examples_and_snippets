<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Rhf\Modules\Video\Models\RelatedVideo;

class AddNewColumnToRelatedVideo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('related_videos', function (Blueprint $table) {
            $table->boolean('single_parent')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('related_videos', function (Blueprint $table) {
            $table->dropColumn('single_parent');
        });
    }
}
