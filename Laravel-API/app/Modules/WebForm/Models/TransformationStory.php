<?php

namespace Rhf\Modules\WebForm\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\WebForm\Services\TransformationService;

class TransformationStory extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'weight_loss',
        'start_weight',
        'current_weight',
        'story',
        'before_photo',
        'after_photo',
        'marketing_accepted',
        'remain_anonymous',
    ];

    protected $casts = [
        'marketing_accepted' => 'boolean',
        'remain_anonymous' => 'boolean'
    ];

    public function getTransformationImage($image)
    {
        $transformationService = new TransformationService($this);
        return $transformationService->getTransformationImage($image);
    }
}
