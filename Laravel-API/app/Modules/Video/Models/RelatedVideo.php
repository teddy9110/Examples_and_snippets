<?php

namespace Rhf\Modules\Video\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Video\Services\RelatedVideoImageService;
use Rhf\Modules\Workout\Models\Workout;

class RelatedVideo extends Model
{
    protected $fillable = [
        'title',
        'url',
        'thumbnail',
        'single_parent'
    ];

    protected $casts = [
        'single_parent' => 'boolean',
    ];

    public function getImage($image)
    {
        $relatedVideoImageService = new RelatedVideoImageService($this);
        return $relatedVideoImageService->getImage($image);
    }

    public function workouts()
    {
        return $this->morphedByMany(
            Workout::class,
            'related',
            'pivot_related_videos',
        );
    }
}
