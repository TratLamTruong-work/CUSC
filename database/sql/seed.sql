-- =====================================================================
-- DỮ LIỆU MẪU CHỈ DÙNG KIỂM THỬ (môi trường phát triển).
-- Chạy SAU schema.sql. Mật khẩu của cả 3 tài khoản: password123
--   admin@example.com  (Admin)
--   an@example.com     (User)
--   binh@example.com   (User)
-- Ngày của task tính theo CURDATE() của MySQL nên luôn có task quá hạn / hôm nay / sắp tới.
-- =====================================================================
SET NAMES utf8mb4;
USE task_manager;

INSERT INTO users (username, email, password, role, created_at, updated_at) VALUES
('admin', 'admin@example.com', '$2y$10$PRz/nha7MYa5RSitZ2WKn.yqlm1/Fset8vycPYTHDDvsgHCoyG5EG', 'admin', NOW(), NOW()),
('an',    'an@example.com',    '$2y$10$PRz/nha7MYa5RSitZ2WKn.yqlm1/Fset8vycPYTHDDvsgHCoyG5EG', 'user',  NOW(), NOW()),
('binh',  'binh@example.com',  '$2y$10$PRz/nha7MYa5RSitZ2WKn.yqlm1/Fset8vycPYTHDDvsgHCoyG5EG', 'user',  NOW(), NOW());

SET @admin_id = (SELECT id FROM users WHERE email = 'admin@example.com');
SET @an_id    = (SELECT id FROM users WHERE email = 'an@example.com');
SET @binh_id  = (SELECT id FROM users WHERE email = 'binh@example.com');

INSERT INTO tasks (user_id, title, description, start_date, due_date, priority, status, created_at, updated_at) VALUES
(@an_id, 'Công việc của An số 1', 'Mô tả công việc số 1. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -14 DAY), DATE_ADD(CURDATE(), INTERVAL -9 DAY), 'medium', 'in_progress', NOW() - INTERVAL 990 MINUTE, NOW() - INTERVAL 990 MINUTE),
(@an_id, 'Công việc của An số 2', 'Mô tả công việc số 2. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -13 DAY), DATE_ADD(CURDATE(), INTERVAL -8 DAY), 'high', 'completed', NOW() - INTERVAL 980 MINUTE, NOW() - INTERVAL 980 MINUTE),
(@an_id, 'Công việc của An số 3', NULL, DATE_ADD(CURDATE(), INTERVAL -12 DAY), DATE_ADD(CURDATE(), INTERVAL -7 DAY), 'low', 'pending', NOW() - INTERVAL 970 MINUTE, NOW() - INTERVAL 970 MINUTE),
(@an_id, 'Công việc của An số 4', 'Mô tả công việc số 4. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -11 DAY), DATE_ADD(CURDATE(), INTERVAL -6 DAY), 'medium', 'in_progress', NOW() - INTERVAL 960 MINUTE, NOW() - INTERVAL 960 MINUTE),
(@an_id, 'Công việc của An số 5', 'Mô tả công việc số 5. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -10 DAY), DATE_ADD(CURDATE(), INTERVAL -5 DAY), 'high', 'completed', NOW() - INTERVAL 950 MINUTE, NOW() - INTERVAL 950 MINUTE),
(@an_id, 'Công việc của An số 6', NULL, DATE_ADD(CURDATE(), INTERVAL -9 DAY), DATE_ADD(CURDATE(), INTERVAL -4 DAY), 'low', 'pending', NOW() - INTERVAL 940 MINUTE, NOW() - INTERVAL 940 MINUTE),
(@an_id, 'Công việc của An số 7', 'Mô tả công việc số 7. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -8 DAY), DATE_ADD(CURDATE(), INTERVAL -3 DAY), 'medium', 'in_progress', NOW() - INTERVAL 930 MINUTE, NOW() - INTERVAL 930 MINUTE),
(@an_id, 'Công việc của An số 8', 'Mô tả công việc số 8. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -7 DAY), DATE_ADD(CURDATE(), INTERVAL -2 DAY), 'high', 'completed', NOW() - INTERVAL 920 MINUTE, NOW() - INTERVAL 920 MINUTE),
(@an_id, 'Công việc của An số 9', NULL, DATE_ADD(CURDATE(), INTERVAL -6 DAY), DATE_ADD(CURDATE(), INTERVAL -1 DAY), 'low', 'pending', NOW() - INTERVAL 910 MINUTE, NOW() - INTERVAL 910 MINUTE),
(@an_id, 'Công việc của An số 10', 'Mô tả công việc số 10. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -5 DAY), DATE_ADD(CURDATE(), INTERVAL 0 DAY), 'medium', 'in_progress', NOW() - INTERVAL 900 MINUTE, NOW() - INTERVAL 900 MINUTE),
(@an_id, 'Công việc của An số 11', 'Mô tả công việc số 11. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -4 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'high', 'completed', NOW() - INTERVAL 890 MINUTE, NOW() - INTERVAL 890 MINUTE),
(@an_id, 'Công việc của An số 12', NULL, DATE_ADD(CURDATE(), INTERVAL -3 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'low', 'pending', NOW() - INTERVAL 880 MINUTE, NOW() - INTERVAL 880 MINUTE),
(@an_id, 'Công việc của An số 13', 'Mô tả công việc số 13. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -2 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'medium', 'in_progress', NOW() - INTERVAL 870 MINUTE, NOW() - INTERVAL 870 MINUTE),
(@an_id, 'Công việc của An số 14', 'Mô tả công việc số 14. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL -1 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'high', 'completed', NOW() - INTERVAL 860 MINUTE, NOW() - INTERVAL 860 MINUTE),
(@an_id, 'Công việc của An số 15', NULL, DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'low', 'pending', NOW() - INTERVAL 850 MINUTE, NOW() - INTERVAL 850 MINUTE),
(@an_id, 'Công việc của An số 16', 'Mô tả công việc số 16. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), 'medium', 'in_progress', NOW() - INTERVAL 840 MINUTE, NOW() - INTERVAL 840 MINUTE),
(@an_id, 'Công việc của An số 17', 'Mô tả công việc số 17. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'high', 'completed', NOW() - INTERVAL 830 MINUTE, NOW() - INTERVAL 830 MINUTE),
(@an_id, 'Công việc của An số 18', NULL, DATE_ADD(CURDATE(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 8 DAY), 'low', 'pending', NOW() - INTERVAL 820 MINUTE, NOW() - INTERVAL 820 MINUTE),
(@an_id, 'Công việc của An số 19', 'Mô tả công việc số 19. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 4 DAY), DATE_ADD(CURDATE(), INTERVAL 9 DAY), 'medium', 'in_progress', NOW() - INTERVAL 810 MINUTE, NOW() - INTERVAL 810 MINUTE),
(@an_id, 'Công việc của An số 20', 'Mô tả công việc số 20. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'high', 'completed', NOW() - INTERVAL 800 MINUTE, NOW() - INTERVAL 800 MINUTE),
(@an_id, 'Công việc của An số 21', NULL, DATE_ADD(CURDATE(), INTERVAL 6 DAY), DATE_ADD(CURDATE(), INTERVAL 11 DAY), 'low', 'pending', NOW() - INTERVAL 790 MINUTE, NOW() - INTERVAL 790 MINUTE),
(@an_id, 'Công việc của An số 22', 'Mô tả công việc số 22. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 12 DAY), 'medium', 'in_progress', NOW() - INTERVAL 780 MINUTE, NOW() - INTERVAL 780 MINUTE),
(@an_id, 'Công việc của An số 23', 'Mô tả công việc số 23. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 8 DAY), DATE_ADD(CURDATE(), INTERVAL 13 DAY), 'high', 'completed', NOW() - INTERVAL 770 MINUTE, NOW() - INTERVAL 770 MINUTE),
(@an_id, 'Công việc của An số 24', NULL, DATE_ADD(CURDATE(), INTERVAL 9 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 'low', 'pending', NOW() - INTERVAL 760 MINUTE, NOW() - INTERVAL 760 MINUTE),
(@an_id, 'Công việc của An số 25', 'Mô tả công việc số 25. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 10 DAY), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 'medium', 'in_progress', NOW() - INTERVAL 750 MINUTE, NOW() - INTERVAL 750 MINUTE),
(@an_id, 'Công việc của An số 26', 'Mô tả công việc số 26. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 11 DAY), DATE_ADD(CURDATE(), INTERVAL 16 DAY), 'high', 'completed', NOW() - INTERVAL 740 MINUTE, NOW() - INTERVAL 740 MINUTE),
(@an_id, 'Công việc của An số 27', NULL, DATE_ADD(CURDATE(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 17 DAY), 'low', 'pending', NOW() - INTERVAL 730 MINUTE, NOW() - INTERVAL 730 MINUTE),
(@an_id, 'Công việc của An số 28', 'Mô tả công việc số 28. Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 13 DAY), DATE_ADD(CURDATE(), INTERVAL 18 DAY), 'medium', 'in_progress', NOW() - INTERVAL 720 MINUTE, NOW() - INTERVAL 720 MINUTE),
(@an_id, 'Hạn chót hôm nay (chưa tính quá hạn)', 'Dùng kiểm tra nhãn quá hạn.', DATE_ADD(CURDATE(), INTERVAL -1 DAY), DATE_ADD(CURDATE(), INTERVAL 0 DAY), 'high', 'pending', NOW() - INTERVAL 5 MINUTE, NOW() - INTERVAL 5 MINUTE),
(@an_id, 'Đã hoàn thành nhưng hạn chót đã qua (không hiện Quá hạn)', NULL, DATE_ADD(CURDATE(), INTERVAL -10 DAY), DATE_ADD(CURDATE(), INTERVAL -3 DAY), 'low', 'completed', NOW() - INTERVAL 4 MINUTE, NOW() - INTERVAL 4 MINUTE),
(@an_id, '<script>alert("xss")</script> phải hiển thị như văn bản', '<b>in đậm?</b> <img src=x onerror=alert(1)>', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'medium', 'in_progress', NOW() - INTERVAL 3 MINUTE, NOW() - INTERVAL 3 MINUTE),
(@an_id, 'Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài Tiêu đề rất dài ', NULL, DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'medium', 'pending', NOW() - INTERVAL 2 MINUTE, NOW() - INTERVAL 2 MINUTE),
(@binh_id, 'Task riêng của Bình số 1 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'medium', 'in_progress', NOW() - INTERVAL 490 MINUTE, NOW() - INTERVAL 490 MINUTE),
(@binh_id, 'Task riêng của Bình số 2 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'high', 'completed', NOW() - INTERVAL 480 MINUTE, NOW() - INTERVAL 480 MINUTE),
(@binh_id, 'Task riêng của Bình số 3 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'low', 'pending', NOW() - INTERVAL 470 MINUTE, NOW() - INTERVAL 470 MINUTE),
(@binh_id, 'Task riêng của Bình số 4 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'medium', 'in_progress', NOW() - INTERVAL 460 MINUTE, NOW() - INTERVAL 460 MINUTE),
(@binh_id, 'Task riêng của Bình số 5 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'high', 'completed', NOW() - INTERVAL 450 MINUTE, NOW() - INTERVAL 450 MINUTE),
(@binh_id, 'Task riêng của Bình số 6 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), 'low', 'pending', NOW() - INTERVAL 440 MINUTE, NOW() - INTERVAL 440 MINUTE),
(@binh_id, 'Task riêng của Bình số 7 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'medium', 'in_progress', NOW() - INTERVAL 430 MINUTE, NOW() - INTERVAL 430 MINUTE),
(@binh_id, 'Task riêng của Bình số 8 (báo cáo)', 'Từ khóa: báo cáo, họp nhóm.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 8 DAY), 'high', 'completed', NOW() - INTERVAL 420 MINUTE, NOW() - INTERVAL 420 MINUTE),
(@admin_id, 'Task của Admin số 1', 'Admin cũng có thể sở hữu task.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), 'medium', 'in_progress', NOW() - INTERVAL 95 MINUTE, NOW() - INTERVAL 95 MINUTE),
(@admin_id, 'Task của Admin số 2', 'Admin cũng có thể sở hữu task.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), 'high', 'completed', NOW() - INTERVAL 90 MINUTE, NOW() - INTERVAL 90 MINUTE),
(@admin_id, 'Task của Admin số 3', 'Admin cũng có thể sở hữu task.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), 'low', 'pending', NOW() - INTERVAL 85 MINUTE, NOW() - INTERVAL 85 MINUTE),
(@admin_id, 'Task của Admin số 4', 'Admin cũng có thể sở hữu task.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), 'medium', 'in_progress', NOW() - INTERVAL 80 MINUTE, NOW() - INTERVAL 80 MINUTE),
(@admin_id, 'Task của Admin số 5', 'Admin cũng có thể sở hữu task.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 6 DAY), 'high', 'completed', NOW() - INTERVAL 75 MINUTE, NOW() - INTERVAL 75 MINUTE),
(@admin_id, 'Task của Admin số 6', 'Admin cũng có thể sở hữu task.', DATE_ADD(CURDATE(), INTERVAL 0 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), 'low', 'pending', NOW() - INTERVAL 70 MINUTE, NOW() - INTERVAL 70 MINUTE);
