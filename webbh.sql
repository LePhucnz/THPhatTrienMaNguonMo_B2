-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- ✅ TẠO DATABASE (nếu chưa có)
CREATE DATABASE IF NOT EXISTS my_store;
USE my_store;

-- =====================================================
-- ✅ BƯỚC 1: TẠO BẢNG KHÔNG CÓ FOREIGN KEY TRƯỚC
-- =====================================================

-- 1. Bảng category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 2. Bảng product (FK tới category - category đã tạo ở trên)
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3. Bảng reviews (FK tới product - product đã tạo ở trên)
CREATE TABLE IF NOT EXISTS `reviews` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 4. Bảng vouchers (KHÔNG có FK, tạo trước orders)
CREATE TABLE IF NOT EXISTS `vouchers` (
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
  `status` tinyint DEFAULT '1' COMMENT '1: Active, 0: Inactive',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- ✅ BƯỚC 2: TẠO BẢNG CÓ FOREIGN KEY SAU
-- =====================================================

-- 5. Bảng orders (FK tới vouchers - vouchers đã tạo ở trên)
CREATE TABLE IF NOT EXISTS `orders` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 6. Bảng order_details (FK tới orders - orders đã tạo ở trên)
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- =====================================================
-- ✅ BƯỚC 3: INSERT DỮ LIỆU MẪU (Dùng INSERT IGNORE để tránh lỗi trùng)
-- =====================================================

-- Insert category
INSERT IGNORE INTO `category` (`id`, `name`, `description`) VALUES
    (1, 'Điện thoại', 'Danh mục các loại điện thoại'),
    (2, 'Laptop', 'Danh mục các loại laptop'),
    (3, 'Máy tính bảng', 'Danh mục các loại máy tính bảng'),
    (4, 'Phụ kiện', 'Danh mục phụ kiện điện tử'),
    (5, 'Thiết bị âm thanh vip', 'Danh mục loa, tai nghe, micro');

-- Insert product
INSERT IGNORE INTO `product` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
    (3, 'iPhone 17 Pro 256GB | Chính hãng Việt Nam', 'Hàng chính hảng', 50000000.00, 'uploads/iphone-17-pro-1.jpg', 1),
    (4, 'Samsung Galaxy S26 Ultra 1TB Chính Hãng', 'Hàng chính hảng', 10000000.00, 'uploads/galaxy-s26-ultra-xanh-1tb.jpg', 1),
    (5, 'MacBook Pro 16 inch 2023 M3 Max 48GB/1TB | Chính hãng Apple Việt Nam', 'Hàng chính hảng', 10000000.00, 'uploads/macbook-pro-16-inch-2023-m3-max-hinh-1_1724820549_1.jpg', 3),
    (6, 'Laptop HP 15-FD0305TU A2NL6PA', 'Hàng chính hảng', 12990000.00, 'uploads/text_ng_n_1__9_4.jpg', 2),
    (8, 'Quạt cầm tay AVA+ JF-412', 'Loại quạt:\r\nQuạt cầm tay\r\nMức gió:\r\n5 mức độ\r\nĐường kính lồng quạt:\r\n5 cm\r\nĐường kính cánh quạt:\r\n3 cm\r\nChất liệu:\r\nNhựa ABS + linh kiện điện tử', 100000.00, 'uploads/quat-cam-tay-ava-jf-412-1-639095877634713782-750x500.jpg', 4),
    (9, 'Loa Bluetooth JBL Charge 5', 'Đặc điểm nổi bật\r\n\r\nKiểu dáng hiện đại, chắc chắn, có tính di động cao.\r\nÂm thanh JBL Original Pro Sound, tổng công suất 40 W sinh động. \r\nChuẩn Bluetooth 5.1 duy trì kết nối không dây chất lượng đến 10 m.\r\nĐồng hành cùng bạn đến bất kỳ nơi nào với chuẩn chống nước, chống bụi IP67.\r\nDùng liên tục trong khoảng 20 tiếng, sạc đầy trong khoảng 4 tiếng.\r\nLiên kết được nhiều loa với nhau nhờ tính năng Party Boost.\r\nDễ chỉnh tăng/giảm âm lượng, phát/dừng chơi nhạc, bật/tắt Bluetooth,...', 500000.00, 'uploads/bluetooth-jbl-charge-5-3-750x500.jpg', 5);

-- Insert vouchers
INSERT IGNORE INTO `vouchers` (`id`, `code`, `type`, `value`, `min_order_value`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `end_date`, `status`, `created_at`) VALUES
    (1, 'FREESHIP', 'freeship', 30000.00, 100000.00, NULL, 100, 0, '2024-01-01', '2026-12-31', 1, '2026-05-25 02:29:18'),
    (2, 'SALE10', 'percent', 10.00, 200000.00, 50000.00, 50, 0, '2024-01-01', '2026-12-31', 1, '2026-05-25 02:29:18'),
    (3, 'GIAM50K', 'fixed', 50000.00, 300000.00, NULL, 30, 0, '2024-01-01', '2026-12-31', 1, '2026-05-25 02:29:18');

-- Insert reviews (optional)
INSERT IGNORE INTO `reviews` (`id`, `product_id`, `user_id`, `username`, `rating`, `content`, `created_at`) VALUES
    (1, 6, NULL, 'phúc', 5, 'ngon luôn', '2026-05-18 03:04:48'),
    (2, 6, NULL, 'mai', 3, 'bình thường chán', '2026-05-18 03:05:06'),
    (3, 8, NULL, 'origin ', 4, 'khá tốt giá cũng ổn', '2026-05-18 04:11:41'),
    (4, 9, NULL, 'pp', 4, 'hàng dùng tốt , giá cũng ổn , không biết sử được bao lâu', '2026-05-25 02:23:33');

-- =====================================================
-- ✅ BƯỚC 4: BẬT LẠI FOREIGN KEY CHECKS
-- =====================================================
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;