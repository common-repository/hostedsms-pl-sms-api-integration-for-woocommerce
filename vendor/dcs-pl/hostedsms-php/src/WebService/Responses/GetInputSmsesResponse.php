<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

use HostedSms\WebService\Responses\ResponseObjects\InputSms;

class GetInputSmsesResponse extends Response
{
    /** @var InputSms[] */
    public $inputSmses;
    function __construct($response)
    {
        parent::__construct($response);

        if (isset($response->InputSms->InputSms)) {
            $arrayObject = $this->createArray($response->InputSms->InputSms);
            foreach ($arrayObject as $obj) {
                $this->inputSmses[] = new InputSms($obj);
            }
        } else
            $this->inputSmses = [];
    }
}