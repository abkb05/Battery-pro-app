-- BatteryPro Management System Database Schema
-- MySQL Database Schema

CREATE DATABASE IF NOT EXISTS batterypro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE batterypro;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    last_login DATETIME NULL,
    last_activity DATETIME NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    photo VARCHAR(255),
    total_purchased INT DEFAULT 0,
    pending_amount DECIMAL(10,2) DEFAULT 0.00,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Batteries table
CREATE TABLE IF NOT EXISTS batteries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand VARCHAR(100) NOT NULL,
    size INT NOT NULL, -- 50, 55, 65, 75, 100, 110, 150, 180, 200, 1800, 2500
    voltage INT NOT NULL,
    plates INT NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NOT NULL,
    supplier_id INT,
    image VARCHAR(255),
    barcode VARCHAR(50),
    qr_code VARCHAR(100),
    min_stock_level INT DEFAULT 5,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Stock table (inventory)
CREATE TABLE IF NOT EXISTS stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    battery_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    location VARCHAR(100) DEFAULT 'Main Store',
    batch_number VARCHAR(50),
    expiry_date DATE NULL,
    received_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (battery_id) REFERENCES batteries(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    total_purchases DECIMAL(10,2) DEFAULT 0.00,
    pending_payments DECIMAL(10,2) DEFAULT 0.00,
    last_purchase_date DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sales table
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    user_id INT NOT NULL,
    sale_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_amount DECIMAL(10,2) NOT NULL,
    total_profit DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'credit', 'bank_transfer') DEFAULT 'cash',
    payment_status ENUM('paid', 'partial', 'pending') DEFAULT 'paid',
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sale items table
CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    stock_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    purchase_price DECIMAL(10,2) NOT NULL,
    profit DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_id) REFERENCES stock(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Expenses table
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('rent', 'electricity', 'salary', 'transport', 'miscellaneous', 'other') NOT NULL,
    description VARCHAR(255),
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    payment_method ENUM('cash', 'card', 'bank_transfer', 'cheque') DEFAULT 'cash',
    receipt_number VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reports table
CREATE TABLE IF NOT EXISTS reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    type ENUM('daily', 'weekly', 'monthly', 'yearly', 'profit_loss', 'supplier', 'inventory', 'sales') NOT NULL,
    period_start DATE NOT NULL,
    period_end DATE NOT NULL,
    data JSON, -- Store report data as JSON
    file_path VARCHAR(255), -- Path to generated PDF/Excel file
    generated_by INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments table (for supplier payments, customer payments, etc.)
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_type ENUM('supplier', 'customer', 'expense', 'other') NOT NULL,
    related_id INT, -- ID of supplier, customer, expense, etc.
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    payment_method ENUM('cash', 'card', 'bank_transfer', 'cheque') DEFAULT 'cash',
    reference_number VARCHAR(100),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(100) NOT NULL DEFAULT 'BatteryPro',
    shop_address TEXT,
    shop_phone VARCHAR(20),
    shop_email VARCHAR(100),
    shop_logo VARCHAR(255),
    currency VARCHAR(10) DEFAULT 'USD',
    currency_symbol VARCHAR(5) DEFAULT '$',
    timezone VARCHAR(50) DEFAULT 'UTC',
    date_format VARCHAR(20) DEFAULT 'Y-m-d',
    time_format VARCHAR(20) DEFAULT 'H:i:s',
    backup_enabled TINYINT(1) DEFAULT 1,
    backup_frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'daily',
    backup_time TIME DEFAULT '02:00:00',
    google_drive_token TEXT,
    theme ENUM('light', 'dark') DEFAULT 'light',
    notifications_enabled TINYINT(1) DEFAULT 1,
    low_stock_alert TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Backups table
CREATE TABLE IF NOT EXISTS backups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    file_size INT, -- in bytes
    backup_type ENUM('manual', 'automatic') DEFAULT 'automatic',
    status ENUM('pending', 'completed', 'failed', 'uploaded') DEFAULT 'pending',
    google_drive_id VARCHAR(255), -- Google Drive file ID
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    completed_at DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Audit log table
CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(100) NOT NULL,
    entity_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Warranties table
CREATE TABLE IF NOT EXISTS warranties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    battery_id INT NOT NULL,
    customer_id INT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired', 'claimed') DEFAULT 'active',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (battery_id) REFERENCES batteries(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT IGNORE INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@batterypro.com', '$2y$10$ldinYNDUzNrxzlEfqlGUXO7aHpsP2kp9dUETVZMz60cKpve6IQ/C.', 'System Administrator', 'admin');

-- Insert default settings
INSERT IGNORE INTO settings (shop_name, shop_address, shop_phone, shop_email) VALUES 
('BatteryPro Management System', '123 Battery Street, Power City', '+1-555-123-4567', 'info@batterypro.com');

-- ============================================
-- v2.0 Migrations: Multi-Tier Pricing, Serial Numbers, Exchange Batteries
-- ============================================

ALTER TABLE batteries ADD COLUMN wholesale_price DECIMAL(10,2) DEFAULT NULL AFTER sale_price;
ALTER TABLE batteries ADD COLUMN dealer_price DECIMAL(10,2) DEFAULT NULL AFTER wholesale_price;
ALTER TABLE customers ADD COLUMN customer_type ENUM('retail','wholesale','dealer') DEFAULT 'retail';
ALTER TABLE sale_items ADD COLUMN serial_number VARCHAR(100) DEFAULT NULL;
ALTER TABLE stock ADD COLUMN serial_number VARCHAR(100) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS exchange_batteries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    old_brand VARCHAR(100) DEFAULT NULL,
    old_size VARCHAR(50) DEFAULT NULL,
    old_condition ENUM('good','fair','poor','dead') DEFAULT 'fair',
    exchange_value DECIMAL(10,2) DEFAULT 0.00,
    scrap_status ENUM('pending','scrapped','sold') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
