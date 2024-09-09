<?php
  /**
     * POST /lead API endpoint to create a new lead.
     * - Extracts lead information from the request.
     * - Validates lead type and user presence for specific forms.
     * - Uses JWT token to assign user ID, or sets guest user details.
     * - Dispatches CreateLeadJob in the background to process the lead creation.
     * 
     */


class LeadController extends ApiController
{
    /**
     * POST /lead API end point
     * Creates a new lead
     *
     * @param  Request  $request  - The server request, including any parameters
     * 
     * @throws InvalidParameterException
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function post(TSRGJWT $jwtToken, Request $request) : JsonResponse
    {
        $leadHelper = new LeadHelper();
        $leadInfo = $request->data;

        $leadInfo['ip_address'] = $request->header('TRUE-CLIENT-IP') ?? null;
        $leadInfo['user_agent'] = $request->header('USER-AGENT') ?? null;

        // TODO: Temporary backwards compatibility for guest_user
        if (isset($leadInfo['guest_user'])) {
            $leadInfo['user'] = $leadInfo['guest_user'];
        }

        if (
            (!isset($leadInfo['user']) || !$leadInfo['user']) &&
            in_array($leadInfo['lead_type_code'], ['PROSPECTUS_FORM', 'OPENDAY_FORM', 'ENQUIRY_FORM'])
        ) {
            throw new InvalidParameterException('user must be set for form submissions');
        }

        // Set userId from JWT token if it exists
        if ($jwtToken->userId) {
            $leadInfo['user_id'] = $jwtToken->userId;
        } else {
            // Add guest user details
            if (isset($leadInfo['user']) && $leadInfo['user']) {
                $leadInfo['guest_user'] = $leadInfo['user'];
            }
        }

        // Kick off the lead creation job in the background
        CreateLeadJob::dispatch($leadInfo, $leadInfo['user'] ?? []);

        return $this->getSuccessfulResponse(204);
    }

    /**
     * GET /lead API end point
     * Retrieves leads
     *
     * @param  Request  $request  - The server request, including any parameters
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(TSRGJWT $jwtToken, Request $request) : JsonResponse
    {
        $matchingPermissions = array_intersect($jwtToken->adminPermissions, ['admin.admin', 'admin.education', 'admin.education.leads']);
        if (!$jwtToken || count($matchingPermissions) == 0) {
             throw new UnauthorizedException('Missing permissions to access this resource (admin or leads)');
        }

        $leadHelper = new LeadHelper();

        $page = (int) $request->input('page', 1);
        if ($page === 0) {
            $page = 1;
        }
        $perPage = (int) $request->input('per_page', 20);
        if ($perPage === 0) {
            $perPage = 20;
        }
        $orderBy = $request->input('order_by', 'created_at');
        $orderByDirection = $request->input('order_by_direction', 'desc');

        $status = $request->input('status');
        $learningProviderId = $request->input('learning_provider_id');

        if (
            ! Validation::isOrderByParamValid($orderBy) ||
            ! Validation::isOrderByDirectionParamValid($orderByDirection)
        ) {
            throw new InvalidParameterException('Invalid order by parameter values');
        }

        // Calculate offset from page and per_page
        $offset = (int) ($page - 1) * $perPage;

        // Get leads
        $leads = $leadHelper->getLeads(
            $perPage,
            $orderBy,
            $orderByDirection,
            $status,
            (int)$learningProviderId
        );

        return (new LeadCollection($leads))
            ->response()
            ->setStatusCode(200);
    }
}
