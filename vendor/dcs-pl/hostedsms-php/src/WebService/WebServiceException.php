<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService;

use Exception;

class WebServiceException extends Exception
{
    function __construct($errorMessage)
    {
        parent::__construct($errorMessage);
    }
}

?>