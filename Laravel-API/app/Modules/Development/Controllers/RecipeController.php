<?php

namespace Rhf\Modules\Development\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Recipe\Models\Recipe;
use Rhf\Modules\Recipe\Models\RecipeIngredient;
use Rhf\Modules\Recipe\Models\RecipeInstruction;
use Rhf\Modules\Recipe\Resources\RecipeResource;

class RecipeController extends Controller
{
    public function createRecipe()
    {
        $recipe = Recipe::create(Recipe::factory()->make()->toArray());
        for ($i = 0; $i <= rand(4, 10); $i++) {
            $data = RecipeIngredient::factory()->make()->toArray();
            $data['order'] = $i;
            $recipe->ingredients()->create($data);
        }

        for ($i = 0; $i <= rand(4, 10); $i++) {
            $data = RecipeInstruction::factory()->make()->toArray();
            $data['order'] = $i;
            $recipe->instructions()->create($data);
        }

        return new RecipeResource($recipe);
    }

    public function createBadRecipe()
    {
        $recipe = Recipe::create(Recipe::factory()->make()->toArray());
        if (rand(0, 1) == 1) {
            for ($i = 0; $i <= rand(4, 10); $i++) {
                $data = RecipeIngredient::factory()->make()->toArray();
                $data['order'] = $i;
                $recipe->ingredients()->create($data);
            }
        } else {
            for ($i = 0; $i <= rand(4, 10); $i++) {
                $data = RecipeInstruction::factory()->make()->toArray();
                $data['order'] = $i;
                $recipe->instructions()->create($data);
            }
        }
        return new RecipeResource($recipe);
    }
}
