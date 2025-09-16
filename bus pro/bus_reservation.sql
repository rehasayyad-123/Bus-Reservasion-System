CREATE DATABASE bus_reservation;

USE bus_reservation;

CREATE TABLE buses (
    bus_id INT AUTO_INCREMENT PRIMARY KEY,
    bus_name VARCHAR(100) NOT NULL,
    bus_number VARCHAR(50) NOT NULL UNIQUE,
    total_seats INT NOT NULL
);

CREATE TABLE routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    distance_km DECIMAL(10, 2)
);

CREATE TABLE schedules (
    schedule_id INT AUTO_INCREMENT PRIMARY KEY,
    bus_id INT NOT NULL,
    route_id INT NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    fare DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id),
    FOREIGN KEY (route_id) REFERENCES routes(route_id)
);

CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    schedule_id INT NOT NULL,
    booking_date DATE NOT NULL,
    num_seats INT NOT NULL,
    passenger_name VARCHAR(255) NOT NULL,
    passenger_email VARCHAR(255) NOT NULL,
    booking_status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    booking_timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (schedule_id) REFERENCES schedules(schedule_id)
);

-- Optional: Insert some sample data
INSERT INTO buses (bus_name, bus_number, total_seats) VALUES
('Luxury Express', 'MH12AB1234', 40),
('Shivshahi', 'MH04CD5678', 50),
('New lalpari','MH04565G',60),
('Ashok layland','MH98H597',70);

INSERT INTO routes (origin, destination, distance_km) VALUES
('Jamkhed', 'Mumbai', 150.00),
('Nashik', 'Tuljapur', 800.00),
('Bhoom','Pune', 700.00),
('Waki','Sorgate', 500.00),
('Tuljapur','Kalyan', 400.00);


INSERT INTO schedules (bus_id, route_id, departure_time, arrival_time, fare) VALUES
(1, 1, '08:00:00', '11:00:00', 350.00),
(2, 1, '10:00:00', '13:00:00', 300.00),
(3, 1, '06:00:00', '11:00:00', 350.00),
(4, 1, '11:00:00', '13:00:00', 300.00);