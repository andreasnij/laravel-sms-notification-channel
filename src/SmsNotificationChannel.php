<?php

namespace LaravelSmsNotificationChannel;

use AnSms\Exception\SendException;
use AnSms\Message\Message;
use AnSms\Message\MessageInterface;
use AnSms\SmsTransceiverInterface;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Illuminate\Contracts\Events\Dispatcher;

class SmsNotificationChannel
{
    public const NAME = 'sms';

    public function __construct(protected SmsTransceiverInterface $smsTransceiver, protected Dispatcher $dispatcher)
    {
    }

    public function send(mixed $notifiable, Notification $notification): ?MessageInterface
    {
        $message = $this->prepareMessage($notifiable, $notification);
        if (! $message) {
            return null;
        }

        try {
            $this->smsTransceiver->sendMessage($message);
        } catch (SendException | InvalidArgumentException $exception) {
            $this->dispatcher->dispatch(new NotificationFailed(
                $notifiable,
                $notification,
                static::NAME,
                [
                    'error' => $exception->getMessage(),
                    'message' => $message->getLogContext(),
                ],
            ));

            throw $exception;
        }

        return $message;
    }

    protected function prepareMessage(mixed $notifiable, Notification $notification): ?MessageInterface
    {
        /** @var MessageInterface|string $message */
        $message = $notification->toSms($notifiable);

        if (is_string($message)) {
            $notifiableTo = $notifiable->routeNotificationFor(static::NAME, $notification);
            if (! $notifiableTo) {
                return null;
            }

            $message = Message::create($notifiableTo, $message);
        }

        return $message;
    }
}
