<?php

/** https://api.hostedsms.pl/WS/smssender.asmx for documentation */

namespace HostedSms\WebService;

use DateTimeInterface;

use HostedSms\WebService\Responses\{CheckPhonesResponse, ConvertToGsm7Response, GetDeliveryReportsResponse, 
    GetInputSmsesResponse, GetUnreadDeliveryReportsResponse, GetUnreadInputSmsesResponse, GetValidSendersResponse, SendSmsResponse, SendSmsesResponse, CustomerInfoResponse};

class HostedSmsWebService
{
    private $client;

    /** 
     * Create client for API with credentials
     * 
     * @param string $userEmail user login in hostedsms.pl
     * @param string $password user password in hostedsms.pl
     */
    public function __construct($userEmail, $password)
    {
        $this->client = new SoapRequestClient($userEmail, $password);
    }

    private function getIsoDateTime($dateTime)
    {
        if($dateTime instanceof DateTimeInterface)
            return $dateTime->format(DateTimeInterface::ATOM);
        else
            return $dateTime;
    }

    /**
     * Check if phone numbers are valid
     *
     * @param string[] $phones in '48xxxxxxxxx' format
     * 
     * @return CheckPhonesResponse if succesful request
     * 
     * @throws WebServiceException if failed request
     */
    public function checkPhones($phones)
    {
        $params = [
            'Phones' => $phones
        ];
        $response = $this->client->sendRequest('CheckPhones', $params);

        if (!$response->CheckPhonesResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new CheckPhonesResponse($response);
    }

    /**
     * Converts text to GSM7
     *
     * @param  string $text
     * 
     * @return ConvertToGsm7Response if succesful request
     * 
     * @throws WebServiceException if failed request 
     */
    public function convertToGsm7($text)
    {
        $params = [
            'Text' => $text
        ];
        $response = $this->client->sendRequest('ConvertToGsm7', $params);

        if (!$response->ConvertToGsm7Result) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new ConvertToGsm7Response($response);
    }

    /**
     * Gets delivery reports
     *
     * @param  string[] $messageIds
     * @param  bool $markAsRead should messages be marked as read
     * 
     * @return GetDeliveryReportsResponse if succesful request
     * 
     * @throws WebServiceException if failed request 
     */
    public function getDeliveryReports($messageIds, $markAsRead = false)
    {
        $params = [
            'MessageIds' => $messageIds,
            'MarkAsRead' => $markAsRead
        ];

        $response = $this->client->sendRequest('GetDeliveryReports', $params);

        if (!$response->GetDeliveryReportsResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new GetDeliveryReportsResponse($response);
    }

    /**
     * Gets received smses
     * 
     * @param DateTimeInterface|string $from in 'YYYY-MM-DDTHH:MM:SS' format if string
     * @param DateTimeInterface|string $to in 'YYYY-MM-DDTHH:MM:SS' format if string
     * @param string $recipient phone number in '48xxxxxxxxx' format
     * @param bool $markAsRead should messages be marked as read
     * 
     * @return GetInputSmsesResponse if succesful request
     * 
     * @throws WebServiceException if failed request 
     */
    public function getInputSmses($from, $to, $recipient, $markAsRead)
    {
        $params = [
            'From' => $this->getIsoDateTime($from),
            'To' => $this->getIsoDateTime($to),
            'Ricipient' => $recipient,
            'MarkAsRead' => $markAsRead
        ];
        $response = $this->client->sendRequest('GetInputSmses', $params);

        if (!$response->GetInputSmsesResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new GetInputSmsesResponse($response);
    }

    /**
     * Gets unread delivery reports
     * 
     * @return GetUnreadDeliveryReportsResponse if succesful request
     * 
     * @throws WebServiceException if failed request 
     */
    public function getUnreadDeliveryReports()
    {
        $params = [];
        $response = $this->client->sendRequest('GetUnreadDeliveryReports', $params);

        if (!$response->GetUnreadDeliveryReportsResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new GetUnreadDeliveryReportsResponse($response);
    }


    /**
     * Gets unread received smses
     * 
     * @return GetUnreadInputSmsesResponse if succesful request
     * 
     * @throws WebServiceException if failed request 
     */
    public function getUnreadInputSmses()
    {
        $params = [];
        $response = $this->client->sendRequest('GetUnreadInputSmses', $params);

        if (!$response->GetUnreadInputSmsesResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new GetUnreadInputSmsesResponse($response);
    }

    /**
     * Gets all valid senders for user
     * 
     * @return GetValidSendersResponse if succesful request
     * 
     * @throws WebServiceException if failed request 
     */
    public function getValidSenders()
    {
        $params = [];
        $response = $this->client->sendRequest('GetValidSenders', $params);

        if (!$response->GetValidSendersResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new GetValidSendersResponse($response);
    }

    /**
     * Send sms message via WebService API
     *
     * @param string $phone in '48xxxxxxxxx' format
     * @param string $message
     * @param string $sender
     * @param string $transactionId
     * @param DateTimeInterface|string $validityPeriod in 'YYYY-MM-DDTHH:MM:SS' format if string
     * @param integer $priority (optional) a number between 0 and 3
     * @param bool $flashSms (optional) should message be sent as flash sms
     * @param string $costCenter (optional) cost center name, null if none is used
     * @param bool $convertMessageToGSM7 (optional) shoud message be converted to GSM7
     * 
     * @return SendSmsResponse if successful request
     * 
     * @throws WebServiceException if failed request
     */
    public function sendSms(
        $phone,
        $message,
        $sender,
        $transactionId,
        $validityPeriod = null,
        $priority = 0,
        $flashSms = false,
        $costCenter = null,
        $convertMessageToGSM7 = null
    ) {
        $params = [
            'Phone' => $phone,
            'Message' => $message,
            'Sender' => $sender,
            'TransactionId' => $transactionId,
            'ValidityPeriod' => $this->getIsoDateTime($validityPeriod),
            'Priority' => $priority,
            'FlashSms' => $flashSms,
            'CostCenter' => $costCenter,
            'ConvertMessageToGSM7' => $convertMessageToGSM7
        ];

        $response = $this->client->sendRequest('SendSms', $params);

        if (!$response->SendSmsResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new SendSmsResponse($response);
    }

    /**
     * Send multiple smses message via WebService API
     *
     * @param string[] $phones in '48xxxxxxxxx' format
     * @param string $message
     * @param string $sender
     * @param string $transactionId
     * @param DateTimeInterface|string $validityPeriod in 'YYYY-MM-DDTHH:MM:SS' format if string
     * @param integer $priority (optional) a number between 0 and 3
     * @param bool $flashSms (optional) should message be sent as flash sms
     * @param string $costCenter (optional) cost center name, null if none is used
     * @param bool $convertMessageToGSM7 (optional) shoud message be converted to GSM7
     * 
     * @return SendSmsesResponse if successful request
     * 
     * @throws WebServiceException if failed request
     */
    public function sendSmses(
        $phones,
        $message,
        $sender,
        $transactionId,
        $validityPeriod = null,
        $priority = 0,
        $flashSms = false,
        $costCenter = null,
        $convertMessageToGSM7 = null
    ) {
        $params = [
            'Phones' => $phones,
            'Message' => $message,
            'Sender' => $sender,
            'TransactionId' => $transactionId,
            'ValidityPeriod' => $this->getIsoDateTime($validityPeriod),
            'Priority' => $priority,
            'FlashSms' => $flashSms,
            'CostCenter' => $costCenter,
            'ConvertMessageToGSM7' => $convertMessageToGSM7
        ];

        $response = $this->client->sendRequest('SendSmses', $params);

        if (!$response->SendSmsesResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new SendSmsesResponse($response);
    }

    /**
     * Gets customer info
     * 
     * @return CustomerInfoResponse if successful request
     * 
     * @throws WebServiceException if failed request
     */
    public function customerInfo()
    {
        $params = [];
        $response = $this->client->sendRequest('CustomerInfo', $params);

        if (!$response->CustomerInfoResult) {
            throw new WebServiceException($response->ErrorMessage);
        }

        return new CustomerInfoResponse($response);
    }
}
