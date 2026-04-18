# DevLokam PHP App

Devlokam is a Hostinger-friendly PHP web application inspired by the provided screenshots. It includes:

- Home page with hero, featured pujas, temple discovery, seva, and store sections
- Puja listing and puja detail booking flow
- Temple explorer with modal-style detail overlay
- Divine store with working cart and order capture
- Sacred seva listing plus seva calendar
- Sign in, registration, and profile editing
- Order status page and working user dropdown menu
- Razorpay order creation, payment verification, and webhook support
- MySQL schema and seed data for phpMyAdmin import

## Folder Structure

- `index.php` - front controller and route entry point
- `includes/bootstrap.php` - config, session, PDO, routing, actions
- `includes/views.php` - page templates
- `assets/css/app.css` - full styling
- `database.sql` - database schema and sample data
- `database_upgrade.sql` - upgrade script for an already imported database
- `.htaccess` - Apache rewrite rules for clean URLs
- `config.sample.php` - sample database configuration

## Hostinger Deployment

1. Upload the contents of this folder to your Hostinger `public_html` directory.
2. Create a MySQL database from the Hostinger hPanel.
3. If this is a fresh install, import `database.sql`. If you already imported the old schema, import `database_upgrade.sql` instead.
4. Confirm `config.php` has your Hostinger DB host, live domain `https://devlokam.in`, and Razorpay credentials.
5. Visit your domain. Apache rewrite rules will route pages like `/pujas`, `/temples`, `/store`, `/services`, `/seva-calendar`, `/order-status`, and `/payment`.
6. In Razorpay Dashboard, add a webhook pointing to `https://devlokam.in/razorpay-webhook` and set the same webhook secret in `config.php`.

## Default Login

- Admin email: `admin@devlokam.com`
- Admin password: `admin123`

## Important Notes

- Orders and bookings are saved in MySQL and marked `pending` for payment until you connect a payment gateway such as Razorpay.
- The app uses server-rendered PHP only, so it works well on standard shared hosting.
- Image URLs for temples, pujas, products, and sevas can be edited directly in phpMyAdmin using the new `image_url` columns.
- Passwords are seeded with a SHA-256 fallback for portability. New projects can upgrade this to `password_hash()` if preferred.
