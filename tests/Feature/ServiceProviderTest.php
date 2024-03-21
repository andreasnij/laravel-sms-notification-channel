<?php

namespace LaravelSmsNotificationChannel\Tests\Feature;

use AnSms\Gateway\CellsyntGateway;
use AnSms\Gateway\FortySixElksGateway;
use AnSms\Gateway\NullGateway;
use AnSms\Gateway\TelenorGateway;
use AnSms\Gateway\TwilioGateway;
use AnSms\Gateway\VonageGateway;
use AnSms\SmsTransceiverInterface;
use Generator;
use Illuminate\Notifications\ChannelManager;
use LaravelSmsNotificationChannel\SmsNotificationChannel;
use LaravelSmsNotificationChannel\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use ReflectionClass;

class ServiceProviderTest extends TestCase
{
    public function testSmsNotificationChannelIsRegistered(): void
    {
        $channelManager = $this->app->make(ChannelManager::class);
        $channel = $channelManager->driver(SmsNotificationChannel::NAME);

        $this->assertSame(SmsNotificationChannel::class, get_class($channel));
    }

    public function testSmsTransceiverIsRegisteredCorrectWithNoConfig()
    {
        $smsTransceiver = $this->app->make(SmsTransceiverInterface::class);

        $this->assertInstanceOf(NullGateway::class, $this->getProtectedProperty($smsTransceiver, 'gateway'));
        $this->assertNull($this->getProtectedProperty($smsTransceiver, 'logger'));
        $this->assertNull($this->getProtectedProperty($smsTransceiver, 'defaultFrom'));
    }

    public function testSmsTransceiverIsRegisteredCorrectWithLogger()
    {
        $this->app['config']['sms.log_channel'] = 'single';
        $smsTransceiver = $this->app->make(SmsTransceiverInterface::class);

        $this->assertNotEmpty($this->getProtectedProperty($smsTransceiver, 'logger'));
    }

    public function testSmsTransceiverIsRegisteredCorrectWithDefaultFrom()
    {
        $this->app['config']['sms.default_from'] = 'Testing';
        $smsTransceiver = $this->app->make(SmsTransceiverInterface::class);

        $this->assertNotEmpty($this->getProtectedProperty($smsTransceiver, 'defaultFrom'));
    }

    #[DataProvider('gatewayDataProvider')]
    public function testSmsTransceiverIsRegisteredWithCorrectGateway(
        string $expectedGatewayClass,
        array $gatewayConfig
    ): void {
        $this->app->instance(HttpClientInterface::class, $this->createMock(HttpClientInterface::class));
        $this->app->instance(RequestFactoryInterface::class, $this->createMock(RequestFactoryInterface::class));
        $this->app->instance(StreamFactoryInterface::class, $this->createMock(StreamFactoryInterface::class));

        $this->app['config']['sms'] = $gatewayConfig;
        $smsTransceiver = $this->app->make(SmsTransceiverInterface::class);

        $this->assertInstanceOf($expectedGatewayClass, $this->getProtectedProperty($smsTransceiver, 'gateway'));
    }

    public static function gatewayDataProvider(): Generator
    {
        yield [CellsyntGateway::class, [
            'gateway' => 'cellsynt',
            'username' => 'user',
            'password' => 'pass',
        ]];

        yield [FortySixElksGateway::class, [
            'gateway' => 'forty_six_elks',
            'api_username' => 'user',
            'api_password' => 'pass',
        ]];

        yield [TelenorGateway::class, [
            'gateway' => 'telenor',
            'username' => 'user',
            'password' => 'pass',
            'customer_id' => 'cust',
            'customer_password' => 'pass2',
        ]];

        yield [TwilioGateway::class, [
            'gateway' => 'twilio',
            'account_sid' => 'sid',
            'auth_token' => 'token',
        ]];

        yield [VonageGateway::class, [
            'gateway' => 'vonage',
            'api_key' => 'key',
            'api_secret' => 'secret',
        ]];
    }

    private function getProtectedProperty(object $object, string $propertyName): mixed
    {
        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
