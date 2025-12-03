CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    contractor_id INT,
    service_name VARCHAR(255),
    address VARCHAR(255),
    description TEXT,
    booking_date DATE,
    status ENUM('pending','accepted','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (contractor_id) REFERENCES users(id)
);