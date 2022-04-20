<?php

namespace Rhf\Modules\Recipe\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Recipe\Models\Recipe;
use Rhf\Modules\Recipe\Resources\RecipePreviewResource;
use Rhf\Modules\Recipe\Resources\RecipeResource;
use Rhf\Modules\Recipe\Resources\UserFavouriteRecipeResource;
use Rhf\Modules\User\Models\User;

class RecipeController extends Controller
{
    /**
     * Fetch recipe by id.
     *
     * @param $id
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function show($id)
    {
        return new RecipeResource(Recipe::findOrFail($id));
    }

    /**
     * Fetch recipes.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'asc');
        $filterBy = $request->get('filter_by', 'title');
        $filterValue = $request->get('filter');

        $query = Recipe::query()
            ->orderBy($orderBy, $orderDirection);

        if ($filterBy && $filterValue) {
            $query->where($filterBy, 'like', "%$filterValue%");
        }

        $recipes = $query->paginate($perPage);
        return RecipePreviewResource::collection($recipes);
    }

    public function toggleFavouriteRecipe(Request $request)
    {
        $validate = $request->validate([
            'recipe' => 'array|min:1'
        ]);

        try {
            $user = User::findOrFail(auth('api')->user()->id);
            $user->recipes()->toggle($request->recipe);
            return response()->json([
                'data' => 'success'
            ]);
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Sorry, unable to favourite this recipe. Please try again');
        }
    }

    public function getUserFavourites()
    {
        $user = User::findOrFail(auth('api')->user()->id);
        return RecipeResource::collection($user->recipes);
    }
}
