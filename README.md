Jersey Store - E-Commerce Website

Jersey Store is a PHP and MySQL based e-commerce website for buying football jerseys online. The project provides a simple customer storefront, user authentication, shopping cart, checkout, orders, reviews, and an admin dashboard.

Features
Customer Features
User registration and login
Browse jerseys by category
View product details
Add products to cart
Update and remove cart items
Checkout
Cash on Delivery
eSewa online payment
View previous orders
Add product reviews
Profile menu showing the logged-in username
Admin Features
Admin dashboard
Add products
Edit products
Delete products
View customer orders
View customer reviews
Manage the store using the same login page as customers
Admin Login

The administrator uses the same login page as normal users.

Username: admin
Password: admin

After successful login, the administrator is automatically redirected to the Admin Dashboard.

Product Categories

The store contains categories such as:

Club
National Team
Retro
Training
Goalkeeper
Payment Methods

The project supports:

Cash on Delivery
eSewa

The eSewa integration is configured for testing/development using the eSewa test environment.

Technologies Used
Frontend
HTML5
CSS3
JavaScript
Bootstrap 5
Backend
PHP
MySQL
PDO
Development Tools
XAMPP
Apache
MySQL
VS Code
Project Structure
jersey-store-crud/
│
├── admin/
│   ├── index.php
│   ├── products.php
│   ├── product_add.php
│   ├── product_edit.php
│   ├── product_delete.php
│   ├── orders.php
│   ├── reviews.php
│   └── logout.php
│
├── assets/
│   └── css/
│       └── main.css
│
├── database/
│   └── schema.sql
│
├── includes/
│   ├── db.php
│   └── auth.php
│
├── payment/
│   ├── config.php
│   ├── esewa_initiate.php
│   ├── esewa_success.php
│   └── esewa_failure.php
│
├── uploads/
│   └── products/
│
├── index.php
├── products.php
├── product.php
├── register.php
├── login.php
├── logout.php
├── cart.php
├── cart_update.php
├── add_to_cart.php
├── checkout.php
├── orders.php
└── review_add.php
Requirements

Before running the project, install:

XAMPP
PHP 8.x
MySQL
A web browser

Apache and MySQL must be running in XAMPP.

Installation
1. Extract the Project

Extract the project into the XAMPP htdocs folder.

Example:

C:\xampp\htdocs\jersey-store-crud
2. Start XAMPP

Open XAMPP Control Panel and start:

Apache
MySQL
3. Create the Database

Open:

http://localhost/phpmyadmin

Create a database named:

jersey_store_crud

Then import:

database/schema.sql

The SQL file creates the required tables for users, administrators, products, orders, order items, reviews, and payments.

4. Configure Database Connection

The database connection is located at:

includes/db.php

For a default XAMPP installation:

Host: localhost
Username: root
Password: empty
Database: jersey_store_crud

If your MySQL configuration is different, update includes/db.php.

5. Open the Website

Open:

http://localhost/jersey-store-crud/
User Registration and Login

Normal customers must first create an account.

Go to:

http://localhost/jersey-store-crud/register.php

After registration, users can log in using their username and password.

The logged-in username is displayed in the navigation bar with a profile icon.

Admin Login

There is no separate Admin Login button visible on the storefront.

The administrator logs in through the same login page:

http://localhost/jersey-store-crud/login.php

Use:

Username: admin
Password: admin

The system recognizes the administrator and redirects to:

Admin Dashboard
Adding Products

After logging in as an administrator:

Admin Dashboard → Products → Add Product

A product contains:

Product name
Category
Price
Size
Stock
Description
Image

Product images are stored in:

uploads/products/
eSewa Payment

The project includes eSewa online payment integration.

The eSewa configuration is located at:

payment/config.php

The project uses the eSewa test/UAT environment during development.

eSewa Payment Flow
Customer
    ↓
Login
    ↓
Browse Jerseys
    ↓
Add Jersey to Cart
    ↓
Checkout
    ↓
Select eSewa
    ↓
Redirect to eSewa
    ↓
Complete Payment
    ↓
Return to Jersey Store
    ↓
Payment Verification
    ↓
Order Status Updated

The eSewa transaction is verified before the payment is marked as successful.

For production deployment, the eSewa test credentials and URLs must be replaced with production credentials.

Database Tables

The main database tables are:

admins

Stores administrator login information.

users

Stores customer account information.

products

Stores jersey/product information.

orders

Stores customer order information and payment status.

order_items

Stores the individual products belonging to an order.

reviews

Stores customer reviews.

payments

Stores payment gateway and transaction information.

Testing

A basic testing process is:

Start Apache and MySQL.
Open the website.
Register a normal user.
Log in.
Browse jerseys.
Add a jersey to the cart.
Update the cart.
Proceed to checkout.
Test Cash on Delivery.
Test eSewa using the test environment.
Check the order history.
Log out.
Log in using admin / admin.
Test product CRUD operations.
Check orders and reviews from the Admin Dashboard.
Important Notes
This project is intended for academic and development purposes.
The default admin/admin credentials should be changed before production use.
Never expose real eSewa secret keys publicly.
Use HTTPS when deploying the website online.
PHP cURL should be enabled for eSewa transaction verification.
XAMPP is recommended for local development.
Troubleshooting
Website Not Opening

Make sure Apache is running and the project is located inside:

C:\xampp\htdocs\

Then open:

http://localhost/jersey-store-crud/
Database Connection Error

Check:

includes/db.php

Make sure:

MySQL is running.
Database name is jersey_store_crud.
MySQL username and password are correct.
eSewa Payment Not Working

Check:

eSewa configuration in payment/config.php
Test product code
Test secret key
Success URL
Failure URL
PHP cURL extension
Author

Jersey Store is a BScCSIT academic e-commerce project developed using PHP, MySQL, Bootstrap, HTML, CSS, and JavaScript.
