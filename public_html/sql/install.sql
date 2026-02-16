SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS restaurants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(140) NOT NULL UNIQUE,
    logo_path VARCHAR(255) NULL,
    primary_color CHAR(7) NOT NULL DEFAULT '#FF7A00',
    primary_color_2 CHAR(7) NOT NULL DEFAULT '#FF9F45',
    sidebar_color_a CHAR(7) NOT NULL DEFAULT '#050505',
    sidebar_color_b CHAR(7) NOT NULL DEFAULT '#151515',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT UNSIGNED NOT NULL,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') NOT NULL DEFAULT 'admin',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_restaurant
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_users_restaurant_id (restaurant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT UNSIGNED NOT NULL,
    name VARCHAR(140) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'Geral',
    cost_production DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    fixed_expenses_unit DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    desired_margin_pct DECIMAL(5,2) NOT NULL DEFAULT 30.00,
    suggested_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    current_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_restaurant
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_products_restaurant_id (restaurant_id),
    INDEX idx_products_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS promotions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT UNSIGNED NOT NULL,
    name VARCHAR(140) NOT NULL,
    type ENUM('percent_discount', 'buy_x_pay_y', 'progressive_discount', 'combo_fixed_price') NOT NULL,
    rules_json TEXT NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_promotions_restaurant
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_promotions_restaurant_id (restaurant_id),
    INDEX idx_promotions_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_simulations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT UNSIGNED NOT NULL,
    event_name VARCHAR(140) NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    promotion_id INT UNSIGNED NULL,
    estimated_qty INT UNSIGNED NOT NULL,
    extra_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_pct DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    used_unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    gross_revenue DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    revenue_after_discount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estimated_profit DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    break_even_qty INT UNSIGNED NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_simulations_restaurant
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_simulations_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_simulations_promotion
        FOREIGN KEY (promotion_id) REFERENCES promotions(id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_simulations_restaurant_id (restaurant_id),
    INDEX idx_simulations_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    action VARCHAR(140) NOT NULL,
    meta_json TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_audit_restaurant
        FOREIGN KEY (restaurant_id) REFERENCES restaurants(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_audit_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_audit_restaurant_id (restaurant_id),
    INDEX idx_audit_user_id (user_id),
    INDEX idx_audit_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO restaurants (
    id, name, slug, logo_path, primary_color, primary_color_2, sidebar_color_a, sidebar_color_b, created_at
)
VALUES (1, 'Restaurante Demo', 'restaurante-demo', NULL, '#FF7A00', '#FF9F45', '#050505', '#151515', NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (id, restaurant_id, name, email, password_hash, role, created_at)
VALUES (
    1,
    1,
    'Administrador',
    'admin@churrosflow.local',
    '$2y$10$7Tvvgw2SHgFY0hLGfrQI8e8WortwPyDz0mB/odkN5BBqSPh/flawK',
    'admin',
    NOW()
)
ON DUPLICATE KEY UPDATE name = VALUES(name);
