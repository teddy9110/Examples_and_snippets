<?php

namespace Rhf\Modules\Workout\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Rhf\Modules\Exercise\Models\ExerciseFrequency;
use Rhf\Modules\Exercise\Models\ExerciseLevel;
use Rhf\Modules\Exercise\Models\ExerciseLocation;
use Rhf\Modules\Product\Models\PromotedProduct;
use Rhf\Modules\Video\Models\RelatedVideo;
use Rhf\Modules\Workout\Decorators\HasThumbnailAndVideo;
use Rhf\Modules\Workout\Services\WorkoutFileService;

class Workout extends Model
{
    use HasThumbnailAndVideo;
    use SoftDeletes;

    public const TYPE_REST = 'rest';
    public const TYPE_WORKOUT = 'workout';

    public const FLOW_YOUTUBE = 'youtube';
    public const FLOW_STANDARD = 'standard';

    protected $table = 'exercise_category';

    protected $fillable = [
        'title',
        'descriptive_title',
        'content',
        'exercise_frequency_id',
        'exercise_level_id',
        'exercise_location_id',
        'order',
        'thumbnail',
        'content_thumbnail',
        'video',
        'youtube',
        'youtube_flow_thumbnail',
        'standard_flow_thumbnail',
        'content_video',
        'duration',
        'workout_flow',
    ];

    protected $hidden = [];

    public function frequency()
    {
        return $this->belongsTo(ExerciseFrequency::class, 'exercise_frequency_id');
    }

    public function level()
    {
        return $this->belongsTo(ExerciseLevel::class, 'exercise_level_id');
    }

    public function location()
    {
        return $this->belongsTo(ExerciseLocation::class, 'exercise_location_id');
    }

    public function rounds()
    {
        return $this->hasMany(Round::class, 'workout_id')->orderBy('order');
    }

    public function promotedProduct()
    {
        return $this->belongsTo(PromotedProduct::class, 'promoted_product_id');
    }

    public function relatedVideos()
    {
        return $this->morphToMany(
            RelatedVideo::class,
            'related',
            'pivot_related_videos',
        )
            ->where('active', 1)
            ->orderBy('order');
    }

    public function getYoutubeFlowThumbnailUrlAttribute()
    {
        if (is_null($this->getAttribute('youtube_flow_thumbnail'))) {
            return null;
        }
        $service = new WorkoutFileService();
        $service->setFileAttribute('youtube_flow_thumbnail');
        return $service->getPublicUrl($this);
    }

    public function getStandardFlowThumbnailUrlAttribute()
    {
        if (is_null($this->getAttribute('standard_flow_thumbnail'))) {
            return null;
        }
        $service = new WorkoutFileService();
        $service->setFileAttribute('standard_flow_thumbnail');
        return $service->getPublicUrl($this);
    }
}
