<?php

namespace Rhf\Modules\Product\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Product\Requests\PromotedProductRequest;
use Rhf\Modules\Product\Resources\PromotedProductResource;
use Rhf\Modules\Product\Models\PromotedProduct;
use Rhf\Modules\Product\Models\PromotedProductPlacement;

class ProductController extends Controller
{
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
     * Get paginated promoted products
     *
     * @param PromotedProductRequest $request
     * @return AnonymousResourceCollection
     */
    public function promoted(PromotedProductRequest $request)
    {
        $perPage = intval($request->input('per_page', 20));
        $orderBy = $request->input('order_by', 'id');
        $orderDirection = $request->input('order_direction', 'asc');
        $filterBy = $request->input('filter_by');
        $filterValue = $request->input('filter');
        $placement = $request->input('placement');
        $content = $request->input('content');

        $query = PromotedProduct::query()
            ->where('active', true)
            ->orderBy($orderBy, $orderDirection);

        if (isset($placement)) {
            $query->where('placement_slug', $placement);
        }

        if ($filterBy && $filterValue) {
            $query->where($filterBy, 'like', "%$filterValue%");
        }

        $products = $query->paginate($perPage);
        return PromotedProductResource::collection($products);
    }
}
