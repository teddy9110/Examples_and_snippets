<?php

namespace Rhf\Modules\Video\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rhf\Modules\System\Traits\Filterable;
use Rhf\Modules\Tags\Models\Tag;
use Rhf\Modules\Video\Services\VideoService;

class Video extends Model
{
    use SoftDeletes;
    use Filterable;

    protected $fillable = [
        'id',
        'title',
        'description',
        'url',
        'thumbnail',
        'view_count',
        'open_count',
        'live',
        'scheduled_date',
        'scheduled_time',
        'active',
        'order'
    ];

    public function getVideoThumbnail($image)
    {
        $videoService = new VideoService($this);
        return $videoService->getThumbnailImage($image);
    }

    /**
     * Many to many for tags
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'video_tags');
    }
}
