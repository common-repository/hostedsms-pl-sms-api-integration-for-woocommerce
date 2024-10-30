<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

class ConvertToGsm7Response extends Response
{
    /** @var string */
    public $gsm7Text;

    function __construct($response)
    {
        parent::__construct($response);
        $this->gsm7Text = $response->Gsm7Text;
    }
}