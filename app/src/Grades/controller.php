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
