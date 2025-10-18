-- database/migrations/0001_initial.sql
-- Tabla de usuarios y progreso (ejemplo mínimo - ajusta a tu BD real)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  email VARCHAR(120) UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS progress (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  date DATE NOT NULL,
  minutes_active INT DEFAULT 0,
  water_glasses TINYINT DEFAULT 0,
  streak INT DEFAULT 0,
  FOREIGN KEY (user_id) REFERENCES users(id)
);