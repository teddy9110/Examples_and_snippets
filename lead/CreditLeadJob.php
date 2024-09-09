<?php

  /**
     * CreditLeadJob handles the crediting process for reconciled leads.
     * - Uses EducationAPI to deduct credits from learning providers.
     * - Updates lead status to rejected moderation after crediting.
     * - Ensures only one lead is credited at a time for a given provider to avoid credit conflicts.
     * - Logs errors and fails the job if an exception occurs.
     */

class CreditLeadJob extends Job implements ShouldBeUniqueUntilProcessing
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
        if ($this->lead->status == Lead::STATUS_RECONCILED) {
            $this->startCrediting();
        }
    }

    protected function startCrediting()
    {
        $leadHelper = $this->getLeadHelper();
        $educationAPI = $this->getEducationAPI();

        $credits = $this->lead->leadType->credits * -1;
        try {
            $learningProvider = $educationAPI->deductLearningProviderCredits($this->lead->learning_provider_id, $credits);

            if (!$learningProvider) {
                // If we get an error, fail the job
                $this->fail('Unable to deduct credits from learning provider: ' . $e->getMessage());
            }

            // Move lead back to rejected moderation
            $leadHelper->updateStatus($this->lead, Lead::STATUS_REJECTED_MODERATION);
        } catch (Throwable $e) {
            // If we get an error, fail the job
            $this->fail('Unable to deduct credits from learning provider: ' . $e->getMessage());
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
