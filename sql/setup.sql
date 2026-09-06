-- setup.sql — Full Database Setup

-- 1. Users table (Creates Table if it doesn't exist)
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_eg VARCHAR(200) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories table (Creates Category if it doesn't exist)
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Products table — linked to categories (Creates Products and linked to category too)
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT UNSIGNED,
    expiry_date DATE NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 4. Stock log table — records every stock adjustment / product change
CREATE TABLE IF NOT EXISTS stock_log (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    change_amount INT NOT NULL,
    reason VARCHAR(255),
    changed_by VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Sample Data (Testing)

INSERT IGNORE INTO categories (name, description) VALUES
    ('Painkillers',   'Pain relief and anti-inflammatory medicines'),
    ('Antibiotics',   'Medicines that fight bacterial infections'),
    ('Vitamins',      'Vitamin and mineral supplements'),
    ('Cough & Cold',  'Medicines for cold, cough and fever');

INSERT INTO products (product_name, description, price, stock, category_id) VALUES
    ('Flexon',        'Common painkiller and fever reducer',     50.00,  75,  1),
    ('Ibuprofen',     'Anti-inflammatory pain relief tablet',    35.00,  120, 1),
    ('Amoxicillin',   'Broad-spectrum antibiotic capsule',       80.00,  60,  2),
    ('Vitamin C',     '500mg Vitamin C effervescent tablet',     45.00,  200, 3),
    ('Sinarest',      'Cough and cold relief tablet',            30.00,  8,   4);

-- Default login: admin / admin123  (password stored as bcrypt hash)
INSERT IGNORE INTO users (username, password_eg, role) VALUES
    ('admin', '$2y$10$PLSUaRfd4Beb06/O9v.KlOvBuxY2CAazz7ZK/aNk6AOri3Gw7v5C.', 'admin');
