<?php
require 'vendor\autoload.php';
use HostedSms\SimpleApi\HostedSmsSimpleApi;
use PHPUnit\Framework\TestCase;
class SimpleApiSendSmsTest extends TestCase
{
    private $hostedSms;

    private function prepareData($userEmail, $password)
    {
        $this->hostedSms = new HostedSmsSimpleApi($userEmail, $password);
    }

    public function providerTestData()
    {
        return [
            [getenv('HSMS_TEST_USERNAME'), getenv('HSMS_TEST_PASSWORD'), getenv('HSMS_TEST_SENDER'), getenv('HSMS_TEST_PHONE')]
        ];
    }

    /** @test */
    /**
     * @dataProvider providerTestData
     */
    public function test_Send_Sms_Should_Be_Successful($userEmail, $password, $sender, $phone): void
    {
        $this->prepareData($userEmail, $password);
        $message = "test sms";
	    $v = null;
	    $convertMessageToGSM7 = false;

        $response = $this->hostedSms->sendSms(
            $sender,
            $phone,
            $message,
            $v,
            $convertMessageToGSM7
        );

        $this->assertNotEmpty($response);
        $this->assertNotNull($response);
    }

    /** @test */
    public function test_Send_Sms_With_Invalid_User_Should_Throw_Exception(): void
    {
        $this->prepareData('invalid.user@dcs.pl', 'invalidpassword');
	    $sender = 'invalidsender';
	    $phone = '000000000';
	    $v = null;
	    $convertMessageToGSM7 = false;
        $message = "test message";

        $this->expectException(Exception::class);

        $response = $this->hostedSms->sendSms(
            $sender,
            $phone,
            $message,
            $v,
            $convertMessageToGSM7
        );
    }
}
?>