-- Active: 1775930662579@@127.0.0.1@3306@algosparkDB

-- COMPETITIVE PROGRAMMING PLATFORM DATABASE
drop database if exists algosparkDB;
CREATE DATABASE IF NOT EXISTS algosparkDB;
CREATE USER IF NOT EXISTS 'clientalgospark'@'localhost' IDENTIFIED BY 'Algospark123!';
GRANT ALL PRIVILEGES ON algosparkDB.* TO 'clientalgospark'@'localhost';
FLUSH PRIVILEGES;

USE algosparkDB;

-- 1. USERS TABLE
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
UPDATE users set is_admin=true WHERE username='admin';
-- 1.B USER FAVORITES TABLE
CREATE TABLE user_favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    favorite_user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (favorite_user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_favorite (user_id, favorite_user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_favorite_user_id (favorite_user_id),
    CONSTRAINT chk_not_self_favorite CHECK (user_id <> favorite_user_id)
);
INSERT INTO user_favorites (user_id, favorite_user_id) VALUES (1, 2);
SELECT * FROM user_favorites;
-- 2. PROGRAMMING LANGUAGES TABLE
CREATE TABLE languages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    compiler_command VARCHAR(255),
    file_extension VARCHAR(10),
    is_enabled BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_name (name)
);

-- Insert default languages
INSERT INTO languages (name, compiler_command, file_extension, is_enabled) VALUES
('C++', 'g++ -O2 -std=c++17', '.cpp', TRUE),
('Python', 'python3', '.py', TRUE),
('Java', 'javac -encoding UTF-8', '.java', TRUE),
('JavaScript', 'node', '.js', TRUE),
('C', 'gcc -O2', '.c', TRUE);

-- 3. VERDICT STATUS TABLE
CREATE TABLE verdict_status (
    id INT PRIMARY KEY AUTO_INCREMENT,
    verdict VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100),
    color_code VARCHAR(10),
    
    INDEX idx_verdict (verdict)
);

-- Insert verdict statuses
INSERT INTO verdict_status (verdict, display_name, color_code) VALUES
('AC', 'Accepted', '#28a745'),
('WA', 'Wrong Answer', '#dc3545'),
('TLE', 'Time Limit Exceeded', '#fd7e14'),
('MLE', 'Memory Limit Exceeded', '#6f42c1'),
('RE', 'Runtime Error', '#e83e8c'),
('CE', 'Compilation Error', '#17a2b8'),
('PE', 'Presentation Error', '#ffc107'),
('PENDING', 'Pending', '#6c757d');

-- 4. PROBLEMS TABLE
CREATE TABLE problems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL UNIQUE,
    description LONGTEXT NOT NULL,
    difficulty INT NOT NULL DEFAULT 900,
    category VARCHAR(300) NOT NULL DEFAULT 'General',
    time_limit_ms INT DEFAULT 1000,
    memory_limit_mb INT DEFAULT 256,
    author_id INT,
    success_count INT DEFAULT 0,
    total_attempts INT DEFAULT 0,
    acceptance_rate DECIMAL(5, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_difficulty (difficulty),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
);

-- 5. TEST CASES TABLE
CREATE TABLE test_cases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    problem_id INT NOT NULL,
    input LONGTEXT NOT NULL,
    expected_output LONGTEXT NOT NULL,
    is_sample BOOLEAN DEFAULT FALSE,
    order_index INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    INDEX idx_problem_id (problem_id),
    INDEX idx_is_sample (is_sample)
);

-- 6. SUBMISSIONS TABLE
CREATE TABLE submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    problem_id INT NOT NULL,
    language_id INT NOT NULL,
    code LONGTEXT NOT NULL,
    verdict_id INT DEFAULT 8,
    execution_time_ms INT,
    memory_used_mb INT,
    passed_tests INT DEFAULT 0,
    total_tests INT,
    error_message LONGTEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    FOREIGN KEY (verdict_id) REFERENCES verdict_status(id),
    INDEX idx_user_problem (user_id, problem_id),
    INDEX idx_verdict (verdict_id),
    INDEX idx_submitted_at (submitted_at)
);

-- 7. CONTESTS TABLE
CREATE TABLE contests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL UNIQUE,
    description LONGTEXT,
    creator_id INT NOT NULL,
    approval_status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    approved_by INT NULL,
    approved_at DATETIME NULL,
    rejection_reason VARCHAR(255) NULL,
    published_at DATETIME NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    duration_minutes INT,
    status VARCHAR(20) DEFAULT 'scheduled',
    max_participants INT,
    contest_type VARCHAR(30) DEFAULT 'ranked',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_approval_status (approval_status),
    INDEX idx_creator_approval (creator_id, approval_status),
    INDEX idx_start_time (start_time),
    INDEX idx_end_time (end_time)
);

-- 7.B CONTEST APPROVAL AUDIT TABLE
CREATE TABLE contest_approval_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contest_id INT NOT NULL,
    reviewed_by INT NOT NULL,
    decision ENUM('approved', 'rejected') NOT NULL,
    note VARCHAR(255),
    reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_contest_reviewed_at (contest_id, reviewed_at),
    INDEX idx_reviewed_by (reviewed_by)
);

-- 8. CONTEST PROBLEMS TABLE
CREATE TABLE contest_problems (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contest_id INT NOT NULL,
    problem_id INT NOT NULL,
    points INT DEFAULT 100,
    order_in_contest INT DEFAULT 0,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    UNIQUE KEY unique_contest_problem (contest_id, problem_id),
    INDEX idx_contest_id (contest_id)
);

-- 9. CONTEST PARTICIPATIONS TABLE
CREATE TABLE contest_participations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    contest_id INT NOT NULL,
    joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_virtual BOOLEAN DEFAULT FALSE,
    final_rank INT,
    final_score INT DEFAULT 0,
    penalty_time INT DEFAULT 0,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    UNIQUE KEY unique_participation (user_id, contest_id),
    INDEX idx_contest_id (contest_id),
    INDEX idx_final_rank (final_rank)
);

-- 10. CONTEST RESULTS TABLE
CREATE TABLE contest_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    contest_id INT NOT NULL,
    final_score INT DEFAULT 0,
    penalty_time INT DEFAULT 0,
    problems_solved INT DEFAULT 0,
    final_rank INT,
    last_submission_time DATETIME,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (contest_id) REFERENCES contests(id) ON DELETE CASCADE,
    UNIQUE KEY unique_result (user_id, contest_id),
    INDEX idx_contest_id (contest_id),
    INDEX idx_final_rank (final_rank)
);

-- 11. PROBLEM RATINGS TABLE
CREATE TABLE problem_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    problem_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (problem_id) REFERENCES problems(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_problem_rating (user_id, problem_id),
    INDEX idx_problem_id (problem_id)
);

-- 12. USER STATISTICS TABLE (DENORMALIZED FOR PERFORMANCE)
CREATE TABLE user_statistics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    total_submissions INT DEFAULT 0,
    accepted_submissions INT DEFAULT 0,
    solved_problems INT DEFAULT 0,
    current_rating INT DEFAULT 1200,
    max_rating INT DEFAULT 1200,
    contests_participated INT DEFAULT 0,
    best_rank INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_current_rating (current_rating),
    INDEX idx_solved_problems (solved_problems)
);

-- INDEXES FOR COMMON QUERIES

-- Submissions by user and verdict
ALTER TABLE submissions ADD INDEX idx_user_verdict (user_id, verdict_id);

-- Submissions by problem
ALTER TABLE submissions ADD INDEX idx_problem_submitted (problem_id, submitted_at);

-- Contest standings
ALTER TABLE contest_results ADD INDEX idx_score_rank (contest_id, final_score DESC);

-- Contest moderation queue
ALTER TABLE contests ADD INDEX idx_approval_queue (approval_status, created_at);

-- User activity
ALTER TABLE submissions ADD INDEX idx_user_submitted (user_id, submitted_at DESC);

-- VIEWS FOR COMMON QUERIES

-- Weekly leaderboard
CREATE VIEW weekly_leaderboard AS
SELECT 
    u.id,
    u.username,
    u.rating,
    COUNT(DISTINCT s.id) as submissions_this_week,
    COUNT(DISTINCT CASE WHEN v.verdict = 'AC' THEN s.id END) as accepted_this_week
FROM users u
LEFT JOIN submissions s ON u.id = s.user_id 
    AND s.submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
LEFT JOIN verdict_status v ON s.verdict_id = v.id
GROUP BY u.id, u.username, u.rating
ORDER BY accepted_this_week DESC, u.rating DESC;

-- Problem difficulty distribution
CREATE VIEW problem_difficulty_stats AS
SELECT 
    difficulty,
    COUNT(*) as total_problems,
    ROUND(AVG(success_count), 2) as avg_success_count,
    ROUND(AVG(acceptance_rate), 2) as avg_acceptance_rate
FROM problems
GROUP BY difficulty;

-- User submission history
CREATE VIEW user_problem_stats AS
SELECT 
    u.id as user_id,
    u.username,
    p.id as problem_id,
    p.title,
    COUNT(*) as total_attempts,
    COUNT(CASE WHEN v.verdict = 'AC' THEN 1 END) as accepted_count,
    MIN(s.submitted_at) as first_submission,
    MAX(s.submitted_at) as last_submission
FROM users u
LEFT JOIN submissions s ON u.id = s.user_id
LEFT JOIN problems p ON s.problem_id = p.id
LEFT JOIN verdict_status v ON s.verdict_id = v.id
GROUP BY u.id, p.id;

-- User favorite counts
CREATE VIEW user_favorite_stats AS
SELECT
    u.id AS user_id,
    u.username,
    COUNT(uf.id) AS favorite_count
FROM users u
LEFT JOIN user_favorites uf ON u.id = uf.favorite_user_id
GROUP BY u.id, u.username;

UPDATE users SET is_admin = TRUE WHERE username = 'admin';

INSERT INTO problems (title, description, difficulty, category, time_limit_ms, memory_limit_mb, author_id) VALUES
('Two Sum', 'Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target.', 800, 'Arrays', 1000, 256, 1),
('Longest Substring Without Repeating Characters', 'Given a string s, find the length of the longest substring without repeating characters.', 1200, 'Strings', 1000, 256, 1),
('Median of Two Sorted Arrays', 'Given two sorted arrays nums1 and nums2 of size m and n respectively, return the median of the two sorted arrays.', 1500, 'Sorting', 1000, 256, 1);

INSERT into test_cases (problem_id, input, expected_output, is_sample, order_index) VALUES
(1, 'nums = [2,7,11,15], target = 9', '[0,1]', TRUE, 0),
(1, 'nums = [3,2,4], target = 6', '[1,2]', TRUE, 1),
(1, 'nums = [3,3], target = 6', '[0,1]', TRUE, 2),
(2, 's = "abcabcbb"', '3', TRUE, 0),
(2, 's = "bbbbb"', '1', TRUE, 1),
(2, 's = "pwwkew"', '3', TRUE, 2),
(3, 'nums1 = [1,3], nums2 = [2]', '2.0', TRUE, 0),
(3, 'nums1 = [1,2], nums2 = [3,4]', '2.5', TRUE, 1),
(3, 'nums1 = [0,0], nums2 = [0,0]', '0.0', TRUE, 2);

SELECT * from submissions;