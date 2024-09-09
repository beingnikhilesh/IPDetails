<?php

namespace beingnikhilesh\IPDetails\Enums;

enum IPErrors: string
{
    case EMPTY_RESPONSE = "No Response Recieved for the Query";
    case INVALID_QUERY = "The Query is Invalid";
    case ACCOUNT_LOCKED = 'Your Account has been Locked';
    case ACCOUNT_INACTIVE = 'Your Account is Inactive. Please Activate it';
    case SUBSCRIPTION_OVER = 'The Subscription to IP Details Plan has Exhausted';
    case RATE_LIMIT = 'You\'ve reached the Maximum Query Limit for the Specified Time';
    case NOT_FOUND = 'The Details of the IP Address were not found in our Database';
    case BOGUS_IP = 'Bogon or Bogus IP Address Provided';
    case PRIVATE_IP_POOL = 'The Provided IP Pool is Private';
    case UNKNOWN_ERROR = 'An Unknown Error was recieved from the Provider';
    case DECODE_ERROR = 'The Response recieved is not in a Valid Format';
        # JSON and XML Errors
    case JSON_ERROR = "The JSON Recieved is not in the Standard Format";
    case XML_ERROR = "The XML Recieved is not in the Standard Format";
    case DATA_ERROR = "The DATA Recieved is not in the Standard Format";

}
