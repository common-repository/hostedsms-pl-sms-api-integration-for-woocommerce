<?php

namespace HostedSms\WebService;

use SoapClient;

class SoapRequestClient
{
    private $apiWSDL = 'https://api.hostedsms.pl/WS/smssender.asmx?WSDL';

    private $client;
    private $credentials;

    public function __construct($userEmail, $password)
    {
        $this->credentials = [
            'UserEmail' => $userEmail,
            'Password' => $password
        ];
        $this->client = new SoapClient($this->apiWSDL, [
            'exceptions' => true,
            'soap_version' => SOAP_1_2,
        ]);
    }

    public function sendRequest($apiCall, $params)
    {
        $args = array_merge($this->credentials, $params);
        $response = $this->client->__soapCall($apiCall, [$args]);

        return $response;
    }
}
