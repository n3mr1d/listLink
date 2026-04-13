<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tor Gateway URL
    |--------------------------------------------------------------------------
    | The .onion address served via Tor. This is the primary APP_URL.
    | Used for the Tor gate badge and onion-location header.
    |
    */
    'tor_url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Clearnet Gateway URL
    |--------------------------------------------------------------------------
    | The HTTPS clearnet address. Used for the clearnet gate badge and for
    | generating live-search API URLs when the page is loaded over HTTPS
    | (to avoid Mixed Content browser errors).
    |
    */
    'clearnet_url' => env('CLEARNET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Contact Email
    |--------------------------------------------------------------------------
    | The official contact email for the platform, used in footers and support
    | pages.
    |
    */
    'contact_email' => env('CONTACT_EMAIL', 'hello@example.com'),
    'whomai' => env('WHOAMI', 'ADMIN'),

];
