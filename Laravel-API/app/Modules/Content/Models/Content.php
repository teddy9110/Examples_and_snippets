<?php

namespace Rhf\Modules\Content\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\URL;
use Rhf\Modules\Content\Services\FacebookContentService;

class Content extends Model
{
    use SoftDeletes;

    protected $table = 'content';
    public $timestamps = true;

    protected $facebookPost; // The facebook post attached to the local post

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'type',
        'title',
        'content',
        'description',
        'image',
        'status',
        'facebook_id',
        'created_at',
        'updated_at',
        'order',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * Convert the local video to a refreshed facebook video url.
     *
     * @return string
     */
    public function getContent()
    {
        if (strpos($this->type, 'facebook') !== false || $this->facebook_id != null) {
            if (!$this->facebookPost) {
                $facebookContentService = new FacebookContentService();
                $this->facebookPost = $facebookContentService->byId($this->facebook_id);
            }

            // Return where possible starting with source
            if (isset($this->facebookPost->source)) {
                return $this->facebookPost->source;
            } elseif (isset($this->facebookPost['source'])) {
                return $this->facebookPost['source'];
            } elseif (isset($this->facebookPost['video'])) {
                return $this->facebookPost['video'];
            } elseif (isset($this->facebookPost->video)) {
                return $this->facebookPost->video;
            }
        }
        return $this->content;
    }

    /**
     * Return the image associated to the content object.
     *
     * @return string
     */
    public function getImage()
    {
        if (strpos($this->type, 'facebook') !== false || strpos($this->image, 'http') !== false) {
            return $this->image;
        } else {
            return URL::to('images') . '/' . $this->image;
        }
    }


    /*
    *
    * RELATIONSHIPS
    *
    */

    /**
     * Relation to exercise level.
     */
    public function category()
    {
        return $this->belongsTo('Rhf\Modules\Content\Models\Category', 'category_id', 'id');
    }


    /*
    *
    * CUSTOM QUERY SCOPES
    *
    */

    /**
     * Filter by category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param (int) category ID
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $category_id)
    {
        return $query->where('category_id', '=', $category_id);
    }

    /**
     * Filter by facebook only content
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsFacebook($query)
    {
        return $query->where('facebook_id', '!=', null);
    }

    /**
     * Filter result set by keyword search
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('title', 'LIKE', '%' . $term['value'] . '%')
                ->orWhere('content', 'LIKE', '%' . $term['value'] . '%');
        });
    }
}
