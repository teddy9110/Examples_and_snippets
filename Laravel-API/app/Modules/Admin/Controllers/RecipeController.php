<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\RecipeRequest;
use Rhf\Modules\Recipe\Models\Recipe;
use Rhf\Modules\Recipe\Resources\TabledRecipeResource;
use Rhf\Modules\Recipe\Services\RecipeService;

class RecipeController extends Controller
{
    /**
     * Show the recipe list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('recipes/index');
    }

    /**
     * Display the create recipe form.
     *
     * @return view
     */
    public function create()
    {
        return view('recipes/form');
    }

    /**
     * Fetch items by ajax.
     *
     * @return Json
     */
    public function get(Request $request)
    {
        // Filter and build the collection
        $recipeCollection = Recipe::get();

        // Calculate the current page
        if ($request->get('start') > 0 && $request->get('length') > 0) {
            $page = $request->get('start') / $request->get('length') + 1;
        } else {
            $page = 1;
        }

        // Size of page
        $size = $request->get('length');

        $recipes = new LengthAwarePaginator(
            $recipeCollection->slice(($page - 1) * $size, $size),
            $recipeCollection->count(),
            $size,
            $page
        );

        return response()->json([
            'data' => TabledRecipeResource::collection($recipes),
            'recordsTotal' => Recipe::count(),
            'recordsFiltered' => $recipeCollection->count(),
        ]);
    }

    /**
     * Store the given recipe
     *
     * @param RecipeRequest $request
     * @return view
     */
    public function store(RecipeRequest $request)
    {
        try {
            $recipeService = new RecipeService();
            $recipe = $recipeService->createRecipe($request->all(), $request->file('image'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->to('/admin/recipes/edit/' . $recipe->id)
            ->with('message', ['status' => 'success', 'message' => 'Recipe successfully created.']);
    }

    /**
     * Display the edit recipe form.
     *
     * @param Request $request
     * @param $id
     * @return view
     */
    public function edit(Request $request, $id)
    {
        try {
            $recipe = Recipe::find($id);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return view('recipes/form', [ 'recipe' => $recipe ]);
    }

    /**
     * Store the given recipe
     *
     * @param RecipeRequest $request
     * @return view
     */
    public function update(RecipeRequest $request, $id)
    {
        try {
            $recipe = Recipe::find($id);
            $recipeService = new RecipeService();

            $recipeService->setRecipe($recipe);
            $recipeService->updateRecipe($request->all(), $request->file('image'));
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->to('/admin/recipes/edit/' . $recipe->id)
            ->with('message', ['status' => 'success', 'message' => 'Recipe successfully updated.']);
    }

    /**
     * Delete a recipe.
     *
     * @return view
     */
    public function delete($id)
    {
        try {
            $recipe = Recipe::find($id);
            $recipe->delete();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->to('/admin/recipes')
            ->with('message', ['status' => 'success', 'message' => 'Recipe successfully deleted.']);
    }
}
