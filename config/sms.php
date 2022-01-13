<?php

return [

    /*
     * Sms gateway to use.
     * Supported gateways: cellsynt, forty_six_elks telenor, twilio, vonage
     * Can also be null for testing purposes.
     */
    'gateway' => env('SMS_GATEWAY'),

    /*
    * Sms credentials.
    * The gateways requires different credentials. Uncomment the gateway
    * credentials you will use.
    */

    // cellsynt
    //'username' => env('SMS_USERNAME'),
    //'password' => env('SMS_PASSWORD'),

    // forty_six_elks
    //'api_username' => env('SMS_API_USERNAME'),
    //'api_password' => env('SMS_API_PASSWORD'),

    // telenor
    //'username' => env('SMS_USERNAME'),
    //'password' => env('SMS_PASSWORD'),
    //'customer_id' => env('SMS_CUSTOMER_ID'),
    //'customer_password' => env('SMS_CUSTOMER_PASSWORD'),

    // twilio
    //'account_sid' => env('SMS_ACCOUNT_SID'),
    //'auth_token' => env('SMS_AUTH_TOKEN'),

    // vonage
    //'api_key' => env('SMS_API_KEY'),
    //'api_secret' => env('SMS_API_SECRET'),

    /*
     * If the sms package should log actions to the info log'
     */
    'log' => env('SMS_LOG', false),


    /**
     * The default name or number the messages should be sent from.
     */
    'default_from' => env('APP_NAME'),
];
