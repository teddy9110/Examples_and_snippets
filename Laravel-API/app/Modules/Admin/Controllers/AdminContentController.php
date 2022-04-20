<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rhf\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rhf\Modules\Admin\Requests\AdminContentRequest;
use Rhf\Modules\Content\Models\Content;
use Rhf\Modules\Admin\Resources\AdminContentDetailedResource;
use Rhf\Modules\Admin\Resources\AdminContentResource;
use Rhf\Modules\Content\Services\ContentService;

class AdminContentController extends Controller
{
    protected $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService =  $contentService;
    }

    /**
     * Fetch paginated content
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $filter = $request->get('filter');

        $query = Content::query();
        if ($filter) {
            $query->where('title', 'like', "%$filter%");
        }

        $content = $query->paginate($perPage);
        return AdminContentResource::collection($content);
    }

    /**
     * Fetch content by id.
     *
     * @param $id
     * @return AdminContentDetailedResource
     */
    public function show($id)
    {
        return new AdminContentDetailedResource(Content::findOrFail($id));
    }

    /**
     * Create content.
     *
     * @param AdminContentRequest $request
     * @return void
     * @throws \Exception
     */
    public function store(AdminContentRequest $request)
    {
        $content = new Content();
        $this->contentService->setContent($content)->update($request->all());
    }

    /**
     * Update content.
     *
     * @param AdminContentRequest $request
     * @param $id
     * @return void
     * @throws \Exception
     */
    public function update(AdminContentRequest $request, $id)
    {
        $content = Content::findOrFail($id);
        $this->contentService->setContent($content)->update($request->all());
    }

    /**
     * Delete a content item.
     *
     * @param $id
     * @return void
     */
    public function delete($id)
    {
        Content::findOrFail($id)->delete();
    }
}
