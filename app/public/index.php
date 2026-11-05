<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/src/Core/Database.php';
$page = htmlspecialchars($_GET['page'] ?? 'home', ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Quản lý sinh viên</title><link rel="stylesheet" href="/assets/css/style.css"></head>
<body><header><h1>StudentHub</h1><nav><a href="/">Trang chủ</a> <a href="/?page=students">Sinh viên</a> <a href="/?page=courses">Học phần</a> <a href="/?page=grades">Điểm</a> <a href="/?page=reports">Thống kê</a> <a href="/?page=login">Đăng nhập</a></nav></header><main><h2>Bộ khung Website quản lý sinh viên và điểm học tập</h2><p>Trang yêu cầu: <strong><?= $page ?></strong>. Chức năng đang chờ thành viên phụ trách triển khai.</p><p>Đây là bộ khung, chưa có đăng nhập, CRUD, nhập điểm hay thống kê.</p></main></body></html>
