CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(100),
    destination VARCHAR(100),
    travel_date DATE,
    travel_time VARCHAR(50),
    seats INT,
    username VARCHAR(100),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE seat_limits (
    route_key VARCHAR(255) PRIMARY KEY,
    total_seats INT DEFAULT 40
);
