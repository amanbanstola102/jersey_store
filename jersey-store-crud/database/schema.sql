CREATE DATABASE IF NOT EXISTS jersey_store_crud;
USE jersey_store_crud;

CREATE TABLE IF NOT EXISTS admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(80) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    size VARCHAR(30) DEFAULT '',
    stock INT NOT NULL DEFAULT 0,
    description TEXT,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_address TEXT NOT NULL,
    payment_method VARCHAR(30) NOT NULL DEFAULT 'COD',
    payment_status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY one_review_per_user_product (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    gateway VARCHAR(30) NOT NULL,
    transaction_id VARCHAR(150) DEFAULT NULL,
    gateway_reference VARCHAR(150) DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'NPR',
    status VARCHAR(30) NOT NULL DEFAULT 'Pending',
    raw_response TEXT DEFAULT NULL,
    paid_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_order_payment (order_id),
    INDEX idx_gateway_reference (gateway_reference),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

INSERT INTO admins (username, password)
VALUES ('admin', '$2y$12$du3mAG6J3w0McKCnxw6pf.IyCDKq1kPrEWCtDb2RAH5ux0jSnIKHO')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO products (name, category, price, size, stock, description)
SELECT 'Home Club Jersey', 'Club Jerseys', 2499.00, 'M', 10, 'Classic home jersey for the new season.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Home Club Jersey');

INSERT INTO products (name, category, price, size, stock, description)
SELECT 'National Team Jersey', 'National Teams', 2299.00, 'L', 8, 'Lightweight national team jersey.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='National Team Jersey');

INSERT INTO products (name, category, price, size, stock, description)
SELECT 'Retro Football Jersey', 'Retro Jerseys', 1999.00, 'L', 6, 'Classic retro-inspired football jersey.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Retro Football Jersey');

INSERT INTO products (name, category, price, size, stock, description)
SELECT 'Training Jersey', 'Training Kits', 1799.00, 'XL', 12, 'Comfortable training jersey for everyday use.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Training Jersey');

INSERT INTO products (name, category, price, size, stock, description)
SELECT 'Goalkeeper Jersey', 'Goalkeeper', 2199.00, 'L', 7, 'Comfortable goalkeeper jersey with long sleeves.'
WHERE NOT EXISTS (SELECT 1 FROM products WHERE name='Goalkeeper Jersey');
