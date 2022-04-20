<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityDeletionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Primary key is split up into sql statements, so allow no primary key for this session to avoid error
        try {
            DB::statement('SET SESSION sql_require_primary_key=0');
        } catch (Exception $e) {
            // if throws, no need to set session var
        }

        Schema::create('activity_deletions', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('user_id');
            $table->string('type');
            $table->string('value');
            $table->date('date');
            $table->timestamps();
            $table->timestamp('deleted_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_deletions');
    }
}
