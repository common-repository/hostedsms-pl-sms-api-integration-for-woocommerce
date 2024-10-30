<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses\ResponseObjects;

class DeliveryReport
{
    /** @var string */
    public $reportId;
    /** @var string */
    public $phone;
    /** @var string */
    public $messageId;
    /** 
     * Message status
     * -3 - message declined by operator
     * -2 - message outdated
     * -1 - message undelivered
     *  0 - undefined state (shouldn't ever occur)
     *  1 - message delivered  
     * @var int 
     */
    public $status;
    /** @var string */
    public $deliveryTime;

    function __construct($report)
    {
        $this->reportId = $report->ReportId;
        $this->phone = $report->Phone;
        $this->messageId = $report->MessageId;
        $this->status = $report->Status;
        $this->deliveryTime = $report->DeliveryTime;
    }
}