<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses\ResponseObjects;

class InputSms
{
    /** @var string */
    public $messageId;
    /** @var string */
    public $phone;
    /** @var string */
    public $recipient;
    /** @var string */
    public $message;
    /** @var string */
    public $receivedTime;

    function __construct($inputSms)
    {
        $this->messageId = $inputSms->MessageId;
        $this->phone = $inputSms->Phone;
        $this->recipient = $inputSms->Recipient;
        $this->message = $inputSms->Message;
        $this->receivedTime = $inputSms->ReceivedTime;
    }
}