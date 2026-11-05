<?php
// Run only from CLI. Demo-only database initializer; never expose under public/.
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$host=getenv('DB_HOST')?:'127.0.0.1'; $port=getenv('DB_PORT')?:'3306'; $db=getenv('DB_NAME')?:'student_manager';
$pdo=new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",getenv('DB_USER')?:'root',getenv('DB_PASSWORD')?:'', [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec(file_get_contents(__DIR__.'/init.sql'));
function seedUser(PDO $pdo,string $username,string $name,string $role,string $password):int {
    $q=$pdo->prepare('INSERT INTO users(username,password_hash,full_name,role) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE full_name=VALUES(full_name),role=VALUES(role)');
    $q->execute([$username,password_hash($password,PASSWORD_DEFAULT),$name,$role]);
    $q=$pdo->prepare('SELECT id FROM users WHERE username=?');$q->execute([$username]);return (int)$q->fetchColumn();
}
$admin=seedUser($pdo,'admin','Quản trị viên','admin','Admin@123');
$lecturer=seedUser($pdo,'giangvien','Nguyễn Thị Lan','lecturer','Teacher@123');
$student=seedUser($pdo,'sinhvien','Trần Minh An','student','Student@123');
$q=$pdo->prepare('INSERT INTO students(user_id,student_code,full_name,email,birth_date,class_name) VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE user_id=VALUES(user_id)');
$q->execute([$student,'SV001','Trần Minh An','an@example.test','2004-06-12','DHKTPM18A']);
$q->execute([null,'SV002','Lê Hoàng Bình','binh@example.test','2004-04-22','DHKTPM18A']);
$q=$pdo->prepare('INSERT INTO courses(course_code,course_name,credits,lecturer_id) VALUES(?,?,?,?) ON DUPLICATE KEY UPDATE course_name=VALUES(course_name),credits=VALUES(credits),lecturer_id=VALUES(lecturer_id)');
$q->execute(['WEB101','Lập trình Web',3,$lecturer]);
$q->execute(['DB201','Cơ sở dữ liệu',3,$lecturer]);
$pdo->exec("INSERT IGNORE INTO enrollments(student_id,course_id,semester) SELECT s.id,c.id,'2026-2027 HK1' FROM students s CROSS JOIN courses c WHERE s.student_code IN ('SV001','SV002') AND c.course_code IN ('WEB101','DB201')");
$q=$pdo->prepare('INSERT IGNORE INTO grades(enrollment_id,process_score,final_score) SELECT en.id,?,? FROM enrollments en JOIN students s ON s.id=en.student_id JOIN courses c ON c.id=en.course_id WHERE s.student_code=? AND c.course_code=? AND en.semester=?');
$q->execute([8.5,9,'SV001','WEB101','2026-2027 HK1']);
$q->execute([7.5,8,'SV001','DB201','2026-2027 HK1']);
echo "Database initialized. Demo logins: admin/Admin@123, giangvien/Teacher@123, sinhvien/Student@123\n";
