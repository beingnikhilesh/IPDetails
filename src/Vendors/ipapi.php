<?php

namespace beingnikhilesh\IPDetails\Vendors;

use beingnikhilesh\IPDetails\Error\Error;
use beingnikhilesh\IPDetails\Enums\IPErrors;
use beingnikhilesh\IPDetails\Vendors\vendorBase;
use beingnikhilesh\IPDetails\Formats\IPLocationInterface;

class ipapi extends vendorBase implements IPLocationInterface
{
    # Documentation - https://ip-api.com/docs

    public function getIPDetails(string $ip)
    {
        $response = $this->_get($this->settings['url'] . $ip);
        return $this->_formatResponse($response);
    }

    # Format the Response received from freegeoip
    public function _formatResponse($rawResponse)
    {
        // echoAll($rawResponse);
        // Check if $rawResponse is an array and has the correct structure
        if (empty($rawResponse) or !is_array($rawResponse))
            return $this->_formatErrorResponse(IPErrors::EMPTY_RESPONSE);

        # Check if there was an Error Message in the HTTP request
        if (!$rawResponse['success']) {
            return $this->_formatErrorResponse(
                (!empty($this->headerErrorMap[$rawResponse['header_code']]))
                    ? $this->headerErrorMap[$rawResponse['header_code']]
                    : IPErrors::UNKNOWN_ERROR
            );
        }

        # There is some Response, Decode it
        $JSONDecodedIPDetails = json_decode($rawResponse['response'], TRUE);
        if ($JSONDecodedIPDetails === null && json_last_error() !== JSON_ERROR_NONE)
            return $this->_formatErrorResponse(IPErrors::JSON_ERROR);
        // echoALl([$this->errorArrayMap, $JSONDecodedIPDetails, $JSONDecodedIPDetails['message'], isset($this->errorArrayMap[$JSONDecodedIPDetails['message']])]);
        # Check if there is an Error set in the Response
        if ($JSONDecodedIPDetails['status'] == 'fail')
            return $this->_formatErrorResponse(
                isset($this->errorArrayMap[$JSONDecodedIPDetails['message']])
                    ? $this->errorArrayMap[$JSONDecodedIPDetails['message']]
                    : IPErrors::UNKNOWN_ERROR
            );

        # The Response is success, Map the response data to the expected format
        return $this->_formatSuccessResponse($this->array_map, $JSONDecodedIPDetails);
    }

    # Array Map for Key Values
    private $array_map = [
        'ip' => 'query',
        'country_code' => 'countryCode',
        'country_name' => 'country',
        'region_code' => 'region',
        'region_name' => 'regionName',
        'city' => 'city',
        'zip_code' => 'zip',
        'time_zone' => 'timezone',
        'latitude' => 'lat',
        'longitude' => 'lon',
        'continent_code' => 'continentCode',
        'continent_name' => 'continent',
        'isp_name' => 'isp',
        'isp_organization' => 'org',
    ];

    # Header Error Array Map
    private $headerErrorMap = [
        '429' => IPErrors::RATE_LIMIT,
    ];

    # Message Error Array Map
    private $errorArrayMap = [
        'private range' => IPErrors::BOGUS_IP,
        'reserved_range' => IPErrors::BOGUS_IP,
        'invalid query' => IPErrors::INVALID_QUERY,
    ];
}
