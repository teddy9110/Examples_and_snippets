<?php

namespace Rhf\Modules\Subscription\Controllers;

use Exception;
use Illuminate\Http\Request;
use Rhf\Http\Controllers\Controller;
use ReceiptValidator\iTunes\AbstractResponse;
use ReceiptValidator\iTunes\Validator as iTunesValidator;
use Rhf\Modules\Subscription\Services\AppleSubscriptionService;

use function Sentry\captureMessage;

class ReceiptVerificationController extends Controller
{
    private $appleSubscriptionService;

    public function __construct(AppleSubscriptionService $appleService)
    {
        $this->appleSubscriptionService = $appleService;
    }

    public function appleValidation(Request $request)
    {
        $this->validate($request, [
            'receiptData' => 'required|string',
        ]);

        $receiptBase64Data = $request->input('receiptData');
        $response = null;

        //TODO: need to use ENV to set this URL
        if (config('services.apple.endpoint') === 'production') {
            $endpoint = iTunesValidator::ENDPOINT_PRODUCTION;
        } else {
            $endpoint = iTunesValidator::ENDPOINT_SANDBOX;
        }
        $validator = new iTunesValidator($endpoint);

        if (!is_null(config('services.apple.secret'))) {
            $validator->setSharedSecret(config('services.apple.secret'));
        }

        try {
            $response = $validator->setReceiptData($receiptBase64Data)->validate();
        } catch (Exception $e) {
            throw new Exception('Error: ' . $e->getMessage());
        }

        if ($response instanceof AbstractResponse && $response->isValid()) {
            $purchaseData = $this->appleSubscriptionService->transformReceiptData($response);
            $this->appleSubscriptionService->setUser(auth('api')->user());
            $this->appleSubscriptionService->updateUser($purchaseData);
            $this->appleSubscriptionService->createAppleSubscription($purchaseData);

            return response()->json([
                'success' => true
            ], 200);
        } else {
            //Capture Error in Sentry
            captureMessage(
                'Error Code: ' . $response->getResultCode() . ', ' .
                $this->appleSubscriptionService->statusCode($response->getResultCode())
            );
            return response()->json([
                'success' => false,
                'reason' => $this->appleSubscriptionService->statusCode($response->getResultCode())
            ], 400);
        }
    }
}
