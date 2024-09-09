<?php

 /**
     * CreateLeadJob handles the creation of a new lead.
     * - Uses LeadHelper to create the lead.
     * - Sanitizes userInfo by removing unnecessary fields.
     * - Publishes LeadCreated event.
     * - Dispatches ModerateLeadJob for further processing.
     * - Logs errors and fails if an exception occurs.
     */

class CreateLeadJob extends Job
{
    /**
     * @var array $lead
     */
    public $leadInfo;

    /**
     * @var array $userInfo
     */
    public $userInfo = [];

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
    public function __construct(array $leadInfo, array $userInfo = [])
    {
        $this->leadInfo = $leadInfo;
        $this->userInfo = $userInfo;
    }


    /**
     * Get the LeadHelper instance
     * 
     * @return \App\Support\LeadHelper
     */
    public function getLeadHelper() {
        return new LeadHelper();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Create our lead
            $newLead = $this->getLeadHelper()->createLead($this->leadInfo);

            // Make sure we don't send country, post_code, city and mobile to the profile service
            $userInfo = $this->userInfo ?? [];
            $keysToRemove = ['country', 'post_code', 'city', 'mobile'];
            foreach($keysToRemove as $key){
                if (array_key_exists($key, $userInfo)) {
                    unset($userInfo[$key]);
                }
            }

            // Publish an event so other services can act upon this lead
            (new LeadCreated($this->leadInfo, $userInfo))->publish();

            // Kick off the moderation job in the background
            ModerateLeadJob::dispatch($newLead);
        } catch(Throwable $e) {
            // Log the error
            Log::error('Error creating lead: ' . $e->getMessage());
            // Fail Job
            $this->fail($e);
        }
    }
}
