<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminRecipeImageRequest;
use Rhf\Modules\Admin\Requests\AdminRecipeRequest;
use Rhf\Modules\Recipe\Models\Recipe;
use Rhf\Modules\Recipe\Resources\RecipePreviewResource;
use Rhf\Modules\Recipe\Resources\RecipeResource;
use Rhf\Modules\Recipe\Services\RecipeService;

class AdminRecipeController extends Controller
{
    private $recipeService;

    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    /**
     * Fetch paginated recipes
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'asc');
        $filterBy = $request->get('filter_by');
        $filterValue = $request->get('filter');

        $query = Recipe::query()
            ->orderBy($orderBy, $orderDirection);

        if ($filterBy && $filterValue) {
            $query->where($filterBy, 'like', "%$filterValue%");
        }

        $recipes = $query->paginate($perPage);
        return RecipePreviewResource::collection($recipes);
    }

    /**
     * Fetch recipe by id.
     *
     * @param $id
     * @return JsonResource
     */
    public function show($id)
    {
        return new RecipeResource(Recipe::findOrFail($id));
    }

    /**
     * Store the given recipe
     *
     * @param AdminRecipeRequest $request
     * @return JsonResource
     * @throws Exception
     */
    public function store(AdminRecipeRequest $request)
    {
        $recipe = $this->recipeService->createRecipe($request->all(), $request->file('image'));
        return new RecipeResource($recipe);
    }

    /**
     * Update the given recipe
     *
     * @param AdminRecipeRequest $request
     * @param $id
     * @return JsonResource
     * @throws Exception
     */
    public function update(AdminRecipeRequest $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $this->recipeService->setRecipe($recipe);

        $this->recipeService->updateRecipe($request->all(), $request->file('image'));
        return new RecipeResource($recipe);
    }

    /**
     * Delete a recipe
     * @param $id
     */
    public function delete($id)
    {
        Recipe::findOrFail($id)->delete();
    }

    /**
     * Update the given recipe image
     *
     * @param AdminRecipeImageRequest $request
     * @param $id
     * @return JsonResource
     * @throws Exception
     */
    public function updateImage(AdminRecipeImageRequest $request, $id)
    {
        $recipe = Recipe::findOrFail($id);
        $this->recipeService->setRecipe($recipe);

        $this->recipeService->updateRecipeImage($request->file('image'));
        return new RecipeResource($recipe);
    }
}
