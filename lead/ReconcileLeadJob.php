<?php

 /**
     * This job processes a lead and reconciles its status by deducting credits 
     * from a learning provider. The main purpose is to ensure that leads with 
     * approved status (or pending credits) are processed and reconciled, updating 
     * their status accordingly. It also handles sending an email to the learning 
     * provider if their delivery method is set to email and the lead type is one 
     * of several specific forms.
     *
     * - Deducts credits from a learning provider associated with the lead.
     * - If credit deduction fails, the lead status is updated to 'APPROVED_PENDING_CREDITS'.
     * - If credit deduction succeeds, the lead status is updated to 'RECONCILED'.
     * - If required, sends an email to the learning provider based on their delivery method.
     * - Logs any errors that occur during email sending, but does not retry to prevent duplicate credit deductions.
     * - Uses middleware to prevent overlapping reconciliation processes for the same provider.
     */

class ReconcileLeadJob extends Job implements ShouldBeUniqueUntilProcessing
{
    /*
        @var Lead $user
    */
    protected $lead;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 1;

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

    public function getLeadHelper() {
        return new LeadHelper();
    }

    public function getEducationAPI() {
        return new EducationAPI();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->lead->status == 'APPROVED' || $this->lead->status == 'APPROVED_PENDING_CREDITS') {
            $this->startReconciliation();
        }
    }

    protected function startReconciliation()
    {
        $leadHelper = $this->getLeadHelper();
        $educationAPI = $this->getEducationAPI();

        $credits = $this->lead->leadType->credits;
        try {
            try {
                $learningProvider = $educationAPI->deductLearningProviderCredits($this->lead->learning_provider_id, $credits);
            } catch (Throwable $e) {
                // If we get an error, we can just mark as pending credits
                $leadHelper->updateStatus($this->lead, Lead::STATUS_APPROVED_PENDING_CREDITS);
                return;
            }

            if (!$learningProvider) {
                // If we get an error, we can just mark as pending credits
                $leadHelper->updateStatus($this->lead, Lead::STATUS_APPROVED_PENDING_CREDITS);
                return;
            }

            // Change the status of the lead
            $leadHelper->updateStatus($this->lead, Lead::STATUS_RECONCILED);

            if ($learningProvider['lead_delivery'] == 'EMAIL' && in_array($this->lead->leadType->code, ['ENQUIRY_FORM', 'OPENDAY_FORM', 'PROSPECTUS_FORM'])) {
                $emailAddress = $learningProvider['enquiry_email'];

                // If we have an email address, try to send an email
                if ($emailAddress) {
                    try {
                        Mail::to($emailAddress)->queue(new NewLeadEmail($this->lead));
                    } catch (Throwable $e) {
                        // Catch error but do not fail the job otherwise it'll try and deduct more credits when it retries
                        Log::error("Failed to send email to learning provider: " . $e->getMessage());
                    }
                }
            }
        } catch (Throwable $e) {
            // Change the status of the lead
            $leadHelper->updateStatus($this->lead, Lead::STATUS_APPROVED_PENDING_CREDITS);
            return;
        }
    }

    /**
     * Get the middleware the job should pass through.
     *
     * @return array<int, object>
     */
    public function middleware(): array
    {
        // Make sure we only reconcile one lead at a time for a given learning provider to avoid missing credits
        return [(new WithoutOverlapping($this->lead->learning_provider_id))->releaseAfter(30)];
    }
}
