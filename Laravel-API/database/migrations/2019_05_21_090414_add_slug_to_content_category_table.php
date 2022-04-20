<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Rhf\Modules\Content\Models\Category;

class AddSlugToContentCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add a slug column and nullify it
        Schema::table('content_category', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable();
        });

        $categories = Category::all();

        // For each for the category titles, we want to generate a slug representation of this
        foreach ($categories as $category) {
            $category->slug = str_slug($category->title);
            $category->save();
        }

        // Set the new slug column to not null
        Schema::table('content_category', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('content_category', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
