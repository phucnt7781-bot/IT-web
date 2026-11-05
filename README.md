# Website quản lý sinh viên và điểm học tập — Bộ khung nhóm 6 người

## Chạy thử

Yêu cầu Docker Desktop đang chạy. Tại thư mục gốc:

```bash
docker compose up -d --build
```

Mở http://localhost:8080. Database tự tạo khi volume MySQL mới. Nếu đã có volume cũ, script `init.sql` không tự chạy lại; cần migration hoặc chủ động reset dữ liệu local.

## Phân công (mỗi người làm file trong module của mình)

| Thành viên | Module | Công việc |
|---|---|---|
| TV1 | `app/src/Core/`, `docker/`, `docker-compose.yml`, `app/public/index.php` | Tích hợp, cấu hình, routing, bảo mật cơ bản |
| TV2 | `app/src/Auth/`, `app/views/auth/` | Đăng nhập/đăng xuất, phân quyền, session |
| TV3 | `app/src/Students/`, `app/views/students/` | CRUD sinh viên, tìm kiếm, kiểm tra dữ liệu |
| TV4 | `app/src/Courses/`, `app/views/courses/` | CRUD học phần, đăng ký học |
| TV5 | `app/src/Grades/`, `app/views/grades/` | Nhập điểm, sửa điểm, tính điểm trung bình |
| TV6 | `app/src/Reports/`, `app/views/reports/`, `app/public/assets/` | Dashboard, báo cáo, giao diện |

**Quy ước:** các thay đổi chung trong `index.php`, `database/init.sql`, Docker phải báo TV1 trước. Schema mở rộng nên tạo file migration riêng `database/migrations/NNN_ten.sql` để tránh xung đột. Tránh đưa mật khẩu thật lên GitHub.

## Cùng push trực tiếp lên main, không Pull Request

Chủ repository thêm 5 thành viên làm Collaborator với quyền Write, đảm bảo nhánh main không bắt buộc PR.

```bash
git clone https://github.com/phucnt7781-bot/website-quan-ly-sinh-vien.git
cd website-quan-ly-sinh-vien
git pull origin main
# Code phần được phân công
git status
git add app/src/Students app/views/students
git commit -m "TV3: them quan ly sinh vien"
git pull --rebase origin main
git push origin main
```

**Lưu ý:** Nếu dùng GitHub repo đã có README, copy **nội dung** bộ khung này vào thư mục clone (không chép `.git`), rồi commit/push. Không force-push. Mỗi người nên kiểm tra chức năng trước khi push.
