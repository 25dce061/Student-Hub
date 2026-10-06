-- ==============================================================================
-- StudentHub - Database Setup Script
-- Database: studenthub
-- ==============================================================================

-- 1. Create Database
CREATE DATABASE IF NOT EXISTS studenthub;

-- 2. Select Database
USE studenthub;

-- Drop existing tables to ensure clean recreation (in child-to-parent order)
DROP TABLE IF EXISTS remember_tokens;
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS students;

-- 3. Create 'students' Table with Role
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'student'
);

-- 4. Create 'remember_tokens' Table for Secure Remember Me
CREATE TABLE remember_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- 5. Create 'events' Table
CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_date DATE NOT NULL,
    description TEXT,
    location VARCHAR(100) NOT NULL
);

-- 6. Create 'registrations' Table
CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE (student_id, event_id)
);

-- 7. Insert Sample Students & Admins (Default passwords hashed with password_hash)
-- Password for students is 'password123'
-- Password for admin is 'Admin@123'
INSERT INTO students (name, username, email, password, role) VALUES
('Rahul Sharma', 'rahul21', 'rahul@example.com', '$2y$10$1DjxJUKpgb1vI6DdbhTnbevYgG2lnF8iQT5ajJOO0d/VZgK8IsIVK', 'student'),
('Priya Patel', 'priya_p', 'priya@example.com', '$2y$10$1DjxJUKpgb1vI6DdbhTnbevYgG2lnF8iQT5ajJOO0d/VZgK8IsIVK', 'student'),
('Amit Kumar', 'amit_k', 'amit@example.com', '$2y$10$1DjxJUKpgb1vI6DdbhTnbevYgG2lnF8iQT5ajJOO0d/VZgK8IsIVK', 'student'),
('System Administrator', 'admin', 'admin@studenthub.com', '$2y$10$1DjxJUKpgb1vI6DdbhTnbevYgG2lnF8iQT5ajJOO0d/VZgK8IsIVK', 'admin');

-- 8. Insert Sample Events
INSERT INTO events (event_name, event_date, description, location) VALUES
('Web Development Workshop', '2026-10-15', 'Hands-on practical session on HTML, CSS, PHP, and MySQL.', 'Auditorium Hall A'),
('Annual Tech Fest', '2026-11-05', 'Inter-college technical competition, hackathon, and project showcase.', 'Campus Ground'),
('Career Guidance Seminar', '2026-11-20', 'Industry experts sharing career insights for IT & Computer Science students.', 'Seminar Room 2');

-- 9. Insert Sample Registrations
INSERT INTO registrations (student_id, event_id, registration_date) VALUES
(1, 1, '2026-10-01'),
(2, 1, '2026-10-02'),
(1, 2, '2026-10-03');
