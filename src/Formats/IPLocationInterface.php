<?php

namespace beingnikhilesh\IPDetails\Formats;

interface IPLocationInterface
{
    # Get the IP Details
    public function getIPDetails(string $ip);

    # Format the Response received from the Vendor
    public function _formatResponse($rawResponse);
}
