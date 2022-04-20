<?php

namespace Rhf\Modules\WebForm\Services;

use Rhf\Modules\WebForm\Requests\ZendeskWebRequest;
use Rhf\Modules\Zendesk\Services\ZendeskService;

class ContactUsService
{
    private $zendeskService;

    public function __construct()
    {
        $this->zendeskService = new ZendeskService();
    }

    public function createContactUsTicket(ZendeskWebRequest $request)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $message = $request->input('message');
        $subject = $request->input('subject');
        $uploads = [];

        if ($request->has('attachments')) {
            $uploads = $this->imageUploadTokens($request, $name);
        }
        return $this->createCustomerServiceTicket($name, $email, $message, $subject, $uploads);
    }

    /**
     * @param ZendeskWebRequest $request
     * @param $name
     * @return array
     */
    private function imageUploadTokens(ZendeskWebRequest $request, $name): array
    {
        $files = $request->file('attachments');
        $attachments = [];

        foreach ($files as $file) {
            $attachments[] = [
                'file' => $file->getPathname(),
                'name' => $name . '__' . $file->getClientOriginalName()
            ];
        }
        $uploads = $this->zendeskService->addAttachmentToTicket($attachments);
        return $uploads;
    }

    /**
     * @param $name
     * @param $email
     * @param $message
     * @param $subject
     * @param array $uploads
     * @return mixed
     * @throws \Zendesk\API\Exceptions\AuthException
     * @throws \Zendesk\API\Exceptions\ResponseException
     */
    private function createCustomerServiceTicket($name, $email, $message, $subject, array $uploads)
    {
        $createTicket = $this->zendeskService->createNewTicket(
            $name,
            $email,
            'Contact Us - ' . $subject,
            $message,
            ['contact-us', $subject],
            $uploads
        );
        $ticketId = $createTicket->ticket->id;
        return $ticketId;
    }
}
