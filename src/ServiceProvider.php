<?php

namespace LaravelSmsNotificationChannel;

use AnSms\Gateway\GatewayInterface;
use AnSms\Gateway\NullGateway;
use AnSms\Gateway\CellsyntGateway;
use AnSms\Gateway\FortySixElksGateway;
use AnSms\Gateway\TelenorGateway;
use AnSms\Gateway\TwilioGateway;
use AnSms\Gateway\VonageGateway;
use AnSms\SmsTransceiver;
use AnSms\SmsTransceiverInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Contracts\Foundation\Application;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Twilio\Rest\Client as TwilioClient;
use Vonage\Client as VonageClient;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->app->singleton(SmsTransceiverInterface::class, function (Application $app) {
            $gateway = $this->createSmsGateway($app);
            $logger = $app['config']['sms.log'] ? Log::driver() : null;

            $smsTransceiver = new SmsTransceiver($gateway, $logger);

            if (($defaultFrom = $app['config']['sms.default_from'])) {
                $smsTransceiver->setDefaultFrom($defaultFrom);
            }

            return $smsTransceiver;
        });

        Notification::resolved(static function (ChannelManager $service) {
            $service->extend(SmsNotificationChannel::NAME, function (Application $app) {
                return new SmsNotificationChannel(
                    $app->make(SmsTransceiverInterface::class),
                    $app->make(Dispatcher::class)
                );
            });
        });
    }

    public function boot()
    {
        $this->publishes([ __DIR__.'/../config/sms.php' => config_path('sms.php')], 'config');
    }

    private function createSmsGateway(Application $app): GatewayInterface
    {
        if (!($gatewayName = $app['config']['sms.gateway'])) {
            return new NullGateway();
        }

        $httpClient = $app->has(HttpClientInterface::class) ? $app->make(HttpClientInterface::class) : null;
        $requestFactory = $app->has(RequestFactoryInterface::class) ? $app->make(RequestFactoryInterface::class) : null;
        $streamFactory = $app->has(StreamFactoryInterface::class) ? $app->make(StreamFactoryInterface::class) : null;

        return match ($gatewayName) {
            'cellsynt' => new CellsyntGateway(
                $app['config']['sms.username'],
                $app['config']['sms.password'],
                $httpClient,
                $requestFactory,
                $streamFactory,
            ),
            'forty_six_elks' => new FortySixElksGateway(
                $app['config']['sms.api_username'],
                $app['config']['sms.api_password'],
                $httpClient,
                $requestFactory,
                $streamFactory,
            ),
            'telenor' => new TelenorGateway(
                $app['config']['sms.username'],
                $app['config']['sms.password'],
                $app['config']['sms.customer_id'],
                $app['config']['sms.customer_password'],
                $app['config']['sms.supplementary_information'],
                $httpClient,
                $requestFactory,
                $streamFactory,
            ),
            'twilio' => new TwilioGateway(
                $app['config']['sms.account_sid'],
                $app['config']['sms.auth_token'],
                $app->has(TwilioClient::class) ? $app->make(TwilioClient::class) : null,
            ),
            'vonage' => new VonageGateway(
                $app['config']['sms.api_key'],
                $app['config']['sms.api_secret'],
                $app->has(VonageClient::class) ? $app->make(VonageClient::class) : null,
            ),
            default => throw new \InvalidArgumentException("Unknown sms gateway \"{$gatewayName}\""),
        };
    }
}
