# Laravel SMS notification channel

An SMS notification channel for the PHP framework Laravel.

Supported SMS gateways:

|                                                   | Configuration gateway name | Required configuration keys                                                                                              | 
|---------------------------------------------------|:---------------------------|:-------------------------------------------------------------------------------------------------------------------------|
| [46elks](https://46elks.com/)                     | `forty_six_elks`           | `username`,  <br/>`password`                                                                                             |
| [Cellsynt](https://www.cellsynt.com)              | `cellsynt`                 | `api_username`,  <br/>`api_password`                                                                                     |
| [Telenor SMS Pro](https://www.smspro.se/)         | `telenor`                  | `username`, <br/>`password`,  <br/>`customer_id`,  <br/>`customer_password`                                              |
| [Twilio](https://www.twilio.com)                  | `twilio`                   | `account_sid`, <br/>`auth_token`                                                                                         |
| [Vonage (formerly Nexmo)](https://www.vonage.com) | `vonage`                   | `api_key`, <br/>`api_secret`                                                                                             |


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

To read more about this at [andreasnij/laravel-sms-notification-channel](https://github.com/andreasnij/an-sms), which 
is the package used to send the sms behind the scenes. 

If you want to use the **Twilio** gateway you also need to install the Twilio SDK:

```bash
$ composer require twilio/sdk
```

If you want to use the **Vonage** gateway you also need to install the Vonage client:

```bash
$ composer require vonage/client-core
```



Next you need to add the sms configuration to your `config/services.php` configuration file. Example:

```php
'sms' => [
    'gateway' => 'forty_six_elks',
    'username' => 'some username',
    'password' => 'a password',
    'log' => true,
    'default_from' => 'App name'
];
```

Check the gateway table above to see which configuration keys your SMS gateway requires.



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
Andreas Nilsson (<http://github.com/andreasnij>)

## License
This software is licensed under the MIT License - see the [LICENSE](LICENSE.md) file for details.
