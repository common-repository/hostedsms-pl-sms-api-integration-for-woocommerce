<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

use ArrayObject;

class Response
{
    /** @var string */
    public $currentTime;

    function __construct($response)
    {
        $this->currentTime = $response->CurrentTime;
    }
    protected function createArray($tab)
    {
        if (is_array($tab))
            $arrayObject = new ArrayObject($tab);
        else
            $arrayObject = new ArrayObject([$tab]);

        return $arrayObject;
    }
}
