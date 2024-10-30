<?php
require 'vendor/autoload.php';
use HostedSms\WebService\HostedSmsWebService;
use PHPUnit\Framework\TestCase;
class WebServiceValidDataTest extends TestCase
{
    private $hostedSms;
    private $phone;
    private $sender;
    private function prepareData()
    {
        $this->hostedSms = new HostedSmsWebService(getenv('HSMS_TEST_USERNAME'), getenv('HSMS_TEST_PASSWORD'));
        $this->phone = getenv('HSMS_TEST_PHONE');
        $this->sender = getenv('HSMS_TEST_SENDER');
    }

    /** @test */
    public function test_SendSms_Should_Be_Valid()
    {
        $this->prepareData();
	    $message = 'test';
        $currentDateTime = date('Y-m-d H:i:s');
	    $transactionId = $this->sender . $this->phone . $message . $currentDateTime; 

        $response = $this->hostedSms->sendSms($this->phone, $message, $this->sender, $transactionId);

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_SendSmses_Should_Be_Valid()
    {
        $this->prepareData();
        $phones = [$this->phone];
	    $message = 'test';
        $sender = $this->sender;
        $currentDateTime = date('Y-m-d H:i:s');
	    $transactionId = $sender . $message . $currentDateTime; 

        $response = $this->hostedSms->sendSmses($phones, $message, $sender, $transactionId);

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_CheckPhones_Should_Be_Valid()
    {
        $this->prepareData();
        $phones = [$this->phone];

        $response = $this->hostedSms->checkPhones($phones);

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_ConvertToGsm7_Should_Be_Valid()
    {
        $this->prepareData();
        $text = 'text';

        $response = $this->hostedSms->convertToGsm7($text);

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_GetDeliveryReports_Should_Be_Valid()
    {
        $this->prepareData();
        $messageIds = ['450dwa2d-0d2e-4d40-ba65-ff39b5164db8', '750dea1d-0d2e-4d40-ba68-ff79b5164db8'];

        $response = $this->hostedSms->getDeliveryReports($messageIds);

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_GetInputSmses_Should_Be_Valid()
    {
        $this->prepareData();
        $from = null;
        $to = null;
        $recipient = $this->phone;
        $markAsRead = false;

        $response = $this->hostedSms->getInputSmses($from, $to, $recipient, $markAsRead);

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_GetUnreadDeliveryReports_Should_Be_Valid()
    {
        $this->prepareData();

        $response = $this->hostedSms->getUnreadDeliveryReports();

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_GetUnreadInputSmses_Should_Be_Valid()
    {
        $this->prepareData();

        $response = $this->hostedSms->getUnreadInputSmses();

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_GetValidSenders_Should_Be_Valid()
    {
        $this->prepareData();

        $response = $this->hostedSms->getValidSenders();

        $this->assertNotNull($response->currentTime);
    }

    /** @test */
    public function test_CustomerInfo_Should_Be_Valid()
    {
        $this->prepareData();

        $response = $this->hostedSms->customerInfo();

        $this->assertNotNull($response->currentTime);
    }
}
?>