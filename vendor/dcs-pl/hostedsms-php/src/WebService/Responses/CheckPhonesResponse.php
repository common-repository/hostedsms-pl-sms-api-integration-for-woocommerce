<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

class CheckPhonesResponse extends Response
{
    /** @var string[] */
    public $validPhones;
    /** @var string[] */
    public $invalidPhones;
    /** @var string[] */
    public $duplicates;
    /** @var string[] */
    public $blockedPhones;

    function __construct($response)
    {
        parent::__construct($response);

        if (isset($response->ValidPhones->string))
            $this->validPhones = $this->createArray($response->ValidPhones->string);
        else
            $this->validPhones = [];

        if (isset($response->InvalidPhones->string))
            $this->invalidPhones = $this->createArray($response->InvalidPhones->string);
        else
            $this->invalidPhones = [];

        if (isset($response->Duplicates->string))
            $this->duplicates = $this->createArray($response->Duplicates->string);
        else
            $this->duplicates = [];

        if (isset($response->BlockedPhones->string))
            $this->blockedPhones = $this->createArray($response->BlockedPhones->string);
        else
            $this->blockedPhones = [];
    }
}