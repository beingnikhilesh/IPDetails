<?php

namespace beingnikhilesh\IPDetails\Vendors;

use beingnikhilesh\IPDetails\Enums\IPErrors;
use beingnikhilesh\IPDetails\Config;
use beingnikhilesh\IPDetails\Error\Error;
class vendorBase
{
    #################################################
    #   GLOBAL VARIABLES
    #################################################

    const CLASSNAME = 'vendorBase';

    private static bool $error = TRUE;

    /** Construct Function */
    function __construct(protected ?array $settings = [])
    { 

        if (empty($settings))
            $this->settings = (new Config(get_class($this)))->get();
        
    }

    #################################################
    #   Call Functions
    #################################################

    protected function _post(string $url, array $params = [], array $headers = [])
    {
        return $this->_call('POST', $url, $params, $headers);
    }

    protected function _get(string $url, array $params = [], array $headers = [])
    {
        $callUrl = (empty($url)) ? $url : $url . '?' . http_build_query($params);
        return $this->_call('GET', $callUrl, $params, $headers);
    }

    private function _call(
        string $requestType = "GET",
        string $url,
        array $params = [],
        array $headers = []
    ) {
        # Declare the required Libraries, Models, etc.
        $client = new \GuzzleHttp\Client();
        $requestOptions = [];

        ## Check and Prepare the Request Options
        # Check the Headers
        if (!empty($headers))
            $requestOptions['headers'] = $headers;
        # Check if any Body Options are set for POST Request
        if (!empty($params))
            $requestOptions = array_merge($requestOptions, $params);

        # Make the Actual Query

        try {
            $response = $client->request($requestType, $url, $requestOptions);
            $response = $response->getBody()->getContents();
            return [1, $response];
        } catch (\GuzzleHttp\Exception\ClientException $j) {
            $response = (string) $j->getResponse()->getBody(true);
            return [0, $response, $j->getCode()];
        }
    }

    public function _formatErrorResponse(IPErrors $errorCode)
    {
        return [
            'error' => 1,
            'msg' => $errorCode->value,
            'code' => $errorCode->name,
            'data' => []
        ];
    }

    protected function _formatSuccessResponse(array $arrayMap, array $values)
    {
        return [
            'error' => 0,
            'msg' => 'success',
            'data' => $this->_mapValues($this->responseArrayFormat, $arrayMap, $values)
        ];
    }

    /** Function to recursively Map Arrays with recieved Values */
    private function _mapValues($responseArrayFormat, $mapArray, array $mapValues)
    {
        if (!is_array($responseArrayFormat))
            return $responseArrayFormat;
        foreach ($responseArrayFormat as $key => $val) {
            if (is_array($val))
                $responseArrayFormat[$key] = $this->_mapValues($val, $mapArray, $mapValues);
            if (isset($mapArray[$key]) && !empty($mapValues[$mapArray[$key]]))
                $responseArrayFormat[$key] = $mapValues[$mapArray[$key]];
        }

        return $responseArrayFormat;
    }

    #################################################
    #   Misc Functions
    #################################################

    public static function muteErrors(bool $mute = TRUE)
    {
        if (is_bool($mute) and $mute != self::$error)
            self::$error = $mute;
    }

    /** Validate the IP Address */
    private function validateIPAddress(string $ip)
    {
        return TRUE;
    }

    #################################################
    #   Response Array Format
    #################################################

    protected $responseArrayFormat = [
        'ip' => 'ip',
        'country' => [
            'country_code' => '',
            'country_name' => '',
            'country_capital' => '',
            'country_flag' => '',
            'country_tld' => '',
        ],
        'continent' => [
            'continent_code' => '',
            'continent_name' => ''
        ],
        'region_code' => '',
        'region_name' => '',
        'city' => '',
        'zip_code' => '',
        'time_zone' => '',
        'latitude' => '',
        'longitude' => '',
        'is_eu' => '',
        'calling_code' => '',
        'languages' => '',
        'isp' => [
            'isp_name' => '',
            'isp_organization' => '',
        ],
    ];
}
