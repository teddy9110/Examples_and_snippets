<?php

namespace Rhf\Modules\Facebook\Models;

use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Facebook\Services\FacebookVideoThumbnailFileService;

class FacebookVideo extends Model
{
    protected $table = 'facebook_videos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'url',
        'thumbnail',
        'created_at',
        'updated_at',
        'live',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that can be directly copied over.
     *
     * @var array
     */
    protected $plainKeys = [
        'title',
        'description',
        'url',
        'live'
    ];

    /**
     * Get the plain keys.
     */
    public function getPlainKeys()
    {
        return $this->plainKeys;
    }

    /**
     * Return the image associated to the recipe.
     *
     * @return string
     */
    public function getThumbnail()
    {
        $fileService = new FacebookVideoThumbnailFileService();
        return $fileService->getPublicUrl($this);
    }
}
