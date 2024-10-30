HostedSMS API
================

PHP API Client HostedSMS.pl SimpleAPI and WEBSERWIS2SMS API

**[HostedSMS.pl API documentation](https://hostedsms.pl/pl/api-sms/opis-techniczny-api/)**

## Requirements
php >= 7.4  
composer (https://getcomposer.org/)

## Install package with dependencies
`composer require dcs-pl/hostedsms-php`

## SimpleApi

#### Send SMS
```php
require 'vendor/autoload.php';
use HostedSms\SimpleApi\HostedSmsSimpleApi;

$simpleApi = new HostedSmsSimpleApi($userEmail, $password);

$response = $simpleApi->sendSms($sender, $phone, $message);
```

## WebService API

#### Create client
```php
require 'vendor/autoload.php';
use HostedSms\WebService\HostedSmsWebService;

$client = new HostedSmsWebService($userEmail, $password);
```

#### Send SMS
```php
$response = $client->sendSms($phone, $message, $sender, $transactionId);
```

#### Send multiple SMSes
```php
$response = $client->sendSmses($phones, $message, $sender, $transactionId);
```

#### Get all valid senders for user
```php
$response = $client->getValidSenders();
```

#### Get delivery reports
```php
$response = $client->getDeliveryReports($messageIds);
```

#### Get unread delivery reports
```php
$response = $client->getunreadDeliveryReports();
```

#### Get received smses
```php
$response = $client->getInputSmses($from, $to, $recipient, $markAsRead);
```

#### Get unread received smses
```php
$response = $client->getUnreadInputSmses();
```

#### Check if phone numbers are valid
```php
$response = $client->checkPhones($phones);
```

#### Convert text to GSM7
```php
$response = $client->convertToGSM7($text);
```

#### Get customer info
```php
$response = $client->customerInfo();
```

