<?php

namespace LaravelSmsNotificationChannel\Tests\Support;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class TestNotifiable
{
    use Notifiable;

    public string $phone_number = '46700000002';

    public function routeNotificationForSms(Notification $notification): ?string
    {
        return $this->phone_number;
    }
}
