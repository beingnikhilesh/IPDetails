<?php

namespace beingnikhilesh\IPDetails;

class Config
{
    private $config = [
        'vendors' => [
            'ipapi' => [
                'url'  => 'http://ip-api.com/json/',

            ],
            'freegeoip' => [
                'key' => '# Insert Key Here #',
                'url' => 'https://api.freegeoip.app/json/',
            ],
            'ipgeolocation' => [
                'key' => '# Insert Key Here #',
                'url' => 'https://api.ipgeolocation.io/ipgeo',
            ],

        ]
    ];
    private $default = 'freegeoip';

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
        return array_keys($this->config['vendors']);
    }
}
