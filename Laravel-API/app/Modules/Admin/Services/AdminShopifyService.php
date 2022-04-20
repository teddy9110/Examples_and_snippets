<?php

namespace Rhf\Modules\Admin\Services;

use Illuminate\Http\UploadedFile;
use Rhf\Modules\Shopify\Models\ShopifyPromotedProducts;

class AdminShopifyService
{
    private $adminShopifyPromotedProductImage;

    public function __construct(AdminShopifyPromotedProductImageService $adminShopifyPromotedProductImageService)
    {
        $this->adminShopifyPromotedProductImage = $adminShopifyPromotedProductImageService;
    }

    /**
     * Get a paginated list of Products
     *
     * @param array $pagination
     * @return mixed
     */
    public function paginate(array $pagination)
    {
        return ShopifyPromotedProducts::paginate($pagination['per_page'], '*', 'page', $pagination['page']);
    }

    /**
     * Create the product
     *
     * @param array $data
     * @param UploadedFile $websiteImage
     * @param UploadedFile $mobileImage
     * @return mixed
     * @throws \Exception
     */
    public function create(array $data, UploadedFile $websiteImage, UploadedFile $mobileImage)
    {
        unset($data['website_image']);
        unset($data['mobile_image']);

        $data['active'] = isset($data['active']) ? (bool)$data['active'] : false;
        $data['website_only'] = isset($data['website_only']) ? (bool)$data['website_only'] : false;
        $data['shopify_product_id'] = (int)$data['shopify_product_id'];

        $shopifyPromotedProduct = ShopifyPromotedProducts::create($data);
        $this->updateProductImage($websiteImage, $shopifyPromotedProduct, 'website_image');
        $this->updateProductImage($mobileImage, $shopifyPromotedProduct, 'mobile_image');

        return $shopifyPromotedProduct;
    }

    /**
     *
     * Update the product
     *
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function update($id, array $data)
    {
        $product = $this->getProduct($id);

        $shopifyPromotedProduct = new ShopifyPromotedProducts();
        foreach ($shopifyPromotedProduct->getPlainKeys() as $key) {
            $product->update([
                $key => $data[$key]
            ]);
        }
        return $product;
    }

    /**
     * Update the Product Image
     *
     * @param UploadedFile $image
     * @param $product
     * @param $type
     * @throws \Exception
     */
    public function updateProductImage(UploadedFile $image, $product, $type): void
    {
        $shopifyProductImage = $this->adminShopifyPromotedProductImage->storeImage($image, $product->id);
        $product->update([
            $type => $shopifyProductImage['path'] . '/' . $shopifyProductImage['file_name']
        ]);
    }

    /**
     * Get the product
     *
     * @param int $id
     * @return mixed
     */
    public function getProduct(int $id)
    {
        return ShopifyPromotedProducts::findOrFail($id);
    }

    /**
     * Delete existing image if an update is triggered
     * @param $image
     */
    public function deleteExistingImage($image)
    {
        $this->adminShopifyPromotedProductImage->deleteImage($image);
    }
}
