<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Tags\Models\Tag;
use Rhf\Modules\Tags\Requests\TagsRequest;
use Rhf\Modules\Tags\Requests\UserTagRequest;
use Rhf\Modules\Tags\Resources\TagsResource;
use Rhf\Modules\User\Models\User;

class AdminTagController extends Controller
{
    public function index()
    {
        return TagsResource::collection(Tag::get());
    }

    /**
     * Create a tag
     *
     * @param TagsRequest $request
     * @return TagsResource
     */
    public function createTag(TagsRequest $request)
    {
        $tag = Tag::create([
            'name' => $request->input('name'),
            'type' => $request->input('type')
        ]);
        return new TagsResource($tag);
    }

    /**
     * Toggle tags for user, values present are attached/detached
     * depending if they exist in the table
     *
     * @param $userId
     * @param UserTagRequest $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function toggleTagsOnUser($userId, UserTagRequest $request)
    {
        try {
            $user = User::findOrFail($userId);
            $user->tags()->toggle($request->tags);

            return TagsResource::collection($user->tags);
        } catch (Exception $e) {
            throw new FitnessBadRequestException('Error: Unable to update User tags. Please try again later');
        }
    }

    /**
     * return a list of tags a user is linked with
     *
     * @param $userId
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function userTags($userId)
    {
        $user = User::findOrFail($userId);
        return TagsResource::collection($user->tags);
    }

    /**
     * Delete a tag
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function deleteTag($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return response()->noContent();
    }
}
