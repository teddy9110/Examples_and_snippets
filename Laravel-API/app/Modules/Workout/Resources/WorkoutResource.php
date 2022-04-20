<?php

namespace Rhf\Modules\Workout\Resources;

use Rhf\Modules\Workout\Models\Workout;
use Rhf\Modules\Product\Models\PromotedProduct;
use Illuminate\Http\Resources\Json\JsonResource;
use Rhf\Modules\Product\Resources\PromotedProductResource;

class WorkoutResource extends JsonResource
{
    public function toArray($request)
    {
        if ($this->type && $this->type === Workout::TYPE_REST) {
            return [
                'id' => $this->id ?? -1,
                'type' => $this->type,
                'title' => $this->title ?? 'Rest',
            ];
        }

        $promotedProductId = $this->promoted_product_id;
        if (api_version() == 20211217 && feature_enabled('workouts_v3') && is_null($promotedProductId)) {
            $promotedProductId = config('app.gym_workout_bundle_id');
        }

        return [
            'id' => $this->id,
            'type' => Workout::TYPE_WORKOUT,
            'title' => $this->title,
            'content' => $this->content,
            'order' => $this->order,
            'thumbnail' => $this->thumbnail,
            'video' => $this->video,
            'youtube' => $this->youtube,
            'youtube_thumbnail' => $this->youtube_flow_thumbnail_url,
            'workout_article_id' => config('app.workout_article_id'),
            'duration' => $this->duration,
            'rounds' => RoundResource::collection($this->whenLoaded('rounds')),
            'related_videos' => RelatedVideoResource::collection($this->whenLoaded('relatedVideos')),
            'promoted_product' => $this->resource->relationLoaded('promotedProduct') ?
                new PromotedProductResource(PromotedProduct::where('id', $promotedProductId)->first()) :
                null
        ];
    }
}
