<?php

namespace Rhf\Modules\Shopify\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rhf\Modules\Admin\Services\AdminShopifyPromotedProductImageService;

class ShopifyPromotedProducts extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'website_image',
        'mobile_image',
        'active',
        'website_only',
        'shopify_product_id',
        'shopify_product_type',
    ];

    protected $casts = [
        'active' => 'boolean',
        'website_only' => 'boolean'
    ];

    protected $plainKeys = [
        'title',
        'active',
        'website_only',
        'shopify_product_id',
        'shopify_product_type',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return ShopifyPromotedProductsFactory::new();
    }

    public function getImage($image)
    {
        $adminShopifyImageService = new AdminShopifyPromotedProductImageService($this);
        return $adminShopifyImageService->getImage($image);
    }

    public function getPlainKeys()
    {
        return $this->plainKeys;
    }
}
