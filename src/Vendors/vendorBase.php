<?php

namespace beingnikhilesh\IPDetails\Vendors;

use beingnikhilesh\IPDetails\Enums\IPErrors;
use beingnikhilesh\IPDetails\Config;
use beingnikhilesh\IPDetails\Error\Error;
use ReflectionClass;

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
            $responseBody = $response->getBody()->getContents();
            return ['success' => 1, 'error_code' => empty($responseBody) ? IPErrors::EMPTY_RESPONSE : '', 'response' => $responseBody];
        } catch (\GuzzleHttp\Exception\TooManyRedirectsException $e) {
            # if the Request / Response is redirecting too much and not returning any response, Handle the TooManyRedirectsException Here
            return ['success' => 0, 'error_code' => IPErrors::TOO_MANY_REDIRECTS, 'response' => 'Redirect Error: ' . $e->getMessage()];
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            # if Internet Connection is not Available, Handle the ConnectException Here
            return ['success' => 0, 'error_code' => IPErrors::NO_NETWORK, 'response' => 'Network Error: ' . substr($e->getMessage(), 0, 60)];
        } catch (\GuzzleHttp\Exception\ClientException | \GuzzleHttp\Exception\ServerException $e) {
            # Handle the ClientException & Server Exception here
            $response = (string) $e->getResponse()->getBody(true);
            return ['success' => 0, 'error_code' => '', 'response' => $response, 'header_code' => $e->getCode()];
        } catch (\Exception $e) {
            # Handle the Generic Exception here
            
        }
    }

    public function _formatErrorResponse(IPErrors $errorCode, string $returnMessage = '')
    {
        return [
            'error' => 1,
            'msg' => $errorCode->value,
            'code' => $errorCode->name,
            'additional_message' => $returnMessage,
            'data' => []
        ];
    }

    protected function _formatSuccessResponse(array $arrayMap, array $values)
    {
        return [
            'error' => 0,
            'msg' => 'success',
            'service' => (new ReflectionClass($this))->getShortName(),
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
