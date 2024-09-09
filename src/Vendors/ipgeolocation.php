<?php

namespace beingnikhilesh\IPDetails\Vendors;

use beingnikhilesh\IPDetails\Enums\IPErrors;
use beingnikhilesh\IPDetails\Vendors\vendorBase;
use beingnikhilesh\IPDetails\Formats\IPLocationInterface;

class ipgeolocation extends vendorBase implements IPLocationInterface
{   
    # Documentation - https://ipgeolocation.io/documentation/ip-geolocation-api.html

    public function getIPDetails(string $ip)
    {   
        $response = $this->_get($this->settings['url'] .  '?' . http_build_query([
            'ip' => $ip,
            'apiKey' => ($this->settings['key']),
            'lang' => 'en',
            'fields' => '*',
            'excludes' => ''
        ]));

        return $this->_formatResponse($response);
    }
    public function _formatResponse($rawResponse)
    {   
        # Check if $rawResponse is an array and has the correct structure
        if (empty($rawResponse) or !is_array($rawResponse))
            return $this->_formatErrorResponse(IPErrors::EMPTY_RESPONSE);

        # Decode the JSON String
        $JSONDecodedIPDetails = json_decode($rawResponse[1], TRUE);

        if ($JSONDecodedIPDetails === null && json_last_error() !== JSON_ERROR_NONE)
            return $this->_formatErrorResponse(IPErrors::JSON_ERROR);

        # The Response is Error, Decode and return the Response
        if (!$rawResponse[0])
            return $this->_formatErrorResponse(
                (!empty($this->errorArrayMap[$rawResponse[2]]))
                    ? $this->errorArrayMap[$rawResponse[2]]
                    : IPErrors::UNKNOWN_ERROR
            );
        ## The Response is Success
        # Map the response data to the expected format
        return $this->_formatSuccessResponse($this->array_map, $JSONDecodedIPDetails);
    }

    # Array Map for Key Values
    private $array_map = [
        'ip' => 'ip',
        'country_code' => 'country_code2',
        'country_code' => 'country_code3',
        'country_name' => 'country_name',
        'country_name_official' => 'country_name_official',
        'region_code' => 'state_code',
        'region_name' => 'state_prov',
        'city' => 'city',
        'zip_code' => 'zipcode',
        'time_zone' => 'timezone',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
        'continent_code' => 'continent_code',
        'continent_name' => 'continent_name',
        'country_capital' => 'country_capital',
        'is_eu' => 'is_eu',
        'calling_code' => 'calling_code',
        'country_tld' => 'country_tld',
        'languages' => 'languages',
        'country_flag' => 'country_flag',
        'isp_name' => 'isp',
    ];

    private $errorArrayMap = [
        '404' => IPErrors::NOT_FOUND,
        '423' => IPErrors::BOGUS_IP,
        '401' => IPErrors::ACCOUNT_LOCKED,
        '429' => IPErrors::SUBSCRIPTION_OVER,
        '403' => IPErrors::ACCOUNT_INACTIVE,
        '429' => IPErrors::RATE_LIMIT,
    ];
}
