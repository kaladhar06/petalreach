-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 08, 2026 at 05:44 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `petalreach`
--

-- --------------------------------------------------------

--
-- Table structure for table `addflower`
--

CREATE TABLE `addflower` (
  `id` int(11) NOT NULL,
  `flower_name` varchar(100) NOT NULL,
  `available_quantity_kg` decimal(10,2) NOT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `state` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `village` varchar(100) NOT NULL,
  `status` enum('DRAFT','ACTIVE') DEFAULT 'DRAFT',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock_status` tinyint(1) DEFAULT 0,
  `contact_phone` varchar(15) DEFAULT '""',
  `image_path` varchar(255) DEFAULT '""',
  `video_path` varchar(255) DEFAULT '""',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addflower`
--

INSERT INTO `addflower` (`id`, `flower_name`, `available_quantity_kg`, `price_per_kg`, `state`, `district`, `village`, `status`, `created_at`, `stock_status`, `contact_phone`, `image_path`, `video_path`, `updated_at`) VALUES
(43, 'Jasmine', 10.00, 250.00, 'Andhra Pradesh', 'Guntur', 'Tenali', 'ACTIVE', '2026-01-05 05:06:31', 1, '9876543210', 'flowerimage/1767589591_photo-1562892302-40f6c820821c.avif', '\"\"', '2026-01-05 05:06:31'),
(44, 'Test', 20.00, 300.00, 'Tamilnadu', 'Chennai', 'Chennai', 'DRAFT', '2026-01-05 05:10:55', 1, '12659874', '', '\"\"', '2026-01-05 05:10:55'),
(48, 'Sunflower', 45.00, 180.00, 'Andhra pradesh', 'Kurnool', 'BPL', 'ACTIVE', '2026-01-05 09:32:02', 1, '8897433470', 'flowerimage/1767605522_flower.jpg', '\"\"', '2026-01-05 09:32:02'),
(49, 'Rose', 45.00, 200.00, 'Andhra pradesh', 'Kurnool', 'BPL', 'ACTIVE', '2026-01-06 10:04:25', 1, '8897433256', 'flowerimage/1767693865_flower.jpg', '\"\"', '2026-01-06 10:04:25'),
(50, 'Marigoald', 40.00, 70.00, 'Andhra pradesh', 'Kurnool', 'BPL', 'ACTIVE', '2026-01-07 03:09:24', 1, '8894766325', 'flowerimage/1767755364_flower.jpg', '\"\"', '2026-01-07 03:09:24'),
(51, 'Lily', 50.00, 189.00, 'Andhra pradesh', 'Kadapa', 'Koduru', 'ACTIVE', '2026-01-07 03:12:07', 1, '9965489712', 'flowerimage/1767755527_flower.jpg', '\"\"', '2026-01-07 03:12:07'),
(52, 'Carnation', 60.00, 349.00, 'Tamil nadu', 'Thiruvallur', 'Ponamalle', 'ACTIVE', '2026-01-07 03:15:24', 1, '9848766258', 'flowerimage/1767755724_flower.jpg', '\"\"', '2026-01-07 03:15:24'),
(53, 'Marigold', 45.00, 200.00, 'Andhra pradesh', 'Kurnool', 'BPL', 'ACTIVE', '2026-01-07 04:59:43', 1, '8897466258', 'flowerimage/1767761983_flower.jpg', '\"\"', '2026-01-07 04:59:43'),
(54, 'Sunflower', 45.00, 200.00, 'Andhra pradesh', 'Kurnool', 'BPL', 'ACTIVE', '2026-01-07 05:02:02', 0, '88974889425', 'flowerimage/1767762122_flower.jpg', '\"\"', '2026-01-07 05:02:02'),
(55, 'Rose', 54.00, 149.00, 'Andhra pradesh', 'Kurnool', 'Koduru', 'ACTIVE', '2026-01-07 05:22:08', 1, '8897466325', 'flowerimage/1767763328_flower.jpg', '\"\"', '2026-01-07 05:22:08');

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `payment_method` enum('UPI','CARD','COD') DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `order_status` enum('Pending','Placed','Paid') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `user_id`, `full_name`, `phone`, `street`, `city`, `pincode`, `payment_method`, `subtotal`, `delivery_fee`, `total`, `order_status`, `created_at`) VALUES
(1, 1, 'Rahul Kumar', '9876543210', 'MG Road', 'Bangalore', '560001', 'UPI', 350.00, 50.00, 400.00, 'Placed', '2025-12-28 14:24:22'),
(2, 1, 'kaladhar', '8866449005', 'smart colony', 'banaganapalle', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2025-12-31 03:20:40'),
(3, 0, 'Sadhula kaladhar reddy', '8897433205', 'Smart colony', 'Nandyal', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2025-12-31 04:07:45'),
(4, 0, 'Kaladhar', '8856744126', 'Villa', 'Banaganapalle', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-03 10:30:11'),
(5, 0, 'Kaladhar', '8184916407', 'Smart beside colony', 'Nandyal', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 03:47:49'),
(6, 0, 'Kaladhar', '8897433405', 'Saveetha university', 'Nandyal', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 08:03:07'),
(7, 0, 'Kaladhar', '8897433405', 'Saveetha', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 08:05:58'),
(8, 0, 'Kaladhar', '8879566328', 'Saveetha', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 08:11:46'),
(9, 0, 'Kaladhar', '8974622158', 'Saveetha', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 08:52:06'),
(10, 0, 'Kaladhar', '9758466218', 'Saveetha', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 08:55:50'),
(11, 0, 'Kaladhar', '9968477569', 'Saveetha', 'Nandyal', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 08:59:31'),
(12, 0, 'Kaladhar', '9945622485', 'Saveetha', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 09:27:36'),
(13, 0, 'Kaladhar', '9965484158', 'Villa no 63', 'Kurnool', '602105', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 09:33:31'),
(14, 0, 'Kaladhar', '9847566894', 'Saveetha', 'Kurnool', '602105', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-05 09:39:40'),
(15, 0, 'Kaladhar', '9865487425', 'Villa no 64', 'Kurnool', '546897', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-06 09:42:47'),
(16, 0, 'Kaladhar', '8184916405', 'Saveetha university', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-06 09:53:13'),
(17, 0, 'Kaladhar', '8184916405', 'Saveetha university', 'Thandalam', '602105', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-06 09:56:32'),
(18, 0, 'Kaladhar', '8184916405', 'Saveetha university', 'Thandalam', '602105', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-06 09:57:03'),
(19, 0, 'Kaladhar', '8184916405', 'Saveetha university', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-06 09:57:56'),
(20, 0, 'Kaladhar', '8897433125', 'Saveetha', 'Kurnool', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-06 10:01:59'),
(21, 0, 'Kaladhar', '8897466251', 'Saveetha university', 'Thandalam', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-07 05:16:13'),
(22, 0, 'Kaladhar', '8184916405', 'Saveetha university', 'Thandalam', '518124', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-07 05:18:02'),
(23, 0, 'Kaladhar', '8897466125', 'villa no 65', 'thandalam', '602105', NULL, 0.00, 0.00, 0.00, 'Pending', '2026-01-07 05:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `street` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `address_type` varchar(50) DEFAULT 'HOME',
  `is_default` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `full_name`, `phone`, `street`, `city`, `pincode`, `address_type`, `is_default`, `created_at`) VALUES
(1, 1, 'ravi kiran', '8847923654', '123 main road', 'Hyderabad', '500001', 'HOME', 0, '2026-01-05 15:21:47');

-- --------------------------------------------------------

--
-- Table structure for table `creategrower`
--

CREATE TABLE `creategrower` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `creategrower`
--

INSERT INTO `creategrower` (`id`, `full_name`, `phone`, `email`, `password`, `created_at`) VALUES
(1, 'kaladhar reddy', '8809677543', 'kala@gmail.com', '$2y$10$dKA0r4SQFtVchYXecSB6s.Ecae8z3sNYJT3EOg6qthteIYIgyPYeG', '2026-01-03 04:31:33'),
(2, 'kaladhar reddy', '8809677543', 'kalas@gmail.com', '$2y$10$qF31BuucPMinbVZg6jQXnuktdwhobxRcOpPJxOWfOvILTELgTU4nq', '2026-01-03 04:31:51'),
(3, 'Kaladhar', '8184896067', 'kalar@gmail.com', '$2y$10$EKa.wB0cP2NIwUZTLiMKOuw8aY7SYh5T60A5Puzn1de0DfxLmqLMm', '2026-01-03 04:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `editprofile`
--

CREATE TABLE `editprofile` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `editprofile`
--

INSERT INTO `editprofile` (`id`, `name`, `email`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'kaladhar', 'reddykaladhar@gmail.com', '9944228812', '2025-12-31 05:06:54', '2025-12-31 05:06:54'),
(2, 'Kaladhar', 'reddykaladhar260@gmail.com', '8184916405', '2025-12-31 05:26:56', '2025-12-31 05:26:56'),
(3, 'Kaladhar', 'hill@gmail.com', '8897466258', '2026-01-05 08:44:17', '2026-01-05 08:44:17'),
(4, 'harsha', 'Harsha@gmail।com', '8897466258', '2026-01-07 05:25:35', '2026-01-07 05:25:35');

-- --------------------------------------------------------

--
-- Table structure for table `flowers`
--

CREATE TABLE `flowers` (
  `id` int(11) NOT NULL,
  `grower_id` int(11) NOT NULL,
  `flower_name` varchar(255) NOT NULL,
  `available_quantity_kg` varchar(50) DEFAULT NULL,
  `price_per_kg` varchar(50) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `village` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Active',
  `stock_status` int(11) DEFAULT 1,
  `contact_phone` varchar(20) DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `video_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `growerhome`
--

CREATE TABLE `growerhome` (
  `id` int(11) NOT NULL,
  `grower_id` int(11) NOT NULL,
  `flower_name` varchar(100) NOT NULL,
  `flower_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(20) DEFAULT 'kg',
  `stock` int(11) DEFAULT 0,
  `status` enum('In Stock','Out of Stock') DEFAULT 'In Stock',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `growerhome`
--

INSERT INTO `growerhome` (`id`, `grower_id`, `flower_name`, `flower_image`, `price`, `unit`, `stock`, `status`, `created_at`) VALUES
(2, 3, 'rose', 'lily.jpg', 40.00, 'kg', 6, 'In Stock', '2025-12-31 06:28:18');

-- --------------------------------------------------------

--
-- Table structure for table `growerlogin`
--

CREATE TABLE `growerlogin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','grower') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loginpage`
--

CREATE TABLE `loginpage` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','grower') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `address` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `status` varchar(50) DEFAULT 'Order Placed',
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_name`, `product_image`, `price`, `address`, `quantity`, `status`, `order_date`) VALUES
(1, 1, 'Rose', 'http://localhost/petalreach/uploads/rose.jpg', 450.00, 'hyderabad, India', 2, 'Order Placed', '2026-01-05 14:49:58'),
(2, 1, 'Sunflower', 'http://localhost/petalreach/flowerimage/1767590511_flower.jpg', 450.00, 'Kaladhar, 8897466258', 2, 'Order Placed', '2026-01-05 14:57:38'),
(3, 1, 'Rose', 'http://localhost/petalreach/flowerimage/1767598713_flower.jpg', 1250.00, 'Kaladhar, 8897466258', 4, 'Order Placed', '2026-01-05 15:03:39'),
(4, 1, 'Jasmine', 'http://localhost/petalreach/flowerimage/1767589591_photo-1562892302-40f6c820821c.avif', 550.00, 'Kaladhar, 8897466258', 2, 'Order Placed', '2026-01-06 15:26:46'),
(5, 1, 'Sunflower', 'http://localhost/petalreach/flowerimage/1767590511_flower.jpg', 250.00, 'Kaladhar, 8897466258', 1, 'Order Placed', '2026-01-06 15:32:05'),
(6, 1, 'Jasmine', 'http://localhost/petalreach/flowerimage/1767589591_photo-1562892302-40f6c820821c.avif', 300.00, 'Kaladhar, 8897466258', 1, 'Order Placed', '2026-01-07 10:46:20'),
(7, 1, 'Jasmine', 'http://localhost/petalreach/flowerimage/1767589591_photo-1562892302-40f6c820821c.avif', 550.00, 'Kaladhar, 8897466258', 2, 'Order Placed', '2026-01-07 10:48:05'),
(8, 1, 'Jasmine', 'http://localhost/petalreach/flowerimage/1767589591_photo-1562892302-40f6c820821c.avif', 500.00, 'Kaladhar, 8897466258', 1, 'Order Placed', '2026-01-07 10:54:32');

-- --------------------------------------------------------

--
-- Table structure for table `productdetail`
--

CREATE TABLE `productdetail` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_kg` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `seller_name` varchar(100) DEFAULT NULL,
  `seller_rating` decimal(2,1) DEFAULT NULL,
  `seller_phone` varchar(20) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `productdetail`
--

INSERT INTO `productdetail` (`id`, `product_name`, `description`, `price_per_kg`, `stock`, `image_url`, `location`, `seller_name`, `seller_rating`, `seller_phone`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 'Sunflower', 'Bright yellow sunflowers for decoration', 350.00, 50, 'sunflower.jpg', 'Yelahanka, Bangalore Urban, Karnataka', 'Ramesh Kumar', 4.8, '9876543210', 'Vikram Singh', 4, 'Good quality flowers', '2025-12-28 08:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `saveaddress`
--

CREATE TABLE `saveaddress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `street` varchar(500) NOT NULL,
  `city` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `address_type` varchar(20) DEFAULT 'HOME',
  `is_default` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `full_name`, `phone`, `email`, `password`, `created_at`) VALUES
(1, 'kala', '9090909090', 'hil@gmail.com', '$2y$10$1klXZAzjehyOG6cnNOcfXO8hhONsP7bHZ4YfDqC.nxLhFeqPDs//G', '2025-12-29 08:55:14'),
(2, 'kala', '9090909098', 'hill@gmail.com', '$2y$10$0dZMu6RPadWHQuWMAMZWnuKtCHpAdbieOwG7iBedYq9d9.KIXE8i6', '2025-12-30 03:31:55'),
(3, 'Test', '9875632014', 'Test@gmail.com', '$2y$10$S2qV6uhn0S6jW.MjhBS6Z.JR33lylYngsGVF6rwA/tJwWh2W4/zru', '2025-12-30 04:12:58'),
(5, 'Sai', '5566223318', 'Sai@gmail.com', '$2y$10$p9Kp/M/AXxpTGpVzmCCAeeDG4Ao5uk20ktKQlLAzkYQaEb7qNgb/C', '2025-12-30 04:48:01');

-- --------------------------------------------------------

--
-- Table structure for table `userhome`
--

CREATE TABLE `userhome` (
  `id` int(11) NOT NULL,
  `flower_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price_per_kg` int(11) NOT NULL,
  `rating` decimal(2,1) DEFAULT NULL,
  `total_reviews` int(11) DEFAULT NULL,
  `seller_name` varchar(100) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `in_stock` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `yourcart`
--

CREATE TABLE `yourcart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `yourcart`
--

INSERT INTO `yourcart` (`id`, `user_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `created_at`) VALUES
(1, 1, 101, 'Sunflower', 'images/sunflower.jpg', 350.00, 1, '2025-12-28 13:58:05');

-- --------------------------------------------------------

--
-- Table structure for table `yourjourney`
--

CREATE TABLE `yourjourney` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `order_date` date NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Order Placed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `address` text DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `yourjourney`
--

INSERT INTO `yourjourney` (`id`, `user_id`, `product_name`, `product_image`, `order_date`, `price`, `status`, `created_at`, `address`, `quantity`) VALUES
(1, 1, 'rose', 'http://localhost/petalreach/uploads/rose.jpg', '2026-01-05', 499.00, 'Order Placed', '2026-01-05 03:35:01', 'hyderabad, India', 2),
(2, 1, 'Sunflower', 'http://localhost/petalreach/flowerimage/1767590511_flower.jpg', '2026-01-05', 1000.00, 'Order Placed', '2026-01-05 08:52:11', 'Kaladhar, 8897466258', 1),
(3, 1, 'Jasmine', 'http://localhost/petalreach/flowerimage/1767589591_photo-1562892302-40f6c820821c.avif', '2026-01-05', 500.00, 'Order Placed', '2026-01-05 08:55:58', 'Kaladhar, 8897466258', 1),
(4, 1, 'Sunflower', 'http://localhost/petalreach/flowerimage/1767590511_flower.jpg', '2026-01-05', 250.00, 'Order Placed', '2026-01-05 08:59:35', 'Kaladhar, 8897466258', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addflower`
--
ALTER TABLE `addflower`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `creategrower`
--
ALTER TABLE `creategrower`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `editprofile`
--
ALTER TABLE `editprofile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `flowers`
--
ALTER TABLE `flowers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `growerhome`
--
ALTER TABLE `growerhome`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `growerlogin`
--
ALTER TABLE `growerlogin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `loginpage`
--
ALTER TABLE `loginpage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `productdetail`
--
ALTER TABLE `productdetail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saveaddress`
--
ALTER TABLE `saveaddress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userhome`
--
ALTER TABLE `userhome`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `yourcart`
--
ALTER TABLE `yourcart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `yourjourney`
--
ALTER TABLE `yourjourney`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addflower`
--
ALTER TABLE `addflower`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `creategrower`
--
ALTER TABLE `creategrower`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `editprofile`
--
ALTER TABLE `editprofile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `flowers`
--
ALTER TABLE `flowers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `growerhome`
--
ALTER TABLE `growerhome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `growerlogin`
--
ALTER TABLE `growerlogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loginpage`
--
ALTER TABLE `loginpage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `productdetail`
--
ALTER TABLE `productdetail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `saveaddress`
--
ALTER TABLE `saveaddress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `userhome`
--
ALTER TABLE `userhome`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `yourcart`
--
ALTER TABLE `yourcart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `yourjourney`
--
ALTER TABLE `yourjourney`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
