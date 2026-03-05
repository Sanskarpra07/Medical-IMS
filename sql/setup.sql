-- setup.sql — Full Database Setup

-- 1. Users table // Creates Table if not exits
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_eg VARCHAR(200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories table (NEW) // For Creating Category if not exits
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Products table — linked to categories (NEW) // Creates Products and linked to category too 
CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    category_id INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);


-- Sample Data // Testing

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
