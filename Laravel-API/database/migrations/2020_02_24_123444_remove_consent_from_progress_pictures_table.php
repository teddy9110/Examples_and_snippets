<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveConsentFromProgressPicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_progress_pictures', function (Blueprint $table) {
            $table->dropColumn('facebook_consent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_progress_pictures', function (Blueprint $table) {
            $table->boolean('facebook_consent')->default(false)->after('public');
        });
    }
}
