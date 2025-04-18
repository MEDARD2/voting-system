-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    is_admin BOOLEAN DEFAULT FALSE,
    has_voted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Positions table
CREATE TABLE IF NOT EXISTS positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    max_winners INT DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Candidates table
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position_id INT NOT NULL,
    bio TEXT,
    image_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (position_id) REFERENCES positions(id)
);

-- Votes table
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    position_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id),
    FOREIGN KEY (position_id) REFERENCES positions(id),
    UNIQUE KEY unique_user_position (user_id, position_id)
);

-- Activity Logs table
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Voting Settings Table
CREATE TABLE IF NOT EXISTS voting_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user
INSERT INTO users (username, password, email, is_admin) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@example.com', TRUE)
ON DUPLICATE KEY UPDATE id=id;

-- Insert political positions
INSERT INTO positions (title, description, max_winners) VALUES
('President', 'Head of State and Government, Chief Executive of the country', 1),
('Vice President', 'Second highest executive official, assists and can succeed the President', 1),
('Senator', 'Member of the Senate, responsible for legislation and representation of their state', 1),
('Member of Parliament', 'Representative in the National Assembly, legislates and represents constituents', 1),
('Governor', 'Chief Executive of a State, manages state affairs and development', 1),
('Deputy Governor', 'Assists the Governor in state administration and development', 1),
('Local Government Chairman', 'Head of Local Government Administration', 1),
('Councilor', 'Local government representative, handles ward-level governance', 1),
('State House of Assembly Member', 'State-level legislator, makes laws for the state', 1),
('Minister', 'Head of Federal Ministry, implements national policies', 1)
ON DUPLICATE KEY UPDATE title=title;

-- Insert default voting settings
INSERT INTO voting_settings (start_time, end_time, is_active) 
VALUES (NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR), TRUE); 