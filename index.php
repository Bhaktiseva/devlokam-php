<?php

declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/views.php';

$route = app_route();

http_response_code($route['status']);

render_app($route);
