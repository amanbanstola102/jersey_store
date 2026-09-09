# JerseyHub - PHP MySQL E-commerce

A simple jersey-selling website using PHP, MySQL, PDO, Bootstrap and session-based features.

## Features
- Storefront with clickable jersey categories
- Category-filtered product listing
- Customer registration and login
- Shopping cart
- Checkout with Cash on Delivery and eSewa
- Payment verification and payment records
- Customer order history with payment status
- Product reviews and ratings
- Admin login
- Admin product CRUD
- Admin order status management
- Admin review management
- Jersey image upload

## XAMPP setup
1. Extract this folder into `C:/xampp/htdocs/`.
2. Start Apache and MySQL.
3. Open phpMyAdmin.
4. Import `database/schema.sql`.
5. If you already have the older JerseyHub database, import `database/payment_migration.sql` instead of re-creating the existing tables.
6. Visit `http://localhost/jersey-store-crud/`.
7. Admin: `http://localhost/jersey-store-crud/admin/login.php`

## Admin credentials
Username: admin  
Password: admin

## Payment gateway setup
Open `payment/config.php`.

### eSewa
The project is configured for eSewa UAT using the documented test product code `EPAYTEST` and UAT secret. Use the test environment while developing. For production, change `ESEWA_ENV` to `production` and use your merchant credentials.

## Payment flow
1. Customer selects eSewa or Cash on Delivery at checkout.
2. An order and pending payment record are created.
3. Customer is redirected to the selected gateway.
4. The callback is validated server-side.
5. eSewa response signature and transaction status are checked.
6. Only confirmed eSewa payments are marked Paid.

## Localhost note
The return URLs are generated from the current site URL, so the project works when the folder is named `jersey-store-crud` under XAMPP. Payment gateways redirect the browser back to the configured localhost URL during testing. For a public deployment, use your real HTTPS domain.

## Images
Upload jersey images from Admin -> Insert Jersey. Files are stored in `uploads/products/`.

## Main database tables
admins, users, products, orders, order_items, reviews, payments


## Login
There is one login page for both customers and the administrator. Use `admin` / `admin` for the administrator. After admin login, the dashboard opens automatically. The separate admin login page is no longer used.
