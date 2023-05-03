# sms.to-php
A PHP library for communicating with the SMS.to REST API

## Installation

You need to have an working account on  Sms.To with sufficient balance loaded to be able to use this package. If you do not have one, [please get one here](https://sms.to).

Require this package with composer.

```shell
composer require intergo/sms.to-php
```

## Prepare Client

```php
<?php
$clientId = 6; // Your client id from www.sms.to
$clientSecret = 'xxxx'; // Your client secret from www.sms.to
$username = 'email@example.com'; // Your email from www.sms.to
$password = 'password'; // Your password from www.sms.to
$client = new Intergo\SmsTo\Http\Client($clientId, $clientSecret, $username, $password);

```

### Sending SMS to multiple numbers (broadcasting):
```php
// Text message that will be sent to multiple numbers:
$message = 'Hello World!';

// Array of mobile phone numbers (starting with the "+" sign and country code):
$recipients = ['+4474*******', '+35799******', '+38164*******'];

// Send (broadcast) the $message to $recipients: 
$client->setMessage($message)
    ->setRecipients($recipients)
    ->sendMultiple();
```
As for the sender ID and callback URL, the values set in the configuration file will be used by default. You can also specify these values by using the `->setSenderId()` and `->setCallbackUrl()` methods:
```php
$client->setMessage($message)
    ->setRecipients($recipients)
    ->setSenderId('YOUR_NAME')
    ->setCallbackUrl('https://your-site.com/smscallback.php')
    ->sendMultiple();
```
Please note that using these methods will override the values set in the configuration file.


### Sending different SMS to single numbers:

```php
 $messages = [
    [
        'to' => '+4474*******',
        'message' => 'Hello World!'
    ],
    [
        'to' => '+35799******',
        'message' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ],
];

$client->setMessages($messages)->sendSingle();
```

### Sending single SMS to a list:

```php
    $message = 'Hello World!';
    $client->setMessage($message)
     ->setListId(1)
     ->setSenderId('YOUR_NAME')
     ->setCallbackUrl('https://your-site.com/smscallback.php')
     ->sendList();
```

### Get Balance:

```php

$client->getBalance();
```

### Fetch paginated lists:

```php
$client->getLists(['limit' => 100, 'page' => 1, 'sort' => 'created_at', 'search' => 'My List']);
```

| Parameter        | Value           | Required  |
| ------------- |-------------| -----|
| limit      | 100 | No |
| page      | 1      |   No |
| sort | created_at      |   No |
| search | name | No |

### Fetch single list:

```php
$client->getList(1);
```
| Parameter        | Value           | Required  |
| ------------- |-------------| -----|
| list id      | 1 | Yes |


### Handling Webhook/Callback

When `Callback URL` is specified, we send Callback data to the `Callback URL`. The parameters are sent via a POST request to your `Callback URL`. SMS.to will be expecting response 200 OK in return, or it will keep retrying every 15 minutes until the Callback expires (up to 48 hours).


```php
// https://your-site.com/smscallback.php
// Use following data to update status of your sms.
$_POST['trackingId']
$_POST['messageId']
$_POST['phone']
$_POST['status']
$_POST['parts']
$_POST['price']

```

| Parameter        | Sample           |
| ------------- |-------------|
| trackingId      | 185c9d63-dae2-4614-b0f4-48453e870dcf |
| messageId      | 185c9d63-dae2-4614-b0f4-48453e870dcf      |
| phone | +3579958****      |
| status | SENT |
| parts | 1 |
| price | 0.015 |
## Documentation

The documentation for the SMS.to REST API is located [here](https://sms.to/api-docs)
