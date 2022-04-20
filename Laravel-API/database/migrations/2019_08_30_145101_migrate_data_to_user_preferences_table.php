<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateDataToUserPreferencesTable extends Migration
{
    private $dataFields = [
        'gender', 'dob', 'daily_step_goal', 'start_height', 'start_weight', 'exercise_location_id',
        'exercise_frequency_id', 'daily_water_goal', 'daily_calorie_goal', 'exercise_level_id',
        'daily_protein_goal', 'daily_carbohydrate_goal', 'daily_fat_goal', 'daily_fiber_goal',
        'personal_goals', 'medical_conditions', 'user_role', 'token', 'mfp_access_token',
        'mfp_refresh_token', 'mfp_token_expires_at', 'mfp_user_id', 'mfp_authentication_code'
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->string('gender')->after('weight_unit')->nullable();
            $table->string('dob')->after('gender')->nullable();
            $table->integer('daily_step_goal')->after('dob')->nullable();
            $table->float('start_height')->after('daily_step_goal')->nullable();
            $table->float('start_weight')->after('start_height')->nullable();
            $table->integer('exercise_location_id')->after('start_weight')->nullable();
            $table->integer('exercise_frequency_id')->after('exercise_location_id')->nullable();
            $table->integer('daily_water_goal')->after('exercise_frequency_id')->nullable();
            $table->integer('daily_calorie_goal')->after('daily_water_goal')->nullable();
            $table->integer('exercise_level_id')->after('daily_calorie_goal')->nullable();
            $table->integer('daily_protein_goal')->after('exercise_level_id')->nullable();
            $table->integer('daily_carbohydrate_goal')->after('daily_protein_goal')->nullable();
            $table->integer('daily_fat_goal')->after('daily_carbohydrate_goal')->nullable();
            $table->integer('daily_fiber_goal')->after('daily_fat_goal')->nullable();
            $table->text('personal_goals')->after('daily_fiber_goal')->nullable();
            $table->text('medical_conditions')->after('personal_goals')->nullable();
            $table->integer('user_role')->after('medical_conditions')->nullable();

            $table->string('token', 2047)->after('user_role')->nullable();
            $table->string('mfp_access_token', 2047)->after('token')->nullable();
            $table->string('mfp_refresh_token', 2047)->after('mfp_access_token')->nullable();
            $table->string('mfp_token_expires_at')->after('mfp_refresh_token')->nullable();
            $table->string('mfp_user_id')->after('mfp_token_expires_at')->nullable();
            $table->string('mfp_authentication_code')->after('mfp_user_id')->nullable();
        });

        $usersPreferences = DB::table('user_preferences')->get(['user_id']);

        foreach ($usersPreferences as $preference) {
            $data = DB::table('user_meta')->where('user_id', $preference->user_id)->get(['meta', 'value']);
            $updates = [];

            foreach ($data as $item) {
                $key = $item->meta != '_token' ? $item->meta : 'token';
                $value = $item->value;

                if (in_array($key, $this->dataFields)) {
                    $updates[$key] = $value;
                }
            }

            if (count($updates) > 0) {
                DB::table('user_preferences')
                    ->where('user_id', $preference->user_id)
                    ->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            foreach ($this->dataFields as $field) {
                $table->dropColumn($field);
            }
        });
    }
}
