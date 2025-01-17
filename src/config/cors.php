<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*'],  // CORSを適用するAPIのパスを指定

    'allowed_methods' => ['*'],  // 許可するHTTPメソッド

    'allowed_origins' => ['*'],  // 任意のオリジンを許可

    'allowed_headers' => ['*'],  // 任意のヘッダーを許可

    'exposed_headers' => [],  // クライアントに渡すヘッダー

    'max_age' => 0,

    'supports_credentials' => false,
];
