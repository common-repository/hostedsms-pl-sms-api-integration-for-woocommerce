<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

class GetValidSendersResponse extends Response
{
    /** @var string[] */
    public $senders;

    function __construct($response)
    {
        parent::__construct($response);

        if (isset($response->Senders->string))
            $this->senders = $this->createArray($response->Senders->string);
        else
            $this->senders = [];
    }
}