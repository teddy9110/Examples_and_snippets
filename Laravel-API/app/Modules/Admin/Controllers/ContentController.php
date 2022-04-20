<?php

namespace Rhf\Modules\Admin\Controllers;

use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Rhf\Modules\Admin\Requests\CategoryRequest;
use Rhf\Modules\Admin\Requests\ContentRequest;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Content\Services\ContentService;
use Rhf\Modules\Content\Resources\TabledContentResource;
use Rhf\Modules\Content\Models\Category;
use Rhf\Modules\Content\Services\CategoryService;
use Rhf\Modules\Content\Resources\TabledCategoryResource;

class ContentController extends Controller
{
    /**
     * Delete a content item.
     *
     * @return redirect
     */
    public function delete(Request $request, $id)
    {
        try {
            $content = Content::find($id);
            $content->delete();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->back()
            ->with('message', ['status' => 'success', 'message' => 'Content successfully deleted.']);
    }

    /**
     * Show the item list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('content/index');
    }

    /**
     * Show the item list.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexCategories()
    {
        return view('categories/index');
    }

    /**
     * Display the create item form.
     *
     * @return view
     */
    public function create(Request $request)
    {
        $categories = ContentService::categoryNav();
        return view('content/form', ['categories' => $categories]);
    }

    /**
     * Display the create item form.
     *
     * @return view
     */
    public function createCategory(Request $request)
    {
        $categories = ContentService::categoryNav();
        return view('categories/form', ['categories' => $categories]);
    }

    /**
     * Delete a category.
     *
     * @return view
     */
    public function deleteCategory(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            $category->fullDelete();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->to('/admin/categories')
            ->with('message', ['status' => 'success', 'message' => 'Category successfully deleted.']);
    }

    /**
     * Display the edit item form.
     *
     * @return view
     */
    public function edit(Request $request, $id)
    {
        try {
            $content = Content::find($id);
            $categories = ContentService::categoryNav();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return view('content/form', ['content' => $content, 'categories' => $categories]);
    }

    /**
     * Display the edit item form.
     *
     * @return view
     */
    public function editCategory(Request $request, $id)
    {
        try {
            $category = Category::find($id);
            $categories = ContentService::categoryNav();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }

        return view('categories/form', ['category' => $category, 'categories' => $categories]);
    }

    /**
     * Retrieve items by ajax.
     *
     * @return Json
     */
    public function get(Request $request)
    {
        // Filter and build the collection
        $contentCollection = ContentService::filtered()->get();

        // Calculate the current page
        if ($request->get('start') > 0 && $request->get('length') > 0) {
            $page = $request->get('start') / $request->get('length') + 1;
        } else {
            $page = 1;
        }

        // Size of page
        $size = $request->get('length');

        $content = new LengthAwarePaginator(
            $contentCollection->slice(($page - 1) * $size, $size),
            $contentCollection->count(),
            $size,
            $page
        );

        return response()->json([
            'data' => TabledContentResource::collection($content),
            'recordsTotal' => Content::count(),
            'recordsFiltered' => $contentCollection->count(),
        ]);
    }

    /**
     * Retrieve items by ajax.
     *
     * @return Json
     */
    public function getCategories(Request $request)
    {
        // Filter and build the collection
        $categoryCollection = CategoryService::filtered()->get();

        // Calculate the current page
        if ($request->get('start') > 0 && $request->get('length') > 0) {
            $page = $request->get('start') / $request->get('length') + 1;
        } else {
            $page = 1;
        }

        // Size of page
        $size = $request->get('length');

        $categories = new LengthAwarePaginator(
            $categoryCollection->slice(($page - 1) * $size, $size),
            $categoryCollection->count(),
            $size,
            $page
        );

        return response()->json([
            'data' => TabledCategoryResource::collection($categories),
            'recordsTotal' => Category::count(),
            'recordsFiltered' => $categoryCollection->count(),
        ]);
    }

    /**
     * Update or store a content item.
     *
     * @return view
     */
    public function store(ContentRequest $request, $id = false)
    {
        try {
            if (!$id) {
                $content = new Content();
            } else {
                $content = Content::findOrFail($id);
            }

            $contentService = new ContentService();
            $contentService->setContent($content)->update($request->all());

            $id = $contentService->getContent()->id;
        } catch (\Exception $e) {
            if ($id) {
                return redirect()
                    ->to('/admin/content/edit/' . $id)
                    ->withInput($request->input())->withErrors([$e->getMessage()]);
            } else {
                return redirect()
                    ->to('/admin/content/add')
                    ->withInput($request->input())->withErrors([$e->getMessage()]);
            }
        }

        return redirect()
            ->to('/admin/content/edit/' . $id)
            ->with('message', ['status' => 'success', 'message' => 'Content successfully updated.']);
    }

    /**
     * Update or store a category.
     *
     * @return view
     */
    public function storeCategory(CategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->title = $request->get('title');
            $category->parent_id = $request->get('parent_id');
            $category->save();
        } catch (\Exception $e) {
            return redirect()
                ->to('/admin/categories/edit/' . $id)
                ->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->to('/admin/categories/edit/' . $id)
            ->with('message', ['status' => 'success', 'message' => 'Category successfully updated.']);
    }

    /**
     * Create a new category.
     *
     * @return view
     */
    public function storeNewCategory(CategoryRequest $request)
    {
        // Create the empty object
        try {
            $category = new Category();
            $category->title = $request->get('title');
            $category->parent_id = $request->get('parent_id');
            $category->save();
        } catch (\Exception $e) {
            return redirect()->back()->withInput($request->input())->withErrors([$e->getMessage()]);
        }

        return redirect()
            ->to('/admin/categories/edit/' . $category->id)
            ->with('message', ['status' => 'success', 'message' => 'Category successfully created.']);
    }
}
