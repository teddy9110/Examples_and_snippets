<?php

namespace Rhf\Modules\Content\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Rhf\Modules\Content\Services\ContentService;

class Category extends Model
{
    use SoftDeletes;
    use HasSlug;

    protected $table = 'content_category';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];


    /*
    *
    * RELATIONSHIPS
    *
    */

    /**
     * Relation to child content.
     */
    public function content()
    {
        return $this->hasMany('Rhf\Modules\Content\Models\Content', 'category_id', 'id');
    }

    /**
     * Relation to parent category.
     */
    public function parent()
    {
        return $this->belongsTo('Rhf\Modules\Content\Models\Category', 'parent_id', 'id');
    }

    /**
     * Relation to child categories.
     */
    public function children()
    {
        return $this->hasMany('Rhf\Modules\Content\Models\Category', 'parent_id', 'id');
    }

    /*
    *
    * ADDITIONAL METHODS
    *
    */

    /**
     * Retrieve all child content including nested.
     */
    public function allChildContent()
    {
        return ContentService::allByCategory($this);
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Recursively delete all content and children.
     */
    public function fullDelete()
    {
        // Sort out content
        foreach ($this->content()->get() as $content) {
            $content->delete();
        }

        // Sort out child categories
        if ($this->category()->count() > 0) {
            foreach ($this->category()->get() as $category) {
                $category->fullDelete();
            }
        }

        $category->delete();
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }
}
