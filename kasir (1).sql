-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 10:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `email`, `username`, `password`, `image`, `otp_code`, `otp_expiry`, `reset_token`, `token_expiry`) VALUES
(1, 'admin@gmail.com', 'admin', '123', 'txt3.jpg', NULL, NULL, '3783b2d1ec5bb72aa02dd8b58ad978ced0d0fcf8ad2fb1674bc4886e28085d4e', '2025-05-07 14:51:17'),
(4, 'lara@gmail.com', 'lara', '$2y$10$88H4oNxzFOE90tpHKUzenuWWoh2gQSM9ahZ4Lv.phC5j4aeQwj7UG', 'txt.jpg', NULL, NULL, NULL, NULL),
(6, 'rusaberkakisatu@gmail.com', 'hai', 'qwerty', 'contoh1.webp', NULL, NULL, '5205b5e1c980faa7f3aa86fff7b13071f38964f3173bf9831cbdf79ef1e7830d', '2025-05-07 14:51:21'),
(7, 'menyalasenterku@gmail.com', 'menyala', '123', 'cont2.jpg', NULL, NULL, NULL, NULL),
(13, 'pp@gmail.com', 'halo', '123', 'logoillit.jpg', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`, `image`) VALUES
(4, 'TXT', 'logotxt.jpg'),
(5, 'BTS', 'logobts.jpg'),
(9, 'Enhypen', 'logoenha.png');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `point` int(11) NOT NULL,
  `status` enum('active','non-active') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `name`, `phone`, `point`, `status`) VALUES
(4, 'Meri', '08089', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `barcode` varchar(100) NOT NULL,
  `qty` int(11) NOT NULL,
  `starting_price` varchar(100) NOT NULL,
  `selling_price` varchar(100) NOT NULL,
  `margin` varchar(100) NOT NULL,
  `fid_category` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `barcode`, `qty`, `starting_price`, `selling_price`, `margin`, `fid_category`, `image`, `description`) VALUES
(13, 'The Dream Chapter 2', '123', -10, '8.000', '10.000', '2.000', 4, 'uploads/681ab07819b59.jpg', 'Debut Album Of  Tomorrow X Together'),
(14, 'Album ENHYPEN - ROMANCE : UNTOLD', '681b083963fa0', -9, '8', '10', '2.000', 9, 'uploads/681b083965860.webp', 'ENHYPEN - 2nd Album [Romance : Untold]'),
(15, 'LOVE YOURSELF 轉 \'TEAR\'', '681cb4ee744d6', 10, '10', '15', '5.000', 5, 'uploads/681cb4ef3edac.webp', 'The 3rd Full Album Release of BTS');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `fid_admin` int(11) NOT NULL,
  `fid_member` int(11) DEFAULT NULL,
  `fid_product` int(11) NOT NULL,
  `detail` varchar(255) NOT NULL,
  `total_price` decimal(10,0) DEFAULT NULL,
  `margin_total` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `date`, `fid_admin`, `fid_member`, `fid_product`, `detail`, `total_price`, `margin_total`) VALUES
(13, '2025-05-09 07:47:51', 1, 4, 14, 'Transaksi oleh admin #1', 30, 0),
(14, '2025-05-09 07:47:51', 1, 4, 13, 'Transaksi oleh admin #1', 10, 0),
(15, '2025-05-11 07:28:05', 1, 4, 14, 'Transaksi oleh admin #1', 30, 0),
(16, '2025-05-11 07:28:05', 1, 4, 13, 'Transaksi oleh admin #1', 30, 0),
(17, '2025-05-11 08:39:26', 1, 4, 14, 'Transaksi oleh admin #1', 30, 0),
(18, '2025-05-11 08:39:26', 1, 4, 13, 'Transaksi oleh admin #1', 30, 0),
(19, '2025-05-11 08:41:53', 1, 4, 14, 'Transaksi oleh admin #1', 30, 0),
(20, '2025-05-11 08:41:53', 1, 4, 13, 'Transaksi oleh admin #1', 30, 0),
(21, '2025-05-12 07:11:12', 1, 4, 14, 'Album ENHYPEN - ROMANCE : UNTOLD (3), The Dream Chapter (3)', 30, 0),
(22, '2025-05-12 07:13:11', 1, 4, 14, 'Album ENHYPEN - ROMANCE : UNTOLD (3), The Dream Chapter (3)', 30, 0),
(23, '2025-05-12 07:13:11', 1, 4, 13, 'Album ENHYPEN - ROMANCE : UNTOLD (3), The Dream Chapter (3)', 30, 0),
(24, '2025-05-12 07:33:21', 1, 4, 14, 'Album ENHYPEN - ROMANCE : UNTOLD (3)', 30, 6),
(25, '2025-05-12 07:33:21', 1, 4, 13, 'The Dream Chapter (3)', 30, 6);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fid_admin` (`fid_admin`),
  ADD KEY `fid_member` (`fid_member`),
  ADD KEY `fid_product` (`fid_product`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`fid_admin`) REFERENCES `admin` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`fid_member`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`fid_product`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
