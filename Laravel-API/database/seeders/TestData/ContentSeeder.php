<?php

namespace Database\Seeders\TestData;

use Illuminate\Database\Seeder;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Content\Models\Category;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category = Category::create([
            'title' => 'Test Content',
        ]);

        $content = Content::create([
            'category_id' => $category->id,
            'type' => 'Video',
            'title' => 'Test Content Item',
            'content' => 'some_url',
            'description' => 'Some description',
            'status' => 1,
        ]);
    }
}
