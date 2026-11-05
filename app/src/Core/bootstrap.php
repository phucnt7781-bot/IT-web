<?php
declare(strict_types=1);
session_start();
function db(): PDO {
    static $connection = null;
    if ($connection === null) {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_NAME') ?: 'student_manager';
        $connection = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD') ?: '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
    return $connection;
}
function e(mixed $value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $page, array $params = []): string { return '/index.php?' . http_build_query(['page' => $page] + $params); }
function redirect(string $page, array $params = []): never { header('Location: ' . url($page, $params)); exit; }
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void { if (!current_user()) redirect('login'); }
function require_role(string ...$roles): void {
    require_login();
    if (!in_array(current_user()['role'], $roles, true)) { http_response_code(403); throw new RuntimeException('Bạn không có quyền thực hiện thao tác này.'); }
}
function csrf_token(): string { if (!isset($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_input(): string { return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">'; }
function verify_csrf(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf'] ?? ''))) { http_response_code(419); throw new RuntimeException('Phiên biểu mẫu hết hạn. Vui lòng thử lại.'); }
}
function flash(string $message, string $kind = 'ok'): void { $_SESSION['flash'] = [$message, $kind]; }
function read_flash(): ?array { $v = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $v; }
function must_post(): void { if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); throw new RuntimeException('Phương thức không hợp lệ.'); } verify_csrf(); }
function render(string $view, array $data = []): void {
    extract($data, EXTR_SKIP);
    require __DIR__ . '/../../views/layout-start.php';
    require __DIR__ . '/../../views/' . $view . '.php';
    require __DIR__ . '/../../views/layout-end.php';
}
function request_int(string $key): int { return filter_var($_POST[$key] ?? $_GET[$key] ?? 0, FILTER_VALIDATE_INT) ?: 0; }
function selected_student_id(): int {
    require_login();
    $q = db()->prepare('SELECT id FROM students WHERE user_id = ?');
    $q->execute([current_user()['id']]);
    return (int)($q->fetchColumn() ?: 0);
}
function scalar(string $sql, array $params = []): int {
    $q = db()->prepare($sql); $q->execute($params); return (int)$q->fetchColumn();
}
