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


-- Dumping database structure for my_store
CREATE DATABASE IF NOT EXISTS `my_store` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `my_store`;

-- Dumping structure for table my_store.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.category: ~5 rows (approximately)
INSERT INTO `category` (`id`, `name`, `description`) VALUES
	(1, 'Điện thoại', 'Danh mục các loại điện thoại'),
	(2, 'Laptop', 'Danh mục các loại laptop'),
	(3, 'Máy tính bảng', 'Danh mục các loại máy tính bảng'),
	(4, 'Phụ kiện', 'Danh mục phụ kiện điện tử'),
	(5, 'Thiết bị âm thanh vip ', 'Danh mục loa, tai nghe, micro');

-- Dumping structure for table my_store.product
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.product: ~4 rows (approximately)
INSERT INTO `product` (`id`, `name`, `description`, `price`, `image`, `category_id`) VALUES
	(3, 'iPhone 17 Pro 256GB | Chính hãng Việt Nam', 'Hàng chính hảng', 50000000.00, 'uploads/iphone-17-pro-1.jpg', 1),
	(4, 'Samsung Galaxy S26 Ultra 1TB Chính Hãng', 'Hàng chính hảng', 10000000.00, 'uploads/galaxy-s26-ultra-xanh-1tb.jpg', 1),
	(5, 'MacBook Pro 16 inch 2023 M3 Max 48GB/1TB | Chính hãng Apple Việt Nam', 'Hàng chính hảng', 10000000.00, 'uploads/macbook-pro-16-inch-2023-m3-max-hinh-1_1724820549_1.jpg', 3),
	(6, 'Laptop HP 15-FD0305TU A2NL6PA', 'Hàng chính hảng', 12990000.00, 'uploads/text_ng_n_1__9_4.jpg', 2),
	(8, 'Quạt cầm tay AVA+ JF-412', 'Loại quạt:\r\nQuạt cầm tay\r\nMức gió:\r\n5 mức độ\r\nĐường kính lồng quạt:\r\n5 cm\r\nĐường kính cánh quạt:\r\n3 cm\r\nChất liệu:\r\nNhựa ABS + linh kiện điện tử', 100000.00, 'uploads/quat-cam-tay-ava-jf-412-1-639095877634713782-750x500.jpg', 4),
	(9, 'Loa Bluetooth JBL Charge 5', 'Đặc điểm nổi bật\r\n\r\nKiểu dáng hiện đại, chắc chắn, có tính di động cao.\r\nÂm thanh JBL Original Pro Sound, tổng công suất 40 W sinh động. \r\nChuẩn Bluetooth 5.1 duy trì kết nối không dây chất lượng đến 10 m.\r\nĐồng hành cùng bạn đến bất kỳ nơi nào với chuẩn chống nước, chống bụi IP67.\r\nDùng liên tục trong khoảng 20 tiếng, sạc đầy trong khoảng 4 tiếng.\r\nLiên kết được nhiều loa với nhau nhờ tính năng Party Boost.\r\nDễ chỉnh tăng/giảm âm lượng, phát/dừng chơi nhạc, bật/tắt Bluetooth,...', 500000.00, 'uploads/bluetooth-jbl-charge-5-3-750x500.jpg', 5);

-- Dumping structure for table my_store.reviews
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table my_store.reviews: ~2 rows (approximately)
INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `username`, `rating`, `content`, `created_at`) VALUES
	(1, 6, NULL, 'phúc', 5, 'ngon luôn', '2026-05-18 03:04:48'),
	(2, 6, NULL, 'mai', 3, 'bình thường chán', '2026-05-18 03:05:06');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
