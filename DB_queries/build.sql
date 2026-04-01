-- ============================================
-- COMPETITIVE PROGRAMMING PLATFORM DATABASE
-- ============================================

CREATE DATABASE IF NOT EXISTS algosparkDB;
CREATE USER IF NOT EXISTS 'clientalgospark'@'localhost' IDENTIFIED BY 'Algospark123!';
GRANT ALL PRIVILEGES ON algosparkDB.* TO 'clientalgospark'@'localhost';
FLUSH PRIVILEGES;

USE algosparkDB;

-- ============================================
-- 1. USERS TABLE
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(30) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(300) NOT NULL,
    bio TEXT,
    avatar_url VARCHAR(255),
    rating INT DEFAULT 1200,        
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_rating (rating)
);
UPDATE users set is_admin=TRUE where username='admin';
UPDATE users set bio="hello i'm the ultimate admin" where username='admin';


SELECT * from users;
DELETE from users WHERE username='Wisdom55';