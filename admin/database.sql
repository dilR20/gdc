-- ============================================
-- College Department Management System
-- Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS college_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE college_db;

-- Departments Table
CREATE TABLE IF NOT EXISTS departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    established_year YEAR,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Faculty Table
CREATE TABLE IF NOT EXISTS faculty (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    qualification VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(20),
    photo_path VARCHAR(500),
    bio TEXT,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    INDEX idx_department (department_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    course_code VARCHAR(50),
    semester INT,
    seat_capacity INT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Results Table
CREATE TABLE IF NOT EXISTS department_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    year INT NOT NULL,
    students_appeared INT DEFAULT 0,
    students_passed INT DEFAULT 0,
    first_class INT DEFAULT 0,
    rank_holders INT DEFAULT 0,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_dept_year (department_id, year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    email VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Activity Log
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin (username: admin, password: admin123)
INSERT INTO admin_users (username, password_hash, full_name, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@college.edu');

-- Sample department
INSERT INTO departments (name, code, description, established_year) VALUES
('Economics', 'ECON', 'Department of Economics', 1950);
