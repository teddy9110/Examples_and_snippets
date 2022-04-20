<?php

namespace Database\seeders;

use Illuminate\Database\Seeder;
use Rhf\Modules\Recipe\Models\Recipe;

class RecipeSeeder extends Seeder
{
    protected function kiwiBananaBreakfastSmoothie()
    {
        $recipe = Recipe::create([
            "title" => "Kiwi Banana Breakfast Smoothie",
            "serves" => "2",
            "prep_time" => "3 MINUTES",
            "total_time" => "3 MINUTES",
            "image" => "recipe-images/84217c5c-759b-4275-a0d7-0faaff7049cd.png",
            "macro_calories" => 297,
            "macro_protein" => 11,
            "macro_carbs" => 59,
            "macro_fats" => 3,
            "macro_fibre" => 9,
        ]);

        $ingredients = [
            [
                "name" => "Kiwi fruits",
                "quantity" => "3",
                "notes" => null,
                "order" => 0
            ],
            [
                "name" => "Milk",
                "quantity" => "180 millilitres",
                "notes" => "Chilled, Any milk is fine, soy, almond, coconut etc",
                "order" => 1
            ],
            [
                "name" => "Low fat yoghurt",
                "quantity" => "190 grams",
                "notes" => "Chilled",
                "order" => 2
            ],
            [
                "name" => "Porridge oats",
                "quantity" => "4 tablespoons",
                "notes" => null,
                "order" => 3
            ],
            [
                "name" => "Ginger",
                "quantity" => "1 thumb sized piece",
                "notes" => "Grated",
                "order" => 4
            ],
            [
                "name" => "Honey",
                "quantity" => "1 teaspoon",
                "notes" => "Optional, if you like it that bit sweeter",
                "order" => 5
            ]
        ];

        foreach ($ingredients as $ingredient) {
            $recipe->ingredients()->create($ingredient);
        }

        $instructions = [
            [
                "type" => "step",
                "value" => "Skin the kiwis, slice off the top and bottom, stand and then slice off the edges",
                "order" => 0
            ],
            [
                "type" => "step",
                "value" => "Peel the bananas then chop or grate the ginger",
                "order" => 1
            ],
            [
                "type" => "step",
                "value" => "Blitz everything until smooth in a blender",
                "order" => 2
            ],
            [
                "type" => "step",
                "value" => "Pour into tall glasses and enjoy!",
                "order" => 3
            ],
            [
                "type" => "fact",
                "value" => "Ginger has MANY health benefits, some including anti-inflammatory properties, blood sugar regulation, and gastrointestinal relief.",
                "order" => 4
            ]
        ];

        foreach ($instructions as $instruction) {
            $recipe->instructions()->create($instruction);
        }
    }

    protected function peanutButterAndJelly()
    {
        $recipe = Recipe::create([
            "title" => "Peanut Butter and Jelly",
            "serves" => "2",
            "prep_time" => "10 MINUTES",
            "total_time" => "10 MINUTES",
            "image" => "recipe-images/d358bccc-58bd-4d7c-ba30-c1e4d35c54a9.png",
            "macro_calories" => 260,
            "macro_protein" => 29,
            "macro_carbs" => 29,
            "macro_fats" => 4.5,
            "macro_fibre" => 5,
        ]);

        $ingredients = [
            [
                "name" => "Plain fat free Greek yoghurt",
                "quantity" => "227 grams",
                "notes" => "Chilled",
                "order" => 0
            ],
            [
                "name" => "Unsweetened vanilla almond milk",
                "quantity" => "120 millilitres",
                "notes" => "Chilled",
                "order" => 1
            ],
            [
                "name" => "Natural sweetener",
                "quantity" => "2 packets",
                "notes" => "Truvia, stevia, etc",
                "order" => 2
            ],
            [
                "name" => "Green grapes",
                "quantity" => "25",
                "notes" => null,
                "order" => 3
            ],
            [
                "name" => "Peanut flour",
                "quantity" => "60 grams",
                "notes" => null,
                "order" => 4
            ],
            [
                "name" => "Ice cubes",
                "quantity" => "4",
                "notes" => null,
                "order" => 5
            ]
        ];

        foreach ($ingredients as $ingredient) {
            $recipe->ingredients()->create($ingredient);
        }

        $instructions = [
            [
                "type" => "step",
                "value" => "Blitz  the Greek yoghurt, milk, sweetener and grapes in a blender until the grape skins are completely mixed in and no bits are visible",
                "order" => 0
            ],
            [
                "type" => "step",
                "value" => "Add the peanut flour and ice and blend again until silky and smooth",
                "order" => 1
            ],
            [
                "type" => "step",
                "value" => "Serve immediately or keep cold in your refrigerator",
                "order" => 2
            ],
            [
                "type" => "fact",
                "value" => "Traditional PB&J recipes are made with grape, jelly (or jam as it’s known in the UK). This recipe makes use of the grapes in their fresh form, bringing down the calories.",
                "order" => 3
            ]
        ];

        foreach ($instructions as $instruction) {
            $recipe->instructions()->create($instruction);
        }
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->kiwiBananaBreakfastSmoothie();
        $this->peanutButterAndJelly();
    }
}
