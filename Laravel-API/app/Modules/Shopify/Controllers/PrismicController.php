<?php

namespace Rhf\Modules\Shopify\Controllers;

use GuzzleHttp\Client;
use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Shopify\Resources\PromotedProductResource;
use Rhf\Modules\Shopify\Resources\SuggestedProductResource;

use function collect;

class PrismicController extends Controller
{
    public function suggested()
    {
        $query = "promoted_products";
        $response = $this->getUrlData($query);
        $results = collect($response->results[0]->data->products);
        return SuggestedProductResource::collection($results);
    }

    public function promoted()
    {
        $query = "shop_landing";
        $response = $this->getUrlData($query);
        $results = collect($response->results[0]->data->banner)
            ->reject(function ($value) {
                return $value->shopify_type[0]->text === 'url';
            });

        return PromotedProductResource::collection($results);
    }

    private function getUrlData($query)
    {
        $url = config('app.prismic_url') . '/api/v2/documents/search?ref=' .
            $this->getPrismicRef() .
            "&q=[[at(document.type,\"{$query}\")]]#format=json";

        $body = $this->getBodyResponse($url);
        return $body;
    }

    private function getPrismicRef()
    {
        $url = config('app.prismic_url') . '/api/v2/';
        $body = $this->getBodyResponse($url);
        return $body->refs[0]->ref;
    }

    private function getBodyResponse(string $url)
    {
        $client = new Client();
        $res = $client->get($url);
        return json_decode($res->getBody()->getContents());
    }
}
