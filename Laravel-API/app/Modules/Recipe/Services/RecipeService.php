<?php

namespace Rhf\Modules\Recipe\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Rhf\Modules\Recipe\Models\Recipe;

class RecipeService
{
    protected $recipe = null;

    /**
     * Create a new Service instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Creates a recipe image
     *
     * @param UploadedFile $image
     * @return void
     * @throws \Exception
     */
    public function createRecipeImage(UploadedFile $image)
    {
        $fileService = new RecipeImageFileService();
        $imagePath = $fileService->createFromUpload($image, "recipe-images", false);
        $this->recipe->image = $imagePath['path'] . '/' . $imagePath['file_name'];
    }

    /**
     * Deletes a recipe image
     *
     * @return void
     * @throws \Exception
     */
    public function deleteRecipeImage()
    {
        $fileService = new RecipeImageFileService();
        $fileService->delete($this->recipe);
    }

    /**
     * Creates a recipe
     *
     * @param array $data
     * @param UploadedFile $image
     * @return Recipe
     * @throws \Exception
     */
    public function createRecipe(array $data, UploadedFile $image)
    {
        $recipe = new Recipe();

        $this->setRecipe($recipe);
        $this->updateRecipe($data, $image);

        return $recipe;
    }

    /**
     * Update a recipe image
     *
     * @param UploadedFile $image
     * @throws \Exception
     */
    public function updateRecipeImage(UploadedFile $image)
    {
        $this->createRecipeImage($image);

        // remove existing image
        if (isset($this->recipe->image)) {
            $count = Recipe::where('image', $image)->count();

            // only delete image if not referenced elsewhere
            if ($count == 1) {
                $this->deleteRecipeImage();
            }
        }

        $this->getRecipe()->save();
    }

    /**
     * Creates a recipe
     *
     * @param array $data
     * @param UploadedFile $image
     * @throws \Exception
     */
    public function updateRecipe(array $data, UploadedFile $image = null)
    {
        DB::transaction(function () use ($data, $image) {
            $recipe = $this->getRecipe();
            $recipe->active = $data['active'] ?? true;

            foreach ($recipe->getPlainKeys() as $key) {
                if (isset($data[$key])) {
                    $recipe[$key] = $data[$key];
                }
            }

            $macros = $data['macros'];
            foreach ($recipe->getMacroKeys() as $key) {
                $recipe["macro_$key"] = $macros[$key];
            }

            if (isset($image)) {
                $this->updateRecipeImage($image);
            }

            $recipe->save();

            $recipe->ingredients()->delete();
            if (isset($data['ingredients'])) {
                $ingredients = $data['ingredients'];

                foreach ($ingredients as $index => $ingredient) {
                    $recipe->ingredients()->create(array_merge($ingredient, [
                        'order' => $index
                    ]));
                }
            }

            $recipe->instructions()->delete();
            if (isset($data['instructions'])) {
                $instructions = $data['instructions'];

                foreach ($instructions as $index => $instruction) {
                    $recipe->instructions()->create(array_merge($instruction, [
                        'order' => $index
                    ]));
                }
            }
        });
    }

    /**
     * Return the item associated to the instance of the service.
     *
     * @return Recipe
     */
    public function getRecipe()
    {
        return $this->recipe;
    }

    /**
     * Set the recipe associated to the instance of the service.
     *
     * @param Recipe $recipe
     * @return self
     */
    public function setRecipe(Recipe $recipe)
    {
        $this->recipe = $recipe;
        return $this;
    }
}
