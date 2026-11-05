# StudentHub — Website quản lý sinh viên và điểm học tập

**Stack:** PHP 8.3 (Apache + PDO), MySQL 8.0, HTML/CSS, Docker Compose. Không yêu cầu Composer để chạy. Đây là **mẫu bài tập lớn** dùng môi trường phát triển, chưa phải hệ thống triển khai production.

## 1. Khởi chạy

Cài Docker Desktop, sau đó mở terminal **ở thư mục gốc dự án**:

```bash
docker compose up -d --build
docker compose exec web php /var/www/database/migrate.php
```

Mở: **http://localhost:8080**.

Tài khoản demo (chỉ dùng để học, đổi mật khẩu nếu triển khai ở bất kỳ môi trường chia sẻ nào):

| Vai trò | Username | Password |
|---|---|---|
| Admin | `admin` | `Admin@123` |
| Giảng viên | `giangvien` | `Teacher@123` |
| Sinh viên | `sinhvien` | `Student@123` |

> Lưu ý: lần đầu khởi tạo dữ liệu nên chạy `migrate.php` **một lần**. Chạy lại vẫn giữ dữ liệu, nhưng sẽ thay mới mật khẩu demo và tài khoản liên quan, nên không dùng script này khi có dữ liệu thật.

## 2. Tính năng

- Đăng nhập, đăng xuất, phân quyền admin / lecturer / student.
- Admin: thêm, sửa, xóa, tìm kiếm sinh viên; thêm, sửa, xóa học phần; đăng ký học.
- Admin / giảng viên phụ trách: nhập và sửa điểm quá trình / thi; sinh viên: chỉ xem điểm cá nhân.
- Dashboard và thống kê trung bình hệ 10 có trọng số tín chỉ.
- Công thức bài mẫu: `điểm tổng kết = điểm quá trình × 0.4 + điểm thi × 0.6`; đạt từ 5.0. Có thể đổi theo yêu cầu giảng viên; chưa xử lý học lại, thang GPA 4, điểm chữ.

## 3. Cấu trúc

```text
app/
  public/index.php              # Route entrypoint
  public/assets/css/style.css   # Giao diện CSS
  src/Core/bootstrap.php        # PDO, session, CSRF, helpers
  src/Auth/controller.php       # Xác thực
  src/Students/controller.php   # Sinh viên
  src/Courses/controller.php    # Học phần + đăng ký
  src/Grades/controller.php     # Điểm số
  src/Reports/controller.php    # Dashboard + thống kê
  views/                        # PHP HTML views
database/init.sql               # SQL schema
database/migrate.php            # Tạo schema + dữ liệu demo
docker/php/Dockerfile
docker-compose.yml
```

## 4. Chia việc 6 thành viên

| Người | Vai trò | File phụ trách chính | Gợi ý branch |
|---|---|---|---|
| 1 | Core + Docker + tích hợp | `app/public/index.php`, `app/src/Core/`, `docker/`, `docker-compose.yml`, `.gitignore` | `feature/core` |
| 2 | Login + phân quyền | `app/src/Auth/`, `app/views/login.php` | `feature/auth` |
| 3 | Sinh viên | `app/src/Students/`, `app/views/students.php` | `feature/students` |
| 4 | Học phần + đăng ký học | `app/src/Courses/`, `app/views/courses.php`, `app/views/enrollments.php` | `feature/courses` |
| 5 | Nhập điểm + bảng điểm | `app/src/Grades/`, `app/views/grades.php` | `feature/grades` |
| 6 | Dashboard, báo cáo, CSS và DB | `app/src/Reports/`, `app/views/dashboard.php`, `app/views/reports.php`, `app/public/assets/css/style.css`, `database/` | `feature/reports` |

**Quy trình khuyên dùng:** Người 1 đẩy bộ khung và các file tích hợp cơ bản lên `main`. Mỗi thành viên checkout branch từ `main`, chỉ chỉnh file phần mình, commit và push lên GitHub; tạo Pull Request (PR) để người 1 duyệt. Không để 6 người cùng sửa `index.php` hoặc `bootstrap.php`; nếu muốn thêm route, nhắn người 1 tích hợp.

Ví dụ cho thành viên 3:

```bash
git clone https://github.com/USERNAME/REPO.git
cd REPO
git switch -c feature/students
# Sửa app/src/Students/controller.php và app/views/students.php
git add app/src/Students/controller.php app/views/students.php
git commit -m "feat(students): complete student CRUD"
git push -u origin feature/students
```

Sau đó mở Pull Request: `feature/students` → `main`.

**Để mỗi người có commit đóng góp thực chất**, mỗi thành viên nên kiểm thử và cải tiến module đã giao (ví dụ thêm bộ lọc, trang sửa riêng, validation, test). Không nên tạo 6 commit giả hoặc xóa/tách rồi commit lại nguyên code mà không có thay đổi có ý nghĩa.

## 5. Những hạn chế cố ý của bản demo

- Chưa có CRUD tài khoản giáo viên/sinh viên và chức năng đặt lại mật khẩu; tài khoản demo tạo từ `database/migrate.php`.
- Form sửa yêu cầu nhập ID và điền lại dữ liệu; nhóm có thể phát triển màn hình edit riêng.
- Thao tác xóa sinh viên/học phần kéo theo xóa đăng ký và điểm (theo ràng buộc FK); phải xác nhận trước khi xóa.
- Chưa có test tự động, kiểm tra phân trang hay xuất Excel/PDF.
- Cần bổ sung kiểm thử bảo mật, logging, HTTPS, rate limiting, kiểm soát xóa dữ liệu, bảo vệ thông tin cá nhân trước khi dùng thật.

## 6. Lệnh thường dùng

```bash
docker compose ps
docker compose logs -f web
docker compose down              # dừng container, GIỮ database
docker compose down -v           # xóa cả volume DB, MẤT dữ liệu demo
```
