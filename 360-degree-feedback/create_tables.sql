-- Create news_categories table
CREATE TABLE IF NOT EXISTS news_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create news_articles table
CREATE TABLE IF NOT EXISTS news_articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    source VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    category_id INT,
    published_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES news_categories(id) ON DELETE SET NULL
);

-- Create users table with roles
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME
);

-- Create articles table
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author VARCHAR(100),
    published_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('draft', 'published') DEFAULT 'published',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create article_feedback table
CREATE TABLE IF NOT EXISTS article_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    article_id INT NOT NULL,
    feedback TEXT NOT NULL,
    impact_rating INT CHECK (impact_rating BETWEEN 1 AND 5),
    accuracy_rating INT CHECK (accuracy_rating BETWEEN 1 AND 5),
    clarity_rating INT CHECK (clarity_rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

-- Create feedback_tags table
CREATE TABLE IF NOT EXISTS feedback_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create feedback_tag_relations table
CREATE TABLE IF NOT EXISTS feedback_tag_relations (
    feedback_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (feedback_id, tag_id),
    FOREIGN KEY (feedback_id) REFERENCES article_feedback(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES feedback_tags(id) ON DELETE CASCADE
);

-- Insert some sample articles
INSERT INTO articles (title, content, author) VALUES
('Introduction to 360-Degree Feedback', 'A comprehensive guide to understanding 360-degree feedback systems...', 'John Doe'),
('Best Practices in Feedback', 'Learn about the best practices when giving and receiving feedback...', 'Jane Smith'),
('The Future of Feedback', 'Exploring how AI and technology are changing feedback systems...', 'Mike Johnson');
