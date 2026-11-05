-- Sơ đồ dữ liệu khởi tạo tối thiểu; các nhóm mở rộng bằng migration có đánh số.
CREATE TABLE IF NOT EXISTS users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('admin','teacher','student') NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS students (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 student_code VARCHAR(30) NOT NULL UNIQUE,
 full_name VARCHAR(150) NOT NULL,
 email VARCHAR(150) UNIQUE,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS courses (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 course_code VARCHAR(30) NOT NULL UNIQUE,
 course_name VARCHAR(150) NOT NULL,
 credits TINYINT UNSIGNED NOT NULL
);
CREATE TABLE IF NOT EXISTS enrollments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 student_id BIGINT UNSIGNED NOT NULL,
 course_id BIGINT UNSIGNED NOT NULL,
 semester VARCHAR(30) NOT NULL,
 UNIQUE KEY unique_registration(student_id,course_id,semester),
 FOREIGN KEY (student_id) REFERENCES students(id),
 FOREIGN KEY (course_id) REFERENCES courses(id)
);
CREATE TABLE IF NOT EXISTS grades (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 enrollment_id BIGINT UNSIGNED NOT NULL UNIQUE,
 process_score DECIMAL(4,2) NULL,
 final_score DECIMAL(4,2) NULL,
 FOREIGN KEY (enrollment_id) REFERENCES enrollments(id),
 CHECK (process_score IS NULL OR process_score BETWEEN 0 AND 10),
 CHECK (final_score IS NULL OR final_score BETWEEN 0 AND 10)
);
