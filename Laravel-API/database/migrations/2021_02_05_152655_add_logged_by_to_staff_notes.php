<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLoggedByToStaffNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_notes', function (Blueprint $table) {
            $table->integer('logged_by')->nullable();
            $table->integer('last_updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_notes', function (Blueprint $table) {
            $table->dropColumn('logged_by');
            $table->dropColumn('last_updated_by');
        });
    }
}
