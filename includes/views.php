<?php

declare(strict_types=1);

function theme_class(?string $theme): string
{
    $allowed = [
        'theme-diya',
        'theme-marigold',
        'theme-plate',
        'theme-temple-stone',
        'theme-temple-gold',
        'theme-sandalwood',
    ];

    return in_array($theme, $allowed, true) ? $theme : 'theme-marigold';
}

function active_nav(string $key, string $current): string
{
    return $key === $current ? 'nav-link is-active' : 'nav-link';
}

function user_prefill(string $field): string
{
    $user = current_user();
    return $user[$field] ?? '';
}

function render_app(array $route): void
{
    $page = $route['page'];
    $flash = pull_flash();
    $dbBanner = db_notice();
    $user = current_user();
    $cartCount = cart_count();
    $title = match ($page) {
        'home' => 'Devlokam',
        'pujas' => 'Pujas',
        'puja-detail' => 'Puja Detail',
        'temples' => 'Temples',
        'store' => 'Store',
        'cart' => 'Shopping Cart',
        'seva' => 'Sacred Seva',
        'calendar' => 'Seva Calendar',
        'profile' => 'Profile',
        'signin' => 'Sign In',
        default => 'Page Not Found',
    };

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= h($title) ?> | DevLokam</title>
        <link rel="stylesheet" href="<?= h(app_url('assets/css/app.css')) ?>">
    </head>
    <body>
    <div class="page-shell">
        <?php render_header($page, $user, $cartCount); ?>

        <?php if ($flash): ?>
            <div class="container">
                <div class="flash flash-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
            </div>
        <?php endif; ?>

        <?php if ($dbBanner): ?>
            <div class="container">
                <div class="setup-banner"><?= h($dbBanner) ?></div>
            </div>
        <?php endif; ?>

        <main class="container">
            <?php
            switch ($page) {
                case 'home':
                    render_home();
                    break;
                case 'pujas':
                    render_pujas();
                    break;
                case 'puja-detail':
                    render_puja_detail($route['slug']);
                    break;
                case 'temples':
                    render_temples();
                    break;
                case 'store':
                    render_store();
                    break;
                case 'cart':
                    render_cart();
                    break;
                case 'seva':
                    render_seva();
                    break;
                case 'calendar':
                    render_calendar();
                    break;
                case 'profile':
                    render_profile();
                    break;
                case 'signin':
                    render_signin();
                    break;
                default:
                    render_not_found();
            }
            ?>
        </main>

        <?php render_footer(); ?>
        <a class="whatsapp-float" href="https://wa.me/919999999999" target="_blank" rel="noreferrer">WA</a>
    </div>
    </body>
    </html>
    <?php
}

function render_header(string $page, ?array $user, int $cartCount): void
{
    ?>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand" href="<?= h(app_url()) ?>">
                <img src="<?= h(app_url('assets/logo.png')) ?>" alt="Devlokam logo">
                <div>
                    <span class="brand-title">DevLokam</span>
                    <span class="brand-tag">Sacred bookings from holy temples</span>
                </div>
            </a>

            <nav class="main-nav">
                <a class="<?= h(active_nav('home', $page)) ?>" href="<?= h(app_url()) ?>">Home</a>
                <a class="<?= h(active_nav('pujas', $page)) ?>" href="<?= h(app_url('pujas')) ?>">Pujas</a>
                <a class="<?= h(active_nav('temples', $page)) ?>" href="<?= h(app_url('temples')) ?>">Temples</a>
                <a class="<?= h(active_nav('store', $page)) ?>" href="<?= h(app_url('store')) ?>">Store</a>
                <a class="<?= h(active_nav('seva', $page)) ?>" href="<?= h(app_url('services')) ?>">Seva</a>
            </nav>

            <div class="header-actions">
                <span class="pill subtle-pill">English</span>
                <a class="cart-pill" href="<?= h(app_url('cart')) ?>">Cart <strong><?= $cartCount ?></strong></a>
                <?php if ($user): ?>
                    <a class="pill" href="<?= h(app_url('profile')) ?>"><?= h($user['role'] === 'admin' ? 'Admin' : 'Profile') ?></a>
                    <form method="post" action="<?= h(app_url()) ?>" class="inline-form">
                        <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="redirect" value="<?= h(app_url()) ?>">
                        <button class="btn btn-outline small-btn" type="submit">Sign Out</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-primary small-btn" href="<?= h(app_url('signin')) ?>">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php
}

function render_footer(): void
{
    ?>
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <h3>DevLokam</h3>
                <p>Hostinger-ready PHP application for puja bookings, temple discovery, sacred seva, and spiritual commerce.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <a href="<?= h(app_url('pujas')) ?>">Book Pujas</a>
                <a href="<?= h(app_url('temples')) ?>">Explore Temples</a>
                <a href="<?= h(app_url('store')) ?>">Visit Store</a>
                <a href="<?= h(app_url('services')) ?>">Sacred Seva</a>
            </div>
            <div>
                <h4>Deployment Notes</h4>
                <p>Import <code>database.sql</code> in phpMyAdmin, update <code>config.php</code>, and upload all files to <code>public_html</code>.</p>
            </div>
        </div>
    </footer>
    <?php
}

function render_home(): void
{
    $featuredPujas = featured_pujas(4);
    $temples = featured_temples(3);
    $products = all_products(['category' => null]);
    $sevas = all_sevas();
    $heroPuja = $featuredPujas[1] ?? $featuredPujas[0] ?? null;
    ?>
    <section class="hero">
        <div class="hero-copy">
            <span class="eyebrow">Sacred blessings from holy temples</span>
            <h1>Experience the Divine from Home</h1>
            <p>Book personalized pujas and sacred seva at India&apos;s most revered temples. The full app is structured for Hostinger Business hosting and MySQL in phpMyAdmin.</p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= h(app_url('pujas')) ?>">Explore Pujas</a>
                <a class="btn btn-outline" href="<?= h(app_url('services')) ?>">Sacred Seva</a>
            </div>
        </div>
        <div class="hero-media <?= h(theme_class($heroPuja['image_theme'] ?? 'theme-marigold')) ?>">
            <?php if ($heroPuja): ?>
                <div class="floating-card">
                    <span class="muted-label">Seva of the Day</span>
                    <strong><?= h($heroPuja['name']) ?></strong>
                    <a href="<?= h(app_url('puja/' . $heroPuja['slug'])) ?>">Book Now</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section-block">
        <div class="section-head">
            <div>
                <span class="eyebrow">Featured</span>
                <h2>Popular Pujas</h2>
            </div>
            <a class="text-link" href="<?= h(app_url('pujas')) ?>">See all pujas</a>
        </div>
        <div class="card-grid four-col">
            <?php foreach ($featuredPujas as $puja): ?>
                <article class="card product-card">
                    <div class="card-media <?= h(theme_class($puja['image_theme'])) ?>">
                        <span class="badge">Featured</span>
                        <span class="discount-badge"><?= h($puja['discount_label']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="card-kicker"><?= h($puja['deity']) ?></p>
                        <h3><?= h($puja['name']) ?></h3>
                        <p class="muted-text"><?= h($puja['temple_name']) ?>, <?= h($puja['city']) ?></p>
                        <p class="line-clamp-2"><?= h($puja['description']) ?></p>
                        <div class="meta-row">
                            <span><?= h($puja['duration_label']) ?></span>
                            <span><?= number_format((int) $puja['devotees_count']) ?> devotees</span>
                        </div>
                        <div class="price-row">
                            <strong><?= h(money($puja['price'])) ?></strong>
                            <span><?= h(money($puja['original_price'])) ?></span>
                        </div>
                        <a class="text-link" href="<?= h(app_url('puja/' . $puja['slug'])) ?>">Book Now</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-block">
        <div class="section-head">
            <div>
                <span class="eyebrow">Explore</span>
                <h2>Sacred Temples of India</h2>
            </div>
            <a class="text-link" href="<?= h(app_url('temples')) ?>">Browse temples</a>
        </div>
        <div class="card-grid three-col">
            <?php foreach ($temples as $temple): ?>
                <a class="temple-card <?= h(theme_class($temple['image_theme'])) ?>" href="<?= h(app_url('temples?temple=' . urlencode($temple['slug']))) ?>">
                    <div class="temple-overlay">
                        <span class="eyebrow small-eyebrow"><?= h($temple['deity_primary']) ?></span>
                        <h3><?= h($temple['name']) ?></h3>
                        <p><?= h($temple['city']) ?>, <?= h($temple['state']) ?></p>
                        <small><?= h($temple['opening_hours']) ?></small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-block">
        <div class="section-head">
            <div>
                <span class="eyebrow">Devlokam Seva</span>
                <h2>Sacred Seva</h2>
            </div>
            <a class="text-link" href="<?= h(app_url('services')) ?>">View seva</a>
        </div>
        <div class="card-grid four-col">
            <?php foreach (array_slice($sevas, 0, 4) as $seva): ?>
                <article class="card seva-card">
                    <div class="card-media <?= h(theme_class($seva['image_theme'])) ?>">
                        <span class="badge subtle-badge"><?= h($seva['place_group']) ?></span>
                    </div>
                    <div class="card-body">
                        <h3><?= h($seva['name']) ?></h3>
                        <p class="line-clamp-2"><?= h($seva['description']) ?></p>
                        <p class="success-note"><?= h($seva['live_stream_note']) ?></p>
                        <div class="price-row">
                            <strong><?= h(money($seva['price'])) ?></strong>
                            <span><?= h(money($seva['original_price'])) ?></span>
                        </div>
                        <a class="btn btn-primary full-width" href="<?= h(app_url('services?book=' . urlencode($seva['slug']))) ?>">Book Seva</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section-block">
        <div class="section-head">
            <div>
                <span class="eyebrow">Shop</span>
                <h2>Divine Store</h2>
            </div>
            <a class="text-link" href="<?= h(app_url('store')) ?>">Visit store</a>
        </div>
        <div class="card-grid four-col">
            <?php foreach (array_slice($products, 0, 4) as $product): ?>
                <article class="card product-card">
                    <div class="card-media <?= h(theme_class($product['image_theme'])) ?>">
                        <span class="discount-badge">
                            <?= h((string) round((1 - ((float) $product['price'] / max((float) $product['original_price'], 1))) * 100)) ?>% OFF
                        </span>
                    </div>
                    <div class="card-body">
                        <h3><?= h($product['name']) ?></h3>
                        <p class="muted-text"><?= h($product['category_name']) ?></p>
                        <div class="meta-row">
                            <span><?= h(number_format((float) $product['rating'], 1)) ?> rating</span>
                            <span><?= h((string) $product['reviews_count']) ?> reviews</span>
                        </div>
                        <div class="price-row">
                            <strong><?= h(money($product['price'])) ?></strong>
                            <span><?= h(money($product['original_price'])) ?></span>
                        </div>
                        <form method="post" action="<?= h(app_url()) ?>">
                            <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="action" value="add-to-cart">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= h(app_url('store')) ?>">
                            <button class="btn btn-outline full-width" type="submit">Add to Cart</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

function render_pujas(): void
{
    $selectedTemple = $_GET['temple'] ?? '';
    $selectedDeity = $_GET['deity'] ?? '';
    $pujas = all_pujas([
        'temple' => is_string($selectedTemple) ? $selectedTemple : '',
        'deity' => is_string($selectedDeity) ? $selectedDeity : '',
    ]);
    $temples = all_temples();
    $deities = puja_deities();
    ?>
    <section class="section-block compact-top">
        <span class="eyebrow">Book Sacred Rituals</span>
        <h1 class="page-title">Curated Temple Pujas</h1>
        <form class="filter-bar" method="get" action="<?= h(app_url('pujas')) ?>">
            <select name="temple">
                <option value="">All temples</option>
                <?php foreach ($temples as $temple): ?>
                    <option value="<?= h($temple['slug']) ?>" <?= $selectedTemple === $temple['slug'] ? 'selected' : '' ?>><?= h($temple['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="deity">
                <option value="">All deities</option>
                <?php foreach ($deities as $deity): ?>
                    <option value="<?= h($deity['deity']) ?>" <?= $selectedDeity === $deity['deity'] ? 'selected' : '' ?>><?= h($deity['deity']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Apply Filters</button>
        </form>
        <div class="card-grid four-col">
            <?php foreach ($pujas as $puja): ?>
                <article class="card product-card">
                    <div class="card-media <?= h(theme_class($puja['image_theme'])) ?>">
                        <span class="badge">Featured</span>
                        <span class="discount-badge"><?= h($puja['discount_label']) ?></span>
                    </div>
                    <div class="card-body">
                        <p class="card-kicker"><?= h($puja['deity']) ?></p>
                        <h3><?= h($puja['name']) ?></h3>
                        <p class="muted-text"><?= h($puja['temple_name']) ?></p>
                        <p class="line-clamp-2"><?= h($puja['description']) ?></p>
                        <div class="meta-row">
                            <span><?= h($puja['duration_label']) ?></span>
                            <span><?= h(number_format((int) $puja['devotees_count'] / 1000, 1)) ?>k</span>
                        </div>
                        <div class="price-row">
                            <strong><?= h(money($puja['price'])) ?></strong>
                            <span><?= h(money($puja['original_price'])) ?></span>
                        </div>
                        <a class="text-link" href="<?= h(app_url('puja/' . $puja['slug'])) ?>">Book Now</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

function render_puja_detail(?string $slug): void
{
    $puja = find_puja_by_slug($slug);
    if (!$puja) {
        render_not_found();
        return;
    }

    $benefits = benefits_for_puja($puja);
    ?>
    <section class="detail-layout">
        <div class="detail-main">
            <div class="detail-media <?= h(theme_class($puja['image_theme'])) ?>"></div>
            <p class="eyebrow small-eyebrow"><?= h($puja['deity']) ?></p>
            <h1 class="page-title left-title"><?= h($puja['name']) ?></h1>
            <p class="muted-text"><?= h($puja['temple_name']) ?>, <?= h($puja['city']) ?>, <?= h($puja['state']) ?></p>
            <p class="lead-text"><?= h($puja['description']) ?></p>

            <div class="stats-grid">
                <div class="stat-card">
                    <span>Duration</span>
                    <strong><?= h($puja['duration_label']) ?></strong>
                </div>
                <div class="stat-card">
                    <span>Devotees participated</span>
                    <strong><?= h(number_format((int) $puja['devotees_count'])) ?></strong>
                </div>
            </div>

            <div class="benefits-grid">
                <?php foreach ($benefits as $benefit): ?>
                    <div class="benefit-item"><?= h($benefit) ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <aside class="detail-sidebar">
            <div class="booking-panel">
                <h2>Book This Puja</h2>
                <p class="muted-text">Your Details</p>
                <form method="post" action="<?= h(app_url()) ?>" class="stack-form">
                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="book-puja">
                    <input type="hidden" name="puja_id" value="<?= (int) $puja['id'] ?>">
                    <input type="hidden" name="redirect" value="<?= h(app_url('puja/' . $puja['slug'])) ?>">

                    <input name="full_name" type="text" placeholder="Full Name" value="<?= h(user_prefill('full_name')) ?>" required>
                    <input name="email" type="email" placeholder="Email Address" value="<?= h(user_prefill('email')) ?>" required>
                    <input name="phone" type="text" placeholder="Phone Number" value="<?= h(user_prefill('phone')) ?>" required>
                    <input name="gotra" type="text" placeholder="Gotra (Optional)" value="<?= h(user_prefill('gotra')) ?>">
                    <input name="preferred_date" type="date" required>
                    <input name="occasion" type="text" placeholder="Occasion (if any)">
                    <input name="address_line" type="text" placeholder="Address (House No, Street, Locality)" value="<?= h(user_prefill('address_line')) ?>" required>
                    <div class="dual-inputs">
                        <input name="city" type="text" placeholder="City" value="<?= h(user_prefill('city')) ?>" required>
                        <input name="state" type="text" placeholder="State" value="<?= h(user_prefill('state')) ?>" required>
                    </div>
                    <input name="pincode" type="text" placeholder="Pincode" value="<?= h(user_prefill('pincode')) ?>" required>

                    <div class="checkout-total">
                        <span>Total Amount</span>
                        <strong><?= h(money($puja['price'])) ?></strong>
                    </div>
                    <button class="btn btn-primary full-width" type="submit">Pay Now</button>
                    <p class="payment-note">Payment gateway placeholder: bookings are stored as pending until Razorpay or another gateway is connected.</p>
                </form>
            </div>
        </aside>
    </section>
    <?php
}

function render_temples(): void
{
    $selectedState = $_GET['state'] ?? '';
    $selectedSearch = $_GET['search'] ?? '';
    $selectedTempleSlug = $_GET['temple'] ?? '';
    $temples = all_temples([
        'state' => is_string($selectedState) ? $selectedState : '',
        'search' => is_string($selectedSearch) ? $selectedSearch : '',
    ]);
    $states = temple_states();
    $modalTemple = is_string($selectedTempleSlug) && $selectedTempleSlug !== '' ? find_temple_by_slug($selectedTempleSlug) : null;
    ?>
    <section class="section-block compact-top">
        <span class="eyebrow">Explore</span>
        <h1 class="page-title left-title">Sacred Temples of India</h1>
        <form class="filter-bar" method="get" action="<?= h(app_url('temples')) ?>">
            <input type="text" name="search" value="<?= h((string) $selectedSearch) ?>" placeholder="Search temples...">
            <select name="state">
                <option value="">Filter by state</option>
                <?php foreach ($states as $state): ?>
                    <option value="<?= h($state['state']) ?>" <?= $selectedState === $state['state'] ? 'selected' : '' ?>><?= h($state['state']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-primary" type="submit">Search</button>
        </form>
        <div class="card-grid three-col">
            <?php foreach ($temples as $temple): ?>
                <a class="temple-card temple-large <?= h(theme_class($temple['image_theme'])) ?>" href="<?= h(app_url('temples?temple=' . urlencode($temple['slug']))) ?>">
                    <div class="temple-overlay">
                        <span class="eyebrow small-eyebrow"><?= h($temple['deity_primary']) ?></span>
                        <h3><?= h($temple['name']) ?></h3>
                        <p><?= h($temple['city']) ?>, <?= h($temple['state']) ?></p>
                        <small>Click to view details</small>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($modalTemple): ?>
        <div class="modal-backdrop">
            <div class="modal-panel">
                <a class="modal-close" href="<?= h(app_url('temples')) ?>">x</a>
                <div class="modal-media <?= h(theme_class($modalTemple['image_theme'])) ?>"></div>
                <div class="modal-content">
                    <p class="eyebrow small-eyebrow"><?= h($modalTemple['deity_primary']) ?> / <?= h($modalTemple['deity_secondary']) ?></p>
                    <h2><?= h($modalTemple['name']) ?></h2>
                    <p class="muted-text"><?= h($modalTemple['city']) ?>, <?= h($modalTemple['state']) ?> | <?= h($modalTemple['opening_hours']) ?></p>
                    <div class="info-box">
                        <h3>About</h3>
                        <p><?= h($modalTemple['about_text']) ?></p>
                    </div>
                    <div class="info-box soft-box">
                        <h3>Significance</h3>
                        <p><?= h($modalTemple['significance_text']) ?></p>
                    </div>
                    <div class="tag-row">
                        <span class="pill"><?= h($modalTemple['deity_primary']) ?></span>
                        <span class="pill"><?= h($modalTemple['deity_secondary']) ?></span>
                        <span class="pill"><?= h($modalTemple['region_label']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php
}

function render_store(): void
{
    $selectedCategory = $_GET['category'] ?? '';
    $categories = store_categories();
    $products = all_products(['category' => is_string($selectedCategory) ? $selectedCategory : '']);
    ?>
    <section class="section-block compact-top">
        <span class="eyebrow">Shop</span>
        <h1 class="page-title left-title">Divine Store</h1>
        <div class="chip-row">
            <a class="<?= $selectedCategory === '' ? 'chip chip-active' : 'chip' ?>" href="<?= h(app_url('store')) ?>">All</a>
            <?php foreach ($categories as $category): ?>
                <a class="<?= $selectedCategory === $category['slug'] ? 'chip chip-active' : 'chip' ?>" href="<?= h(app_url('store?category=' . urlencode($category['slug']))) ?>"><?= h($category['name']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (!current_user()): ?>
            <div class="sign-in-banner">
                <span>Sign in to add items to your cart and place orders</span>
                <a class="btn btn-primary small-btn" href="<?= h(app_url('signin')) ?>">Sign In</a>
            </div>
        <?php endif; ?>

        <div class="card-grid four-col">
            <?php foreach ($products as $product): ?>
                <article class="card product-card">
                    <div class="card-media <?= h(theme_class($product['image_theme'])) ?>">
                        <span class="discount-badge">
                            <?= h((string) round((1 - ((float) $product['price'] / max((float) $product['original_price'], 1))) * 100)) ?>% OFF
                        </span>
                    </div>
                    <div class="card-body">
                        <h3><?= h($product['name']) ?></h3>
                        <p class="meta-inline"><?= h(number_format((float) $product['rating'], 1)) ?> | <?= h((string) $product['reviews_count']) ?> reviews</p>
                        <div class="price-row">
                            <strong><?= h(money($product['price'])) ?></strong>
                            <span><?= h(money($product['original_price'])) ?></span>
                        </div>
                        <form method="post" action="<?= h(app_url()) ?>">
                            <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                            <input type="hidden" name="action" value="add-to-cart">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= h(app_url('store' . ($selectedCategory ? '?category=' . urlencode((string) $selectedCategory) : ''))) ?>">
                            <button class="icon-action" type="submit">Add to Cart</button>
                        </form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php
}

function render_cart(): void
{
    $items = cart_items();
    $user = current_user();
    ?>
    <section class="cart-layout">
        <div class="cart-column">
            <a class="text-link back-link" href="<?= h(app_url('store')) ?>">Back</a>
            <h1 class="page-title left-title">Shopping Cart</h1>

            <?php if ($items === []): ?>
                <div class="empty-state">
                    <p>Your cart is empty right now.</p>
                    <a class="btn btn-primary" href="<?= h(app_url('store')) ?>">Browse Store</a>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <article class="cart-item">
                        <div class="mini-media <?= h(theme_class($item['image_theme'])) ?>"></div>
                        <div class="cart-copy">
                            <h3><?= h($item['name']) ?></h3>
                            <strong><?= h(money($item['price'])) ?></strong>
                            <div class="quantity-actions">
                                <form method="post" action="<?= h(app_url()) ?>">
                                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="update-cart">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="mode" value="decrease">
                                    <input type="hidden" name="redirect" value="<?= h(app_url('cart')) ?>">
                                    <button type="submit">-</button>
                                </form>
                                <span><?= (int) $item['cart_quantity'] ?></span>
                                <form method="post" action="<?= h(app_url()) ?>">
                                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="update-cart">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="mode" value="increase">
                                    <input type="hidden" name="redirect" value="<?= h(app_url('cart')) ?>">
                                    <button type="submit">+</button>
                                </form>
                                <form method="post" action="<?= h(app_url()) ?>">
                                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                                    <input type="hidden" name="action" value="update-cart">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="mode" value="remove">
                                    <input type="hidden" name="redirect" value="<?= h(app_url('cart')) ?>">
                                    <button class="ghost-button" type="submit">Remove</button>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <aside class="summary-column">
            <div class="booking-panel">
                <h2>Order Summary</h2>
                <?php foreach ($items as $item): ?>
                    <div class="summary-row">
                        <span><?= h($item['name']) ?> x<?= (int) $item['cart_quantity'] ?></span>
                        <strong><?= h(money($item['line_total'])) ?></strong>
                    </div>
                <?php endforeach; ?>
                <div class="checkout-total">
                    <span>Total</span>
                    <strong><?= h(money(cart_total())) ?></strong>
                </div>

                <form method="post" action="<?= h(app_url()) ?>" class="stack-form">
                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="checkout">
                    <input type="hidden" name="redirect" value="<?= h(app_url('cart')) ?>">
                    <input name="full_name" type="text" placeholder="Full Name" value="<?= h($user['full_name'] ?? '') ?>" required>
                    <input name="email" type="email" placeholder="Email Address" value="<?= h($user['email'] ?? '') ?>" required>
                    <input name="phone" type="text" placeholder="Phone Number" value="<?= h($user['phone'] ?? '') ?>" required>
                    <textarea name="address_line" placeholder="Address (House No, Street, Locality)" required><?= h($user['address_line'] ?? '') ?></textarea>
                    <div class="dual-inputs">
                        <input name="city" type="text" placeholder="City" value="<?= h($user['city'] ?? '') ?>" required>
                        <input name="state" type="text" placeholder="State" value="<?= h($user['state'] ?? '') ?>" required>
                    </div>
                    <input name="pincode" type="text" placeholder="Pincode" value="<?= h($user['pincode'] ?? '') ?>" required>
                    <button class="btn btn-primary full-width" type="submit">Proceed to Pay</button>
                    <p class="payment-note">Orders are stored in MySQL and marked pending until a payment gateway is connected.</p>
                </form>
            </div>
        </aside>
    </section>
    <?php
}

function render_seva(): void
{
    $selectedPlace = $_GET['place'] ?? '';
    $selectedSlug = $_GET['book'] ?? '';
    $places = seva_places();
    $sevas = all_sevas(['place_group' => is_string($selectedPlace) ? $selectedPlace : '']);
    $selectedSeva = is_string($selectedSlug) && $selectedSlug !== '' ? find_seva_by_slug($selectedSlug) : null;
    ?>
    <section class="section-block compact-top">
        <span class="eyebrow">Devlokam Seva</span>
        <h1 class="page-title">Sacred Seva</h1>
        <p class="page-subtitle centered-copy">Earn divine merit through sacred seva. Watch your seva being performed live via a shared video link after booking.</p>
        <div class="center-actions">
            <a class="btn btn-outline" href="<?= h(app_url('seva-calendar')) ?>">View Seva Calendar</a>
        </div>

        <div class="chip-row centered-row">
            <a class="<?= $selectedPlace === '' ? 'chip chip-active' : 'chip' ?>" href="<?= h(app_url('services')) ?>">All Places</a>
            <?php foreach ($places as $place): ?>
                <a class="<?= $selectedPlace === $place['place_group'] ? 'chip chip-active' : 'chip' ?>" href="<?= h(app_url('services?place=' . urlencode($place['place_group']))) ?>"><?= h($place['place_group']) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="card-grid four-col">
            <?php foreach ($sevas as $seva): ?>
                <article class="card seva-card">
                    <div class="card-media <?= h(theme_class($seva['image_theme'])) ?>">
                        <span class="badge subtle-badge"><?= h($seva['place_group']) ?></span>
                        <span class="discount-badge"><?= h($seva['badge_label']) ?></span>
                    </div>
                    <div class="card-body">
                        <h3><?= h($seva['name']) ?></h3>
                        <p class="line-clamp-2"><?= h($seva['description']) ?></p>
                        <p class="success-note"><?= h($seva['live_stream_note']) ?></p>
                        <div class="price-row">
                            <strong><?= h(money($seva['price'])) ?></strong>
                            <span><?= h(money($seva['original_price'])) ?></span>
                        </div>
                        <a class="btn btn-primary full-width" href="<?= h(app_url('services?book=' . urlencode($seva['slug']))) ?>">Book Seva</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($selectedSeva): ?>
            <div class="booking-panel inset-panel">
                <div class="section-head">
                    <div>
                        <span class="eyebrow">Booking with your profile details</span>
                        <h2><?= h($selectedSeva['name']) ?></h2>
                    </div>
                    <a class="text-link" href="<?= h(app_url('services')) ?>">Cancel</a>
                </div>
                <form method="post" action="<?= h(app_url()) ?>" class="stack-form">
                    <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                    <input type="hidden" name="action" value="book-seva">
                    <input type="hidden" name="seva_id" value="<?= (int) $selectedSeva['id'] ?>">
                    <input type="hidden" name="redirect" value="<?= h(app_url('services?book=' . urlencode($selectedSeva['slug']))) ?>">

                    <div class="dual-inputs">
                        <input name="full_name" type="text" placeholder="Full Name" value="<?= h(user_prefill('full_name')) ?>" required>
                        <input name="email" type="email" placeholder="Email Address" value="<?= h(user_prefill('email')) ?>" required>
                    </div>
                    <div class="dual-inputs">
                        <input name="phone" type="text" placeholder="Phone Number" value="<?= h(user_prefill('phone')) ?>" required>
                        <input name="booking_for" type="text" placeholder="Book for someone else">
                    </div>
                    <div class="dual-inputs">
                        <input name="preferred_date" type="date" required>
                        <input name="occasion" type="text" placeholder="Occasion (if any)">
                    </div>
                    <input name="address_line" type="text" placeholder="Address (House No, Street, Locality)" value="<?= h(user_prefill('address_line')) ?>" required>
                    <div class="dual-inputs">
                        <input name="city" type="text" placeholder="City" value="<?= h(user_prefill('city')) ?>" required>
                        <input name="state" type="text" placeholder="State" value="<?= h(user_prefill('state')) ?>" required>
                    </div>
                    <input name="pincode" type="text" placeholder="Pincode" value="<?= h(user_prefill('pincode')) ?>" required>
                    <div class="checkout-total">
                        <span>Amount</span>
                        <strong><?= h(money($selectedSeva['price'])) ?></strong>
                    </div>
                    <button class="btn btn-primary" type="submit">Pay <?= h(money($selectedSeva['price'])) ?></button>
                </form>
            </div>
        <?php endif; ?>
    </section>
    <?php
}

function render_calendar(): void
{
    $selectedPlace = $_GET['place'] ?? '';
    $places = seva_places();
    $groups = grouped_calendar_events(['place_group' => is_string($selectedPlace) ? $selectedPlace : '']);
    ?>
    <section class="section-block compact-top">
        <span class="eyebrow">Upcoming</span>
        <h1 class="page-title">Seva Calendar</h1>
        <p class="page-subtitle centered-copy">Plan your seva in advance. Book special sevas on auspicious dates at sacred places across Bharat.</p>
        <div class="chip-row centered-row">
            <a class="<?= $selectedPlace === '' ? 'chip chip-active' : 'chip' ?>" href="<?= h(app_url('seva-calendar')) ?>">All Places</a>
            <?php foreach ($places as $place): ?>
                <a class="<?= $selectedPlace === $place['place_group'] ? 'chip chip-active' : 'chip' ?>" href="<?= h(app_url('seva-calendar?place=' . urlencode($place['place_group']))) ?>"><?= h($place['place_group']) ?></a>
            <?php endforeach; ?>
        </div>

        <?php foreach ($groups as $monthLabel => $events): ?>
            <div class="calendar-block">
                <h2><?= h($monthLabel) ?></h2>
                <?php foreach ($events as $event): ?>
                    <article class="calendar-item">
                        <div class="calendar-date">
                            <strong><?= h(date('j', strtotime((string) $event['event_date']))) ?></strong>
                            <span><?= h(date('D', strtotime((string) $event['event_date']))) ?></span>
                        </div>
                        <div class="calendar-copy">
                            <h3><?= h($event['title']) ?></h3>
                            <p><?= h($event['description']) ?></p>
                            <small><?= h($event['place_group']) ?> | <?= h($event['event_type']) ?> | <?= h(money($event['price'])) ?></small>
                        </div>
                        <a class="btn btn-primary small-btn" href="<?= h(app_url('services?book=' . urlencode($event['seva_slug']))) ?>">Book</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php
}

function render_profile(): void
{
    $user = current_user();

    if (!$user) {
        ?>
        <section class="auth-shell">
            <div class="auth-card">
                <h1 class="page-title left-title">Your Profile</h1>
                <p>Please sign in to view and edit your profile.</p>
                <a class="btn btn-primary" href="<?= h(app_url('signin')) ?>">Sign In</a>
            </div>
        </section>
        <?php
        return;
    }
    ?>
    <section class="profile-shell">
        <div class="profile-card">
            <div class="profile-head">
                <div class="avatar-circle"><?= h(strtoupper(substr($user['full_name'], 0, 1))) ?></div>
                <div>
                    <h1><?= h($user['full_name']) ?></h1>
                    <p><?= h($user['email']) ?></p>
                </div>
            </div>
            <form method="post" action="<?= h(app_url()) ?>" class="stack-form">
                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="save-profile">
                <input type="hidden" name="redirect" value="<?= h(app_url('profile')) ?>">

                <label>Full Name
                    <input name="full_name" type="text" value="<?= h($user['full_name']) ?>" required>
                </label>
                <label>Email
                    <input type="email" value="<?= h($user['email']) ?>" disabled>
                </label>
                <label>Mobile Number
                    <input name="phone" type="text" value="<?= h($user['phone']) ?>">
                </label>
                <div class="triple-inputs">
                    <label>Age
                        <input name="age" type="number" value="<?= h((string) ($user['age'] ?? '')) ?>">
                    </label>
                    <label>Gender
                        <select name="gender">
                            <option value="">Select</option>
                            <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Other" <?= ($user['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                    </label>
                    <label>Gotra
                        <input name="gotra" type="text" value="<?= h((string) ($user['gotra'] ?? '')) ?>">
                    </label>
                </div>
                <label>Address
                    <input name="address_line" type="text" value="<?= h((string) ($user['address_line'] ?? '')) ?>">
                </label>
                <div class="triple-inputs">
                    <label>City
                        <input name="city" type="text" value="<?= h((string) ($user['city'] ?? '')) ?>">
                    </label>
                    <label>State
                        <input name="state" type="text" value="<?= h((string) ($user['state'] ?? '')) ?>">
                    </label>
                    <label>Pincode
                        <input name="pincode" type="text" value="<?= h((string) ($user['pincode'] ?? '')) ?>">
                    </label>
                </div>
                <button class="btn btn-primary full-width" type="submit">Save Profile</button>
            </form>
        </div>
    </section>
    <?php
}

function render_signin(): void
{
    ?>
    <section class="auth-shell auth-grid">
        <div class="auth-card">
            <span class="eyebrow">Welcome Back</span>
            <h1 class="page-title left-title">Sign In</h1>
            <form method="post" action="<?= h(app_url()) ?>" class="stack-form">
                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="login">
                <input type="hidden" name="redirect" value="<?= h(app_url('signin')) ?>">
                <input name="email" type="email" placeholder="Email address" required>
                <input name="password" type="password" placeholder="Password" required>
                <button class="btn btn-primary full-width" type="submit">Sign In</button>
            </form>
            <p class="hint-text">Seed login: <strong>admin@devlokam.com</strong> / <strong>admin123</strong></p>
        </div>
        <div class="auth-card">
            <span class="eyebrow">New Devotee</span>
            <h2>Create Account</h2>
            <form method="post" action="<?= h(app_url()) ?>" class="stack-form">
                <input type="hidden" name="_token" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="action" value="register">
                <input type="hidden" name="redirect" value="<?= h(app_url('signin')) ?>">
                <input name="full_name" type="text" placeholder="Full Name" required>
                <input name="email" type="email" placeholder="Email address" required>
                <input name="phone" type="text" placeholder="Phone number">
                <input name="password" type="password" placeholder="Password" required>
                <button class="btn btn-outline full-width" type="submit">Create Account</button>
            </form>
        </div>
    </section>
    <?php
}

function render_not_found(): void
{
    ?>
    <section class="auth-shell">
        <div class="auth-card">
            <span class="eyebrow">404</span>
            <h1 class="page-title left-title">Page not found</h1>
            <p>The page you requested does not exist in this build.</p>
            <a class="btn btn-primary" href="<?= h(app_url()) ?>">Return Home</a>
        </div>
    </section>
    <?php
}
