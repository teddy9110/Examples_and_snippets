<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminShopifyPromotedProductRequest;
use Rhf\Modules\Admin\Resources\AdminShopifyProductResource;
use Rhf\Modules\Admin\Services\AdminShopifyService;
use Rhf\Modules\Shopify\Models\ShopifyPromotedProducts;

class AdminShopifyController extends Controller
{
    protected $adminShopifyService;

    public function __construct(AdminShopifyService $adminShopifyService)
    {
        $this->adminShopifyService = $adminShopifyService;
    }

    /**
     * Get a list of all products
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $pagination['page'] = intval($request->input('page', 1));
        $pagination['per_page'] = $request->input('limit', 20);

        $products = $this->adminShopifyService->paginate($pagination);
        return AdminShopifyProductResource::collection($products);
    }

    /**
     * Create a Shopify Promoted Product
     *
     * @param AdminShopifyPromotedProductRequest $request
     * @return AdminShopifyProductResource
     * @throws Exception
     */
    public function createPromotedProduct(AdminShopifyPromotedProductRequest $request): AdminShopifyProductResource
    {
        $create = $this->adminShopifyService->create(
            $request->validated(),
            $request->file('website_image'),
            $request->file('mobile_image')
        );
        return new AdminShopifyProductResource($create);
    }

    /**
     * Update the text components of a Shopify Promoted Product
     *
     * @param AdminShopifyPromotedProductRequest $request
     * @param $id
     */
    public function updatePromotedProduct(AdminShopifyPromotedProductRequest $request, $id)
    {
        $update = $this->adminShopifyService->update($id, $request->validated());
        return new AdminShopifyProductResource($update);
    }

    /**
     * Get the ShopifyPromoted Product
     *
     * @param $id
     * @return AdminShopifyProductResource
     */
    public function getPromotedProduct($id): AdminShopifyProductResource
    {
        $product = $this->adminShopifyService->getProduct($id);
        return new AdminShopifyProductResource($product);
    }

    /**
     * Toggle if a promoted product is active/inactive
     *
     * @param $id
     */
    public function toggleActivity($id)
    {
        $product = $this->adminShopifyService->getProduct($id);
        $product->update([
            'active' => !$product->active
        ]);
        return new AdminShopifyProductResource($product);
    }

    /**
     * Toggle a promoted product to website only or website/mobile
     *
     * @param $id
     */
    public function toggleWebsiteOnly($id)
    {
        $product = $this->adminShopifyService->getProduct($id);
        $product->update([
            'website_only' => !$product->website_only
        ]);
        return new AdminShopifyProductResource($product);
    }

    /**
     * Edit an image and delete the existing image
     *
     * @param Request $request
     * @param $id
     */
    public function editImage(Request $request, $id)
    {
        $array = explode('/', $request->getPathInfo());
        $type = end($array) . '_image';
        $image = $request->file($type);
        $product = $this->adminShopifyService->getProduct($id);
        $this->adminShopifyService->deleteExistingImage($product->{$type});
        $this->adminShopifyService->updateProductImage($image, $product, $type);
        return new AdminShopifyProductResource($product);
    }

    /**
     * Delete a Shopify Promoted Product
     *
     * @param $id
     */
    public function deletePromotedProduct($id)
    {
        try {
            $product = ShopifyPromotedProducts::findOrFail($id);
            $product->delete();
            return response()->noContent();
        } catch (Exception $e) {
            throw new Exception(
                'Sorry, unable to delete this record. Reason: ' . $e->getMessage(),
                $e->getCode()
            );
        }
    }
}
