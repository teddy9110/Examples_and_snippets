<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\Admin\Requests\AdminCategoryRequest;
use Rhf\Modules\Admin\Resources\AdminCategoryDetailedResource;
use Rhf\Modules\Admin\Resources\AdminCategoryResource;
use Rhf\Modules\Content\Models\Category;

class AdminCategoryController extends Controller
{
    /**
     * Fetch paginated categories
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $filter = $request->get('filter');

        $query = Category::query();
        if ($filter) {
            $query->where('title', 'like', "%$filter%");
        }

        $categories = $query->paginate($perPage);
        return AdminCategoryResource::collection($categories);
    }

    /**
     * Fetch category by id.
     *
     * @param $id
     * @return AdminCategoryDetailedResource
     */
    public function show($id)
    {
        return new AdminCategoryDetailedResource(Category::findOrFail($id));
    }

    /**
     * Create a category.
     *
     * @param AdminCategoryRequest $request
     * @param $id
     * @return void
     */
    public function store(AdminCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->json();
        $category->title = $data->get('title');
        $category->parent_id = $data->get('parent_id');
        $category->save();
    }

    /**
     * Update a category.
     *
     * @param AdminCategoryRequest $request
     * @param $id
     * @return void
     */
    public function update(AdminCategoryRequest $request, $id)
    {
        $category = Category::findOrFail($id);
        $data = $request->json();
        $category->title = $data->get('title');
        $category->parent_id = $data->get('parent_id');
        $category->save();
    }

    /**
     * Delete a category.
     *
     * @param $id
     * @return void
     */
    public function delete($id)
    {
        Category::findOrFail($id)->fullDelete();
    }
}
