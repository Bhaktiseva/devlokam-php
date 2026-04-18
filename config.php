<?php

declare(strict_types=1);

return [
    'app_name' => 'DevLokam',
    'base_path' => env_value('APP_BASE_PATH', ''),
    'db_host' => env_value('DB_HOST', 'localhost'),
    'db_port' => env_value('DB_PORT', '3306'),
    'db_name' => env_value('DB_NAME', 'devlokam'),
    'db_user' => env_value('DB_USER', 'root'),
    'db_pass' => env_value('DB_PASS', ''),
];
