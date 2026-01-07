CREATE DATABASE YOUR_DATABASE_NAME;
USE YOUR_DATABASE_NAME;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('customer', 'farmer', 'nutritionist', 'admin') NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(15),
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100),
    address TEXT,
    location VARCHAR(255),
    contact_number VARCHAR(15),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT,
    category ENUM('vegetables', 'fruits', 'nuts', 'dairy') NOT NULL,
    name VARCHAR(100) NOT NULL,
    image_url VARCHAR(255),
    quantity_available INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id)
);

-- Health Boxes table
CREATE TABLE health_boxes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nutritionist_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nutritionist_id) REFERENCES users(id)
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    health_box_id INT,
    quantity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (health_box_id) REFERENCES health_boxes(id)
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    payment_method ENUM('cod') NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order Items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    health_box_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    farmer_id INT,
    nutritionist_id INT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (health_box_id) REFERENCES health_boxes(id),
    FOREIGN KEY (farmer_id) REFERENCES users(id),
    FOREIGN KEY (nutritionist_id) REFERENCES users(id)
);

-- Favorites table
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    health_box_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (health_box_id) REFERENCES health_boxes(id)
);

-- Insert admin user
INSERT INTO users (role, email, password, name) 
VALUES admin ( ENTER YOUR DETAILS HERE TO MAKE YOU AS ADMIN)
