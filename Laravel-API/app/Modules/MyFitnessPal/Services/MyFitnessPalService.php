<?php

namespace Rhf\Modules\MyFitnessPal\Services;

use Rhf\Exceptions\FitnessBadRequestException;
use Rhf\Modules\System\Services\CsvService;
use Rhf\Modules\User\Models\User;

class MyFitnessPalService extends MyFitnessPalConnection
{
    // Lists the data types by slug associated to MFP
    public static $dataTypes = ['calories','fiber','fat','protein','carbohydrates'];

    /**************************************************
    *
    * PUBLIC METHODS
    *
    ***************************************************/

    /*
     * Function request
     *
     * submit a request to My FitnessPal
     *
     * @param (string) GET|POST etc.
     * @param (string) url route to submit to
     * @param (array) data to send for if method requires it
     * @return (Guzzle object)
     */
    public function request($method, $route, $data = null)
    {

        $this->headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
        $this->headers['mfp-user-id'] = $this->user->getPreference('mfp_user_id');

        switch ($method) {
            case 'POST':
            case 'PATCH':
                $response = $this->client
                    ->request($method, $this->url . $route, ['headers' => $this->headers, 'form_params' => $data]);
                break;
            case 'GET':
                $response = $this->client
                    ->request('GET', $this->url . $route, ['headers' => $this->headers, 'query' => $data]);
                break;
        }

        // Validate
        if (!$response->getStatusCode() == 200) {
            throw new FitnessBadRequestException('Error: Unable to connect to MyFitnessPal. Please try again later.');
        }

        return $response;
    }

    /*
     * Function getFirstToken
     *
     * do a token only request to get the first token after authentication
     * (wraps protected method token)
     *
     * @return (Guzzle object)
     */
    public function getFirstToken()
    {
        $this->token();
    }

    /*
     * Function redisKey
     *
     * generate a unique redis key
     *
     * @param (string)
     * @param (array)
     * @return (string)
     */
    public function redisKey($string, $array = null)
    {
        $removeChars = [' ', '[', ']', ',', '"', '{', '}'];

        if ($array) {
            $arrayString = json_encode($array);
            foreach ($removeChars as $remove) {
                $arrayString = str_replace($remove, '', $arrayString);
            }
            $string .= $arrayString;
        }
        return $string;
    }

    /**************************************************
    *
    * PROTECTED METHODS
    *
    ***************************************************/

    /*
     * Function request
     *
     * submit a request to My FitnessPal
     *
     * @return (string)
     */
    protected function getAccessToken()
    {
        // Check if we already have an available access token
        if (!$this->user->hasPreference('mfp_access_token')) {
            // Request and set a new token
            $this->token();
        } elseif (time() >= $this->user->getPreference('mfp_token_expires_at')) {
            // Refresh the current token
            $this->refreshToken();
        }
        return $this->user->getPreference('mfp_access_token');
    }

    /**************************************************
    *
    * GETTERS
    *
    ***************************************************/

    /**
     * Return the user associated to the instance of the service.
     *
     */
    public function getUser()
    {
        return isset($this->user) ? $this->user : null;
    }

    /**
     * Retrieve a CSV targets file and orient into array.
     *
     * @param string
     * @return void
     */
    private function getCsvData($csv)
    {
        $csvService = new CsvService('app/csv/' . $csv);
        return $csvService->toArray();
    }


    /**************************************************
    *
    * SETTERS
    *
    ***************************************************/

    /**
     * Set the user associated to the instance of the service.
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }
}
