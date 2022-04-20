<?php

namespace Rhf\Modules\Workout\Decorators;

use Rhf\Modules\Workout\Services\WorkoutFileService;

trait HasThumbnailAndVideo
{
    public function getVideoAttribute()
    {
        if (is_null($this->getAttribute('content_video'))) {
            return null;
        }
        $service = new WorkoutFileService();
        $service->setFileAttribute('content_video');
        return $service->getPublicUrl($this);
    }

    public function getThumbnailAttribute()
    {
        if (is_null($this->getAttribute('content_thumbnail'))) {
            return null;
        }
        $service = new WorkoutFileService();
        $service->setFileAttribute('content_thumbnail');
        return $service->getPublicUrl($this);
    }
}
