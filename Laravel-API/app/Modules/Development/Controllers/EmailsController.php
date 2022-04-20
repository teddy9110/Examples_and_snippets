<?php

namespace Rhf\Modules\Development\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\Subscription\Services\DirectDebitApiService;
use Rhf\Modules\System\Services\SendInBlueService;
use Rhf\Modules\User\Models\User;

class EmailController extends Controller
{
    public function __construct(SendInBlueService $sendInBlueService)
    {
        $this->sendInBlueService = $sendInBlueService;
    }

    public function sendSendinBlueEmails(User $user)
    {
        $this->sendPasswordResetEmail($user);
        $this->sendDirectDebitNotificationEmail($user);
        $this->sendAnnualReminderEmail($user);
        $this->directDebitSignupNotification($user);
        return response()->noContent();
    }

    public function sendPasswordResetEmail($user)
    {
        $url = secure_url(route('password.send-reset-email', ['token' => random_int(10000000, 999999999)], false));
        $this->sendInBlueService->sendPasswordResetEmail(
            [
                'name' => $user->name,
                'email' => $user->email,
            ],
            [
                'url' => $url,
            ]
        );
    }

    public function sendDirectDebitNotificationEmail($user)
    {
        $this->sendInBlueService->sendDirectDebitNotificationEmail(
            [
                'name' => $user->name,
                'email' => $user->email,
            ],
            [
                'name' => $user->name,
            ]
        );
    }

    public function sendAnnualReminderEmail($user)
    {
        $this->sendInBlueService->sendAnnualRenewalEmail(
            [
                'name' => $user->name,
                'email' => $user->email,
            ],
            [
                'email' => $user->email,
            ]
        );
    }

    public function directDebitSignupNotification($user)
    {
        $directDebitApiService = new DirectDebitApiService();
        $ukSignupUrl = $directDebitApiService->generateUkSignupUrl(random_int(10000000, 999999999));
        $this->sendInBlueService->sendWelcomeEmail(
            [
                'name' => $user->name,
                'email' => $user->email,
            ],
            [
                'ukSignupUrl' => $ukSignupUrl,
            ]
        );
    }

    public function renewalWelcomeEmail($user)
    {
        $this->sendInBlueService->sendRenewalWelcomeEmail(
            [
                'name' => $user->name,
                'email' => $user->email,
            ]
        );
    }
}
