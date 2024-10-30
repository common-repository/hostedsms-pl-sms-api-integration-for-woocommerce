<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService\Responses;

use HostedSms\WebService\Responses\ResponseObjects\DeliveryReport;

class GetDeliveryReportsResponse extends Response
{
    /** @var DeliveryReport[] */
    public $deliveryReports;
    function __construct($response)
    {
        parent::__construct($response);

        if (isset($response->DeliveryReports->DeliveryReport)) {
            $arrayObject = $this->createArray($response->DeliveryReports->DeliveryReport);
            foreach ($arrayObject as $obj) {
                $this->deliveryReports[] = new DeliveryReport($obj);
            }
        } else
            $this->deliveryReports = [];
    }
}