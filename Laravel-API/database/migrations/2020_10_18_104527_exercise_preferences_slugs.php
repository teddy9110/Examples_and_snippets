<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Models\ExerciseLocation;

class ExercisePreferencesSlugs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addSlugToTable('exercise_level', new ExerciseLevel(), 'title');
        $this->addSlugToTable('exercise_location', new ExerciseLocation(), 'title');
        $this->addSlugToTable('exercise_frequency', new ExerciseFrequency(), 'amount');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->removeSlugFromTable('exercise_level');
        $this->removeSlugFromTable('exercise_location');
        $this->removeSlugFromTable('exercise_frequency');
    }

    private function addSlugToTable(string $tableName, Model $model, string $titlePropName)
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->string('slug', 50)->nullable();
        });

        $model::all()->each(function ($item) use ($titlePropName) {
            $item->slug = Str::slug($item->{$titlePropName});
            $item->save();
        });

        Schema::table($tableName, function (Blueprint $table) {
            $table->string('slug', 50)->unique()->nullable(false)->change();
        });
    }

    private function removeSlugFromTable(string $tableName)
    {
        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
