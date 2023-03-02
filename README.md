# Laravel SMS notification channel

[![Version](http://img.shields.io/packagist/v/andreasnij/laravel-sms-notification-channel.svg?style=flat-square)](https://packagist.org/packages/andreasnij/laravel-sms-notification-channel)

An SMS notification channel for the PHP framework Laravel.

Supported SMS gateways:

- [46elks](https://46elks.com/)                         
- [Cellsynt](https://www.cellsynt.com)     
- [Telenor SMS Pro](https://www.smspro.se/)   
- [Twilio](https://www.twilio.com)     
- [Vonage (formerly Nexmo)](https://www.vonage.com) 



## Installation
Add the package as a requirement to your `composer.json`:
```bash
$ composer require andreasnij/laravel-sms-notification-channel
```

If you want to use the **46elks**, **Cellsynt** or **Telenor SMS Pro** gateway you also you need
implementations of PSR-7: HTTP message interfaces, PSR-17: HTTP Factories and
PSR-18: HTTP Client. A popular package for this is Guzzle. You can install it with:

```bash
$ composer require guzzlehttp/guzzle:^7.0 guzzlehttp/psr7:^2.0
```

You may choose to use any other implementations of the PSR interfaces though.


If you want to use the **Twilio** gateway you also need to install the Twilio SDK:

```bash
$ composer require twilio/sdk
```

If you want to use the **Vonage** gateway you also need to install the Vonage client:

```bash
$ composer require vonage/client-core
```

Next you need to publish the configuration file to your config directory:

```bash
$ php artisan vendor:publish --provider="LaravelSmsNotificationChannel\ServiceProvider" --tag="config"
```

Edit your `sms.php` configuration file to fit your application.



## Usage example
```php
class TestUser 
{
    use Notifiable;

    public string $phone_number = '46700000000';

    public function routeNotificationForSms(Notification $notification): ?string
    {
        return $this->phone_number;
    }
}

class TestNotification extends Notification
{
    public function via(mixed $notifiable): array
    {
        return ['sms'];
    }

    public function toSms(mixed $notifiable): string
    {
        return 'Test message';
    }
}

$user = new User();
$user->notify(new TestNotification());
```

You can also use this package to send sms manually (without notifications):

```php
use AnSms\SmsTransceiverInterface;
use AnSms\Message\Message;

$smsTransceiver = app(SmsTransceiverInterface::class);

$message = Message::create('46700000000', 'Hello world!');
$smsTransceiver->sendMessage($message);
```



## Requirements
- Requires PHP 8.0 or above.

## Author
Andreas Nilsson (<https://github.com/andreasnij>)

## License
This software is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details.
