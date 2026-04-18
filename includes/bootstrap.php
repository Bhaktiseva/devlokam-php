<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

date_default_timezone_set('Asia/Kolkata');

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

$configPath = __DIR__ . '/../config.php';
$sampleConfigPath = __DIR__ . '/../config.sample.php';
$appConfig = file_exists($configPath) ? require $configPath : require $sampleConfigPath;

function app_config(?string $key = null, mixed $default = null): mixed
{
    global $appConfig;

    if ($key === null) {
        return $appConfig;
    }

    return $appConfig[$key] ?? $default;
}

function app_url(string $path = ''): string
{
    $basePath = trim((string) app_config('base_path', ''), '/');
    $prefix = $basePath === '' ? '' : '/' . $basePath;
    $path = ltrim($path, '/');

    if ($path === '') {
        return $prefix === '' ? '/' : $prefix . '/';
    }

    return $prefix . '/' . $path;
}

function request_path(): string
{
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePath = trim((string) app_config('base_path', ''), '/');

    if ($basePath !== '' && str_starts_with($uriPath, '/' . $basePath)) {
        $uriPath = substr($uriPath, strlen('/' . $basePath));
    }

    if ($uriPath === '' || $uriPath === false) {
        return '/';
    }

    return '/' . trim($uriPath, '/');
}

function app_route(): array
{
    $path = request_path();
    $segments = array_values(array_filter(explode('/', trim($path, '/'))));

    if ($segments === []) {
        return ['page' => 'home', 'slug' => null, 'status' => 200];
    }

    return match ($segments[0]) {
        'pujas' => ['page' => 'pujas', 'slug' => null, 'status' => 200],
        'puja' => ['page' => 'puja-detail', 'slug' => $segments[1] ?? null, 'status' => isset($segments[1]) ? 200 : 404],
        'temples' => ['page' => 'temples', 'slug' => null, 'status' => 200],
        'store' => ['page' => 'store', 'slug' => null, 'status' => 200],
        'cart' => ['page' => 'cart', 'slug' => null, 'status' => 200],
        'services', 'seva' => ['page' => 'seva', 'slug' => null, 'status' => 200],
        'seva-calendar' => ['page' => 'calendar', 'slug' => null, 'status' => 200],
        'profile' => ['page' => 'profile', 'slug' => null, 'status' => 200],
        'signin' => ['page' => 'signin', 'slug' => null, 'status' => 200],
        default => ['page' => 'not-found', 'slug' => null, 'status' => 404],
    };
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
    return trim($value, '-');
}

function money(float|int|string $value): string
{
    return 'Rs ' . number_format((float) $value, 0);
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function pull_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }

    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';

    if (!is_string($token) || !is_string($sessionToken) || !hash_equals($sessionToken, $token)) {
        flash('error', 'Your session token expired. Please try again.');
        redirect($_POST['redirect'] ?? app_url());
    }
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function db(): ?PDO
{
    static $pdo = false;

    if ($pdo !== false) {
        return $pdo;
    }

    $host = (string) app_config('db_host', '');
    $port = (string) app_config('db_port', '3306');
    $name = (string) app_config('db_name', '');
    $user = (string) app_config('db_user', '');
    $pass = (string) app_config('db_pass', '');

    if ($host === '' || $name === '' || $user === '') {
        $pdo = null;
        return $pdo;
    }

    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    } catch (Throwable $exception) {
        $pdo = null;
    }

    return $pdo;
}

function db_notice(): ?string
{
    return db() === null ? 'Database is not connected yet. Import database.sql in phpMyAdmin and update config.php for live data.' : null;
}

function fetch_all(string $sql, array $params = []): array
{
    $pdo = db();

    if ($pdo === null) {
        return [];
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
}

function fetch_one(string $sql, array $params = []): ?array
{
    $rows = fetch_all($sql, $params);
    return $rows[0] ?? null;
}

function execute_query(string $sql, array $params = []): bool
{
    $pdo = db();

    if ($pdo === null) {
        return false;
    }

    $statement = $pdo->prepare($sql);
    return $statement->execute($params);
}

function current_user(): ?array
{
    static $cachedUser = false;

    if ($cachedUser !== false) {
        return $cachedUser;
    }

    $userId = $_SESSION['user_id'] ?? null;
    if (!$userId) {
        $cachedUser = null;
        return $cachedUser;
    }

    $cachedUser = fetch_one('SELECT * FROM users WHERE id = :id LIMIT 1', ['id' => $userId]);
    return $cachedUser;
}

function password_matches(string $password, string $storedHash): bool
{
    if (str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$argon2')) {
        return password_verify($password, $storedHash);
    }

    return hash_equals($storedHash, hash('sha256', $password));
}

function sign_in_user(int $userId): void
{
    $_SESSION['user_id'] = $userId;
}

function sign_out_user(): void
{
    unset($_SESSION['user_id']);
}

function cart_map(): array
{
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    return $_SESSION['cart'];
}

function set_cart_map(array $items): void
{
    $_SESSION['cart'] = $items;
}

function cart_count(): int
{
    return array_sum(array_map('intval', cart_map()));
}

function cart_items(): array
{
    $cart = cart_map();
    if ($cart === []) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $products = fetch_all("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN product_categories c ON c.id = p.category_id WHERE p.id IN ({$placeholders}) ORDER BY p.name", array_keys($cart));

    foreach ($products as &$product) {
        $quantity = max(1, (int) ($cart[$product['id']] ?? 1));
        $product['cart_quantity'] = $quantity;
        $product['line_total'] = $quantity * (float) $product['price'];
    }
    unset($product);

    return $products;
}

function cart_total(): float
{
    $total = 0.0;
    foreach (cart_items() as $item) {
        $total += (float) $item['line_total'];
    }

    return $total;
}

function all_temples(array $filters = []): array
{
    $sql = 'SELECT * FROM temples WHERE 1 = 1';
    $params = [];

    if (!empty($filters['state'])) {
        $sql .= ' AND state = :state';
        $params['state'] = $filters['state'];
    }

    if (!empty($filters['search'])) {
        $sql .= ' AND (name LIKE :search OR city LIKE :search OR deity_primary LIKE :search)';
        $params['search'] = '%' . $filters['search'] . '%';
    }

    $sql .= ' ORDER BY name';

    return fetch_all($sql, $params);
}

function temple_states(): array
{
    return fetch_all('SELECT DISTINCT state FROM temples WHERE state <> "" ORDER BY state');
}

function featured_temples(int $limit = 3): array
{
    return fetch_all('SELECT * FROM temples ORDER BY id LIMIT ' . (int) $limit);
}

function find_temple_by_slug(string $slug): ?array
{
    return fetch_one('SELECT * FROM temples WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
}

function all_pujas(array $filters = []): array
{
    $sql = 'SELECT p.*, t.name AS temple_name, t.city, t.state FROM pujas p LEFT JOIN temples t ON t.id = p.temple_id WHERE 1 = 1';
    $params = [];

    if (!empty($filters['temple'])) {
        $sql .= ' AND t.slug = :temple_slug';
        $params['temple_slug'] = $filters['temple'];
    }

    if (!empty($filters['deity'])) {
        $sql .= ' AND p.deity = :deity';
        $params['deity'] = $filters['deity'];
    }

    $sql .= ' ORDER BY p.is_featured DESC, p.price ASC';

    return fetch_all($sql, $params);
}

function featured_pujas(int $limit = 4): array
{
    return fetch_all('SELECT p.*, t.name AS temple_name, t.city, t.state FROM pujas p LEFT JOIN temples t ON t.id = p.temple_id ORDER BY p.is_featured DESC, p.id LIMIT ' . (int) $limit);
}

function puja_deities(): array
{
    return fetch_all('SELECT DISTINCT deity FROM pujas WHERE deity <> "" ORDER BY deity');
}

function find_puja_by_slug(?string $slug): ?array
{
    if (!$slug) {
        return null;
    }

    return fetch_one(
        'SELECT p.*, t.name AS temple_name, t.city, t.state, t.region_label FROM pujas p LEFT JOIN temples t ON t.id = p.temple_id WHERE p.slug = :slug LIMIT 1',
        ['slug' => $slug]
    );
}

function benefits_for_puja(array $puja): array
{
    if (empty($puja['benefits_json'])) {
        return [];
    }

    $decoded = json_decode((string) $puja['benefits_json'], true);
    return is_array($decoded) ? $decoded : [];
}

function store_categories(): array
{
    return fetch_all('SELECT * FROM product_categories ORDER BY name');
}

function all_products(array $filters = []): array
{
    $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p LEFT JOIN product_categories c ON c.id = p.category_id WHERE 1 = 1';
    $params = [];

    if (!empty($filters['category'])) {
        $sql .= ' AND c.slug = :category_slug';
        $params['category_slug'] = $filters['category'];
    }

    $sql .= ' ORDER BY p.id';

    return fetch_all($sql, $params);
}

function find_product(int $productId): ?array
{
    return fetch_one('SELECT * FROM products WHERE id = :id LIMIT 1', ['id' => $productId]);
}

function all_sevas(array $filters = []): array
{
    $sql = 'SELECT * FROM sevas WHERE 1 = 1';
    $params = [];

    if (!empty($filters['place_group'])) {
        $sql .= ' AND place_group = :place_group';
        $params['place_group'] = $filters['place_group'];
    }

    $sql .= ' ORDER BY price ASC';

    return fetch_all($sql, $params);
}

function seva_places(): array
{
    return fetch_all('SELECT DISTINCT place_group FROM sevas WHERE place_group <> "" ORDER BY place_group');
}

function find_seva_by_slug(string $slug): ?array
{
    return fetch_one('SELECT * FROM sevas WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
}

function seva_calendar_events(array $filters = []): array
{
    $sql = 'SELECT e.*, s.name AS seva_name, s.slug AS seva_slug FROM seva_events e LEFT JOIN sevas s ON s.id = e.seva_id WHERE 1 = 1';
    $params = [];

    if (!empty($filters['place_group'])) {
        $sql .= ' AND e.place_group = :place_group';
        $params['place_group'] = $filters['place_group'];
    }

    $sql .= ' ORDER BY e.event_date ASC';

    return fetch_all($sql, $params);
}

function grouped_calendar_events(array $filters = []): array
{
    $groups = [];

    foreach (seva_calendar_events($filters) as $event) {
        $label = date('F Y', strtotime((string) $event['event_date']));
        $groups[$label][] = $event;
    }

    return $groups;
}

function require_login_for_profile(): void
{
    if (!current_user()) {
        flash('error', 'Please sign in first.');
        redirect(app_url('signin'));
    }
}

function post_value(string $key, string $default = ''): string
{
    $value = $_POST[$key] ?? $default;
    return is_string($value) ? trim($value) : $default;
}

function handle_actions(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return;
    }

    verify_csrf();

    $action = $_POST['action'] ?? '';
    $redirectTarget = is_string($_POST['redirect'] ?? null) ? $_POST['redirect'] : app_url();

    switch ($action) {
        case 'login':
            $user = fetch_one('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => post_value('email')]);
            if (!$user || !password_matches(post_value('password'), (string) $user['password_hash'])) {
                flash('error', 'Invalid email or password.');
                redirect($redirectTarget);
            }

            sign_in_user((int) $user['id']);
            flash('success', 'Welcome back, ' . $user['full_name'] . '.');
            redirect(app_url());

        case 'register':
            $name = post_value('full_name');
            $email = post_value('email');
            $password = post_value('password');

            if ($name === '' || $email === '' || $password === '') {
                flash('error', 'Name, email, and password are required.');
                redirect($redirectTarget);
            }

            if (fetch_one('SELECT id FROM users WHERE email = :email LIMIT 1', ['email' => $email])) {
                flash('error', 'That email is already registered.');
                redirect($redirectTarget);
            }

            $created = execute_query(
                'INSERT INTO users (full_name, email, password_hash, phone, role, created_at) VALUES (:full_name, :email, :password_hash, :phone, :role, NOW())',
                [
                    'full_name' => $name,
                    'email' => $email,
                    'password_hash' => hash('sha256', $password),
                    'phone' => post_value('phone'),
                    'role' => 'devotee',
                ]
            );

            if (!$created) {
                flash('error', 'Database is not connected yet. Please import database.sql first.');
                redirect($redirectTarget);
            }

            $newUser = fetch_one('SELECT * FROM users WHERE email = :email LIMIT 1', ['email' => $email]);
            if ($newUser) {
                sign_in_user((int) $newUser['id']);
            }

            flash('success', 'Your account has been created.');
            redirect(app_url('profile'));

        case 'logout':
            sign_out_user();
            flash('success', 'You have been signed out.');
            redirect(app_url());

        case 'add-to-cart':
            $productId = (int) ($_POST['product_id'] ?? 0);
            $product = find_product($productId);
            if (!$product) {
                flash('error', 'Product not found.');
                redirect($redirectTarget);
            }

            $cart = cart_map();
            $cart[$productId] = ($cart[$productId] ?? 0) + 1;
            set_cart_map($cart);
            flash('success', $product['name'] . ' added to cart.');
            redirect($redirectTarget);

        case 'update-cart':
            $productId = (int) ($_POST['product_id'] ?? 0);
            $mode = post_value('mode');
            $cart = cart_map();
            if (!isset($cart[$productId])) {
                redirect($redirectTarget);
            }

            if ($mode === 'decrease') {
                $cart[$productId] = max(0, ((int) $cart[$productId]) - 1);
            } elseif ($mode === 'increase') {
                $cart[$productId] = ((int) $cart[$productId]) + 1;
            } else {
                $cart[$productId] = 0;
            }

            if ($cart[$productId] <= 0) {
                unset($cart[$productId]);
            }

            set_cart_map($cart);
            redirect($redirectTarget);

        case 'checkout':
            $items = cart_items();
            if ($items === []) {
                flash('error', 'Your cart is empty.');
                redirect($redirectTarget);
            }

            $pdo = db();
            if ($pdo === null) {
                flash('error', 'Please connect MySQL before placing orders.');
                redirect($redirectTarget);
            }

            $fullName = post_value('full_name');
            $email = post_value('email');
            $phone = post_value('phone');
            $addressLine = post_value('address_line');
            $city = post_value('city');
            $state = post_value('state');
            $pincode = post_value('pincode');

            if ($fullName === '' || $email === '' || $phone === '' || $addressLine === '' || $city === '' || $state === '' || $pincode === '') {
                flash('error', 'Please complete the delivery form.');
                redirect($redirectTarget);
            }

            try {
                $pdo->beginTransaction();

                $statement = $pdo->prepare(
                    'INSERT INTO orders (user_id, full_name, email, phone, address_line, city, state, pincode, total_amount, status, payment_status, created_at)
                    VALUES (:user_id, :full_name, :email, :phone, :address_line, :city, :state, :pincode, :total_amount, :status, :payment_status, NOW())'
                );

                $statement->execute([
                    'user_id' => current_user()['id'] ?? null,
                    'full_name' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'address_line' => $addressLine,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode,
                    'total_amount' => cart_total(),
                    'status' => 'received',
                    'payment_status' => 'pending',
                ]);

                $orderId = (int) $pdo->lastInsertId();

                $itemStatement = $pdo->prepare(
                    'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, line_total)
                    VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price, :line_total)'
                );

                foreach ($items as $item) {
                    $itemStatement->execute([
                        'order_id' => $orderId,
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['cart_quantity'],
                        'unit_price' => $item['price'],
                        'line_total' => $item['line_total'],
                    ]);
                }

                $pdo->commit();
                set_cart_map([]);
                flash('success', 'Order placed successfully. Payment status is pending until your gateway is connected.');
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                flash('error', 'Order could not be placed. Please try again.');
            }

            redirect(app_url('cart'));

        case 'book-puja':
            $pujaId = (int) ($_POST['puja_id'] ?? 0);
            $puja = fetch_one('SELECT * FROM pujas WHERE id = :id LIMIT 1', ['id' => $pujaId]);
            if (!$puja) {
                flash('error', 'Puja not found.');
                redirect($redirectTarget);
            }

            $saved = execute_query(
                'INSERT INTO puja_bookings (user_id, puja_id, full_name, email, phone, gotra, preferred_date, occasion, address_line, city, state, pincode, amount, status, payment_status, created_at)
                VALUES (:user_id, :puja_id, :full_name, :email, :phone, :gotra, :preferred_date, :occasion, :address_line, :city, :state, :pincode, :amount, :status, :payment_status, NOW())',
                [
                    'user_id' => current_user()['id'] ?? null,
                    'puja_id' => $pujaId,
                    'full_name' => post_value('full_name'),
                    'email' => post_value('email'),
                    'phone' => post_value('phone'),
                    'gotra' => post_value('gotra'),
                    'preferred_date' => post_value('preferred_date'),
                    'occasion' => post_value('occasion'),
                    'address_line' => post_value('address_line'),
                    'city' => post_value('city'),
                    'state' => post_value('state'),
                    'pincode' => post_value('pincode'),
                    'amount' => $puja['price'],
                    'status' => 'received',
                    'payment_status' => 'pending',
                ]
            );

            flash($saved ? 'success' : 'error', $saved ? 'Puja booking captured successfully.' : 'Please connect MySQL before taking live puja bookings.');
            redirect($redirectTarget);

        case 'book-seva':
            $sevaId = (int) ($_POST['seva_id'] ?? 0);
            $seva = fetch_one('SELECT * FROM sevas WHERE id = :id LIMIT 1', ['id' => $sevaId]);
            if (!$seva) {
                flash('error', 'Seva not found.');
                redirect($redirectTarget);
            }

            $saved = execute_query(
                'INSERT INTO seva_bookings (user_id, seva_id, full_name, email, phone, booking_for, preferred_date, occasion, address_line, city, state, pincode, amount, status, payment_status, created_at)
                VALUES (:user_id, :seva_id, :full_name, :email, :phone, :booking_for, :preferred_date, :occasion, :address_line, :city, :state, :pincode, :amount, :status, :payment_status, NOW())',
                [
                    'user_id' => current_user()['id'] ?? null,
                    'seva_id' => $sevaId,
                    'full_name' => post_value('full_name'),
                    'email' => post_value('email'),
                    'phone' => post_value('phone'),
                    'booking_for' => post_value('booking_for'),
                    'preferred_date' => post_value('preferred_date'),
                    'occasion' => post_value('occasion'),
                    'address_line' => post_value('address_line'),
                    'city' => post_value('city'),
                    'state' => post_value('state'),
                    'pincode' => post_value('pincode'),
                    'amount' => $seva['price'],
                    'status' => 'received',
                    'payment_status' => 'pending',
                ]
            );

            flash($saved ? 'success' : 'error', $saved ? 'Seva booking captured successfully.' : 'Please connect MySQL before taking live seva bookings.');
            redirect($redirectTarget);

        case 'save-profile':
            require_login_for_profile();
            $saved = execute_query(
                'UPDATE users SET full_name = :full_name, phone = :phone, age = :age, gender = :gender, gotra = :gotra, address_line = :address_line, city = :city, state = :state, pincode = :pincode WHERE id = :id',
                [
                    'full_name' => post_value('full_name'),
                    'phone' => post_value('phone'),
                    'age' => post_value('age'),
                    'gender' => post_value('gender'),
                    'gotra' => post_value('gotra'),
                    'address_line' => post_value('address_line'),
                    'city' => post_value('city'),
                    'state' => post_value('state'),
                    'pincode' => post_value('pincode'),
                    'id' => current_user()['id'],
                ]
            );

            flash($saved ? 'success' : 'error', $saved ? 'Profile updated.' : 'Profile could not be updated.');
            redirect(app_url('profile'));

        default:
            redirect($redirectTarget);
    }
}

handle_actions();
