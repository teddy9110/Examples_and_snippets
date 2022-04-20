<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Rhf\Modules\Product\Models\PromotedProduct;
use Illuminate\Database\Eloquent\Factories\Factory;
use Rhf\Modules\Product\Models\PromotedProductPlacement;

class PromotedProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PromotedProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        $name = $this->faker->word();
        return [
            'placement_slug' => PromotedProductPlacement::factory()->create()->slug,
            'name' => $name,
            'image' => 'product-images/' . Str::random(24) . '.png',
            'active' => 1,
            'type' => array_rand(array_flip(['shopify-category', 'shopify-product'])),
            'value' => Str::random(25)
        ];
    }
}
