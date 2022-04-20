<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Rhf\Modules\Shopify\Models\ShopifyPromotedProducts;

class ShopifyPromotedProductsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ShopifyPromotedProducts::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'title' => $this->faker->words(4),
            'website_image' => $this->faker->image('/tmp', 2280, 700, 'cats', true),
            'mobile_image' => $this->faker->image('/tmp', 750, 420, 'cats', true),
            'active' => $this->faker->boolean,
            'website_only' => $this->faker->boolean,
            'shopify_product' => $this->faker->numberBetween(0, 10000),
        ];
    }
}
