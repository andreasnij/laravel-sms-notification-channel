# Laravel SMS notification channel

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

If you don't already have a http library implementing PSR-7, PSR-17 and PSR-18 installed you
need to add that to. For example:
```bash
$ composer require guzzlehttp/guzzle:^7.0 guzzlehttp/psr7:^2.0
```

To read more about this at [andreasnij/an-sms](https://github.com/andreasnij/an-sms), which 
is the package used to send the sms behind the scenes. 

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


## Requirements
- Requires PHP 8.0 or above.

## Author
Andreas Nilsson (<https://github.com/andreasnij>)

## License
This software is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details.
