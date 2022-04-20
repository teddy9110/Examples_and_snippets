<?php

namespace Rhf\Modules\System\Services;

use Exception;
use GuzzleHttp\Client;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;

class SendInBlueService
{
    /**
     * @var \SendinBlue\Client\Api\TransactionalEmailsApi
     */
    protected $api;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', config('sendinblue.api_key'));

        $this->api = new TransactionalEmailsApi(new Client(), $config);
    }

    public function sendDirectDebitNotification(array $recipient, array $params)
    {
        $recipients = [$recipient];

        $sendSmtpEmail = new SendSmtpEmail([
            'templateId' => (int) config('sendinblue.template_direct_debit_change'),
            'to' => $recipients,
            'params' => $params,
        ]);

        return $this->sendEmail($sendSmtpEmail);
    }

    private function sendEmail(SendSmtpEmail $params)
    {
        try {
            return $this->api->sendTransacEmail($params);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }
}
