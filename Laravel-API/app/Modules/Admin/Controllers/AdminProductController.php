<?php

namespace Rhf\Modules\Admin\Controllers;

use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Admin\Requests\AdminPromotedProductImageRequest;
use Rhf\Modules\Admin\Requests\AdminPromotedProductRequest;
use Rhf\Modules\Admin\Resources\AdminPromotedProductResource;
use Rhf\Modules\Product\Models\PromotedProduct;
use Rhf\Modules\Product\Models\PromotedProductPlacement;
use Rhf\Modules\Product\Services\PromotedProductService;

class AdminProductController extends Controller
{
    private $productService;

    public function __construct(PromotedProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Get paginated promoted products
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function promotedProducts(Request $request)
    {
        $perPage = intval($request->get('per_page', 20));
        $orderBy = $request->get('order_by', 'id');
        $orderDirection = $request->get('order_direction', 'asc');
        $filterBy = $request->get('filter_by');
        $filterValue = $request->get('filter');

        $query = PromotedProduct::query()
            ->orderBy($orderBy, $orderDirection);

        if ($filterBy && $filterValue) {
            $query->where($filterBy, 'like', "%$filterValue%");
        }

        $products = $query->paginate($perPage);
        return AdminPromotedProductResource::collection($products);
    }

    /**
     * Show all promoted product placements available
     *
     * @return ResponseFactory|Response
     */
    public function placements()
    {
        return response([
            'data' => PromotedProductPlacement::all()
        ]);
    }

    /**
     * Show promoted product by id
     *
     * @param string $id
     * @return AdminPromotedProductResource
     */
    public function showPromotedProduct(string $id)
    {
        return new AdminPromotedProductResource(PromotedProduct::findOrFail($id));
    }

    /**
     * Create promoted product
     *
     * @param AdminPromotedProductRequest $request
     * @return AdminPromotedProductResource
     *
     * @throws Exception
     */
    public function createPromotedProduct(AdminPromotedProductRequest $request)
    {
        $product = $this->productService->createPromotedProduct($request->all(), $request->file('image'));
        return new AdminPromotedProductResource($product);
    }

    /**
     * Update promoted products
     * @param int $id
     * @param AdminPromotedProductRequest $request
     * @return AdminPromotedProductResource
     *
     * @throws Exception
     */
    public function updatePromotedProduct(int $id, AdminPromotedProductRequest $request)
    {
        $product = PromotedProduct::findOrFail($id);
        $this->productService->setPromotedProduct($product);

        $this->productService->updatePromotedProduct($request->all(), $request->file('image'));
        return new AdminPromotedProductResource($product);
    }

    /**
     * Delete promoted product
     *
     * @param int $id
     * @return ResponseFactory|Response
     */
    public function deletePromotedProduct(int $id)
    {
        PromotedProduct::findOrFail($id)->delete();
        return response(null, 204);
    }

    /**
     * Update the given promoted product image
     *
     * @param AdminPromotedProductImageRequest $request
     * @param $id
     *
     * @return AdminPromotedProductResource
     * @throws Exception
     */
    public function updateImage(AdminPromotedProductImageRequest $request, $id)
    {
        $product = PromotedProduct::findOrFail($id);
        $this->productService->setPromotedProduct($product);

        $this->productService->updateProductImage($request->file('image'));
        return new AdminPromotedProductResource($product);
    }
}
