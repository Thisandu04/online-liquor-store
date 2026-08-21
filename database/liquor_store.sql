CREATE DATABASE IF NOT EXISTS liquor_store;
USE liquor_store;

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    brand VARCHAR(100) NOT NULL,
    category_id INT,
    volume_ml INT NOT NULL,
    abv DECIMAL(4,2) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) DEFAULT 'assets/images/default.jpg',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    product_id INT,
    quantity INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(30) NOT NULL,
    payment_status ENUM('Pending','Paid','Failed') DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Default admin login: username = admin, password = Admin@123
-- (hash generated with PHP password_hash, bcrypt)
INSERT INTO admins (username, password) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.YeIVzMi0RVUFZlnZbYSU9j4qOu3ZocEJa');

INSERT INTO categories (name) VALUES ('Beer'), ('Wine'), ('Whiskey'), ('Spirits');

INSERT INTO products (name, brand, category_id, volume_ml, abv, price, stock, description) VALUES
('Lager Beer', 'Lion', 1, 625, 4.80, 550.00, 100, 'Classic Sri Lankan lager'),
('Red Wine', 'DGWU', 2, 750, 12.50, 2200.00, 40, 'Smooth dry red wine'),
('Single Malt Whiskey', 'Old Reserve', 3, 750, 40.00, 4500.00, 25, 'Aged single malt'),
('Vodka', 'Smirnoff', 4, 750, 37.50, 3200.00, 30, 'Triple distilled vodka');