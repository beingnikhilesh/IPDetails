<?php

namespace beingnikhilesh\IPDetails\Vendors;

#####################################################
# IP to Details Service by freegeoip
# https://freegeoip.app
# https://api.freegeoip.app/json/{ip}?apikey={key}
#####################################################

use beingnikhilesh\IPDetails\Enums\IPErrors;
use beingnikhilesh\IPDetails\Error\Error;
use beingnikhilesh\IPDetails\Formats\IPLocationInterface;

class freegeoip extends vendorBase implements IPLocationInterface
{
    # Documentation - 

    # Get the IP Details
    public function getIPDetails(string $ip)
    {
        # Get the IP Details using the _post or _get methods
        $response = $this->_get($this->settings['url'] . $ip . '?apikey=' . $this->settings['key']);
        echo( $response);
        return $this->_formatResponse($response);
    }

    # Format the Response received from freegeoip
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
        if (empty($JSONDecodedIPDetails['country_code']))
            return $this->_formatErrorResponse(IPErrors::UNKNOWN_ERROR);

        ## The Response is Success
        # Map the response data to the expected format
        return $this->_formatSuccessResponse($this->array_map, $JSONDecodedIPDetails);
    }
    # Array Map for Key Values
    private $array_map = [
        'ip' => 'ip',
        'country_code' => 'country_code',
        'country_name' => 'country_name',
        'region_code' => 'region_code',
        'region_name' => 'region_name',
        'city' => 'city',
        'zip_code' => 'zip_code',
        'time_zone' => 'time_zone',
        'latitude' => 'latitude',
        'longitude' => 'longitude',
    ];
}
