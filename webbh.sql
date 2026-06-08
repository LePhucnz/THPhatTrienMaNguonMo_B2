CREATE DATABASE IF NOT EXISTS my_store;
USE my_store;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Xóa bảng cũ nếu có (đúng thứ tự)
DROP TABLE IF EXISTS `order_details`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `product`;
DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `vouchers`;
DROP TABLE IF EXISTS `account`;

-- 2. Tạo bảng KHÔNG có foreign key trước
CREATE TABLE `account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text,
  `is_locked` tinyint DEFAULT '0',
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expire` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `vouchers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` enum('percent','fixed','freeship') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_order_value` decimal(10,2) DEFAULT '0.00',
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int DEFAULT '0',
  `used_count` int DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- 3. Tạo bảng product (tham chiếu category)
CREATE TABLE `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- 4. Tạo bảng orders (tham chiếu vouchers)
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `notes` text,
  `payment_method` varchar(50) DEFAULT 'cod',
  `order_code` varchar(50) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT '0.00',
  `shipping_fee` decimal(10,2) DEFAULT '0.00',
  `tax` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(15,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `payment_proof` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `voucher_id` int DEFAULT NULL,
  `voucher_code` varchar(50) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT '0.00',
  `final_total` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `voucher_id` (`voucher_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tạo bảng reviews (tham chiếu product)
CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `rating` int NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- 6. Tạo bảng order_details (tham chiếu orders)
CREATE TABLE `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Nhập dữ liệu
INSERT INTO `account` (`id`, `username`, `fullname`, `email`, `password`, `role`, `avatar`, `phone`, `address`, `is_locked`, `remember_token`, `token_expire`, `reset_token`, `reset_expire`, `security_question`, `security_answer`) VALUES
  (1, 'lhphuc', 'le mai hoang phuc', NULL, '$2y$10$eF34.daYjCnOzrZsqiSqD.2RFCc4fqjN51Bibyy1Si1kgJEGQX3rG', 'user', 'uploads/avatars/avatar_1_1780277547.png', '0123456789', 'bãi rác', 0, NULL, NULL, NULL, NULL, NULL, NULL),
  (2, 'admin', 'Administrator', NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
  (5, 'lhmai', 'lhmai', 'lhmai@gmail.com', '$2y$10$wz.yQqGRYO7AGcP6tbS58.3RT6SJF/om5LU3ua/nHVODAsNCy5nde', 'user', 'uploads/avatars/avatar_5_1780282344.png', '', '', 0, NULL, NULL, NULL, NULL, 'Tên thú cưng đầu tiên của bạn?', '$2y$10$HHXeGOz491P11L.fisl1yuyAxncme7nerh4yXIIck/8tLcvztVsfe');

INSERT INTO `category` (`id`, `name`, `description`) VALUES
  (1, 'Điện thoại', 'Danh mục các loại điện thoại'),
  (2, 'Laptop', 'Danh mục các loại laptop'),
  (3, 'Máy tính bảng', 'Danh mục các loại máy tính bảng'),
  (4, 'Phụ kiện', 'Danh mục phụ kiện điện tử'),
  (5, 'Thiết bị âm thanh vip', 'Danh mục loa, tai nghe, micro');

INSERT INTO `vouchers` (`id`, `code`, `type`, `value`, `min_order_value`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`) VALUES
  (1, 'FREESHIP', 'freeship', 30000.00, 100000.00, NULL, 100, 0, '2024-01-01', '2026-12-31', 1, '2026-05-25 02:29:18'),
  (2, 'SALE10', 'percent', 10.00, 200000.00, 50000.00, 50, 0, '2024-01-01', '2026-12-31', 1, '2026-05-25 02:29:18'),
  (3, 'GIAM50K', 'fixed', 50000.00, 300000.00, NULL, 30, 0, '2024-01-01', '2026-12-31', 1, '2026-05-25 02:29:18');

INSERT INTO `product` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
  (3, 'iPhone 17 Pro 256GB | Chính hãng Việt Nam', 'Hàng chính hảng', 50000000.00, 'uploads/iphone-17-pro-1.jpg', 1),
  (4, 'Samsung Galaxy S26 Ultra 1TB Chính Hãng', 'Hàng chính hảng', 10000000.00, 'uploads/galaxy-s26-ultra-xanh-1tb.jpg', 1),
  (5, 'MacBook Pro 16 inch 2023 M3 Max 48GB/1TB | Chính hãng Apple Việt Nam', 'Hàng chính hảng', 10000000.00, 'uploads/macbook-pro-16-inch-2023-m3-max-hinh-1_1724820549_1.jpg', 3),
  (6, 'Laptop HP 15-FD0305TU A2NL6PA', 'Hàng chính hảng', 12990000.00, 'uploads/text_ng_n_1__9_4.jpg', 2),
  (8, 'Quạt cầm tay AVA+ JF-412', 'Quạt cầm tay nhỏ gọn tiện lợi', 100000.00, 'uploads/quat-cam-tay-ava-jf-412-1-639095877634713782-750x500.jpg', 4),
  (9, 'Loa Bluetooth JBL Charge 5', 'Loa bluetooth chống nước IP67', 500000.00, 'uploads/bluetooth-jbl-charge-5-3-750x500.jpg', 5);

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `username`, `rating`, `content`, `created_at`) VALUES
  (1, 6, NULL, 'phúc', 5, 'ngon luôn', '2026-05-18 03:04:48'),
  (2, 6, NULL, 'mai', 3, 'bình thường chán', '2026-05-18 03:05:06'),
  (3, 8, NULL, 'origin', 4, 'khá tốt giá cũng ổn', '2026-05-18 04:11:41'),
  (4, 9, NULL, 'pp', 4, 'hàng dùng tốt, giá cũng ổn', '2026-05-25 02:23:33');

SET FOREIGN_KEY_CHECKS = 1;