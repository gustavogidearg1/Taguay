<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    /*
    | WhatsApp Business Platform (Cloud API de Meta) para cargar Avance de Campo por WhatsApp.
    | Los valores salen de developers.facebook.com → tu app → WhatsApp → Configuración de la API.
    | Ver docs/whatsapp/tutorial-whatsapp.md
    */
    'whatsapp' => [
        'token'           => env('WHATSAPP_TOKEN'),            // token de acceso (temporal o permanente)
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),  // "Identificador del número de teléfono"
        'verify_token'    => env('WHATSAPP_VERIFY_TOKEN'),     // texto secreto que inventamos para verificar el webhook
        'app_secret'      => env('WHATSAPP_APP_SECRET'),       // "Clave secreta de la app": valida que los avisos vengan de Meta
        'api_version'     => env('WHATSAPP_API_VERSION', 'v27.0'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
