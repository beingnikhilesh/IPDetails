<?php

namespace beingnikhilesh\IPDetails;

use beingnikhilesh\IPDetails\Error\Error;

class IPDetails
{
    private Config $config;
    //private Value $value;
    private $vendorLocation = '\beingnikhilesh\IPDetails\Vendors\\';
    /** Construct Function */

    function __construct(private string $vendor = '')
    {
        $this->config = new Config($vendor);
        // echoALl([$vendor, $this->config]);
    }

    /** Function to get the IPDetails */
    function getIPDetails(string $ip)
    {
        if (empty($ip))
            return Error::set_error('Invalid IP Address Provided to get the Details');
        $className = $this->vendorLocation . $this->config->getName();
        return (new $className($this->config->get()))->getIPDetails($ip);
    }

    function __get($vendorName)
    {
        if (!in_array($vendorName, $this->config->getVendorList()))
            return Error::set_error('Invalid or No IP Service Vendor Provided');

        return new self($vendorName);
    }

    function __call($vendorName, $params)
    {
        if (!in_array($vendorName, $this->config->getVendorList()))
            return Error::set_error('Invalid or No IP Service Vendor Provided');

        # Set the Vendor name
        $this->config->set($vendorName);
        # Call and get the IP Details                                                                                                                                                                                                                                                                                                                     b   
        return $this->getIPDetails($params[0]);
    }
}
