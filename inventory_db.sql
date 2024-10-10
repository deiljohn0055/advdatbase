-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2024 at 03:16 AM
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
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `date_received` date NOT NULL,
  `expiry_date` date NOT NULL,
  `type` enum('Frozen','Chilled','Dry','Drinks','Seasonings') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `quantity`, `date_received`, `expiry_date`, `type`) VALUES
(2, 'burger patty', 100, '2024-10-08', '2024-11-30', 'Frozen'),
(3, 'pasta', 1010, '2024-10-08', '2025-01-01', 'Dry'),
(4, 'ketchup ', 46, '2024-10-08', '2024-11-21', 'Dry'),
(5, 'coca-cola', 1000, '2024-10-08', '2025-10-22', 'Drinks'),
(6, 'Sprite', 200, '2024-10-08', '2026-06-17', 'Drinks'),
(7, 'Royal', 98, '2024-10-08', '2027-06-16', 'Drinks'),
(8, 'pineapple juice', 44, '2024-10-08', '2024-11-30', 'Drinks'),
(9, 'Ice Tea', 200, '2024-10-08', '2025-11-15', 'Drinks'),
(10, 'Iced Coffee', 500, '2024-10-09', '2026-06-10', 'Drinks'),
(11, 'Bottled Water', 2000, '2024-10-08', '2027-10-15', 'Drinks'),
(12, 'Rice', 500, '2024-10-08', '2024-10-08', 'Dry'),
(13, 'Chicken', 500, '2024-10-08', '2025-07-09', 'Frozen');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
