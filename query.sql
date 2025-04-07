-- Drop tables in reverse order of dependencies

-- Create base users table first since other tables depend on it
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255), -- Store hashed passwords
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    email VARCHAR(100)
);

-- Create students table with foreign key to users
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20) UNIQUE,
    name VARCHAR(100),
    age INT,
    email VARCHAR(100),
    class_type ENUM('CIT', 'ELC', 'CIVIL') NOT NULL,
    class_year ENUM('1st', '2nd', '3rd') NOT NULL,
    user_id INT UNIQUE, -- Links to users table for student login
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create teachers table with foreign key to users
CREATE TABLE teachers (
    teacher_id VARCHAR(10) PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    class_type ENUM('CIT', 'ELC', 'CIVIL'),
    class_year ENUM('1st', '2nd', '3rd'),
    user_id INT UNIQUE, -- Links to users table for teacher login
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create attendance table with foreign keys to students and teachers
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    date DATE,
    status ENUM('Present', 'Absent', 'Leave', 'Holiday') NOT NULL DEFAULT 'Present',
    teacher_id VARCHAR(10),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id),
    UNIQUE KEY unique_attendance (student_id, date)
);

-- Create grades table with foreign keys to students and teachers
CREATE TABLE grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    subject VARCHAR(50),
    score INT,
    teacher_id VARCHAR(10),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id)
);

-- Create contacts table for general communication
CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create messages table for communication between users
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    subject VARCHAR(200),
    message TEXT,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);

-- Create vacations table for managing holidays and breaks
CREATE TABLE vacations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Create attendance_admin table for admin's attendance records
CREATE TABLE attendance_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(20),
    date DATE,
    status ENUM('Present', 'Absent', 'Leave', 'Holiday') NOT NULL DEFAULT 'Present',
    admin_id INT,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id),
    FOREIGN KEY (admin_id) REFERENCES users(id),
    UNIQUE KEY unique_admin_attendance (student_id, date)
);

-- Create weekend_config table for managing weekend settings
CREATE TABLE weekend_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    day_of_week ENUM('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday') NOT NULL,
    is_weekend BOOLEAN DEFAULT FALSE,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id),
    UNIQUE KEY unique_day (day_of_week)
);

-- Initialize weekend configuration (default weekends: Saturday and Sunday)
INSERT INTO weekend_config (day_of_week, is_weekend) VALUES
('Sunday', TRUE),
('Monday', FALSE),
('Tuesday', FALSE),
('Wednesday', FALSE),
('Thursday', FALSE),
('Friday', FALSE),
('Saturday', TRUE);

-- Add admin
INSERT INTO users (username, password, role, email) 
VALUES ('admin', MD5('admin111'), 'admin', 'admin111@example.com');

-- Add teachers for each department and year
-- CIT Department Teachers
INSERT INTO users (username, password, role, email) VALUES
('cit_1st', MD5('teacher123'), 'teacher', 'cit_1st@example.com'),
('cit_2nd', MD5('teacher123'), 'teacher', 'cit_2nd@example.com'),
('cit_3rd', MD5('teacher123'), 'teacher', 'cit_3rd@example.com');

INSERT INTO teachers (teacher_id, name, email, class_type, class_year, user_id) VALUES
('CIT1', 'CIT First Year Teacher', 'cit_1st@example.com', 'CIT', '1st', (SELECT id FROM users WHERE username = 'cit_1st')),
('CIT2', 'CIT Second Year Teacher', 'cit_2nd@example.com', 'CIT', '2nd', (SELECT id FROM users WHERE username = 'cit_2nd')),
('CIT3', 'CIT Third Year Teacher', 'cit_3rd@example.com', 'CIT', '3rd', (SELECT id FROM users WHERE username = 'cit_3rd'));

-- ELC Department Teachers
INSERT INTO users (username, password, role, email) VALUES
('elc_1st', MD5('teacher123'), 'teacher', 'elc_1st@example.com'),
('elc_2nd', MD5('teacher123'), 'teacher', 'elc_2nd@example.com'),
('elc_3rd', MD5('teacher123'), 'teacher', 'elc_3rd@example.com');

INSERT INTO teachers (teacher_id, name, email, class_type, class_year, user_id) VALUES
('ELC1', 'ELC First Year Teacher', 'elc_1st@example.com', 'ELC', '1st', (SELECT id FROM users WHERE username = 'elc_1st')),
('ELC2', 'ELC Second Year Teacher', 'elc_2nd@example.com', 'ELC', '2nd', (SELECT id FROM users WHERE username = 'elc_2nd')),
('ELC3', 'ELC Third Year Teacher', 'elc_3rd@example.com', 'ELC', '3rd', (SELECT id FROM users WHERE username = 'elc_3rd'));

-- CIVIL Department Teachers
INSERT INTO users (username, password, role, email) VALUES
('civil_1st', MD5('teacher123'), 'teacher', 'civil_1st@example.com'),
('civil_2nd', MD5('teacher123'), 'teacher', 'civil_2nd@example.com'),
('civil_3rd', MD5('teacher123'), 'teacher', 'civil_3rd@example.com');

INSERT INTO teachers (teacher_id, name, email, class_type, class_year, user_id) VALUES
('CVL1', 'CIVIL First Year Teacher', 'civil_1st@example.com', 'CIVIL', '1st', (SELECT id FROM users WHERE username = 'civil_1st')),
('CVL2', 'CIVIL Second Year Teacher', 'civil_2nd@example.com', 'CIVIL', '2nd', (SELECT id FROM users WHERE username = 'civil_2nd')),
('CVL3', 'CIVIL Third Year Teacher', 'civil_3rd@example.com', 'CIVIL', '3rd', (SELECT id FROM users WHERE username = 'civil_3rd'));

-- Add 10 students for each department and year
-- CIT Department Students
INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('cit_1st_', numbers.n), MD5('student123'), 'student', CONCAT('cit_1st_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('CIT1', LPAD(numbers.n, 2, '0')),
    CONCAT('CIT First Year Student ', numbers.n),
    18 + FLOOR(RAND() * 3),
    CONCAT('cit_1st_', numbers.n, '@example.com'),
    'CIT',
    '1st',
    (SELECT id FROM users WHERE username = CONCAT('cit_1st_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('cit_2nd_', numbers.n), MD5('student123'), 'student', CONCAT('cit_2nd_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('CIT2', LPAD(numbers.n, 2, '0')),
    CONCAT('CIT Second Year Student ', numbers.n),
    19 + FLOOR(RAND() * 3),
    CONCAT('cit_2nd_', numbers.n, '@example.com'),
    'CIT',
    '2nd',
    (SELECT id FROM users WHERE username = CONCAT('cit_2nd_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('cit_3rd_', numbers.n), MD5('student123'), 'student', CONCAT('cit_3rd_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('CIT3', LPAD(numbers.n, 2, '0')),
    CONCAT('CIT Third Year Student ', numbers.n),
    20 + FLOOR(RAND() * 3),
    CONCAT('cit_3rd_', numbers.n, '@example.com'),
    'CIT',
    '3rd',
    (SELECT id FROM users WHERE username = CONCAT('cit_3rd_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

-- ELC Department Students
INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('elc_1st_', numbers.n), MD5('student123'), 'student', CONCAT('elc_1st_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('ELC1', LPAD(numbers.n, 2, '0')),
    CONCAT('ELC First Year Student ', numbers.n),
    18 + FLOOR(RAND() * 3),
    CONCAT('elc_1st_', numbers.n, '@example.com'),
    'ELC',
    '1st',
    (SELECT id FROM users WHERE username = CONCAT('elc_1st_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('elc_2nd_', numbers.n), MD5('student123'), 'student', CONCAT('elc_2nd_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('ELC2', LPAD(numbers.n, 2, '0')),
    CONCAT('ELC Second Year Student ', numbers.n),
    19 + FLOOR(RAND() * 3),
    CONCAT('elc_2nd_', numbers.n, '@example.com'),
    'ELC',
    '2nd',
    (SELECT id FROM users WHERE username = CONCAT('elc_2nd_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('elc_3rd_', numbers.n), MD5('student123'), 'student', CONCAT('elc_3rd_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('ELC3', LPAD(numbers.n, 2, '0')),
    CONCAT('ELC Third Year Student ', numbers.n),
    20 + FLOOR(RAND() * 3),
    CONCAT('elc_3rd_', numbers.n, '@example.com'),
    'ELC',
    '3rd',
    (SELECT id FROM users WHERE username = CONCAT('elc_3rd_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

-- CIVIL Department Students
INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('civil_1st_', numbers.n), MD5('student123'), 'student', CONCAT('civil_1st_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('CVL1', LPAD(numbers.n, 2, '0')),
    CONCAT('CIVIL First Year Student ', numbers.n),
    18 + FLOOR(RAND() * 3),
    CONCAT('civil_1st_', numbers.n, '@example.com'),
    'CIVIL',
    '1st',
    (SELECT id FROM users WHERE username = CONCAT('civil_1st_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('civil_2nd_', numbers.n), MD5('student123'), 'student', CONCAT('civil_2nd_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('CVL2', LPAD(numbers.n, 2, '0')),
    CONCAT('CIVIL Second Year Student ', numbers.n),
    19 + FLOOR(RAND() * 3),
    CONCAT('civil_2nd_', numbers.n, '@example.com'),
    'CIVIL',
    '2nd',
    (SELECT id FROM users WHERE username = CONCAT('civil_2nd_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO users (username, password, role, email)
SELECT 
    CONCAT('civil_3rd_', numbers.n), MD5('student123'), 'student', CONCAT('civil_3rd_', numbers.n, '@example.com')
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;

INSERT INTO students (student_id, name, age, email, class_type, class_year, user_id)
SELECT 
    CONCAT('CVL3', LPAD(numbers.n, 2, '0')),
    CONCAT('CIVIL Third Year Student ', numbers.n),
    20 + FLOOR(RAND() * 3),
    CONCAT('civil_3rd_', numbers.n, '@example.com'),
    'CIVIL',
    '3rd',
    (SELECT id FROM users WHERE username = CONCAT('civil_3rd_', numbers.n))
FROM (SELECT 1 as n UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10) numbers;