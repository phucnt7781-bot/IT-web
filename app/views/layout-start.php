<!doctype html><html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quản lý sinh viên</title><link rel="stylesheet" href="/assets/css/style.css"></head><body>
<header class="header"><a class="brand" href="<?= e(url('dashboard')) ?>">🎓 StudentHub</a><nav>
<?php if (current_user()): ?>
<a href="<?= e(url('dashboard')) ?>">Tổng quan</a>
<?php if (current_user()['role'] === 'admin'): ?><a href="<?= e(url('students')) ?>">Sinh viên</a><?php endif; ?>
<a href="<?= e(url('courses')) ?>">Học phần</a>
<?php if (current_user()['role'] === 'admin'): ?><a href="<?= e(url('enrollments')) ?>">Đăng ký học</a><?php endif; ?>
<a href="<?= e(url('grades')) ?>">Điểm</a><a href="<?= e(url('reports')) ?>">Thống kê</a>
<span class="user"><?= e(current_user()['full_name']) ?> (<?= e(current_user()['role']) ?>)</span>
<form class="inline" action="<?= e(url('logout')) ?>" method="post"><?= csrf_input() ?><button class="linklike" type="submit">Đăng xuất</button></form>
<?php endif; ?></nav></header><main class="container">
<?php if ($f = read_flash()): ?><div class="alert <?= e($f[1]) ?>"><?= e($f[0]) ?></div><?php endif; ?>
