# DevLokam PHP App

Devlokam is a Hostinger-friendly PHP web application inspired by the provided screenshots. It includes:

- Home page with hero, featured pujas, temple discovery, seva, and store sections
- Puja listing and puja detail booking flow
- Temple explorer with modal-style detail overlay
- Divine store with cart and order capture
- Sacred seva listing plus seva calendar
- Sign in, registration, and profile editing
- MySQL schema and seed data for phpMyAdmin import

## Folder Structure

- `index.php` - front controller and route entry point
- `includes/bootstrap.php` - config, session, PDO, routing, actions
- `includes/views.php` - page templates
- `assets/css/app.css` - full styling
- `database.sql` - database schema and sample data
- `.htaccess` - Apache rewrite rules for clean URLs
- `config.sample.php` - sample database configuration

## Hostinger Deployment

1. Upload the contents of this folder to your Hostinger `public_html` directory.
2. Create a MySQL database from the Hostinger hPanel.
3. Open phpMyAdmin and import `database.sql`.
4. Copy `config.sample.php` to `config.php`.
5. Update `config.php` with your Hostinger DB host, DB name, DB user, and DB password.
6. Visit your domain. Apache rewrite rules will route pages like `/pujas`, `/temples`, `/store`, `/services`, and `/seva-calendar`.

## Default Login

- Admin email: `admin@devlokam.com`
- Admin password: `admin123`

## Important Notes

- Orders and bookings are saved in MySQL and marked `pending` for payment until you connect a payment gateway such as Razorpay.
- The app uses server-rendered PHP only, so it works well on standard shared hosting.
- Passwords are seeded with a SHA-256 fallback for portability. New projects can upgrade this to `password_hash()` if preferred.
