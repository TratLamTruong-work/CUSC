-- =====================================================================
-- Tạo database và các bảng cho ứng dụng "Quản lý công việc".
-- Chạy trong MySQL Workbench: mở file này -> bấm biểu tượng tia sét (Execute).
--
-- CẢNH BÁO: script XÓA và tạo lại database `task_manager`.
-- Chỉ dùng cho môi trường phát triển.
-- =====================================================================

DROP DATABASE IF EXISTS task_manager;
CREATE DATABASE task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE task_manager;

CREATE TABLE users (
    id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    username       VARCHAR(50)     NOT NULL,
    email          VARCHAR(255)    NOT NULL,
    password       VARCHAR(255)    NOT NULL COMMENT 'Mật khẩu đã hash (bcrypt)',
    role           ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    remember_token VARCHAR(100)    NULL,
    created_at     TIMESTAMP       NULL,
    updated_at     TIMESTAMP       NULL,
    PRIMARY KEY (id),
    UNIQUE KEY users_username_unique (username),
    UNIQUE KEY users_email_unique (email)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE tasks (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id     BIGINT UNSIGNED NOT NULL,
    title       VARCHAR(255)    NOT NULL,
    description TEXT            NULL,
    start_date  DATE            NOT NULL,
    due_date    DATE            NOT NULL,
    priority    ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium',
    status      ENUM('pending', 'in_progress', 'completed') NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP       NULL,
    updated_at  TIMESTAMP       NULL,
    PRIMARY KEY (id),
    KEY tasks_user_id_index (user_id),
    CONSTRAINT tasks_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
