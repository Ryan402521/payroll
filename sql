-- 1. Create the database
CREATE DATABASE employee_system;
USE employee_system;

-- 2. Create positions table
CREATE TABLE positions (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    rate_per_hour DECIMAL(10,2) NOT NULL
);

-- 3. Create users table
CREATE TABLE users (
    id VARCHAR(20) NOT NULL PRIMARY KEY,
    full_name VARCHAR(100),
    email VARCHAR(100),
    role ENUM('admin', 'employee'),
    password VARCHAR(255),
    profile_pic VARCHAR(255),
    position_id INT(11),
    INDEX(email),
    INDEX(position_id),
    FOREIGN KEY (position_id) REFERENCES positions(id)
);

-- 4. Create attendance_logs table
CREATE TABLE attendance_logs (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    date DATE,
    time_in DATETIME,
    time_out DATETIME,
    INDEX(user_id)
);

-- 5. Create cash_advance table
CREATE TABLE cash_advance (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    ca_date DATE NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    INDEX(user_id)
);

-- 6. Create deductions table
CREATE TABLE deductions (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL
);

-- 7. Create overtime table
CREATE TABLE overtime (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(20) NOT NULL,
    ot_date DATE NOT NULL,
    hours DECIMAL(5,2) NOT NULL,
    rate DECIMAL(10,2) NOT NULL,
    INDEX(user_id)
);

-- 8. Create payroll table
CREATE TABLE payroll (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) NOT NULL,
    employee_name VARCHAR(100) NOT NULL,
    base_salary DECIMAL(10,2) NOT NULL,
    overtime_pay DECIMAL(10,2) NOT NULL,
    deductions DECIMAL(10,2) NOT NULL,
    cash_advance DECIMAL(10,2) NOT NULL,
    net_pay DECIMAL(10,2) NOT NULL,
    pay_date DATE NOT NULL
);
