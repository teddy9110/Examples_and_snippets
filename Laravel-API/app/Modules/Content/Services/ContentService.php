<?php

namespace Rhf\Modules\Content\Services;

use Rhf\Modules\Content\Models\Category;
use Rhf\Modules\Content\Models\Content;

class ContentService
{
    protected $content;

    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /**
     * Retrieve the child content by a category through all levels.
     */
    public static function allByCategory($category, $content = null)
    {
        if (!$content) {
            $content = collect();
        }
        $items = Content::where('category_id', '=', $category->id)->get();
        if ($items->count()) {
            if ($content->count()) {
                $content = $content->merge($items);
                $content = $content->unique();
            } else {
                $content = $items;
                $content = $content->unique();
            }

            // Loop all categories
            foreach ($category->children()->get() as $child) {
                $childContent = ContentService::allByCategory($child, $content);
                $content = $content->merge($childContent->count() ? $childContent : collect());
                $content = $content->unique();
            }
            return $content;
        } else {
            foreach ($category->children()->get() as $child) {
                $childContent = ContentService::allByCategory($child, $content);
                if ($childContent->count()) {
                    $content = $content->merge($childContent);
                    $content = $content->unique();
                }
            }
            return $content;
        }
    }

    /**
     * Retrieve the child categories through all levels with their depth as an integer.
     *
     * @return array
     */
    public static function categoryNav($parent = 0, $categories = null, $depth = '')
    {
        if (!$categories) {
            $categories = array();
        }
        $items = Category::where('parent_id', '=', $parent)->get();
        if ($items->count()) {
            foreach ($items as $item) {
                $category = $item;
                $category->depth = $depth;
                $categories[] = $category;
                $categories = ContentService::categoryNav($item->id, $categories, $depth . ' - ');
            }
        }
        return $categories;
    }

    /**
     * Return filtered list of items.
     *
     * @return query
     */
    public static function filtered()
    {
        $contentModel = new Content();

        // Check for relevant filter conditions and apply to query object
        if (request()->get('search')['value'] != null) {
            $contentModel = $contentModel->search(request()->get('search'));
        }

        // Check for order by
        if (request()->has('order')) {
            $order = request()->get('columns')[request()->get('order')[0]['column']]['name'];
            $direction = request()->get('order')[0]['dir'];
            $contentModel = $contentModel->orderBy($order, $direction);
        }

        return $contentModel;
    }

    /**
     * Process an image.
     *
     * @param Illuminate\Http\UploadedFile
     * @return string
     */
    public function processImage($file)
    {
        // get current time and append the upload file extension to it,
        // then put that name to $photoName variable.
        $filename = time() . $file->getClientOriginalName();

        /*
        talk the select file and move it public directory and make avatars
        folder if doesn't exsit then give it that unique name.
        */
        $file->move(public_path('images'), $filename);

        return $filename;
    }

    /**
     * Update the item.
     *
     * @param array
     * @return self
     * @throws \Exception
     */
    public function update($data)
    {
        if (isset($data['image'])) {
            if (!is_string($data['image'])) {
                $data['image'] = $this->processImage($data['image']);
            }
        } else {
            $data['image'] = $this->content->image;
        }

        // If video in the data is set, we assume its being overwritten or being set for the first time
        // if it's NULL then we can assume its a Text content upload, or an existing Video upload with no new video
        $videoContent = isset($data['video']) ? $data['video'] : null;

        // However, if content is NULL from above, we need to check if the existing Content model
        // object doesn't already have it set, as we don't want to overwrite existing content S3 URL's
        // if one already exists, and a new one isn't being uploaded
        $contentFieldExists = is_null($this->content->content);

        // Grab the video from the form and send it up to AWS, if it fails we throw back an error
        if (!is_null($videoContent) && $data['type'] == 'Video' && isset($data['video'])) {
            $contentVideoService = new ContentVideoFileService();

            // Grab the category slug, as the video will be placed in a folder named that
            $categoryId = $data['category_id'];
            $categorySlug = Category::findOrFail($categoryId)->slug;

            $uploadedVideo = $contentVideoService->createFromUpload(
                $data['video'],
                'content-videos/' . $categorySlug,
                false
            );

            $content = $uploadedVideo['path'] . '/' . $uploadedVideo['file_name'];
        } elseif (is_null($videoContent) && !is_null($contentFieldExists) && $data['type'] == 'Video') {
            // Otherwise set the content field to the existing objects content field as it is not being overwritten
            $content = $this->content->content;
        } else {
            $content = null;
        }

        $this->content->category_id     = $data['category_id'];
        $this->content->title           = $data['title'];
        // Check the type is set, should only be set on a new content
        $this->content->type            = $data['type'];
        $this->content->description     = $data['description'];
        $this->content->image           = $data['image'];
        $this->content->status          = isset($data['status']) ? $data['status'] : 1;
        $this->content->facebook_id     = isset($data['facebook_id']) ? $data['facebook_id'] : null;
        $this->content->order           = isset($data['order']) ? $data['order'] : null;
        $this->content->content         = $content;

        if (isset($data['created_at'])) {
            $this->content->created_at = $data['created_at'];
        }
        if (isset($data['updated_at'])) {
            $this->content->updated_at = $data['updated_at'];
        }

        $this->content->save();

        return $this;
    }


    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Return the item associated to the instance of the service.
     */
    public function getContent()
    {
        return isset($this->content) ? $this->content : null;
    }


    /**************************************************
    *
    * SETTERS
    *
    ***************************************************/

    /**
     * Set the content associated to the instance of the service.
     *
     * @param Content $content
     * @return self
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
        return $this;
    }
}
