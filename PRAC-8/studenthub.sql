-- ==============================================================================
-- StudentHub - Database Setup Script
-- ==============================================================================

-- 1. Create the database
CREATE DATABASE IF NOT EXISTS studenthub;

-- 2. Select the database
USE studenthub;

-- 3. Create the 'students' table
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- 4. Create the 'events' table
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    description TEXT
);

-- 5. Create the 'registrations' table (Junction table with foreign keys)
CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

-- 6. Insert sample records into 'students'
INSERT INTO students (full_name, username, email, password) VALUES
('Rahul Sharma', 'rahul21', 'rahul@example.com', 'rahul@123'),
('Priya Patel', 'priya_p', 'priya@example.com', 'priya@123'),
('Amit Kumar', 'amit_k', 'amit@example.com', 'amit@123');

-- 7. Insert sample records into 'events'
INSERT INTO events (event_name, event_date, venue, description) VALUES
('Web Development Workshop', '2026-10-15', 'Auditorium Hall A', 'Hands-on practical session on HTML, CSS, PHP, and MySQL.'),
('Annual Tech Fest', '2026-11-05', 'Campus Ground', 'Inter-college technical competition and coding hackathon.'),
('Career Guidance Seminar', '2026-11-20', 'Seminar Room 2', 'Industry experts sharing career insights for IT graduates.');

-- 8. Insert sample records into 'registrations'
INSERT INTO registrations (student_id, event_id, registration_date) VALUES
(1, 1, '2026-10-03 10:15:00'),
(2, 1, '2026-10-03 11:30:00'),
(1, 2, '2026-10-03 12:00:00');
