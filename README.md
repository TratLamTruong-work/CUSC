# Quản lý công việc có phân quyền Admin/User

Laravel 11/12 + Blade + Bootstrap 5 (CDN) + MySQL. Database tạo bằng **file SQL chạy trong MySQL Workbench** (không dùng migration).

## Yêu cầu môi trường

- PHP ≥ 8.2 (khuyên dùng 8.3) với extension `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`, `zip`, `curl` (và `pdo_sqlite` nếu chạy test).
- Composer, MySQL/MariaDB (XAMPP), MySQL Workbench.
- Cần Internet khi mở giao diện (Bootstrap tải từ CDN jsDelivr).

## Cài đặt

1. Tạo project: `composer create-project laravel/laravel:^12.0 task-manager`
2. Chép toàn bộ nội dung gói này vào `task-manager/`, **ghi đè** các file trùng tên.
3. **Xóa toàn bộ file trong `task-manager/database/migrations/`** (các migration mặc định của Laravel) vì database do SQL tạo. Không chạy `php artisan migrate`.
4. Trong MySQL Workbench (bật MySQL ở XAMPP, kết nối `127.0.0.1:3306`, user `root`):
   - Mở `database/sql/schema.sql` → Execute (tạo lại database `task_manager`, **xóa dữ liệu cũ**).
   - Mở `database/sql/seed.sql` → Execute (dữ liệu mẫu, chỉ dùng phát triển).
5. Sửa `.env`:

```env
APP_ENV=local
APP_TIMEZONE=Asia/Ho_Chi_Minh

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_manager
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```
(`SESSION_DRIVER=file` vì không có bảng `sessions`.)

6. Chạy: `php artisan serve` rồi mở http://127.0.0.1:8000

## Tài khoản mẫu (chỉ để kiểm thử, mật khẩu `password123`)

| Email | Vai trò |
|---|---|
| admin@example.com | Admin |
| an@example.com | User (32 task) |
| binh@example.com | User (8 task, có từ khóa trùng với An để thử tìm kiếm) |

## Cấu trúc chính

- `database/sql/` – `schema.sql`, `seed.sql`.
- `routes/web.php` – route guest / auth, resource `tasks`, `tasks.status`.
- `app/Http/Controllers/` – `AuthController`, `TaskController`.
- `app/Http/Requests/` – validation (Form Request).
- `app/Policies/TaskPolicy.php` – phân quyền Admin/User.
- `app/Models/` – `User`, `Task`.
- `resources/views/`, `public/css/app.css`, `public/js/app.js`.
- `tests/` – test tự động (SQLite in-memory, bảng tạo trong `tests/Concerns/CreatesTestTables.php`).
- `CHANGELOG_ADDED.md` – các phần [BỔ SUNG] và vị trí trong code.

## Chạy test

`php artisan test` (dùng SQLite in-memory theo `phpunit.xml` mặc định, cần `pdo_sqlite`; không đụng vào MySQL).

## Trạng thái kiểm thử

Gói code này được viết trong môi trường **không có PHP/Composer/MySQL**, nên:

- Chưa chạy `schema.sql`/`seed.sql` trên MySQL, chưa chạy `php artisan test`, chưa chạy `php -l`.
- Chưa mở giao diện trên trình duyệt (desktop/mobile).
- Các test trong `tests/` chưa được chạy; có thể cần chỉnh nhỏ sau lần chạy đầu.

Danh sách kiểm tra thủ công cần làm: đăng ký (trùng username/email, gửi `role=admin`), đăng nhập đúng/sai, đăng xuất,
User A không truy cập task của B (xem/sửa/đổi trạng thái/xóa), Admin làm được mọi thao tác và lọc theo chủ sở hữu,
sửa task không đổi chủ, CRUD, tìm kiếm + lọc + sắp xếp + phân trang giữ query, thống kê theo phạm vi, quy tắc ngày,
nhãn quá hạn (hôm qua / hôm nay / đã hoàn thành), HTML hiển thị như văn bản, giao diện mobile.
