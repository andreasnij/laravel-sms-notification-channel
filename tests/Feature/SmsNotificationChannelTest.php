<?php

namespace LaravelSmsNotificationChannel\Tests\Feature;

use AnSms\Exception\SendException;
use AnSms\Message\MessageInterface;
use AnSms\SmsTransceiverInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use LaravelSmsNotificationChannel\Tests\Support\TestNotifiable;
use LaravelSmsNotificationChannel\Tests\Support\TestNotificationWithMessage;
use LaravelSmsNotificationChannel\Tests\Support\TestNotificationWithString;
use LaravelSmsNotificationChannel\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SmsNotificationChannelTest extends TestCase
{
    private MockObject $smsTransceiverMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->smsTransceiverMock = $this->createMock(SmsTransceiverInterface::class);
        $this->app->instance(SmsTransceiverInterface::class, $this->smsTransceiverMock);
    }

    public function testCanNotifyNotifiableWithMessageNotification(): void
    {
        $this->smsTransceiverMock->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (MessageInterface $message) {
                return (string) $message->getTo() === '46700000001'
                    && $message->getText() === 'Test message';
            }));

        $notifiable = new TestNotifiable();
        $notifiable->notify(new TestNotificationWithMessage());
    }

    public function testCanNotifyNotifiableWithStringNotification(): void
    {
        $this->smsTransceiverMock->expects($this->once())
            ->method('sendMessage')
            ->with($this->callback(function (MessageInterface $message) {
                return (string) $message->getTo() === '46700000002'
                    && $message->getText() === 'Test message 2';
            }));

        $notifiable = new TestNotifiable();
        $notifiable->notify(new TestNotificationWithString());
    }

    public function testNotificationFailedEventIsDispatchedOnError(): void
    {
        $notifiable = new TestNotifiable();
        $notification =  new TestNotificationWithString();

        $notificationFailedCount = 0;
        $dispatcher = $this->createMock(Dispatcher::class);
        $this->app->instance(Dispatcher::class, $dispatcher);
        $dispatcher->expects($this->atLeastOnce())
            ->method('dispatch')
            ->willReturnCallback(function ($event) use (&$notificationFailedCount) {

                if ($event instanceof NotificationFailed) {
                    $notificationFailedCount++;
                }
            });

        $this->smsTransceiverMock->expects($this->once())
            ->method('sendMessage')
            ->willThrowException(new SendException('Send failed'));

        $this->expectException(SendException::class);

        $notifiable->notify($notification);

        $this->assertSame(
            1,
            $notificationFailedCount,
            'Expected dispatch to be called exactly once with NotificationFailed'
        );
    }

    public function testWillNotSendMessageToEmpty(): void
    {
        $notifiable = new TestNotifiable();
        $notifiable->phone_number = null;

        $this->smsTransceiverMock->expects($this->never())->method('sendMessage');

        $notifiable->notify(new TestNotificationWithString());
    }
}
