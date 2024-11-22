-- Create database
CREATE DATABASE link_manager;

-- Use the database
USE link_manager;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create table for storing links
CREATE TABLE links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    tags VARCHAR(255)
);

ALTER TABLE links
ADD COLUMN username VARCHAR(255),
ADD COLUMN email VARCHAR(255),
ADD COLUMN added_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE links
ADD COLUMN visibility ENUM('public', 'private') NOT NULL DEFAULT 'public';


-- Create table for temporarily storing deleted links
CREATE TABLE deleted_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    tags VARCHAR(255),
    deleted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table for search log
CREATE TABLE search_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    query VARCHAR(255),
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create table for user accounts
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
);

ALTER TABLE users ADD reset_token VARCHAR(64) DEFAULT NULL;


-- -- Insert sample data
-- INSERT INTO links (url, name, description, tags) VALUES 
-- ('https://www.example.com', 'Example Website', 'This is an example website.', 'example'),
-- ('https://www.openai.com', 'OpenAI', 'OpenAI is an artificial intelligence research laboratory.', 'ai, research'),
-- ('https://www.wikipedia.org', 'Wikipedia', 'Wikipedia is a free online encyclopedia.', 'encyclopedia, reference');
