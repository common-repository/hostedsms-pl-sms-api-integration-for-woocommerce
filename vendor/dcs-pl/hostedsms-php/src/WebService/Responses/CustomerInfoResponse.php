<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

class CustomerInfoResponse extends Response
{
    /** @var string */
    public $customerValidTo;
    /** @var int */
    public $smsCounter;

    function __construct($response)
    {
        parent::__construct($response);

        $this->customerValidTo = $response->CustomerValidTo;
        $this->smsCounter = $response->SmsCounter;
    }
}