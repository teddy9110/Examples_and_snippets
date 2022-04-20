<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Content\Services\ContentVideoFileService;

class AdminContentDetailedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        /** @var ContentVideoFileService $contentVideoFileService */
        $contentVideoFileService = app(ContentVideoFileService::class);

        $data = [
            'id' => $this->id,
            'category' => new AdminCategoryResource($this->category()->first()),
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->getContent(),
            'description' => $this->description,
            'image' => $this->image != '' ? $this->getImage() : '',
            'created_at' => $this->created_at->format('Y-m-d'),
            'facebook_id' => $this->facebook_id,
            'order' => $this->order
        ];

        if ($this->facebook_id == null && $this->type != 'Text') {
            // If the Facebook ID field is null , and the type is not Text, then we can assume
            // this is a video is not hosted on Facebook, so generate a signed storage URL
            $data['content'] = $contentVideoFileService->getPublicUrl($this->load('category'));
        } else {
            // Otherwise assume it is a video stored on Facebook or a Text post so get the content
            $data['content'] = $this->getContent();
        }

        return $data;
    }
}
