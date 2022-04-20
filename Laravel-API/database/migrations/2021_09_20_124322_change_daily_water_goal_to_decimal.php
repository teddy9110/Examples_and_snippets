<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

class ChangeDailyWaterGoalToDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->decimal('daily_water_goal', 7, 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $output = new ConsoleOutput();
            // This will mess up values that are already set as decimals...
            // Currently will not revert, but will log an info statement.
            $output->writeln(
                'Reverting this migration has no effect, as if ran - it could permanently remove important user data.'
            );
            $output->writeln(
                'If this has to be reverted, it is necessary to update all existing water ' .
                'activity log values to correct values.'
            );
            // $table->integer('daily_water_goal')->nullable()->change();
        });
    }
}
