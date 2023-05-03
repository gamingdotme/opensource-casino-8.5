<p align="center">
  <a href="https://sms.to"><img width="400" src="https://sms.to/images/logo.svg"></a>
</p>

<h2 align="center">
SMS API integration for Laravel / Lumen (https://sms.to)
</h2>

This package allows you to send SMS messages to your users in just a few steps.  

1) Intergate [SMS.to API](https://sms.to/api-docs/) (this repo) into your Laravel and/or Lumen projects.
2) Create account on SMS Gateway provider [HERE](https://sms.to/register)


[SMS.to](https://sms.to) is a bulk SMS / marketing / SMS API platform that offers a smarter way for businesses to communicate with customers through multiple channels. Reach customers in their preferred channel through SMS, WhatsApp or Viber Messages. For more details, please visit: https://sms.to

## Requirements

This package requires Laravel 5.2 or higher and you need to have a working [SMS.to](https://sms.to) account with sufficient balance. 

## Installation

You can install this package into Laravel project via Composer:

```shell
composer require intergo/sms.to-laravel-lumen
```

If you are using Laravel 5.5 or above, the package will automatically register the `SmsTo` provider.
If you are using Laravel 5.4 or below, add `Intergo\SmsTo\ServiceProvider::class` to the `providers` array and `'SmsTo' => Intergo\SmsTo\Facades\SmsToFacade::class` in the `aliases` array in  `config/app.php`:

```php
'providers' => [
    // Other service providers...
    Intergo\SmsTo\ServiceProvider::class,
],
'aliases' => [
    'SmsTo' => Intergo\SmsTo\Facades\SmsToFacade::class,
],
```
The instructions for installing this package into Lumen project are given [below](#lumen).

## Configuration

Publish the configuration file:

```shell
php artisan vendor:publish --provider="Intergo\SmsTo\ServiceProvider" --tag=config
```
Then set the values for the parameters in `config/smsto.php`. Credentials (`client_id`, `client_secret`, `username` and `password`) are required. Optionally, you can set your sender ID (`sender_id`) and callback URL (`callback_url`).

It is recommended that you set all these configuration values in your `.env` file:

```shell
#REQUIRED:
SMSTO_CLIENT_ID=
SMSTO_CLIENT_SECRET=
SMSTO_EMAIL=
SMSTO_PASSWORD=
#OPTIONAL:
SMSTO_BASE_URL=https://api.sms.to/v1
SMSTO_SENDER_ID=
SMSTO_CALLBACK_URL=
```

## Usage

After the package is installed and configured, the only thing you have to do is to use the `SmsTo` facade:

```php
use SmsTo;
```
in class files (typically controllers) in which you will use this package for sending SMS.

### Sending SMS to multiple numbers (broadcasting):
```php
// Text message that will be sent to multiple numbers:
$message = 'Hello World!';

// Array of mobile phone numbers (starting with the "+" sign and country code):
$recipients = ['+4474*******', '+35799******', '+38164*******'];

// Send (broadcast) the $message to $recipients: 
SmsTo::setMessage($message)
    ->setRecipients($recipients)
    ->sendMultiple();
```
As for the sender ID and callback URL, the values set in the configuration file will be used by default. You can also specify these values by using the `->setSenderId()` and `->setCallbackUrl()` methods:
```php
SmsTo::setMessage($message)
    ->setRecipients($recipients)
    ->setSenderId('YOUR_NAME')
    ->setCallbackUrl('https://your-site.com/smscallback')
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

SmsTo::setMessages($messages)->sendSingle();
```
### Working with lists
With your [SMS.to](https://sms.to) account you can create and manage your lists on https://sms.to/app#/manage/lists. Creating a list allows you to manage your customers more efficiently. It enables features like *Optout Management* and *Personalisation*.

**Fetch paginated lists:**
```php
SmsTo::getLists(['limit' => 100, 'page' => 1, 'sort' => 'created_at', 'search' => 'My List']);
```
The following parameters can be specified:

| Parameter | Type    | Description                                                                                            | Default Value | Required |
|-----------|---------|--------------------------------------------------------------------------------------------------------|---------------|----------|
| limit     | integer | The number of lists per page.                                                                         | 100           | No       |
| page      | integer | The page number (when you are using pagination).                                                        | 1             | No       |
| sort      | string  | The field which you want to sort,  e.g. use `created_at` for ASC order or `-created_at` for DESC order. | created_at    | No       |
| search    | string  | Keywords to search for a list name.                                                                     |               | No       |

The response will be in `array` format following the structure provided in our API documentation: https://sms.to/api-docs/endpoints.html#fetch-lists

**Fetch single list:**

```php
SmsTo::getList(1);
```
| Parameter        | Default Value           | Required  |
| ------------- |-------------| -----|
| list id      | 1 | Yes |

**Sending single SMS to a list:**

```php
$message = 'Hello World!';

SmsTo::setMessage($message)
    ->setListId(1)
    ->sendList();
```

### Get Balance

To get the current balance of your [SMS.to](https://sms.to) account as well as the approximate number of messages:

```php
SmsTo::getBalance();
```
Response: 

```php
array: [
  "balance" => 1296.81
  "currency" => "EUR"
  "sms_count" => 6454 // REMAINING SMS (APROX.)
]
```

### Handling Webhook/Callback

If specified, we will send responses to your *callback URL* via a **POST** method. [SMS.to](https://sms.to) will be expecting response with the status code `200` (OK) in return, or it will keep retrying every 15 minutes until the callback expires (up to 48 hours). 

Usage Example:

```php
// routes/web.php
Route::post('/smsto/callback', 'SmsToController@callback');
```

```php
// SmsToController.php
public function callback(Request $request)
{
    // Use following data to update status of your sms.
    $request->trackingId;
    $request->messageId;
    $request->phone;
    $request->status;
    $request->parts;
    $request->price;
}
```

| Parameter        | Sample           |
| ------------- |-------------|
| trackingId      | 185c9d63-dae2-4614-b0f4-48453e870dcf |
| messageId      | 185c9d63-dae2-4614-b0f4-xxxxxxxxxxxx      |
| phone | +3579958****      |
| status | SENT |
| parts | 1 |
| price | 0.015 |

## Lumen

Require this package with composer.

```shell
composer require intergo/sms.to-laravel-lumen
```

Uncomment the following lines in the bootstrap file:
```php
// bootstrap/app.php:
$app->withFacades();
$app->withEloquent();

// Add SmsTo Facade
if (!class_exists('SmsTo')) {
    class_alias('Intergo\SmsTo\Facades\SmsToFacade', 'SmsTo');
}
```
Configure the service provider (and AppServiceProvider if not already enabled):

```php
// bootstrap/app.php:
$app->register(App\Providers\AppServiceProvider::class);
$app->register(Intergo\SmsTo\LumenServiceProvider::class);
```

Publish the configuration file:

```shell
php artisan vendor:publish --provider="Intergo\SmsTo\LumenServiceProvider" --tag=config
```
Then set the values for the parameters in `config/smsto.php`. Credentials (`client_id`, `client_secret`, `username` and `password`) are required. Optionally, you can set your sender ID (`sender_id`) and callback URL (`callback_url`).

It is recommended that you set all these configuration values in your `.env` file:

```shell
#REQUIRED:
SMSTO_CLIENT_ID=
SMSTO_CLIENT_SECRET=
SMSTO_EMAIL=
SMSTO_PASSWORD=
#OPTIONAL:
SMSTO_SENDER_ID=
SMSTO_CALLBACK_URL=
```

Finally, update boostrap/app.php to load both config files:

```php
// bootstrap/app.php
$app->configure('smsto');
```

## Send SMS using Lumen

```php
// routes/web.php
$router->post('send', function () {
    $messages = [['to' => '+63917*******', 'message' => 'Hi Market!']];
    return \SmsTo::setMessages($messages)
               ->setSenderId('YOUR_NAME')
               ->setCallbackUrl('https://your-site.com/smscallback')
               ->sendSingle();
});
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
