<?php

namespace Rhf\Modules\Admin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Product\Resources\PromotedProductResource;
use Rhf\Modules\Workout\Resources\RelatedVideoResource;

class AdminWorkoutResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'workout_flow' => $this->workout_flow,
            'title' => $this->title,
            'descriptive_title' => $this->descriptive_title,
            'content' => $this->content,
            'order' => $this->order,
            'thumbnail' => $this->thumbnail,
            'content_thumbnail' => $this->content_thumbnail,
            'video' => $this->video,
            'youtube' => $this->youtube,
            'content_video' => $this->content_video,
            'youtube_flow_thumbnail' => $this->youtube_flow_thumbnail,
            'standard_flow_thumbnail' => $this->standard_flow_thumbnail,
            'youtube_flow_thumbnail_url' => $this->youtube_flow_thumbnail_url,
            'standard_flow_thumbnail_url' => $this->standard_flow_thumbnail_url,
            'frequency' => new AdminExerciseFrequencyResource($this->whenLoaded('frequency')),
            'level' => new AdminExerciseLevelResource($this->whenLoaded('level')),
            'location' => new AdminExerciseLocationResource($this->whenLoaded('location')),
            'rounds' => AdminWorkoutRoundResource::collection($this->whenLoaded('rounds')),
            'promoted_product' => new PromotedProductResource($this->whenLoaded('promotedProduct')),
            'related_videos' => RelatedVideoResource::collection($this->whenLoaded('relatedVideos')),
            'duration' => $this->duration,
        ];
    }
}
