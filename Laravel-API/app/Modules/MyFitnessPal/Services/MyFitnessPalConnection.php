<?php

namespace Rhf\Modules\MyFitnessPal\Services;

use GuzzleHttp\Client as Guzzle;
use Rhf\Modules\User\Services\UserService;
use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Exceptions\FitnessPreconditionException;

class MyFitnessPalConnection
{
    protected $client;
    protected $url;
    protected $client_id;
    protected $secret;
    /** @var \Rhf\Modules\User\Models\User */
    protected $user;
    protected $headers = [
        'Accept-Language' => 'en-US',
        'Accept' => 'application/json',
    ];

    /**
     * Create a new MyFitnessPalConnection instance.
     *
     * @return void
     */
    public function __construct($user = null)
    {
        // Set the user if possible
        if ($user) {
            $this->user = $user;
        } else {
            $this->user = auth('api')->user();
        }

        // Set the configs
        $this->url = config('myfitnesspal.url');
        $this->client_id = config('myfitnesspal.client_id');
        $this->secret = config('myfitnesspal.secret');
        $this->headers['mfp-client-id'] = $this->client_id;

        // Initiate the client
        $this->client = new Guzzle();
    }

    /**************************************************
    *
    * PROTECTED METHODS
    *
    ***************************************************/

    /*
     * Function refreshToken
     *
     * perform My Fitness Pal auth token refresh
     *
     * @return (void)
     */
    public function refreshToken()
    {
        $route = 'oauth2/token';

        $query = [
            'grant_type' => 'refresh_token',
            'client_id' => $this->client_id,
            'refresh_token' => $this->user->getPreference('mfp_refresh_token'),
            'client_secret' => $this->secret,
            'user_id' => $this->user->getPreference('mfp_user_id'),
        ];

        $res = $this->client
            ->request('POST', $this->url . $route, ['headers' => $this->headers, 'form_params' => $query]);

        if ($res->getStatusCode() != 200 && $res->getStatusCode() != 503) {
            // If we get back anything other than a 200 from MFP, we can assume the refresh token has expired
            throw new FitnessBadRequestException(
                'Error: Unable to authenticate with MyFitnessPal. Please contact Team RH Support.'
            );
        } elseif ($res->getStatusCode() == 503) {
            // If we get back a 503, then the service is down (happens more often than we'd hope), so inform the user
            throw new FitnessPreconditionException(
                'Error: Unable to communicate with MyFitnessPal. Please try again later.'
            );
        }

        // Process the result into the data store§
        $data = json_decode($res->getBody()->getContents());
        $userService = new UserService();
        $userService->setUser($this->user);
        $userService->updatePreferences([
            'mfp_access_token' => $data->access_token,
            'mfp_refresh_token' => $data->refresh_token,
            'mfp_token_expires_at' => time() + $data->expires_in,
            'mfp_user_id' => $data->user_id
        ]);
    }

    /*
     * Function token
     *
     * retrieve an access token for the current user from My Fitness Pal
     *
     * @return (void)
     */
    protected function token()
    {
        $route = 'oauth2/token';

        $query = [
            'grant_type' => 'authorization_code',
            'code' => $this->user->getPreference('mfp_authentication_code'),
            'redirect_uri' => config('app.url') . '/api/1.0/auth/my-fitness-pal',
            'client_id' => $this->client_id,
            'client_secret' => $this->secret,
        ];

        $res = $this->client->request('POST', $this->url . $route, [
            'headers' => $this->headers,
            'form_params' => $query
        ]);

        if ($res->getStatusCode() != 200 && $res->getStatusCode() != 503) {
            // If we get back anything other than a 200 from MFP, we can assume the refresh token has expired
            throw new FitnessBadRequestException(
                'Error: Unable to authenticate with MyFitnessPal. Please contact Team RH Support.'
            );
        } elseif ($res->getStatusCode() == 503) {
            // If we get back a 503, then the service is down (happens more often than we'd hope), so inform the user
            throw new FitnessPreconditionException(
                'Error: Unable to communicate with MyFitnessPal. Please try again later.'
            );
        }

        // Process the result into the data store
        $data = json_decode($res->getBody()->getContents());
        $userService = new UserService();
        $userService->setUser($this->user);

        $userService->updatePreferences([
            'mfp_access_token' => $data->access_token,
            'mfp_refresh_token' => $data->refresh_token,
            'mfp_token_expires_at' => time() + $data->expires_in,
            'mfp_user_id' => $data->user_id,
        ]);
    }


    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /*
     * Function authUrl
     *
     * Retrieve the auth url to send the user to
     *
     */
    public function authUrl()
    {
        $route = 'oauth2/auth';

        $query = 'client_id=' .
            $this->client_id .
            '&response_type=code&scope=measurements diary&state=' .
            $this->user->refreshMfpStateToken() .
            '&redirect_uri=' .
            config('app.url') .
            '/api/1.0/auth/my-fitness-pal';

        // Execute the auth request
        $url = $this->url . $route . '?' . $query;

        return $url;
    }
}
