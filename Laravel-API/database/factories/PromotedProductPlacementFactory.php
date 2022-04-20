<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rhf\Modules\Product\Models\PromotedProductPlacement;

class PromotedProductPlacementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PromotedProductPlacement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'name' => 'Dashboard',
            'slug' => 'dashboard',
            'description' => 'Top of the dashboard'
        ];
    }
}
