CREATE TABLE users(
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (50) NOT NULL UNIQUE,
    password_eg VARCHAR (200) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- auto reord the time when it was created
    ); -- Should end with the semi-colon

-- Example: Insert a test user
INSERT INTO users (username, password_eg) -- No semicolon after the column name
VALUES ('admin','admin#');