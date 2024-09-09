<?php


  /**
     * - Moderate Lead Job checks the status of the lead, determines if it should be rejected or approved based on various criteria
     * - It then updates the lead's status accordingly
     * -  If the lead is approved, a reconciliation job is triggered
     * - Logs errors and fails the job if an exception occurs.
     */

class ModerateLeadJob extends Job implements ShouldBeUniqueUntilProcessing
{
    /*
        @var Lead $lead
    */
    protected $lead;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead->withoutRelations();
    }

    /**
     * The unique ID of the job.
     *
     * @return string
     */
    public function uniqueId()
    {
        return $this->lead->id;
    }

    /**
     * Get the lead helper
     */
    public function getLeadHelper() {
        return new LeadHelper();
    }

    /**
     * Helper method to create a DetectDevice object for us. Allows us to mock this in tests
     *
     * @param  Request|null  $overrideRequest - A request object to be used if this is being used outside of an actual request
     * @return DetectDevice
     */
    protected static function getDetectDevice(?Request $overrideRequest = null): DetectDevice
    {
        return new DetectDevice($overrideRequest);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->lead->status == Lead::STATUS_PENDING_MODERATION) {
            $this->startModeration();
        }
    }

    /**
     * Start the moderation process for the lead
     *
     * @return void
     */
    protected function startModeration()
    {
        $leadHelper = $this->getLeadHelper();

        if ($this->isBot() || $this->isTest()) {
            $newStatus = Lead::STATUS_REJECTED_MODERATION;
        } else {
            $learningProvider = EducationAPI::getLearningProviderById($this->lead->learning_provider_id);
            $newStatus = $this->lead->status;
            if ($this->isRejectedByLearningProvider($learningProvider)) {
                $newStatus = Lead::STATUS_REJECTED_LEARNING_PROVIDER;
            } else {
                // Check if this is a duplicate
                $isDuplicate = $leadHelper->isLeadADuplicate($this->lead->fresh());
                if ($isDuplicate) {
                    $newStatus = Lead::STATUS_REJECTED_DUPLICATE;
                } else {
                    $newStatus = Lead::STATUS_APPROVED;
                }
            }
        }

        // Change the status of the lead
        $leadHelper->updateStatus($this->lead, $newStatus);

        // Kick off a reconciliation job if the lead has been approved
        if ($newStatus === Lead::STATUS_APPROVED) {
            ReconcileLeadJob::dispatch($this->lead);
        }
    }

    /**
     * Check if the email address is a test email
     *
     * @param string $email
     *
     * @return bool
     */
    protected function isTestEmail(string $email) {
        return strpos($email, '@tsrmail.co.uk') !== false || strpos($email, '@thestudentroom.com') !== false;
    }

    /**
     * Check if user is a test user
     *
     * @return bool
     */
    public function isTest() {
        if (!$this->lead->user_id) {
            if ($this->lead->guestUser) {
                return $this->isTestEmail($this->lead->guestUser->email);
            }
            return false;
        }
        $user = TSRGUserAPI::getUser($this->lead->user_id);
        if ($user) {
            return $this->isTestEmail($user['email'] ?? '');
        }
        return false;
    }

    /**
     * Check if the lead is from a bot
     *
     * @return bool
     */
    protected function isBot() {
        $dummyRequest = new Request([], [], [], [], [], ['HTTP_USER_AGENT' => $this->lead->user_agent]);
        return $this->getDetectDevice($dummyRequest)->getDeviceType() === 'Bot';
    }

    /**
     * Check if the learning provider has the lead type switched on (and if the learning provider exists)
     *
     * @return bool
     */
    protected function isRejectedByLearningProvider($learningProvider) {
        return (!$learningProvider || !$this->checkLeadTypeForLearningProvider($learningProvider['lead_generation'], $this->lead->leadType->code));
    }

    /**
     * Check if the learning provider has the lead type switched on
     *
     * @param array $leadGenerationSettings ['request_info' => 'form'|null|string, 'open_days' => 'form'|null|string, 'prospectus' => 'form'|null|string, 'visit_website' => 'form'|null|string]
     * @param string $leadTypeCode
     *
     * @return bool
     */
    protected function checkLeadTypeForLearningProvider($leadGenerationSettings, string $leadTypeCode)
    {
        switch ($leadTypeCode) {
            case 'ENQUIRY_FORM':
                return $leadGenerationSettings['request_info'] === 'form';
            case 'ENQUIRY_REFERRAL':
                return !is_null($leadGenerationSettings['request_info']) && $leadGenerationSettings['request_info'] !== 'form';
            case 'OPENDAY_FORM':
                return $leadGenerationSettings['open_days'] === 'form';
            case 'OPENDAY_REFERRAL':
                return !is_null($leadGenerationSettings['open_days']) && $leadGenerationSettings['open_days'] !== 'form';
            case 'PROSPECTUS_FORM':
                return $leadGenerationSettings['prospectus'] === 'form';
            case 'PROSPECTUS_REFERRAL':
                return !is_null($leadGenerationSettings['prospectus']) && $leadGenerationSettings['prospectus'] !== 'form';
            case 'WEBSITE_CLICK':
                return !is_null($leadGenerationSettings['visit_website']);
        }

        // If by some weird chance we get here, we'll just return false
        return false;
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        // Make sure we only moderate one lead at a time for a given learning provider to make sure dupes are picked up properly
        return [new WithoutOverlapping($this->lead->learning_provider_id)];
    }
}
