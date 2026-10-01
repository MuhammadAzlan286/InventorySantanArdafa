-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jan 16, 2026 at 07:23 AM
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
-- Database: `santanardafa`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_user` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `nama_user`, `role`, `action`, `details`, `tanggal`) VALUES
(1, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-03 23:56:55'),
(2, 9, 'muhammad azlan allin', 'owner', 'Add Expense: gaji', 'Add Expense: gaji (500.000)', '2026-01-04 00:17:32'),
(3, 9, 'muhammad azlan allin', 'owner', 'Add Supplier: kara', 'Add Supplier: kara', '2026-01-04 00:25:59'),
(4, 9, 'muhammad azlan allin', 'owner', 'Add Expense: benarin warung', 'Add Expense: benarin warung (1.000.000)', '2026-01-04 00:31:08'),
(5, 9, 'muhammad azlan allin', 'owner', 'Add Supplier: minyak kita', 'Add Supplier: minyak kita', '2026-01-04 00:31:27'),
(6, 9, 'muhammad azlan allin', 'owner', 'Add Item', 'Add Item: minyak (Kode: Srg002)', '2026-01-04 00:34:42'),
(7, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-04 00:37:03'),
(8, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-04 00:38:50'),
(9, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-04 00:44:16'),
(10, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-05 14:07:53'),
(11, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-09 12:12:03'),
(12, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-09 12:12:42'),
(13, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-09 13:08:43'),
(14, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-09 13:09:52'),
(15, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 19:19:05'),
(16, 9, 'muhammad azlan allin', 'owner', 'Add Item', 'Add Item: Santan (Kode: Srg001)', '2026-01-10 19:21:33'),
(17, 9, 'muhammad azlan allin', 'owner', 'Login Failed', 'Attempted login for username: Alan with role: kasir', '2026-01-10 19:23:32'),
(18, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 19:23:45'),
(19, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 19:31:08'),
(20, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 19:32:30'),
(21, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 19:59:33'),
(22, 9, 'muhammad azlan allin', 'owner', 'Delete Item', 'Deleted item: Santan', '2026-01-10 20:15:01'),
(23, 9, 'muhammad azlan allin', 'owner', 'Delete Item', 'Deleted item: minyak', '2026-01-10 20:15:05'),
(24, 9, 'muhammad azlan allin', 'owner', 'Delete Item', 'Deleted item: Santan', '2026-01-10 20:15:10'),
(25, 9, 'muhammad azlan allin', 'owner', 'Delete Item', 'Deleted item: Santan', '2026-01-10 20:15:14'),
(26, 9, 'muhammad azlan allin', 'owner', 'Delete Item', 'Deleted item: Santan', '2026-01-10 20:15:20'),
(27, 9, 'muhammad azlan allin', 'owner', 'Add Item', 'Add Item: Santan (Kode: BRG-0013)', '2026-01-10 20:18:39'),
(28, 9, 'muhammad azlan allin', 'owner', 'Delete Item', 'Deleted item: Santan', '2026-01-10 20:23:50'),
(29, 9, 'muhammad azlan allin', 'owner', 'Add Item', 'Add Item: Santan (Kode: BRG-0001)', '2026-01-10 20:24:15'),
(30, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 20:26:00'),
(31, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 20:29:15'),
(32, 9, 'muhammad azlan allin', 'owner', 'Update Item', 'Update Item: Santan', '2026-01-10 20:29:41'),
(33, 9, 'muhammad azlan allin', 'owner', 'Update Item', 'Update Item: Santan', '2026-01-10 20:29:58'),
(34, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 20:30:21'),
(35, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 450.000', '2026-01-10 20:30:37'),
(36, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-10 20:32:26'),
(37, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 20:37:35'),
(38, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 20:38:45'),
(39, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 20:49:22'),
(40, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 20:50:58'),
(41, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-10 20:53:43'),
(42, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-10 20:55:58'),
(43, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-10 20:57:21'),
(44, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 20:59:26'),
(45, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 21:05:36'),
(46, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 21:11:29'),
(47, 0, 'System', 'Guest', 'Login Failed', 'Attempted login for username: makItam with role: owner', '2026-01-10 21:24:58'),
(48, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-10 21:25:07'),
(49, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-10 21:25:43'),
(50, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 10:40:14'),
(51, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 10:40:48'),
(52, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 10:46:51'),
(53, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 10:50:40'),
(54, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 10:56:36'),
(55, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 11:00:41'),
(56, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 11:08:38'),
(57, 9, 'muhammad azlan allin', 'owner', 'Login Failed', 'Attempted login for username: makItam with role: owner', '2026-01-11 11:09:19'),
(58, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 11:09:31'),
(59, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-11 11:09:54'),
(60, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-11 11:25:23'),
(61, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-11 11:26:51'),
(62, 10, 'Verification User', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 11:28:20'),
(63, 11, 'Kasir Verification', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 11:29:47'),
(64, 7, 'shandi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 5.000', '2026-01-11 11:35:57'),
(65, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 11:38:43'),
(66, 11, 'Kasir Verification', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 11:56:42'),
(67, 7, 'shandi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 11:58:06'),
(68, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 11:59:41'),
(69, 9, 'muhammad azlan allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 20:21:41'),
(70, 9, 'muhammad azlan allin', 'owner', 'Add Item', 'Add Item: minyak (Kode: BRG-0002)', '2026-01-11 21:46:24'),
(71, 9, 'muhammad azlan allin', 'owner', 'Update Item', 'Update Item: minyak', '2026-01-11 21:46:49'),
(72, 9, 'muhammad azlan allin', 'owner', 'Update Item', 'Update Item: minyak', '2026-01-11 21:48:00'),
(73, 9, 'muhammad azlan allin', 'owner', 'Add Supplier: kara', 'Add Supplier: kara', '2026-01-11 21:50:41'),
(74, 0, 'System', 'Guest', 'Login Failed', 'Attempted login for username: makItam with role: kasir', '2026-01-11 21:53:40'),
(75, 0, 'System', 'Guest', 'Login Failed', 'Attempted login for username: makItam with role: kasir', '2026-01-11 21:53:50'),
(76, 12, 'Shandi Kurnia Ilahi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 21:54:23'),
(77, 13, 'Muhammad Azlan Allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 22:43:54'),
(78, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Item', 'Deleted item: minyak', '2026-01-11 22:44:29'),
(79, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Item', 'Deleted item: Santan', '2026-01-11 22:44:33'),
(80, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Supplier', 'Deleted supplier ID: 1', '2026-01-11 22:44:40'),
(81, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Supplier', 'Deleted supplier ID: 3', '2026-01-11 22:44:42'),
(82, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Supplier', 'Deleted supplier ID: 2', '2026-01-11 22:44:45'),
(83, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Expense', 'Deleted expense ID: 1', '2026-01-11 22:44:49'),
(84, 13, 'Muhammad Azlan Allin', 'owner', 'Delete Expense', 'Deleted expense ID: 2', '2026-01-11 22:44:51'),
(85, 12, 'Shandi Kurnia Ilahi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 22:45:32'),
(86, 13, 'Muhammad Azlan Allin', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 22:47:12'),
(87, 13, 'Muhammad Azlan Allin', 'owner', 'Add Item', 'Add Item: Santan (Kode: BRG-0001)', '2026-01-11 22:47:35'),
(88, 13, 'Muhammad Azlan Allin', 'owner', 'Update Item', 'Update Item: Santan', '2026-01-11 22:47:58'),
(89, 12, 'Shandi Kurnia Ilahi', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 22:48:11'),
(90, 12, 'Shandi Kurnia Ilahi', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 140.000', '2026-01-11 22:49:02'),
(91, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-11 23:17:24'),
(92, 15, 'Velisa', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-11 23:19:43'),
(93, 15, 'Velisa', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 210.000', '2026-01-11 23:20:24'),
(94, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-12 15:50:18'),
(95, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-12 17:02:16'),
(96, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-12 17:17:08'),
(97, 15, 'Velisa', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-12 17:18:58'),
(98, 15, 'Velisa', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 280.000', '2026-01-12 17:19:51'),
(99, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-12 17:21:15'),
(100, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-12 17:26:06'),
(101, 15, 'Velisa', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-12 17:30:15'),
(102, 1, 'System', 'superadmin', 'Login Failed', 'Attempted login for username: admin with role: owner', '2026-01-13 22:31:58'),
(103, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-13 22:32:09'),
(104, 15, 'Velisa', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-13 22:34:46'),
(105, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-13 23:02:46'),
(106, 15, 'Velisa', 'kasir', 'Login Success', 'User logged into the system as kasir', '2026-01-13 23:39:54'),
(107, 15, 'Velisa', 'kasir', 'Checkout Success', 'Batch payment completed for total: Rp 350.000', '2026-01-13 23:47:49'),
(108, 14, 'Velisa Putri Ramadhani', 'owner', 'Login Success', 'User logged into the system as owner', '2026-01-15 12:44:19'),
(109, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Item', 'Add Item: ABC Sambal Extreme Pedas 135ml (Kode: BRG-0002)', '2026-01-15 12:57:08'),
(110, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Item', 'Add Item: ABC Sambal Asli 135ml (Kode: BRG-0003)', '2026-01-15 12:59:58'),
(111, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Item', 'Add Item: ABC Sambal Extra Pedas 135ml (Kode: BRG-0004)', '2026-01-15 13:05:45'),
(112, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Item', 'Add Item: ABC Saus Tomat 135ml (Kode: BRG-0005)', '2026-01-15 13:11:05'),
(113, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Item', 'Add Item: ABC Kecap Manis 135ml (Kode: BRG-0006)', '2026-01-15 13:15:26'),
(114, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Bon Cabe (Kobe Boga Utama)', 'Add Supplier: PT Bon Cabe (Kobe Boga Utama)', '2026-01-16 09:00:44'),
(115, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Kikma (Kikma Food)', 'Add Supplier: PT Kikma (Kikma Food)', '2026-01-16 09:01:15'),
(116, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: CV Desaku (PT Motasa Indonesia)', 'Add Supplier: CV Desaku (PT Motasa Indonesia)', '2026-01-16 09:01:59'),
(117, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Supplier: PT Motasa Indonesia', 'Update Supplier: PT Motasa Indonesia', '2026-01-16 09:02:33'),
(118, 14, 'Velisa Putri Ramadhani', 'owner', 'Delete Supplier', 'Deleted supplier ID: 6', '2026-01-16 09:06:07'),
(119, 14, 'Velisa Putri Ramadhani', 'owner', 'Delete Supplier', 'Deleted supplier ID: 5', '2026-01-16 09:06:10'),
(120, 14, 'Velisa Putri Ramadhani', 'owner', 'Delete Supplier', 'Deleted supplier ID: 4', '2026-01-16 09:06:14'),
(121, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Unilever Indonesia', 'Add Supplier: PT Unilever Indonesia', '2026-01-16 09:28:58'),
(122, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Heinz ABC Indonesia', 'Add Supplier: PT Heinz ABC Indonesia', '2026-01-16 09:29:20'),
(123, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Indofood CBP', 'Add Supplier: PT Indofood CBP', '2026-01-16 09:29:44'),
(124, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Mayora Indah', 'Add Supplier: PT Mayora Indah', '2026-01-16 09:30:02'),
(125, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Wing’s Surya', 'Add Supplier: PT Wing’s Surya', '2026-01-16 09:31:03'),
(126, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Nestlé Indonesia', 'Add Supplier: PT Nestlé Indonesia', '2026-01-16 09:31:30'),
(127, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Frisian Flag', 'Add Supplier: PT Frisian Flag', '2026-01-16 09:31:47'),
(128, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Ajinomoto Indonesia', 'Add Supplier: PT Ajinomoto Indonesia', '2026-01-16 09:32:11'),
(129, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT GarudaFood', 'Add Supplier: PT GarudaFood', '2026-01-16 09:32:30'),
(130, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Ultra Jaya', 'Add Supplier: PT Ultra Jaya', '2026-01-16 09:32:48'),
(131, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Sinar Sosro', 'Add Supplier: PT Sinar Sosro', '2026-01-16 09:33:07'),
(132, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Kao Indonesia', 'Add Supplier: PT Kao Indonesia', '2026-01-16 09:34:13'),
(133, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Nutrifood Indonesia', 'Add Supplier: PT Nutrifood Indonesia', '2026-01-16 09:34:37'),
(134, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Fonterra Brands', 'Add Supplier: PT Fonterra Brands', '2026-01-16 09:35:25'),
(135, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Bon Cabe', 'Add Supplier: PT Bon Cabe', '2026-01-16 09:35:56'),
(136, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: PT Kikma', 'Add Supplier: PT Kikma', '2026-01-16 09:36:12'),
(137, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: CV Desaku', 'Add Supplier: CV Desaku', '2026-01-16 09:36:35'),
(138, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: CV Lafancy', 'Add Supplier: CV Lafancy', '2026-01-16 09:37:00'),
(139, 14, 'Velisa Putri Ramadhani', 'owner', 'Add Supplier: CV Dapurasa', 'Add Supplier: CV Dapurasa', '2026-01-16 09:37:18'),
(140, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Soda Kue Cap Raja Tawon 40g', '2026-01-16 09:54:09'),
(141, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nikma Spesial Bumbu Kari/Gulai Daging 50g', '2026-01-16 09:55:29'),
(142, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nikma Spesial Bumbu Rendang 50g', '2026-01-16 10:08:03'),
(143, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Pemasak Kuning Sachet', '2026-01-16 10:09:29'),
(144, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Pemasak Kambing Asli 4 Binatang', '2026-01-16 10:10:18'),
(145, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Pemasak Soto No.1 50g', '2026-01-16 10:11:00'),
(146, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Totole Kaldu Rasa Jamur', '2026-01-16 10:12:33'),
(147, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Vetsin No.1 Asli Cap Anggur 50g', '2026-01-16 10:13:31'),
(148, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Soto Cap Ayam', '2026-01-16 10:14:15'),
(149, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Kambing Cap Udang', '2026-01-16 10:15:01'),
(150, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kerupuk Ubi Kecil Mentah', '2026-01-16 10:15:49'),
(151, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Kambing Racikan', '2026-01-16 10:16:29'),
(152, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Soto Mak Hamid', '2026-01-16 10:17:03'),
(153, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sunlight Jeruk Nipis Sachet', '2026-01-16 10:18:24'),
(154, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Soda Kue Pengembang Roti', '2026-01-16 10:19:33'),
(155, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bihun Kering Plastik', '2026-01-16 10:21:37'),
(156, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kerupuk Ubi Mentah Pipih', '2026-01-16 10:22:17'),
(157, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sagu Mutiara Warna-Warni', '2026-01-16 10:23:09'),
(158, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kayu Manis Batang', '2026-01-16 10:24:43'),
(159, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: WOW Spaghetti Aglio Olio', '2026-01-16 10:27:38'),
(160, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: WOW Spaghetti Carbonara', '2026-01-16 10:28:16'),
(161, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: WOW Spaghetti Bolognese', '2026-01-16 10:28:39'),
(162, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: WOW Spaghetti Carbonara', '2026-01-16 10:29:07'),
(163, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Ketan Hitam Curah 250g', '2026-01-16 10:30:20'),
(164, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Telur Puyuh', '2026-01-16 10:31:54'),
(165, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Telur Ayam Kampung', '2026-01-16 10:32:28'),
(166, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: So Firman Kawi Bumbu Rempah', '2026-01-16 10:34:02'),
(167, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kopi Bubuk Hitam ', '2026-01-16 10:35:13'),
(168, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Soto Padang ', '2026-01-16 10:37:28'),
(169, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Gula Pasir Curah', '2026-01-16 10:40:31'),
(170, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saus Yummi Pedas', '2026-01-16 10:43:34'),
(171, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kerupuk Merah 500g', '2026-01-16 10:44:53'),
(172, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kerupuk Pangsit Kuning Mentah 500g', '2026-01-16 10:50:41'),
(173, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Chili Sauce Value Pouch 500g', '2026-01-16 10:53:58'),
(174, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Dua Udang 600ml Pouch', '2026-01-16 10:54:46'),
(175, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Tepung Antaka Patatip 400g', '2026-01-16 11:53:22'),
(176, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kobe Tepung Serbaguna Special 80g', '2026-01-16 11:54:03'),
(177, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kobe Tepung Bumbu Bakwan Kress 75g', '2026-01-16 11:57:43'),
(178, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kobe Tepung Bumbu Tempe Kriuk 75g', '2026-01-16 11:58:15'),
(179, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kobe Super Crispy Kentucky 75g', '2026-01-16 11:58:46'),
(180, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kobe Tepung Bumbu Ayam Geprek 110g', '2026-01-16 11:59:23'),
(181, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kobe Tepung Bumbu Pisang Crispy 75g', '2026-01-16 12:00:00'),
(182, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sajiku Tepung Bumbu Golden Crispy 75g', '2026-01-16 12:00:46'),
(183, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sajiku Bumbu Nasi Goreng 20g', '2026-01-16 12:01:26'),
(184, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sajiku Bumbu Penyedap Serbaguna 8g', '2026-01-16 12:01:57'),
(185, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Racik Bumbu Ikan Goreng 20g', '2026-01-16 12:03:14'),
(186, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Racik Bumbu Ayam Goreng 20g', '2026-01-16 12:03:41'),
(187, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Indomilk Susu Cair Sachet Original 40ml', '2026-01-16 12:04:09'),
(188, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Spons Cuci Piring Bintang', '2026-01-16 12:04:54'),
(189, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Sania 1 Liter Pouch', '2026-01-16 12:05:33'),
(190, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Sania 500ml Pouch', '2026-01-16 12:06:14'),
(191, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Sania 250ml Pouch', '2026-01-16 12:06:58'),
(192, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Indofood 600ml Pouch', '2026-01-16 12:07:33'),
(193, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saus Tiram Saori 380ml', '2026-01-16 12:08:23'),
(194, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: ABC Kecap Asli 135ml', '2026-01-16 12:08:56'),
(195, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: ABC Sambal Asli Pouch 380g', '2026-01-16 12:10:07'),
(196, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: ABC Sambal Extra Pedas Pouch 380g', '2026-01-16 12:10:47'),
(197, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saus ABC Extra Pedas 335ml', '2026-01-16 12:11:18'),
(198, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nutrijell Jelly Powder Orange 15g', '2026-01-16 12:11:46'),
(199, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nutrijell Jelly Powder Mangga 15g', '2026-01-16 12:12:21'),
(200, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Makarel MiN Saus Tomat 425g', '2026-01-16 12:12:54'),
(201, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sarden Asahin 155g', '2026-01-16 12:14:04'),
(202, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sarden ABC Saus Cabai 155g', '2026-01-16 12:14:37'),
(203, 14, 'Velisa Putri Ramadhani', 'owner', 'Delete Item', 'Deleted item: Susu Segar Botol 1 Liter', '2026-01-16 12:15:35'),
(204, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Raja  Kerupuk Mentah', '2026-01-16 12:17:10'),
(205, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Tepung Ketan Aromanis 500g', '2026-01-16 12:17:35'),
(206, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sarimi Rasa Ayam Bawang 75g', '2026-01-16 12:17:57'),
(207, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Indomie Mi Goreng 85g', '2026-01-16 12:18:25'),
(208, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Bango 700g Pouch', '2026-01-16 12:18:48'),
(209, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Bango 265g Pouch', '2026-01-16 12:19:11'),
(210, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sambal Me Lewis Hot Sauce', '2026-01-16 12:19:42'),
(211, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sedap Rasa Sambal Sachet', '2026-01-16 12:21:05'),
(212, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Rose Brand 600ml', '2026-01-16 12:21:40'),
(213, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Sedep 600ml', '2026-01-16 12:22:15'),
(214, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Sedep 600ml', '2026-01-16 12:23:20'),
(215, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Curah 1 Liter', '2026-01-16 12:23:56'),
(216, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Curah 500ml', '2026-01-16 12:24:43'),
(217, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Curah 250ml', '2026-01-16 12:25:10'),
(218, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Tepung Beras Rose Brand 500g', '2026-01-16 12:25:36'),
(219, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Tepung Ketan Putih Rose Brand 500g', '2026-01-16 12:25:59'),
(220, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Asam Cuka 150ml', '2026-01-16 12:26:21'),
(221, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kacang Tanah Kupas Premium', '2026-01-16 12:26:52'),
(222, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Gula Pasir Curah 1 Kg', '2026-01-16 12:27:24'),
(223, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Kambing Cap Udang 1 Kg (20x50g)', '2026-01-16 12:28:05'),
(224, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Terasi Udang Sambal Merah', '2026-01-16 12:28:34'),
(225, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Cap Kepiting 600g', '2026-01-16 12:29:23'),
(226, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Goreng Rose Brand 1 Liter', '2026-01-16 12:29:53'),
(227, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saus Sambal Cap Orang Tua Pedas Sachet', '2026-01-16 12:30:19'),
(228, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Desaku Bumbu Marinasi 20g', '2026-01-16 12:30:39'),
(229, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Ladaku Merica Bubuk 10g', '2026-01-16 12:31:02'),
(230, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Desaku Bawang Putih Bubuk 10g', '2026-01-16 12:31:23'),
(231, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Desaku Kunyit Bubuk 10g', '2026-01-16 12:31:48'),
(232, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Barkat Bawang Putih Bubuk 10g', '2026-01-16 12:32:34'),
(233, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nikma Bumbu Kambing 20g', '2026-01-16 12:33:48'),
(234, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nikma Bumbu Sop 20g', '2026-01-16 12:34:18'),
(235, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Manis Bango Sachet 10ml', '2026-01-16 12:34:43'),
(236, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bon Cabe Sachet Level 15', '2026-01-16 12:35:09'),
(237, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Cabai Bubuk 10g', '2026-01-16 12:35:56'),
(238, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Sate 20g', '2026-01-16 12:36:33'),
(239, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Pala Bubuk 8g', '2026-01-16 12:36:58'),
(240, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Jahe Bubuk 10g', '2026-01-16 12:37:26'),
(241, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Kari 20g', '2026-01-16 12:37:53'),
(242, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Rendang 20g', '2026-01-16 12:38:23'),
(243, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Soto Padang 20g', '2026-01-16 12:39:09'),
(244, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Opor Kurma 20g', '2026-01-16 12:40:37'),
(245, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Lada Hitam Bubuk 10g', '2026-01-16 12:41:09'),
(246, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kemiri Bubuk 10g', '2026-01-16 12:41:39'),
(247, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kaldu Jamur Bubuk 10g', '2026-01-16 12:42:25'),
(248, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kencur Bubuk 10g', '2026-01-16 12:42:48'),
(249, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bawang Putih Bubuk 10g', '2026-01-16 12:43:13'),
(250, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bawang Merah Bubuk 10g', '2026-01-16 12:43:42'),
(251, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Masako Kaldu Ayam 8g', '2026-01-16 12:44:07'),
(252, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Masako Kaldu Daging Sapi 8g', '2026-01-16 12:44:28'),
(253, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Masako Kaldu Jamur 8g', '2026-01-16 12:44:49'),
(254, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Nusantara Sayur Lodeh 20g', '2026-01-16 12:45:13'),
(255, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bumbu Nusantara Bumbu Ayam 20g', '2026-01-16 12:45:46'),
(256, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saori Saus Tiram Sachet 7ml', '2026-01-16 12:46:05'),
(257, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saori Saus Teriyaki Sachet 7ml', '2026-01-16 12:46:24'),
(258, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Saori Sapi Lada Hitam Sachet 7ml', '2026-01-16 12:46:45'),
(259, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: AJI Terasi Udang Bubuk 8g', '2026-01-16 12:47:06'),
(260, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: ABC Saus Terasi Sachet 8ml', '2026-01-16 12:47:25'),
(261, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: ABC Saus Extra Pedas Sachet 9ml', '2026-01-16 12:47:47'),
(262, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: ABC Saus Tiram Sachet 9ml', '2026-01-16 12:48:15'),
(263, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Royco Bumbu Pelezat Rasa Ayam 8g', '2026-01-16 12:48:38'),
(264, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Royco Bumbu Pelezat Rasa Sapi 8g', '2026-01-16 12:48:57'),
(265, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Dapurasa Bumbu Ayam Goreng 20g', '2026-01-16 12:49:32'),
(266, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Dapurasa Bumbu Soto Ayam 20g', '2026-01-16 12:49:57'),
(267, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Dapurasa Bumbu Hot Aburi 20g', '2026-01-16 12:50:22'),
(268, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Lafancy Jinten Bubuk 10g', '2026-01-16 12:50:55'),
(269, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Desaku Bumbu Balado 20g', '2026-01-16 12:51:25'),
(270, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Desaku Ketumbar Bubuk 10g', '2026-01-16 12:51:49'),
(271, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Terasi ABC Sachet', '2026-01-16 12:52:15'),
(272, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Asam Madura / Asam Jawa', '2026-01-16 12:52:53'),
(273, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kunci Vanili Bubuk', '2026-01-16 12:53:25'),
(274, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Terasi Cabe Rawit Sachet', '2026-01-16 12:53:47'),
(275, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Nutri Sedap Maizena 100g', '2026-01-16 12:54:23'),
(276, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Teh Poci Celup', '2026-01-16 12:54:45'),
(277, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyakita 1 Liter', '2026-01-16 12:55:12'),
(278, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Tepung Beras Rose Brand 500g', '2026-01-16 12:55:44'),
(279, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sinti ', '2026-01-16 12:56:42'),
(280, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Teh Bendera Celup', '2026-01-16 12:57:02'),
(281, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kopi Hitam Rangkiang Ambo', '2026-01-16 12:57:23'),
(282, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Blueband Serbaguna 200g', '2026-01-16 12:58:04'),
(283, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Mentega Kiloan', '2026-01-16 12:58:26'),
(284, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Tepung Maizena 250g', '2026-01-16 12:58:58'),
(285, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Beras Premium Karung', '2026-01-16 13:00:00'),
(286, 14, 'Velisa Putri Ramadhani', 'owner', 'Delete Item', 'Deleted item: Minyak Jelatah (Limbah)', '2026-01-16 13:00:27'),
(287, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Telur Itik / Bebek', '2026-01-16 13:17:44'),
(288, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Telur Ayam Ras', '2026-01-16 13:18:05'),
(289, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Larutan Cuka Makan', '2026-01-16 13:18:55'),
(290, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kecap Indofood Manis 520ml', '2026-01-16 13:19:15'),
(291, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Minyak Kuwali 1 Liter', '2026-01-16 13:19:36'),
(292, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Garam Kasar (Garam Krosok)', '2026-01-16 13:20:01'),
(293, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Garam Halus Yodium', '2026-01-16 13:20:22'),
(294, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Bihun Jagung', '2026-01-16 13:20:45'),
(295, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Sohun Jagung 250g', '2026-01-16 13:21:21'),
(296, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Mie Telur Kokiku', '2026-01-16 13:21:41'),
(297, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Mie Telor AA', '2026-01-16 13:22:00'),
(298, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Mie Telor Superior', '2026-01-16 13:22:21'),
(299, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Kwetiauw Basah 500g', '2026-01-16 13:22:48'),
(300, 14, 'Velisa Putri Ramadhani', 'owner', 'Update Item', 'Update Item: Gula Pasir Kristal 1kg', '2026-01-16 13:23:06');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(150) NOT NULL,
  `kategori` varchar(100) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `posisi` varchar(150) DEFAULT NULL,
  `satuan` varchar(20) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `stok_min` int(11) NOT NULL DEFAULT 0,
  `harga_beli` int(11) NOT NULL DEFAULT 0,
  `harga_jual` int(11) NOT NULL DEFAULT 0,
  `tgl_masuk` date NOT NULL,
  `tgl_kadaluarsa` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `kategori`, `supplier_id`, `posisi`, `satuan`, `stok`, `stok_min`, `harga_beli`, `harga_jual`, `tgl_masuk`, `tgl_kadaluarsa`, `created_at`, `gambar`) VALUES
(1, 'BRG-0001', 'Santan', 'Sembako', 0, 'rak a1', 'Liter', 87, 5, 5000, 70000, '2026-01-11', '2026-01-24', '2026-01-11 15:47:35', 'IMG_1768146478_212.png'),
(2, 'BRG-0002', 'ABC Sambal Extreme Pedas 135ml', 'Sembako', 0, 'Rak', 'Botol', 20, 5, 7000, 8500, '2025-05-18', '2026-05-05', '2026-01-15 05:57:08', 'IMG_1768456628_363.jpeg'),
(3, 'BRG-0003', 'ABC Sambal Asli 135ml', 'Sembako', 0, 'Rak', 'Botol', 30, 5, 6000, 8000, '2025-07-24', '2016-10-12', '2026-01-15 05:59:58', 'IMG_1768456798_351.jpeg'),
(4, 'BRG-0004', 'ABC Sambal Extra Pedas 135ml', 'Sembako', 0, 'Rak', 'Botol', 30, 5, 6500, 8000, '2025-10-24', '2026-10-12', '2026-01-15 06:05:45', 'IMG_1768457145_862.jpeg'),
(5, 'BRG-0005', 'ABC Saus Tomat 135ml', 'Sembako', 0, 'Rak', 'Botol', 30, 5, 6000, 7500, '2025-09-15', '2026-10-12', '2026-01-15 06:11:05', 'IMG_1768457465_414.jpeg'),
(6, 'BRG-0006', 'ABC Kecap Manis 135ml', 'Sembako', 0, 'Rak', 'Botol', 25, 5, 7500, 9000, '2025-09-04', '2026-05-14', '2026-01-15 06:15:26', 'IMG_1768457726_990.jpeg'),
(7, 'BRG-0007', 'Saus Yummi Pedas', 'Lainnya', 8, 'Rak A3', 'Pack', 50, 20, 7500, 10000, '2026-01-12', '2026-11-25', '2026-01-16 02:41:04', 'IMG_1768535014_605.jpeg'),
(8, 'BRG-0008', 'Gula Pasir Curah', 'Sembako', 22, 'Rak C2', 'Kg', 50, 20, 16000, 18500, '2026-01-14', '2027-12-31', '2026-01-16 02:41:04', 'IMG_1768534831_107.jpeg'),
(9, 'BRG-0009', 'Bumbu Soto Padang ', 'Lainnya', 22, 'Rak A4', 'Pack', 20, 5, 15000, 20000, '2026-01-13', '2026-04-30', '2026-01-16 02:41:04', 'IMG_1768534648_119.jpeg'),
(10, 'BRG-0010', 'Kopi Bubuk Hitam ', 'Minuman', 22, 'Rak C1', 'Kg', 15, 5, 35000, 45000, '2026-01-06', '2026-06-20', '2026-01-16 02:41:04', 'IMG_1768534513_128.jpeg'),
(11, 'BRG-0011', 'So Firman Kawi Bumbu Rempah', 'Lainnya', 22, 'Rak A4', 'Pack', 25, 8, 12000, 16000, '2026-01-07', '2026-05-15', '2026-01-16 02:41:04', 'IMG_1768534442_697.jpeg'),
(12, 'BRG-0012', 'Telur Ayam Kampung', 'Sembako', 22, 'Rak Pendingin', 'Kg', 20, 5, 38000, 45000, '2026-01-15', '2026-02-05', '2026-01-16 02:41:04', 'IMG_1768534348_615.jpeg'),
(13, 'BRG-0013', 'Telur Puyuh', 'Sembako', 22, 'Rak Pendingin', 'Kg', 15, 5, 28000, 35000, '2026-01-15', '2026-02-05', '2026-01-16 02:41:04', 'IMG_1768534314_589.jpeg'),
(14, 'BRG-0014', 'Ketan Hitam Curah 250g', 'Sembako', 22, 'Gudang 1', 'Pack', 40, 15, 5500, 7500, '2026-01-09', '2026-07-20', '2026-01-16 02:41:04', 'IMG_1768534220_110.jpeg'),
(15, 'BRG-0015', 'WOW Spaghetti Bolognese', 'Makanan', 11, 'Rak B2', 'Pack', 30, 10, 11000, 15000, '2026-01-11', '2026-08-18', '2026-01-16 02:41:04', 'IMG_1768534119_767.jpeg'),
(16, 'BRG-0016', 'WOW Spaghetti Carbonara', 'Makanan', 11, 'Rak B2', 'Pack', 30, 10, 11000, 15000, '2026-01-11', '2026-08-18', '2026-01-16 02:41:04', 'IMG_1768534147_244.jpeg'),
(17, 'BRG-0017', 'WOW Spaghetti Aglio Olio', 'Makanan', 11, 'Rak B2', 'Pack', 30, 10, 11000, 15000, '2026-01-11', '2026-08-18', '2026-01-16 02:41:04', 'IMG_1768534058_883.jpeg'),
(18, 'BRG-0018', 'Kayu Manis Batang', 'Lainnya', 22, 'Rak A4', 'Pack', 15, 5, 18000, 24000, '2026-01-10', '2026-10-25', '2026-01-16 02:41:04', 'IMG_1768533883_407.jpeg'),
(19, 'BRG-0019', 'Sagu Mutiara Warna-Warni', 'Lainnya', 22, 'Rak C2', 'Pack', 35, 10, 6000, 8500, '2026-01-08', '2026-08-15', '2026-01-16 02:41:04', 'IMG_1768533789_528.jpg'),
(20, 'BRG-0020', 'Kerupuk Ubi Mentah Pipih', 'Makanan', 22, 'Gudang 2', 'Kg', 15, 5, 20000, 27000, '2026-01-14', '2026-04-20', '2026-01-16 02:41:04', 'IMG_1768533737_559.jpeg'),
(21, 'BRG-0021', 'Bihun Kering Plastik', 'Makanan', 11, 'Rak B3', 'Pack', 30, 10, 10000, 13500, '2026-01-11', '2026-08-25', '2026-01-16 02:41:04', 'IMG_1768533697_708.jpeg'),
(22, 'BRG-0022', 'Soda Kue Pengembang Roti', 'Lainnya', 22, 'Rak C2', 'Pack', 40, 15, 4500, 6500, '2026-01-07', '2026-12-30', '2026-01-16 02:41:04', 'IMG_1768533573_328.jpeg'),
(23, 'BRG-0023', 'Sunlight Jeruk Nipis Sachet', 'Lainnya', 7, 'Rak D1', 'Sachet', 100, 30, 1500, 2000, '2026-01-12', '2027-06-20', '2026-01-16 02:41:04', 'IMG_1768533504_393.jpeg'),
(24, 'BRG-0024', 'Minyak Soto Mak Hamid', 'Lainnya', 22, 'Rak A5', 'Pack', 25, 8, 8000, 11000, '2026-01-09', '2026-07-18', '2026-01-16 02:41:04', 'IMG_1768533423_613.jpeg'),
(25, 'BRG-0025', 'Bumbu Kambing Racikan', 'Lainnya', 22, 'Rak A5', 'Pack', 20, 5, 12000, 16000, '2026-01-13', '2026-05-30', '2026-01-16 02:41:04', 'IMG_1768533389_211.jpeg'),
(26, 'BRG-0026', 'Kerupuk Ubi Kecil Mentah', 'Makanan', 22, 'Gudang 2', 'Kg', 20, 8, 18000, 24000, '2026-01-14', '2026-04-20', '2026-01-16 02:41:04', 'IMG_1768533349_459.jpeg'),
(27, 'BRG-0027', 'Bumbu Kambing Cap Udang', 'Lainnya', 22, 'Rak A5', 'Pack', 40, 15, 6000, 8500, '2026-01-10', '2026-06-25', '2026-01-16 02:41:04', 'IMG_1768533301_489.jpeg'),
(28, 'BRG-0028', 'Minyak Soto Cap Ayam', 'Lainnya', 22, 'Rak A5', 'Pack', 30, 10, 7000, 10000, '2026-01-09', '2026-07-20', '2026-01-16 02:41:04', 'IMG_1768533255_856.jpeg'),
(29, 'BRG-0029', 'Vetsin No.1 Asli Cap Anggur 50g', 'Lainnya', 14, 'Rak A1', 'Pack', 60, 20, 3000, 4500, '2026-01-08', '2026-12-15', '2026-01-16 02:41:04', 'IMG_1768533211_484.jpeg'),
(30, 'BRG-0030', 'Totole Kaldu Rasa Jamur', 'Lainnya', 22, 'Rak A1', 'Pack', 30, 10, 7500, 10000, '2026-01-12', '2026-08-30', '2026-01-16 02:41:04', 'IMG_1768533153_842.jpeg'),
(31, 'BRG-0031', 'Pemasak Soto No.1 50g', 'Lainnya', 22, 'Rak A5', 'Pack', 35, 12, 4500, 6500, '2026-01-11', '2026-06-22', '2026-01-16 02:41:04', 'IMG_1768533060_926.jpeg'),
(32, 'BRG-0032', 'Pemasak Kambing Asli 4 Binatang', 'Lainnya', 22, 'Rak A5', 'Pack', 30, 10, 5000, 7000, '2026-01-13', '2026-05-28', '2026-01-16 02:41:04', 'IMG_1768533018_282.jpeg'),
(33, 'BRG-0033', 'Bumbu Pemasak Kuning Sachet', 'Lainnya', 22, 'Rak A5', 'Pack', 40, 15, 4000, 6000, '2026-01-10', '2026-06-18', '2026-01-16 02:41:04', 'IMG_1768532969_493.jpeg'),
(34, 'BRG-0034', 'Nikma Spesial Bumbu Rendang 50g', 'Lainnya', 22, 'Rak A5', 'Pack', 40, 15, 4500, 6500, '2026-01-14', '2026-10-30', '2026-01-16 02:41:04', 'IMG_1768532883_221.jpg'),
(35, 'BRG-0035', 'Nikma Spesial Bumbu Kari/Gulai Daging 50g', 'Lainnya', 22, 'Rak A5', 'Pack', 40, 15, 4500, 6500, '2026-01-11', '2026-06-25', '2026-01-16 02:41:04', 'IMG_1768532129_424.jpg'),
(36, 'BRG-0036', 'Soda Kue Cap Raja Tawon 40g', 'Lainnya', 22, 'Rak C2', 'Pack', 50, 20, 3500, 5000, '2026-01-07', '2027-01-15', '2026-01-16 02:41:04', 'IMG_1768532049_280.jpeg'),
(37, 'BRG-0037', 'Saus Sambal Cap Orang Tua Pedas Sachet', 'Lainnya', 10, 'Rak A1', 'Pack', 48, 10, 18500, 22000, '2026-01-08', '2026-07-08', '2026-01-16 02:41:46', 'IMG_1768541419_847.jpeg'),
(38, 'BRG-0038', 'Minyak Goreng Rose Brand 1 Liter', 'Sembako', 22, 'Rak B2', 'Pcs', 36, 15, 15000, 17500, '2026-01-10', '2027-01-10', '2026-01-16 02:41:46', 'IMG_1768541393_130.jpeg'),
(39, 'BRG-0039', 'Kecap Manis Cap Kepiting 600g', 'Lainnya', 22, 'Rak A2', 'Pcs', 24, 8, 12500, 15000, '2026-01-12', '2027-07-12', '2026-01-16 02:41:46', 'IMG_1768541363_157.jpeg'),
(40, 'BRG-0040', 'Terasi Udang Sambal Merah', 'Lainnya', 22, 'Rak A3', 'Pack', 30, 8, 9000, 11500, '2026-01-09', '2026-04-09', '2026-01-16 02:41:46', 'IMG_1768541314_520.jpeg'),
(41, 'BRG-0041', 'Bumbu Kambing Cap Udang 1 Kg (20x50g)', 'Lainnya', 22, 'Rak A4', 'Pack', 12, 5, 45000, 55000, '2026-01-11', '2026-07-11', '2026-01-16 02:41:46', 'IMG_1768541285_757.jpeg'),
(42, 'BRG-0042', 'Gula Pasir Curah 1 Kg', 'Sembako', 22, 'Gudang', 'Kg', 45, 20, 13500, 16000, '2026-01-13', '2028-01-13', '2026-01-16 02:41:46', 'IMG_1768541244_412.jpeg'),
(43, 'BRG-0043', 'Kacang Tanah Kupas Premium', 'Makanan', 22, 'Gudang', 'Kg', 35, 10, 22000, 27000, '2026-01-14', '2026-04-14', '2026-01-16 02:41:46', 'IMG_1768541212_450.jpeg'),
(44, 'BRG-0044', 'Asam Cuka 150ml', 'Lainnya', 8, 'Rak A5', 'Botol', 48, 12, 3500, 5000, '2026-01-07', '2028-01-07', '2026-01-16 02:41:46', 'IMG_1768541181_860.png'),
(45, 'BRG-0045', 'Tepung Ketan Putih Rose Brand 500g', 'Lainnya', 22, 'Rak C1', 'Pack', 25, 8, 8500, 11000, '2026-01-15', '2026-07-15', '2026-01-16 02:41:46', 'IMG_1768541159_193.jpeg'),
(46, 'BRG-0046', 'Tepung Beras Rose Brand 500g', 'Lainnya', 22, 'Rak C2', 'Pack', 28, 8, 7500, 10000, '2026-01-15', '2026-07-15', '2026-01-16 02:41:46', 'IMG_1768541136_779.jpeg'),
(47, 'BRG-0047', 'Minyak Goreng Curah 250ml', 'Sembako', 22, 'Rak B3', 'Pcs', 60, 20, 4000, 5000, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768541110_542.jpeg'),
(48, 'BRG-0048', 'Minyak Goreng Curah 500ml', 'Sembako', 22, 'Rak B3', 'Pcs', 50, 20, 7500, 9000, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768541083_187.jpeg'),
(49, 'BRG-0049', 'Minyak Goreng Curah 1 Liter', 'Sembako', 22, 'Rak B3', 'Pcs', 45, 15, 14500, 17000, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768541036_570.jpeg'),
(50, 'BRG-0050', 'Kecap Manis Sedep 600ml', 'Lainnya', 22, 'Rak A2', 'Botol', 20, 8, 11500, 14000, '2026-01-10', '2027-07-10', '2026-01-16 02:41:46', 'IMG_1768541000_358.png'),
(51, 'BRG-0051', 'Kecap Manis Rose Brand 600ml', 'Lainnya', 22, 'Rak A2', 'Botol', 18, 8, 12000, 14500, '2026-01-10', '2027-07-10', '2026-01-16 02:41:46', 'IMG_1768540900_500.jpeg'),
(52, 'BRG-0052', 'Sedap Rasa Sambal Sachet', 'Lainnya', 11, 'Rak A3', 'Renceng', 35, 10, 16000, 19500, '2026-01-11', '2026-07-11', '2026-01-16 02:41:46', 'IMG_1768540865_790.jpeg'),
(53, 'BRG-0053', 'Sambal Me Lewis Hot Sauce', 'Lainnya', 25, 'Rak A3', 'Botol', 24, 8, 14500, 18000, '2026-01-09', '2026-07-09', '2026-01-16 02:41:46', 'IMG_1768540782_691.jpeg'),
(54, 'BRG-0054', 'Kecap Manis Bango 265g Pouch', 'Lainnya', 7, 'Rak A2', 'Pcs', 30, 10, 8500, 10500, '2026-01-12', '2028-01-12', '2026-01-16 02:41:46', 'IMG_1768540751_242.jpeg'),
(55, 'BRG-0055', 'Kecap Manis Bango 700g Pouch', 'Lainnya', 7, 'Rak A2', 'Pcs', 24, 8, 21000, 26000, '2026-01-12', '2028-01-12', '2026-01-16 02:41:46', 'IMG_1768540728_256.jpeg'),
(56, 'BRG-0056', 'Indomie Mi Goreng 85g', 'Makanan', 9, 'Rak D1', 'Pcs', 120, 50, 2800, 3500, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768540705_683.jpeg'),
(57, 'BRG-0057', 'Sarimi Rasa Ayam Bawang 75g', 'Makanan', 9, 'Rak D1', 'Pcs', 100, 40, 2500, 3000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768540677_728.jpeg'),
(58, 'BRG-0058', 'Tepung Ketan Aromanis 500g', 'Lainnya', 22, 'Rak C1', 'Pack', 22, 8, 8000, 10500, '2026-01-14', '2026-07-14', '2026-01-16 02:41:46', 'IMG_1768540655_746.jpeg'),
(59, 'BRG-0059', 'Raja  Kerupuk Mentah', 'Makanan', 22, 'Rak C3', 'Pack', 18, 6, 12000, 15000, '2026-01-10', '2026-04-10', '2026-01-16 02:41:46', 'IMG_1768540630_402.jpeg'),
(60, 'BRG-0060', 'Sarden ABC Saus Cabai 155g', 'Makanan', 8, 'Rak F1', 'Pcs', 36, 12, 11000, 13500, '2026-01-11', '2028-01-11', '2026-01-16 02:41:46', 'IMG_1768540477_353.jpeg'),
(61, 'BRG-0061', 'Sarden Asahin 155g', 'Makanan', 9, 'Rak F1', 'Pcs', 30, 10, 10500, 13000, '2026-01-11', '2028-01-11', '2026-01-16 02:41:46', 'IMG_1768540444_389.jpeg'),
(62, 'BRG-0062', 'Makarel MiN Saus Tomat 425g', 'Makanan', 9, 'Rak F2', 'Pcs', 24, 8, 17000, 21000, '2026-01-11', '2028-01-11', '2026-01-16 02:41:46', 'IMG_1768540374_718.jpeg'),
(63, 'BRG-0063', 'Nutrijell Jelly Powder Mangga 15g', 'Makanan', 19, 'Rak E2', 'Sachet', 60, 20, 1200, 1500, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768540341_253.jpeg'),
(64, 'BRG-0064', 'Nutrijell Jelly Powder Orange 15g', 'Makanan', 19, 'Rak E2', 'Sachet', 55, 20, 1200, 1500, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768540306_101.jpeg'),
(65, 'BRG-0065', 'Saus ABC Extra Pedas 335ml', 'Lainnya', 8, 'Rak A3', 'Botol', 20, 8, 12000, 15000, '2026-01-10', '2027-01-10', '2026-01-16 02:41:46', 'IMG_1768540278_777.jpeg'),
(66, 'BRG-0066', 'ABC Sambal Extra Pedas Pouch 380g', 'Lainnya', 8, 'Rak A3', 'Pcs', 18, 6, 14500, 18000, '2026-01-10', '2026-07-10', '2026-01-16 02:41:46', 'IMG_1768540247_881.jpeg'),
(67, 'BRG-0067', 'ABC Sambal Asli Pouch 380g', 'Lainnya', 8, 'Rak A3', 'Pcs', 20, 6, 13500, 17000, '2026-01-10', '2026-07-10', '2026-01-16 02:41:46', 'IMG_1768540206_594.jpeg'),
(68, 'BRG-0068', 'Saus Tomat ABC 335ml', 'Lainnya', 8, 'Rak A3', 'Botol', 24, 8, 11000, 14000, '2026-01-10', '2027-01-10', '2026-01-16 02:41:46', NULL),
(69, 'BRG-0069', 'ABC Kecap Asli 135ml', 'Lainnya', 8, 'Rak A2', 'Botol', 30, 10, 6000, 8000, '2026-01-10', '2027-07-10', '2026-01-16 02:41:46', 'IMG_1768540136_714.jpeg'),
(70, 'BRG-0070', 'Saus Tiram Saori 380ml', 'Lainnya', 14, 'Rak A4', 'Botol', 18, 6, 13000, 16500, '2026-01-11', '2027-01-11', '2026-01-16 02:41:46', 'IMG_1768540103_633.jpeg'),
(71, 'BRG-0071', 'Kecap Manis Indofood 600ml Pouch', 'Lainnya', 9, 'Rak A2', 'Pcs', 20, 8, 11000, 14000, '2026-01-12', '2027-07-12', '2026-01-16 02:41:46', 'IMG_1768540053_327.jpeg'),
(72, 'BRG-0072', 'Minyak Goreng Sania 250ml Pouch', 'Sembako', 22, 'Rak B4', 'Pcs', 50, 20, 4500, 5500, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768540018_154.jpeg'),
(73, 'BRG-0073', 'Minyak Goreng Sania 500ml Pouch', 'Sembako', 22, 'Rak B4', 'Pcs', 40, 15, 8000, 10000, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768539974_173.jpeg'),
(74, 'BRG-0074', 'Minyak Goreng Sania 1 Liter Pouch', 'Sembako', 22, 'Rak B4', 'Pcs', 35, 12, 15500, 18500, '2026-01-14', '2027-01-14', '2026-01-16 02:41:46', 'IMG_1768539933_677.jpeg'),
(75, 'BRG-0075', 'Spons Cuci Piring Bintang', 'Lainnya', 18, 'Rak G1', 'Pcs', 40, 15, 8000, 10000, '2026-01-13', NULL, '2026-01-16 02:41:46', 'IMG_1768539894_933.jpeg'),
(76, 'BRG-0076', 'Indomilk Susu Cair Sachet Original 40ml', 'Minuman', 9, 'Rak E3', 'Renceng', 50, 20, 10000, 12500, '2026-01-15', '2026-03-15', '2026-01-16 02:41:46', 'IMG_1768539849_900.jpeg'),
(77, 'BRG-0077', 'Racik Bumbu Ayam Goreng 20g', 'Lainnya', 9, 'Rak A5', 'Sachet', 100, 30, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:41:46', 'IMG_1768539821_306.jpeg'),
(78, 'BRG-0078', 'Racik Bumbu Ikan Goreng 20g', 'Lainnya', 9, 'Rak A5', 'Sachet', 90, 30, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:41:46', 'IMG_1768539794_587.jpeg'),
(79, 'BRG-0079', 'Sajiku Bumbu Penyedap Serbaguna 8g', 'Lainnya', 14, 'Rak A6', 'Sachet', 150, 50, 800, 1000, '2026-01-11', '2027-01-11', '2026-01-16 02:41:46', 'IMG_1768539717_640.jpeg'),
(80, 'BRG-0080', 'Sajiku Bumbu Nasi Goreng 20g', 'Lainnya', 14, 'Rak A6', 'Sachet', 120, 40, 1500, 2000, '2026-01-11', '2026-07-11', '2026-01-16 02:41:46', 'IMG_1768539686_514.jpeg'),
(81, 'BRG-0081', 'Sajiku Tepung Bumbu Golden Crispy 75g', 'Lainnya', 14, 'Rak C4', 'Pack', 40, 15, 4500, 6000, '2026-01-11', '2026-07-11', '2026-01-16 02:41:46', 'IMG_1768539646_700.jpeg'),
(82, 'BRG-0082', 'Kobe Tepung Bumbu Pisang Crispy 75g', 'Lainnya', 21, 'Rak C5', 'Pack', 35, 12, 3500, 5000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768539600_615.jpeg'),
(83, 'BRG-0083', 'Kobe Tepung Bumbu Ayam Geprek 110g', 'Lainnya', 21, 'Rak C5', 'Pack', 30, 10, 4500, 6000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768539563_960.jpeg'),
(84, 'BRG-0084', 'Kobe Super Crispy Kentucky 75g', 'Lainnya', 21, 'Rak C5', 'Pack', 32, 12, 3800, 5000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768539526_146.jpeg'),
(85, 'BRG-0085', 'Kobe Tepung Bumbu Tempe Kriuk 75g', 'Lainnya', 21, 'Rak C5', 'Pack', 28, 10, 3500, 5000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768539495_779.jpeg'),
(86, 'BRG-0086', 'Kobe Tepung Bumbu Bakwan Kress 75g', 'Lainnya', 21, 'Rak C5', 'Pack', 30, 10, 3500, 5000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768539463_922.jpeg'),
(87, 'BRG-0087', 'Kobe Tepung Serbaguna Special 80g', 'Lainnya', 21, 'Rak C5', 'Pack', 25, 10, 3800, 5000, '2026-01-13', '2026-07-13', '2026-01-16 02:41:46', 'IMG_1768539243_500.jpeg'),
(88, 'BRG-0088', 'Tepung Antaka Patatip 400g', 'Lainnya', 22, 'Rak C6', 'Pack', 30, 10, 11000, 14000, '2026-01-14', '2026-07-14', '2026-01-16 02:41:46', 'IMG_1768539202_190.jpeg'),
(89, 'BRG-0089', 'Kecap Manis Dua Udang 600ml Pouch', 'Lainnya', 22, 'Rak A2', 'Pcs', 24, 8, 12500, 15500, '2026-01-10', '2027-07-10', '2026-01-16 02:41:46', 'IMG_1768535686_741.jpeg'),
(90, 'BRG-0090', 'Chili Sauce Value Pouch 500g', 'Lainnya', 25, 'Rak A3', 'Pcs', 20, 8, 9500, 12000, '2026-01-11', '2026-07-11', '2026-01-16 02:41:46', 'IMG_1768535638_132.jpg'),
(91, 'BRG-0091', 'Kerupuk Pangsit Kuning Mentah 500g', 'Makanan', 22, 'Rak C7', 'Kg', 15, 5, 18000, 22000, '2026-01-14', '2026-04-14', '2026-01-16 02:41:46', 'IMG_1768535441_833.jpeg'),
(92, 'BRG-0092', 'Kerupuk Merah 500g', 'Makanan', 22, 'Rak C7', 'Kg', 12, 5, 19000, 23000, '2026-01-14', '2026-04-14', '2026-01-16 02:41:46', 'IMG_1768535093_218.jpeg'),
(93, 'BRG-0093', 'Royco Bumbu Pelezat Rasa Sapi 8g', 'Lainnya', 7, 'Display Gantung', 'Sachet', 200, 60, 800, 1000, '2026-01-13', '2027-01-13', '2026-01-16 02:42:04', 'IMG_1768542537_657.jpeg'),
(94, 'BRG-0094', 'Royco Bumbu Pelezat Rasa Ayam 8g', 'Lainnya', 7, 'Display Gantung', 'Sachet', 200, 60, 800, 1000, '2026-01-13', '2027-01-13', '2026-01-16 02:42:04', 'IMG_1768542518_857.jpeg'),
(95, 'BRG-0095', 'ABC Saus Tiram Sachet 9ml', 'Lainnya', 8, 'Display Gantung', 'Sachet', 150, 50, 500, 1000, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542495_226.jpeg'),
(96, 'BRG-0096', 'ABC Saus Extra Pedas Sachet 9ml', 'Lainnya', 8, 'Display Gantung', 'Sachet', 150, 50, 500, 1000, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542467_231.jpeg'),
(97, 'BRG-0097', 'ABC Saus Terasi Sachet 8ml', 'Lainnya', 8, 'Display Gantung', 'Sachet', 120, 40, 500, 1000, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542445_692.jpeg'),
(98, 'BRG-0098', 'AJI Terasi Udang Bubuk 8g', 'Lainnya', 14, 'Display Gantung', 'Sachet', 100, 30, 600, 1000, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768542426_870.jpeg'),
(99, 'BRG-0099', 'Saori Sapi Lada Hitam Sachet 7ml', 'Lainnya', 14, 'Display Gantung', 'Sachet', 80, 30, 700, 1000, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768542405_747.jpeg'),
(100, 'BRG-0100', 'Saori Saus Teriyaki Sachet 7ml', 'Lainnya', 14, 'Display Gantung', 'Sachet', 80, 30, 700, 1000, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768542384_604.jpeg'),
(101, 'BRG-0101', 'Saori Saus Tiram Sachet 7ml', 'Lainnya', 14, 'Display Gantung', 'Sachet', 90, 30, 700, 1000, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768542365_583.jpeg'),
(102, 'BRG-0102', 'Bumbu Nusantara Bumbu Ayam 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 80, 25, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', 'IMG_1768542346_478.jpeg'),
(103, 'BRG-0103', 'Bumbu Nusantara Sayur Lodeh 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 70, 25, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', 'IMG_1768542313_117.jpeg'),
(104, 'BRG-0104', 'Masako Kaldu Jamur 8g', 'Lainnya', 11, 'Display Gantung', 'Sachet', 150, 50, 700, 1000, '2026-01-13', '2027-01-13', '2026-01-16 02:42:04', 'IMG_1768542289_893.jpeg'),
(105, 'BRG-0105', 'Masako Kaldu Daging Sapi 8g', 'Lainnya', 11, 'Display Gantung', 'Sachet', 150, 50, 700, 1000, '2026-01-13', '2027-01-13', '2026-01-16 02:42:04', 'IMG_1768542268_828.jpeg'),
(106, 'BRG-0106', 'Masako Kaldu Ayam 8g', 'Lainnya', 11, 'Display Gantung', 'Sachet', 180, 60, 700, 1000, '2026-01-13', '2027-01-13', '2026-01-16 02:42:04', 'IMG_1768542247_688.jpeg'),
(107, 'BRG-0107', 'Bawang Merah Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 100, 30, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542222_954.jpeg'),
(108, 'BRG-0108', 'Bawang Putih Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 100, 30, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542193_928.jpeg'),
(109, 'BRG-0109', 'Kencur Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 60, 20, 900, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542168_907.jpeg'),
(110, 'BRG-0110', 'Kaldu Jamur Bubuk 10g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 90, 30, 800, 1000, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768542145_195.jpeg'),
(111, 'BRG-0111', 'Kemiri Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 70, 20, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542099_340.jpeg'),
(112, 'BRG-0112', 'Lada Hitam Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 80, 25, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768542069_127.jpeg'),
(113, 'BRG-0113', 'Bumbu Gulai Kalio 20g', 'Lainnya', 4, 'Display Gantung', 'Sachet', 60, 20, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', NULL),
(114, 'BRG-0114', 'Bumbu Opor Kurma 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 55, 20, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', NULL),
(115, 'BRG-0115', 'Bumbu Sop 20g', 'Lainnya', 4, 'Display Gantung', 'Sachet', 80, 25, 1000, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', NULL),
(116, 'BRG-0116', 'Bumbu Soto Padang 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 65, 20, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', 'IMG_1768541949_157.jpeg'),
(117, 'BRG-0117', 'Bumbu Rendang 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 70, 25, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', 'IMG_1768541903_823.jpeg'),
(118, 'BRG-0118', 'Bumbu Kari 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 75, 25, 1000, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', 'IMG_1768541873_839.jpeg'),
(119, 'BRG-0119', 'Jahe Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 70, 20, 900, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768541846_582.jpeg'),
(120, 'BRG-0120', 'Pala Bubuk 8g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 60, 20, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768541818_896.jpeg'),
(121, 'BRG-0121', 'Bumbu Sate 20g', 'Lainnya', 0, 'Display Gantung', 'Sachet', 65, 20, 1200, 1500, '2026-01-10', '2026-07-10', '2026-01-16 02:42:04', 'IMG_1768541793_390.jpeg'),
(122, 'BRG-0122', 'Cabai Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 90, 30, 1000, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768541756_682.jpeg'),
(123, 'BRG-0123', 'Bon Cabe Sachet Level 15', 'Lainnya', 21, 'Display Gantung', 'Sachet', 100, 30, 1500, 2000, '2026-01-14', '2026-07-14', '2026-01-16 02:42:04', 'IMG_1768541709_617.jpeg'),
(124, 'BRG-0124', 'Kecap Manis Bango Sachet 10ml', 'Lainnya', 7, 'Display Gantung', 'Sachet', 150, 50, 500, 1000, '2026-01-12', '2027-01-12', '2026-01-16 02:42:04', 'IMG_1768541683_887.jpeg'),
(125, 'BRG-0125', 'Nikma Bumbu Sop 20g', 'Lainnya', 22, 'Display Gantung', 'Sachet', 60, 20, 1000, 1500, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768541658_591.jpeg'),
(126, 'BRG-0126', 'Kikma Bumbu Gulai Daging 20g', 'Lainnya', 22, 'Display Gantung', 'Sachet', 55, 20, 1200, 1500, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', NULL),
(127, 'BRG-0127', 'Kikma Bumbu Rendang 20g', 'Lainnya', 22, 'Display Gantung', 'Sachet', 60, 20, 1200, 1500, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', NULL),
(128, 'BRG-0128', 'Nikma Bumbu Kambing 20g', 'Lainnya', 22, 'Display Gantung', 'Sachet', 50, 20, 1200, 1500, '2026-01-11', '2026-07-11', '2026-01-16 02:42:04', 'IMG_1768541628_157.jpeg'),
(129, 'BRG-0129', 'Barkat Bawang Putih Bubuk 10g', 'Lainnya', 9, 'Display Gantung', 'Sachet', 70, 20, 900, 1500, '2026-01-12', '2026-07-12', '2026-01-16 02:42:04', 'IMG_1768541554_202.jpeg'),
(130, 'BRG-0130', 'Desaku Kunyit Bubuk 10g', 'Lainnya', 23, 'Display Gantung', 'Sachet', 60, 20, 900, 1500, '2026-01-13', '2026-07-13', '2026-01-16 02:42:04', 'IMG_1768541508_439.jpeg'),
(131, 'BRG-0131', 'Desaku Bawang Putih Bubuk 10g', 'Lainnya', 23, 'Display Gantung', 'Sachet', 65, 20, 900, 1500, '2026-01-13', '2026-07-13', '2026-01-16 02:42:04', 'IMG_1768541483_641.jpeg'),
(132, 'BRG-0132', 'Ladaku Merica Bubuk 10g', 'Lainnya', 22, 'Display Gantung', 'Sachet', 70, 20, 1000, 1500, '2026-01-13', '2026-07-13', '2026-01-16 02:42:04', 'IMG_1768541462_593.jpeg'),
(133, 'BRG-0133', 'Desaku Bumbu Marinasi 20g', 'Lainnya', 23, 'Display Gantung', 'Sachet', 50, 15, 1200, 1500, '2026-01-13', '2026-07-13', '2026-01-16 02:42:04', 'IMG_1768541439_800.jpeg'),
(134, 'BRG-0134', 'Desaku Ketumbar Bubuk 10g', 'Lainnya', 23, 'Display Gantung', 'Sachet', 60, 20, 900, 1500, '2026-01-13', '2026-07-13', '2026-01-16 02:42:21', 'IMG_1768542709_430.jpeg'),
(135, 'BRG-0135', 'Desaku Bumbu Balado 20g', 'Lainnya', 23, 'Display Gantung', 'Sachet', 55, 20, 1200, 1500, '2026-01-13', '2026-07-13', '2026-01-16 02:42:21', 'IMG_1768542685_554.jpeg'),
(136, 'BRG-0136', 'Lafancy Jinten Bubuk 10g', 'Lainnya', 24, 'Display Gantung', 'Sachet', 50, 15, 1000, 1500, '2026-01-14', '2026-07-14', '2026-01-16 02:42:21', 'IMG_1768542655_115.jpeg'),
(137, 'BRG-0137', 'Dapurasa Bumbu Hot Aburi 20g', 'Lainnya', 25, 'Display Gantung', 'Sachet', 45, 15, 1200, 1500, '2026-01-14', '2026-07-14', '2026-01-16 02:42:21', 'IMG_1768542622_719.jpeg'),
(138, 'BRG-0138', 'Dapurasa Bumbu Soto Ayam 20g', 'Lainnya', 25, 'Display Gantung', 'Sachet', 50, 15, 1200, 1500, '2026-01-14', '2026-07-14', '2026-01-16 02:42:21', 'IMG_1768542597_271.jpeg'),
(139, 'BRG-0139', 'Dapurasa Bumbu Ayam Goreng 20g', 'Lainnya', 25, 'Display Gantung', 'Sachet', 50, 15, 1200, 1500, '2026-01-14', '2026-07-14', '2026-01-16 02:42:21', 'IMG_1768542572_343.jpeg'),
(140, 'BRG-0140', 'Gula Pasir Kristal 1kg', 'Sembako', 22, 'Gudang', 'Kg', 100, 20, 16500, 18000, '2026-01-15', '2028-01-15', '2026-01-16 02:48:50', 'IMG_1768544586_456.jpeg'),
(141, 'BRG-0141', 'Kwetiauw Basah 500g', 'Makanan', 22, 'Rak B2', 'Pack', 20, 5, 7000, 9000, '2026-01-16', '2026-01-20', '2026-01-16 02:48:50', 'IMG_1768544568_498.jpeg'),
(142, 'BRG-0142', 'Mie Telor Superior', 'Makanan', 11, 'Rak D2', 'Pack', 40, 10, 4500, 6000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768544541_761.jpeg'),
(143, 'BRG-0143', 'Mie Telor AA', 'Makanan', 11, 'Rak D2', 'Pack', 40, 10, 4000, 5500, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768544520_509.jpeg'),
(144, 'BRG-0144', 'Mie Telur Kokiku', 'Makanan', 11, 'Rak D2', 'Pack', 35, 10, 4500, 6000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768544501_823.jpeg'),
(145, 'BRG-0145', 'Sohun Jagung 250g', 'Makanan', 22, 'Rak D2', 'Pack', 50, 15, 5000, 7000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768544481_399.jpeg'),
(146, 'BRG-0146', 'Bihun Jagung', 'Makanan', 11, 'Rak D2', 'Pack', 50, 15, 5000, 7000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768544445_434.jpeg'),
(147, 'BRG-0147', 'Garam Halus Yodium', 'Lainnya', 22, 'Rak A1', 'Pack', 100, 20, 2000, 3000, '2026-01-15', NULL, '2026-01-16 02:48:50', 'IMG_1768544422_814.jpeg'),
(148, 'BRG-0148', 'Garam Kasar (Garam Krosok)', 'Lainnya', 22, 'Gudang', 'Kg', 50, 10, 5000, 7000, '2026-01-15', NULL, '2026-01-16 02:48:50', 'IMG_1768544401_781.jpeg'),
(149, 'BRG-0149', 'Minyak Kuwali 1 Liter', 'Sembako', 22, 'Rak B1', 'Botol', 36, 10, 15000, 17500, '2026-01-15', '2027-01-15', '2026-01-16 02:48:50', 'IMG_1768544376_199.jpeg'),
(150, 'BRG-0150', 'Kecap Indofood Manis 520ml', 'Lainnya', 9, 'Rak A2', 'Pcs', 24, 8, 14000, 16500, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768544355_446.jpeg'),
(151, 'BRG-0151', 'Larutan Cuka Makan', 'Lainnya', 8, 'Rak A5', 'Botol', 48, 12, 3000, 5000, '2026-01-12', '2028-01-12', '2026-01-16 02:48:50', 'IMG_1768544335_564.jpeg'),
(152, 'BRG-0152', 'Telur Ayam Ras', 'Sembako', 22, 'Rak Telur', 'Pcs', 300, 50, 1800, 2200, '2026-01-16', '2026-02-10', '2026-01-16 02:48:50', 'IMG_1768544285_175.jpeg'),
(153, 'BRG-0153', 'Telur Itik / Bebek', 'Sembako', 22, 'Rak Telur', 'Pcs', 100, 20, 3000, 3500, '2026-01-16', '2026-02-15', '2026-01-16 02:48:50', 'IMG_1768544264_994.jpeg'),
(154, 'BRG-0154', 'Beras Premium Karung', 'Sembako', 22, 'Gudang', 'Kg', 200, 50, 14000, 16000, '2026-01-16', NULL, '2026-01-16 02:48:50', 'IMG_1768543200_767.jpeg'),
(155, 'BRG-0155', 'Tepung Maizena 250g', 'Lainnya', 19, 'Rak C1', 'Pack', 30, 10, 5000, 7000, '2026-01-15', '2027-01-15', '2026-01-16 02:48:50', 'IMG_1768543138_842.jpeg'),
(156, 'BRG-0156', 'Mentega Kiloan', 'Lainnya', 22, 'Rak Pendingin', 'Kg', 10, 2, 22000, 28000, '2026-01-15', '2026-07-15', '2026-01-16 02:48:50', 'IMG_1768543106_420.jpeg'),
(157, 'BRG-0157', 'Blueband Serbaguna 200g', 'Lainnya', 7, 'Rak C1', 'Sachet', 40, 10, 8500, 10500, '2026-01-15', '2027-01-15', '2026-01-16 02:48:50', 'IMG_1768543084_527.jpeg'),
(158, 'BRG-0158', 'Kopi Hitam Nicky', 'Minuman', 22, 'Rak C1', 'Pack', 20, 5, 12000, 15000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', NULL),
(159, 'BRG-0159', 'Kopi Hitam Rangkiang Ambo', 'Minuman', 22, 'Rak C1', 'Pack', 20, 5, 15000, 18000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768543043_745.jpeg'),
(160, 'BRG-0160', 'Teh Bendera Celup', 'Minuman', 7, 'Rak C1', 'Pcs', 50, 10, 6000, 8000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768543022_403.jpeg'),
(161, 'BRG-0161', 'Sinti ', 'Minuman', 22, 'Rak C1', 'Pcs', 30, 10, 5000, 7000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768543002_432.jpeg'),
(162, 'BRG-0162', 'Tepung Beras Rose Brand 500g', 'Lainnya', 22, 'Rak C1', 'Pack', 40, 10, 7500, 9500, '2026-01-15', '2027-01-15', '2026-01-16 02:48:50', 'IMG_1768542944_827.jpeg'),
(163, 'BRG-0163', 'Minyakita 1 Liter', 'Sembako', 22, 'Rak B1', 'Pcs', 60, 20, 14000, 15700, '2026-01-16', '2027-01-16', '2026-01-16 02:48:50', 'IMG_1768542912_439.jpeg'),
(164, 'BRG-0164', 'Teh Poci Celup', 'Minuman', 22, 'Rak C1', 'Pcs', 40, 10, 6000, 8000, '2026-01-14', '2027-01-14', '2026-01-16 02:48:50', 'IMG_1768542885_938.jpeg'),
(165, 'BRG-0165', 'Nutri Sedap Maizena 100g', 'Lainnya', 19, 'Rak C1', 'Pack', 50, 15, 3000, 4500, '2026-01-15', '2027-01-15', '2026-01-16 02:48:50', 'IMG_1768542863_380.jpeg'),
(166, 'BRG-0166', 'Terasi Cabe Rawit Sachet', 'Lainnya', 22, 'Rak A3', 'Pack', 60, 20, 5000, 7000, '2026-01-12', '2027-01-12', '2026-01-16 02:48:50', 'IMG_1768542827_113.jpeg'),
(167, 'BRG-0167', 'Kunci Vanili Bubuk', 'Lainnya', 22, 'Rak C2', 'Pcs', 100, 20, 500, 1000, '2026-01-15', '2028-01-15', '2026-01-16 02:48:50', 'IMG_1768542805_328.jpeg'),
(168, 'BRG-0168', 'Asam Madura / Asam Jawa', 'Lainnya', 22, 'Rak A4', 'Pack', 30, 10, 4000, 6000, '2026-01-15', NULL, '2026-01-16 02:48:50', 'IMG_1768542773_531.jpeg'),
(169, 'BRG-0169', 'Terasi ABC Sachet', 'Lainnya', 8, 'Rak A3', 'Pack', 100, 30, 500, 1000, '2026-01-12', '2027-01-12', '2026-01-16 02:48:50', 'IMG_1768542735_258.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` int(11) NOT NULL,
  `nama_pengeluaran` varchar(255) NOT NULL,
  `nominal` decimal(15,2) NOT NULL,
  `kategori` varchar(100) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL,
  `kode_transaksi` varchar(30) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `total_bayar` decimal(15,2) NOT NULL,
  `bayar` decimal(15,2) NOT NULL,
  `kembalian` decimal(15,2) NOT NULL,
  `status` enum('lunas','pending','batal') DEFAULT 'lunas',
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stok_masuk`
--

CREATE TABLE `stok_masuk` (
  `id` int(11) NOT NULL,
  `barang_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `nama_supplier` varchar(255) NOT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `nama_supplier`, `kontak`, `alamat`, `tanggal`) VALUES
(7, 'PT Unilever Indonesia', '08001558000', '(Distributor Padang) Jl. Bypass KM 13, Padang', '2026-01-16 09:28:58'),
(8, 'PT Heinz ABC Indonesia', '0215200222', '(Distributor Padang) Jl. Bypass KM 10, Padang', '2026-01-16 09:29:20'),
(9, 'PT Indofood CBP', '0751480001', 'Jl. Raya Lubuk Begalung, Padang', '2026-01-16 09:29:44'),
(10, 'PT Mayora Indah', '02180637000', '(Distributor) Jl. Bypass KM 11, Padang', '2026-01-16 09:30:02'),
(11, 'PT Wing’s Surya', '0751482000', 'Jl. Bypass KM 14, Koto Tangah, Padang', '2026-01-16 09:31:03'),
(12, 'PT Nestlé Indonesia', '08001122111', '(Distributor) Jl. Bypass KM 7, Padang', '2026-01-16 09:31:30'),
(13, 'PT Frisian Flag', '02129958000', '(Distributor) Jl. Bypass KM 12, Padang', '2026-01-16 09:31:47'),
(14, 'PT Ajinomoto Indonesia', '08001886688', 'Jl. Bypass KM 8, Kuranji, Padang', '2026-01-16 09:32:11'),
(15, 'PT GarudaFood', '0217290111', '(Distributor) Jl. Bypass KM 9, Padang', '2026-01-16 09:32:30'),
(16, 'PT Ultra Jaya', '08001185872', '(Distributor) Jl. Bypass KM 10, Padang', '2026-01-16 09:32:48'),
(17, 'PT Sinar Sosro', '0751485001', 'Jl. Bypass KM 16, Padang', '2026-01-16 09:33:07'),
(18, 'PT Kao Indonesia', '08001808080', '(Distributor) Jl. Bypass KM 11, Padang', '2026-01-16 09:34:13'),
(19, 'PT Nutrifood Indonesia', '0214605777', '(Distributor) Jl. Bypass KM 9, Padang', '2026-01-16 09:34:37'),
(20, 'PT Fonterra Brands', '08001651000', '(Distributor) Jl. Bypass KM 12, Padang', '2026-01-16 09:35:25'),
(21, 'PT Bon Cabe', '02158908888', 'Jl. Bypass KM 12, Kuranji, Padang', '2026-01-16 09:35:56'),
(22, 'PT Kikma', '0751480112', 'Jl. Adinegoro No. 15, Koto Tangah, Padang', '2026-01-16 09:36:12'),
(23, 'CV Desaku', '08113300555', 'Jl. Bypass KM 9, Kuranji, Padang', '2026-01-16 09:36:35'),
(24, 'CV Lafancy', '081267882341', 'Jl. Raya Pasar Baru No. 15, Pauh, Padang', '2026-01-16 09:37:00'),
(25, 'CV Dapurasa', '082170334455', 'Jl. Mohammad Hatta, Limau Manis, Pauh, Padang', '2026-01-16 09:37:18');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `status` enum('lunas','belum_bayar') DEFAULT 'belum_bayar',
  `tanggal` datetime DEFAULT NULL,
  `total_harga` decimal(15,2) DEFAULT 0.00,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_barang`, `nama_barang`, `qty`, `total`, `status`, `tanggal`, `total_harga`, `is_read`) VALUES
(1, 6, NULL, 1, 0, 'lunas', '2026-01-03 20:57:38', 7000.00, 1),
(2, 6, NULL, 1, 0, 'lunas', '2026-01-03 21:00:19', 7000.00, 1),
(3, 6, NULL, 5, 0, 'lunas', '2026-01-03 21:00:33', 35000.00, 1),
(4, 6, NULL, 3, 0, 'lunas', '2026-01-03 23:21:42', 21000.00, 1),
(5, 9, NULL, 3, 0, 'lunas', '2026-01-03 23:26:43', 30000.00, 1),
(6, 9, NULL, 2, 0, 'lunas', '2026-01-03 23:27:16', 20000.00, 1),
(7, 10, NULL, 5, 0, 'lunas', '2026-01-03 23:31:45', 60000.00, 1),
(9, 10, NULL, 5, 0, 'lunas', '2026-01-03 23:39:17', 60000.00, 1),
(10, 12, NULL, 4, 0, 'lunas', '2026-01-10 19:23:58', 400000.00, 1),
(11, 1, NULL, 10, 0, 'lunas', '2026-01-10 20:30:34', 50000.00, 1),
(12, 1, NULL, 1, 0, 'lunas', '2026-01-10 20:32:26', 5000.00, 1),
(13, 1, NULL, 1, 0, 'lunas', '2026-01-10 20:53:43', 5000.00, 1),
(14, 1, NULL, 1, 0, 'lunas', '2026-01-10 20:55:58', 5000.00, 1),
(15, 1, NULL, 1, 0, 'lunas', '2026-01-10 20:57:21', 5000.00, 1),
(17, 1, NULL, 1, 0, 'lunas', '2026-01-11 11:09:54', 5000.00, 1),
(18, 1, NULL, 1, 0, 'lunas', '2026-01-11 11:25:23', 5000.00, 1),
(19, 1, NULL, 1, 0, 'lunas', '2026-01-11 11:26:51', 5000.00, 1),
(20, 1, NULL, 1, 0, 'lunas', '2026-01-11 11:35:57', 5000.00, 1),
(21, 1, NULL, 2, 0, 'lunas', '2026-01-11 22:49:02', 140000.00, 1),
(22, 1, NULL, 3, 0, 'lunas', '2026-01-11 23:20:24', 210000.00, 0),
(23, 1, NULL, 4, 0, 'lunas', '2026-01-12 17:19:51', 280000.00, 0),
(24, 1, NULL, 5, 0, 'lunas', '2026-01-13 23:47:49', 350000.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('owner','kasir') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`) VALUES
(12, 'Shandi Kurnia Ilahi', 'MakItam', '$2y$10$NZrrRGvbiglSbQ3.IQ8.h.Ng4.fme1IfnbDsvHutNRFfeFaQGg7vW', 'kasir'),
(13, 'Muhammad Azlan Allin', 'Azlan', '$2y$10$yol86Nr3fRrGPZu4.cHou.ZyMd7Zgq9xODRTmmvXuDhW5EWGLpRBy', 'owner'),
(14, 'Velisa Putri Ramadhani', 'veli23', '$2y$10$L.Ms8/81nhcVCo57evkK5eL822J..Q0coZQEHxIK29b4PfF9M0ZYS', 'owner'),
(15, 'Velisa', 'veli123', '$2y$10$t/kDn2m/rhKtrwAvonBhAOnopG2l6VVaNf/4JAEmsmJ8TmLrOB7Ai', 'kasir');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  ADD KEY `tanggal` (`tanggal`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=301;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stok_masuk`
--
ALTER TABLE `stok_masuk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
