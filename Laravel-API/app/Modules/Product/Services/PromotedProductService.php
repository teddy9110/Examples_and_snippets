<?php

namespace Rhf\Modules\Product\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Rhf\Modules\Product\Models\PromotedProduct;

class PromotedProductService
{
    /**
     * @var PromotedProduct
     */
    protected $promotedProduct = null;

    /**
     * Creates a promoted product image
     *
     * @param UploadedFile $image
     * @return void
     * @throws Exception
     */
    public function createPromotedProductImage(UploadedFile $image)
    {
        $fileService = new ProductImageFileService();
        $imagePath = $fileService->createFromUpload($image, "product-images", false);
        $this->getPromotedProduct()->image = $imagePath['path'] . '/' . $imagePath['file_name'];
    }

    /**
     * Deletes a promoted product image
     *
     * @return void
     * @throws Exception
     */
    public function deletePromotedProductImage()
    {
        $fileService = new ProductImageFileService();
        $fileService->delete($this->getPromotedProduct());
    }

    /**
     * Creates a promoted product
     *
     * @param array $data
     * @param UploadedFile $image
     * @return PromotedProduct
     * @throws Exception
     */
    public function createPromotedProduct(array $data, UploadedFile $image)
    {
        $promotedProduct = new PromotedProduct();

        $this->setPromotedProduct($promotedProduct);
        $this->updatePromotedProduct($data, $image);

        return $promotedProduct;
    }

    /**
     * Update a promoted product image
     *
     * @param UploadedFile $image
     * @throws Exception
     */
    public function updateProductImage(UploadedFile $image)
    {
        $this->createPromotedProductImage($image);

        // remove existing image
        if (isset($this->getPromotedProduct()->image)) {
            $count = PromotedProduct::where('image', $image)->count();

            // only delete image if not referenced elsewhere
            if ($count == 1) {
                $this->deletePromotedProductImage();
            }
        }

        $this->getPromotedProduct()->save();
    }

    /**
     * Creates a promoted product
     *
     * @param array $data
     * @param UploadedFile $image
     * @throws Exception
     */
    public function updatePromotedProduct(array $data, UploadedFile $image = null)
    {
        $promotedProduct = $this->getPromotedProduct();

        foreach ($promotedProduct->getPlainKeys() as $key) {
            if ($key == 'active') {
                $promotedProduct[$key] = isset($data[$key]) ? !!$data[$key] : false;
            } elseif (isset($data[$key])) {
                $promotedProduct[$key] = $data[$key];
            }
        }

        if (isset($image)) {
            $this->updateProductImage($image);
        }

        $promotedProduct->save();
    }

    /**
     * Return the item associated to the instance of the service.
     *
     * @return PromotedProduct
     * @throws Exception
     */
    public function getPromotedProduct()
    {
        if (!isset($this->promotedProduct)) {
            throw new Exception("The promoted product is not set.");
        }

        return $this->promotedProduct;
    }

    /**
     * Set the product associated to the instance of the service.
     *
     * @param PromotedProduct $promotedProduct
     * @return self
     */
    public function setPromotedProduct(PromotedProduct $promotedProduct)
    {
        $this->promotedProduct = $promotedProduct;
        return $this;
    }
}
