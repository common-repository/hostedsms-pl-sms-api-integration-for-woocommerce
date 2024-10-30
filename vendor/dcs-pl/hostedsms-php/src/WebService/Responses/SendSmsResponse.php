<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

class SendSmsResponse extends Response
{
    /** @var string */
    public $messageId;

    function __construct($response)
    {
        parent::__construct($response);
        $this->messageId = $response->MessageId;
    }
}