<?php
require 'vendor/autoload.php';
use HostedSms\WebService\HostedSmsWebService;
use PHPUnit\Framework\TestCase;
class WebServiceInvalidCredentialsTest extends TestCase
{
    private $hostedSms;
    private function prepareData()
    {
        $this->hostedSms = new HostedSmsWebService('invalid.user', 'invalid.password');
    }

    /** @test */
    public function test_SendSms_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();
        $phone = '48000000000';
	    $message = 'test';
        $sender = 'Test';
        $currentDateTime = date('Y-m-d H:i:s');
	    $transactionId = $sender . $phone . $message . $currentDateTime; 

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->sendSms($phone, $message, $sender, $transactionId);
    }

    public function test_SendSmses_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();
        $phones = ['48000000000', '48111111111'];
	    $message = 'test';
        $sender = 'Test';
        $currentDateTime = date('Y-m-d H:i:s');
	    $transactionId = $sender . $message . $currentDateTime; 

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->sendSmses($phones, $message, $sender, $transactionId);
    }

    public function test_CheckPhones_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();
        $phones = ['48000000000', '48111111111'];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->checkPhones($phones);
    }

    public function test_ConvertToGsm7_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();
        $text = 'text';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->convertToGsm7($text);
    }

    public function test_GetDeliveryReports_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();
        $messageIds = ['120dea2d-0d2e-4d20-ba68-ff49n9954db8', '733dea7d-0d4e-4p70-ba68-fa39b5164db8'];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->getDeliveryReports($messageIds);
    }

    public function test_GetInputSmses_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();
        $from = null;
        $to = null;
        $recipient = 'test';
        $markAsRead = false;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->getInputSmses($from, $to, $recipient, $markAsRead);
    }

    public function test_GetUnreadDeliveryReports_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->getUnreadDeliveryReports();
    }

    public function test_GetUnreadInputSmses_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->getUnreadInputSmses();
    }

    public function test_GetValidSenders_Should_Be_Invalid_Credentials()
    {
        $this->prepareData();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid Credentials');

        $response = $this->hostedSms->getValidSenders();
    }
}
?>