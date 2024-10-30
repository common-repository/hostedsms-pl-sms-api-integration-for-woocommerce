<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

class SendSmsesResponse extends Response
{
    /** @var string[] */
    public $messageIds;

    function __construct($response)
    {
        parent::__construct($response);

        if (isset($response->MessageIds->guid))
            $this->messageIds = $this->createArray($response->MessageIds->guid);
        else
            $this->messageIds = [];
    }
}