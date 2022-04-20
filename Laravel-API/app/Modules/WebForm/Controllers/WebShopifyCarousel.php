<?php

namespace Rhf\Modules\WebForm\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Shopify\Models\ShopifyPromotedProducts;
use Rhf\Modules\WebForm\Resources\ShopifyCarouselResponse;

class WebShopifyCarousel extends Controller
{
    public function index()
    {
        $products = ShopifyPromotedProducts::whereActive(1)->get();
        return ShopifyCarouselResponse::collection($products);
    }
}
