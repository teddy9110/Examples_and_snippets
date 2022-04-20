<?php

namespace Rhf\Modules\System\Services;

use Facebook\Facebook;
use Rhf\Modules\System\Models\ActivityLog;

class FacebookExtender extends Facebook
{
    /**
     * Create a new Facebook instance.
     *
     * @param array
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @return void
     */
    public function __construct(array $config = [])
    {
        parent::__construct(array_merge([
            'app_id' => config('services.facebook.client_id'),
            'app_secret' => config('services.facebook.client_secret'),
        ], $config));

        $this->setDefaultAccessToken(
            config('services.facebook.client_id') . '|' . config('services.facebook.client_secret')
        );
    }

    /**
     * Connect the Facebook "page" to the platform.
     *
     * @return void
     */
    public function authPageAccessToken()
    {
        try {
            $pageAccessToken = $this->get('/teamrhfitness?fields=access_token')->getGraphNode()->asArray();
            $this->setDefaultAccessToken($pageAccessToken['access_token']);
        } catch (\Exception $e) {
            $log = new ActivityLog();
            $user = auth('api')->user();
            if (is_null($user)) {
                $user = auth()->user();
            }
            $log->user_id = $user->id;
            $log->action = 'FacebookFail';
            $log->save();
            $log = new ActivityLog();
            $log->user_id = $user->id;
            $log->action = 'FacebookMessage - ' . $e->getMessage();
            $log->save();
        }
    }
}
