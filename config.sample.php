<?php

declare(strict_types=1);

return [
    'app_name' => 'DevLokam',
    'base_path' => env_value('APP_BASE_PATH', ''),
    'app_domain' => env_value('APP_DOMAIN', 'https://devlokam.in'),
    'db_host' => env_value('DB_HOST', 'localhost'),
    'db_port' => env_value('DB_PORT', '3306'),
    'db_name' => env_value('DB_NAME', 'devlokam'),
    'db_user' => env_value('DB_USER', 'root'),
    'db_pass' => env_value('DB_PASS', ''),
    'razorpay_key_id' => env_value('RAZORPAY_KEY_ID', ''),
    'razorpay_key_secret' => env_value('RAZORPAY_KEY_SECRET', ''),
    'razorpay_webhook_secret' => env_value('RAZORPAY_WEBHOOK_SECRET', ''),
];
