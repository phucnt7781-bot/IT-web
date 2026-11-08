<?php
function grades_rows(): array {
    $u=current_user();
    $sql='SELECT en.id AS enrollment_id,en.semester,s.student_code,s.full_name AS student_name,c.course_code,c.course_name,c.credits,g.process_score,g.final_score,ROUND(g.process_score*0.4+g.final_score*0.6,2) AS average_score FROM enrollments en JOIN students s ON s.id=en.student_id JOIN courses c ON c.id=en.course_id LEFT JOIN grades g ON g.enrollment_id=en.id';
    $params=[];
    if($u['role']==='student'){ $sql.=' WHERE s.user_id=?'; $params[]=$u['id']; }
    if($u['role']==='lecturer'){ $sql.=' WHERE c.lecturer_id=?'; $params[]=$u['id']; }
    $sql.=' ORDER BY en.semester DESC,s.student_code,c.course_code';
    $q=db()->prepare($sql); $q->execute($params); return $q->fetchAll();
}
function grades_index(): void { require_login(); render('grades',['rows'=>grades_rows()]); }
function grades_save(): void {
    require_role('admin','lecturer'); must_post();
    $id=request_int('enrollment_id');
    $process=filter_var($_POST['process_score'] ?? null,FILTER_VALIDATE_FLOAT);
    $final=filter_var($_POST['final_score'] ?? null,FILTER_VALIDATE_FLOAT);
    if(!$id || $process===false || $final===false || $process<0 || $process>10 || $final<0 || $final>10) throw new RuntimeException('Điểm phải nằm trong khoảng từ 0 đến 10.');
    $sql='SELECT COUNT(*) FROM enrollments en JOIN courses c ON c.id=en.course_id WHERE en.id=?';
    $params=[$id];
    if(current_user()['role']==='lecturer') { $sql.=' AND c.lecturer_id=?'; $params[]=current_user()['id']; }
    if(!scalar($sql,$params)) { http_response_code(403); throw new RuntimeException('Bạn không được phép sửa điểm của đăng ký này.'); }
    $q=db()->prepare('INSERT INTO grades(enrollment_id,process_score,final_score) VALUES(?,?,?) ON DUPLICATE KEY UPDATE process_score=VALUES(process_score),final_score=VALUES(final_score)');
    $q->execute([$id,$process,$final]);
    flash('Đã lưu điểm.'); redirect('grades');
}
