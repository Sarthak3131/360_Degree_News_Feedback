<?php
$servername = "localhost";
$username = "root";
$password = "";

// First, create a connection without selecting a database
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS 360_feedback";
if ($conn->query($sql) === TRUE) {
    // Select the database
    $conn->select_db("360_feedback");
    
    // Create users table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_login DATETIME
    )";
    
    if ($conn->query($sql) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }

    // Create article_feedback table if it doesn't exist
    $sql = "CREATE TABLE IF NOT EXISTS article_feedback (
        id INT AUTO_INCREMENT PRIMARY KEY,
        article_id INT NOT NULL,
        user_id INT NOT NULL,
        impact_rating INT NOT NULL CHECK (impact_rating BETWEEN 1 AND 5),
        accuracy_rating INT NOT NULL CHECK (accuracy_rating BETWEEN 1 AND 5),
        clarity_rating INT NOT NULL CHECK (clarity_rating BETWEEN 1 AND 5),
        feedback TEXT,
        suggestions TEXT,
        sentiment ENUM('positive', 'neutral', 'negative'),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) !== TRUE) {
        die("Error creating table: " . $conn->error);
    }
} else {
    die("Error creating database: " . $conn->error);
}
?>