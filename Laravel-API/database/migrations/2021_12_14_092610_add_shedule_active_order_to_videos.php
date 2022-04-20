<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSheduleActiveOrderToVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->date('scheduled_date')->nullable(true)->after('live');
            $table->time('scheduled_time')->nullable(true)->after('scheduled_date');
            $table->boolean('active')->default(false)->after('scheduled_time');
            $table->integer('order')->nullable()->after('active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn(
                'scheduled_date',
                'order',
                'scheduled_time',
                'active'
            );
        });
    }
}
