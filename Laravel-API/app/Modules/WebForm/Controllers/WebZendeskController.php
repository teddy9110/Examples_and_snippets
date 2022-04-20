<?php

namespace Rhf\Modules\WebForm\Controllers;

use Rhf\Http\Controllers\Controller;
use Rhf\Modules\WebForm\Requests\ZendeskWebRequest;
use Rhf\Modules\WebForm\Services\ContactUsService;

class WebZendeskController extends Controller
{
    private $contactUsService;

    public function __construct()
    {
        $this->contactUsService = new ContactUsService();
    }

    public function createSupportTicket(ZendeskWebRequest $request)
    {
        $createContactUsTicket = $this->contactUsService->createContactUsTicket($request);
        if ($createContactUsTicket) {
            return response('Success', 200);
        }
        return false;
    }
}
