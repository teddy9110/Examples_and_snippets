<?php

namespace Rhf\Modules\Subscription\Services;

use Closure;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

class DirectDebitApiService
{
    public const TYPE_NEW_CONTRACT_SIGNUP = 'new_contract_signup';
    public const TYPE_DEFAULTED_CONTRACT_SIGNUP = 'defaulted_contract_signup';

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    public function __construct()
    {
        $apiToken = config('app.subscriptions_api_token');
        $this->httpClient = new Client([
            'base_uri' => config('app.subscriptions_api_url'),
            'headers' => [
                'Authorization' => "Bearer {$apiToken}",
            ]
        ]);
    }

    /**
     * DIRECT DEBITS
     */

    public function getDirectDebitsForUser(int $userId)
    {
        $url = '/direct-debits' . $this->getQueryString([
            'appUserId' => $userId,
        ]);
        $request = $this->httpClient->get($url);
        $responseContents = $this->getResponseContents($request);
        return $responseContents;
    }

    public function getDirectDebit(int $id)
    {
        $url = '/direct-debits/' . $id;
        $request = $this->httpClient->get($url);
        $responseContents = $this->getResponseContents($request);
        return $responseContents;
    }

    public function cancelDirectDebit(int $id, array $params)
    {
        return $this->withErrorHandling(function () use ($id, $params) {
            $url = '/direct-debits/' . $id . '/cancel';
            $request = $this->httpClient->post($url, [
                RequestOptions::JSON => $params
            ]);
            return $this->getResponseContents($request);
        });
    }

    public function setAdvanceCancellation(int $id, array $params)
    {
        return $this->withErrorHandling(function () use ($id, $params) {
            $url = '/direct-debits/' . $id . '/advance-cancellation';
            $request = $this->httpClient->post($url, [
                RequestOptions::JSON => $params
            ]);
            return $this->getResponseContents($request);
        });
    }

    /**
     * DIRECT DEBIT CANCELLATIONS
     */
    public function discardDirectDebitCancellation(int $id, array $params)
    {
        return $this->withErrorHandling(function () use ($id, $params) {
            $url = '/direct-debit-cancellations/' . $id . '/discard';
            $request = $this->httpClient->post($url, [
                RequestOptions::JSON => $params
            ]);
            return $this->getResponseContents($request);
        });
    }

    /**
     * DIRECT DEBIT SIGNUPS
     */

    public function generateUkSignupUrl(string $reference)
    {
        return config('app.direct_debit_signup_url') . '?reference=' . $reference;
    }

    public function getDirectDebitSignups(array $params)
    {
        $url = '/direct-debit-signups' . $this->getQueryString($params);
        $request = $this->httpClient->get($url);
        $response = $request->getBody();
        $responseContents = json_decode($response->getContents(), true);
        return $responseContents;
    }

    public function createDirectDebitSignup(string $type, string $email, ?int $userId = null)
    {
        $url = '/direct-debit-signups';
        $body = [
            'type' => $type,
            'email' => $email,
        ];
        if (!is_null($userId)) {
            $body['app_user_id'] = $userId;
        }

        $request = $this->httpClient->post($url, [
            RequestOptions::JSON => $body,
        ]);

        if ($request->getStatusCode() < 200 || $request->getStatusCode() > 299) {
            return null;
        }
        $response = $request->getBody();
        $responseContents = json_decode($response->getContents(), true);
        return $responseContents['data'];
    }

    /**
     * HELPERS
     */

    private function getQueryString(array $params)
    {

        $queryString = collect($params)
            ->filter(fn ($item) => !is_null($item))
            ->keys()
            ->map(function ($item) use ($params) {
                if ($item == 'filters') {
                    return collect($params[$item])
                        ->filter(fn ($k) => !is_null($k))
                        ->keys()
                        ->map(function ($key) use ($params, $item) {
                            return 'filters[' . $key . ']=' . $this->urlEncode($params[$item][$key]);
                        })
                        ->join('&');
                }
                return $item . '=' . $this->urlEncode($params[$item]);
            })
            ->join('&');

        return strlen($queryString) > 0 ? '?' . $queryString : '';
    }

    private function urlEncode(string $str)
    {
        return str_replace('+', '%2B', urlencode($str));
    }

    private function withErrorHandling(Closure $cb)
    {
        try {
            return $cb();
        } catch (Exception $e) {
            if ($e instanceof ClientException) {
                return $this->getResponseContents($e->getResponse());
            }
            return [
                'statusCode' => 500,
                'error' => 'Unknown Direct Debit API error occurred.',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function getResponseContents($reqRes)
    {
        return json_decode($reqRes->getBody()->getContents(), true);
    }
}
