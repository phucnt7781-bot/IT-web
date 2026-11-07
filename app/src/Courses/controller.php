<?php
function courses_index(): void {
    require_login();
    $role = current_user()['role'];
    if ($role === 'student') {
        $q = db()->prepare('SELECT DISTINCT c.*,u.full_name AS lecturer_name FROM courses c JOIN enrollments en ON en.course_id=c.id JOIN students s ON s.id=en.student_id LEFT JOIN users u ON u.id=c.lecturer_id WHERE s.user_id=? ORDER BY c.id DESC');
        $q->execute([current_user()['id']]);
    } elseif ($role === 'lecturer') {
        $q = db()->prepare('SELECT c.*,u.full_name AS lecturer_name FROM courses c LEFT JOIN users u ON u.id=c.lecturer_id WHERE c.lecturer_id=? ORDER BY c.id DESC');
        $q->execute([current_user()['id']]);
    } else {
        $q = db()->query('SELECT c.*,u.full_name AS lecturer_name FROM courses c LEFT JOIN users u ON u.id=c.lecturer_id ORDER BY c.id DESC');
    }
    $lecturers = db()->query("SELECT id,full_name FROM users WHERE role='lecturer' ORDER BY full_name")->fetchAll();
    render('courses', ['courses' => $q->fetchAll(), 'lecturers' => $lecturers]);
}
function courses_save(): void {
    require_role('admin'); must_post();
    $id=request_int('id'); $code=strtoupper(trim((string)($_POST['course_code'] ?? '')));
    $name=trim((string)($_POST['course_name'] ?? '')); $credits=request_int('credits');
    $lecturer = request_int('lecturer_id') ?: null;
    if (!$code || !$name || $credits < 1 || $credits > 10) throw new RuntimeException('Dữ liệu học phần không hợp lệ.');
    if ($lecturer && !scalar("SELECT COUNT(*) FROM users WHERE id=? AND role='lecturer'", [$lecturer])) throw new RuntimeException('Giảng viên không hợp lệ.');
    if ($id) {
        $q=db()->prepare('UPDATE courses SET course_code=?,course_name=?,credits=?,lecturer_id=? WHERE id=?');
        $q->execute([$code,$name,$credits,$lecturer,$id]);
    } else {
        $q=db()->prepare('INSERT INTO courses(course_code,course_name,credits,lecturer_id) VALUES(?,?,?,?)');
        $q->execute([$code,$name,$credits,$lecturer]);
    }
    flash('Đã lưu học phần.'); redirect('courses');
}
function courses_delete(): void {
    require_role('admin'); must_post();
    $q=db()->prepare('DELETE FROM courses WHERE id=?'); $q->execute([request_int('id')]);
    flash('Đã xóa học phần.'); redirect('courses');
}
function enrollments_index(): void {
    require_role('admin');
    $rows=db()->query('SELECT en.*,s.student_code,s.full_name AS student_name,c.course_code,c.course_name FROM enrollments en JOIN students s ON s.id=en.student_id JOIN courses c ON c.id=en.course_id ORDER BY en.id DESC')->fetchAll();
    $students=db()->query('SELECT id,student_code,full_name FROM students ORDER BY student_code')->fetchAll();
    $courses=db()->query('SELECT id,course_code,course_name FROM courses ORDER BY course_code')->fetchAll();
    render('enrollments',['rows'=>$rows,'students'=>$students,'courses'=>$courses]);
}
function enrollments_save(): void {
    require_role('admin'); must_post();
    $student=request_int('student_id'); $course=request_int('course_id'); $semester=trim((string)($_POST['semester'] ?? ''));
    if (!$student || !$course || !preg_match('/^[0-9]{4}-[0-9]{4} HK[123]$/', $semester)) throw new RuntimeException('Thông tin đăng ký học không hợp lệ. Ví dụ học kỳ: 2026-2027 HK1.');
    $q=db()->prepare('INSERT INTO enrollments(student_id,course_id,semester) VALUES(?,?,?)'); $q->execute([$student,$course,$semester]);
    flash('Đã đăng ký học.'); redirect('enrollments');
}
function enrollments_delete(): void {
    require_role('admin'); must_post();
    $q=db()->prepare('DELETE FROM enrollments WHERE id=?'); $q->execute([request_int('id')]);
    flash('Đã hủy đăng ký học.'); redirect('enrollments');
}
