<?php

namespace App\Thirdparty\IPLocation;

class Config
{
    private $config = [
        'vendors' => [
            'ipapi' => [
               'url'  =>'http://ip-api.com/json/',
               'website_url' => 'https://ipapi.co', # To Register Account
            ],
            'freegeoip' => [
                'key' => '', # Insert Key Here
                'url' => 'https://api.freegeoip.app/json/',
                'website_url' => 'https://freegeoip.app', # To Register Account
            ],
            'ipgeolocation' => [
                'key' => '', # Insert Key Here
                'url' => 'https://api.ipgeolocation.io/ipgeo',
                'website_url' => 'https://ipgeolocation.io', # To Register Account
            ],

        ]
    ];
    private $default = 'ipapi';

    function __construct(string $vendor)
    {
        $this->set($vendor);
    }

    function set(string $vendor = '')
    {
        if (isset($this->config['vendors'][$vendor]))
            $this->default = $vendor;
    }

    function getName()
    {
        return $this->default;
    }

    function get()
    {
        return $this->config['vendors'][$this->default];
    }

    function getVendorList()
    {
        return array_keys($this->config);
    }
}
?>