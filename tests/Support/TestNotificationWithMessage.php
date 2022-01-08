<?php

namespace LaravelSmsNotificationChannel\Tests\Support;

use AnSms\Message\Message;
use AnSms\Message\MessageInterface;
use Illuminate\Notifications\Notification;
use LaravelSmsNotificationChannel\SmsNotificationChannel;

class TestNotificationWithMessage extends Notification
{
    public function via(mixed $notifiable): array
    {
        return [SmsNotificationChannel::NAME];
    }

    public function toSms(mixed $notifiable): MessageInterface
    {
        return Message::create('46700000001', 'Test message');
    }
}
