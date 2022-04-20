<?php

namespace Rhf\Modules\Product\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\PromotedProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Rhf\Modules\Product\Services\ProductImageFileService;

class PromotedProduct extends Model
{
    use HasFactory;

    protected $table = 'promoted_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'placement_slug',
        'name',
        'image',
        'active',
        'type',
        'value',
        'video_url',
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
        'placement_slug',
        'name',
        'active',
        'type',
        'value',
        'video_url',
    ];


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return PromotedProductFactory::new();
    }

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
    public function getImage()
    {
        $fileService = new ProductImageFileService();
        return $fileService->getPublicUrl($this);
    }
}
