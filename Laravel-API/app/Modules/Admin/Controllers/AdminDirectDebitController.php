<?php

namespace Rhf\Modules\Admin\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Rhf\Http\Controllers\Controller;
use Rhf\Mail\WelcomeEmail;
use Rhf\Modules\Admin\Requests\AdminCreateDirectDebitSignupRequest;
use Rhf\Modules\Admin\Requests\AdminDirectDebitSignupsRequest;
use Rhf\Modules\Admin\Requests\AdminDirectDebitWelcomeEmailRequest;
use Rhf\Modules\Subscription\Services\DirectDebitApiService;
use Rhf\Modules\User\Models\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AdminDirectDebitController extends Controller
{
    /**
     * @var DirectDebitApiService
     */
    private $directDebitApiService;

    public function __construct(
        DirectDebitApiService $directDebitApiService
    ) {
        $this->directDebitApiService = $directDebitApiService;
    }

    /**
     * DIRECT DEBITS
     */

    public function find(Request $request)
    {
        $validated = $request->validate([
            'appUserId' => 'required|numeric',
        ]);

        $directDebits = $this->directDebitApiService->getDirectDebitsForUser($validated['appUserId']);

        return response()->json($directDebits);
    }

    public function findOneById(int $id)
    {
        $directDebit = $this->directDebitApiService->getDirectDebit($id);
        return response()->json($directDebit);
    }

    public function cancelDirectDebit(Request $request, int $id)
    {
        $validated = $request->validate([
            'cancellationType' => 'required|string',
            'cancellationReason' => 'sometimes|string',
        ]);
        $validated['actionedBy'] = $this->getActionedBy();

        $responseData = $this->directDebitApiService->cancelDirectDebit($id, $validated);

        return response()->json($responseData, $responseData['statusCode'] ?? 200);
    }

    public function setDirectDebitAdvanceCancellation(Request $request, int $id)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d',
        ]);
        $validated['actionedBy'] = $this->getActionedBy();

        $responseData = $this->directDebitApiService->setAdvanceCancellation($id, $validated);

        return response()->json($responseData, $responseData['statusCode'] ?? 200);
    }

    /**
     * DIRECT DEBIT CANCELLATIONS
     */

    public function discardDirectDebitCancellation(Request $request, int $id)
    {
        $validated = $request->validate([
            'discardReason' => 'sometimes|string',
        ]);
        $validated['actionedBy'] = $this->getActionedBy();

        $responseData = $this->directDebitApiService->discardDirectDebitCancellation($id, $validated);

        return response()->json($responseData, $responseData['statusCode'] ?? 200);
    }

    /**
     * DIRECT DEBIT SIGNUPS
     */

    public function getDirectDebitSignups(AdminDirectDebitSignupsRequest $request)
    {
        $params = [
            'page' => $request->input('page'),
            'pageSize' => $request->input('page_size'),
            'filters' => $request->input('filter'),
        ];

        $signupData = $this->directDebitApiService->getDirectDebitSignups($params);

        return response()->json($signupData);
    }

    public function createDirectDebitSignup(AdminCreateDirectDebitSignupRequest $request)
    {
        $type = $request->json('type');
        switch ($type) {
            case DirectDebitApiService::TYPE_NEW_CONTRACT_SIGNUP:
                $email = $request->json('email');
                break;
            case DirectDebitApiService::TYPE_DEFAULTED_CONTRACT_SIGNUP:
                $userId = $request->json('user_id');
                $email = User::findOrFail($userId)->email;
                break;
        }

        $signupData = $this->directDebitApiService->createDirectDebitSignup($type, $email, $userId ?? null);

        if (is_null($signupData)) {
            throw new BadRequestHttpException('Signup could not be created.');
        }

        $ukSignupUrl = $this->directDebitApiService->generateUkSignupUrl($signupData['payer_reference']);
        if ($request->json('send')) {
            $this->sendWelcomeEmail($email, $ukSignupUrl);
        }

        return response()->json($signupData);
    }

    public function resendWelcomeEmail(AdminDirectDebitWelcomeEmailRequest $request)
    {
        $email = $request->json('email');
        $ukSignupUrl = $this->directDebitApiService->generateUkSignupUrl($request->json('reference'));
        $this->sendWelcomeEmail($email, $ukSignupUrl);
        return response()->noContent();
    }

    private function sendWelcomeEmail(string $email, string $ukSignupUrl)
    {
        Mail::to($email)->queue(new WelcomeEmail(
            $email ?? 'New User',
            now()->toDateString(),
            $ukSignupUrl
        ));
    }

    private function getActionedBy()
    {
        $user = auth()->user();
        return [
            'name' => $user->name,
            'externalUserId' => $user->id,
        ];
    }
}
