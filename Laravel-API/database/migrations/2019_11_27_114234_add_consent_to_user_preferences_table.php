<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsentToUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->boolean('marketing_email_consent')->default(false)->after('mfp_authentication_code');
            $table->boolean('medical_conditions_consent')->default(false)->after('marketing_email_consent');
        });

        DB::table('user_preferences')
            ->whereNotNull('medical_conditions')
            ->update([
                'medical_conditions_consent' => true
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_preferences', function (Blueprint $table) {
            $table->dropColumn('marketing_email_consent');
            $table->dropColumn('medical_conditions_consent');
        });
    }
}
