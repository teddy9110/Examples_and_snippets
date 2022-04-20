<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Rhf\Modules\Recipe\Models\RecipeIngredient;

class RecipeIngredientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RecipeIngredient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(
                [
                    'Water',
                    'Juice of lime',
                    'White rice',
                    'Shredded Beef Brisket',
                    'Garlic clove',
                    'Avocado',
                    'Maple syrup',
                    'Cheese',
                    'White rice',
                    'Juice of lime',
                    'Shredded Beef Brisket',
                    'Tomatoes',
                    'Black beans',
                    'Frozen corn kernels',
                    'Spring onions',
                    'Avocado',
                    'Coriander leaves',
                    'Rapeseed oil',
                    'Red chilli',
                    'Garlic clove',
                    'Eggs',
                    'Black beans',
                    'Tinned cherry tomatoes',
                    'cumin seeds',
                    'Avocado',
                    'Fresh chopped corriander',
                    'Lime',
                    'Avocado',
                    'Fresh flat-leaf parsley',
                    'Fresh dill',
                    'Lime juice, plus 1 tsp lime zest',
                    'Avocado oil',
                    'Salt and cracked pepper',
                    'Red onion, thinly sliced',
                    'Large romaine lettuce, leaves torn',
                    'Lebanese cucumber, sliced',
                    'Tomatoes, chopped',
                    'Parmesan cheese',
                    'Allspice',
                    'Amaranth',
                    'Cinnamon',
                    'Dried goji berries',
                    'Hemp seeds',
                    'Sea salt',
                    'Sunflower seeds',
                    'Walnut halves',
                ]
            ),
            'quantity' => $this->faker->numberBetween(1, 200) . ' ' . $this->faker->randomElement(
                ['grams', 'cups', 'l', 'drops', 'small']
            ),
            'notes' => $this->faker->sentence(),
        ];
    }
}
