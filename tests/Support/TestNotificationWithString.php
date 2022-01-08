<?php

namespace LaravelSmsNotificationChannel\Tests\Support;

use Illuminate\Notifications\Notification;
use LaravelSmsNotificationChannel\SmsNotificationChannel;

class TestNotificationWithString extends Notification
{
    public function via(mixed $notifiable): array
    {
        return [SmsNotificationChannel::NAME];
    }

    public function toSms(mixed $notifiable): string
    {
        return 'Test message 2';
    }
}
