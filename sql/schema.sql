-- Database Schema for Blog Application
-- IN2120 Web Programming - University of Moratuwa

-- Create database (run this separately if needed)
-- CREATE DATABASE IF NOT EXISTS blog_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE blog_app;

-- Drop tables if they exist (for clean setup)
DROP TABLE IF EXISTS blogPost;
DROP TABLE IF EXISTS topic;
DROP TABLE IF EXISTS user;

-- User table
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Topic table (used for "Browse Topics" / "Explore Topic")
CREATE TABLE topic (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    icon VARCHAR(10) DEFAULT '🏷️',
    color VARCHAR(20) DEFAULT '#f97316',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blog post table
CREATE TABLE blogPost (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    topic_id INT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY (topic_id) REFERENCES topic(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_topic_id (topic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed the default topic list (Browse Topics sidebar)
INSERT INTO topic (name, slug, icon, color, sort_order) VALUES
('Web Development',      'web-development',      '🌐', '#f97316', 1),
('AI & Machine Learning', 'ai-machine-learning',  '🤖', '#fbbf24', 2),
('DevOps & Cloud',       'devops-cloud',          '☁️', '#10b981', 3),
('Security',             'security',              '🔒', '#f59e0b', 4),
('Systems',              'systems',               '⚙️', '#ef4444', 5),
('Mobile',               'mobile',                '📱', '#fdba74', 6),
('Data Science',         'data-science',          '📊', '#34d399', 7),
('Open Source',          'open-source',           '💻', '#f472b6', 8);

-- Optional: Insert a sample admin user (password: admin123)
-- Password hash for 'admin123' generated with password_hash()
-- INSERT INTO user (username, email, password, role) VALUES
-- ('admin', 'admin@example.com', '$2y$10$YourGeneratedHashHere', 'admin');

-- Note: To generate password hashes, use PHP:
-- echo password_hash('yourpassword', PASSWORD_DEFAULT);
