<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Product\Models\PromotedProductPlacement;

class PromotedProductPlacementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PromotedProductPlacement::create([
            'slug' => 'dashboard',
            'description' => 'Top of the dashboard'
        ]);

        PromotedProductPlacement::create([
            'slug' => 'settings',
            'description' => 'Main settings page'
        ]);
    }
}
