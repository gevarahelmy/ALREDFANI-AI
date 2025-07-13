-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql111.infinityfree.com
-- Generation Time: 12 يوليو 2025 الساعة 15:55
-- إصدار الخادم: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39371272_ai_alredfani`
--

-- --------------------------------------------------------

--
-- بنية الجدول `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'alredfani', '$2y$10$mYkjrsMl7ei7h7ChPiuBSuZ71lsrrZscmhr9iTHWaEE8eGtXIuqJK', '2025-07-07 22:17:33');

-- --------------------------------------------------------

--
-- بنية الجدول `fields`
--

CREATE TABLE `fields` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` varchar(50) NOT NULL DEFAULT 'text',
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `fields`
--

INSERT INTO `fields` (`id`, `template_id`, `field_name`, `field_type`, `is_required`, `created_at`) VALUES
(1, 1, 'رقم الطالب', 'number', 1, '2025-07-07 23:18:59'),
(2, 1, 'اسم الطالب', 'text', 1, '2025-07-07 23:19:17'),
(4, 1, 'اسم المدرسة', 'text', 1, '2025-07-07 23:19:55'),
(5, 1, 'الصف الدراسي', 'text', 1, '2025-07-07 23:20:19'),
(6, 1, 'العام الدراسي', 'date', 1, '2025-07-08 18:43:44'),
(9, 1, 'الإسلامية', 'number', 1, '2025-07-08 18:45:18'),
(10, 1, 'اللغة العربية', 'number', 1, '2025-07-08 18:46:02'),
(11, 1, 'اللغه الانجليزيه', 'number', 1, '2025-07-08 18:46:14'),
(12, 1, 'القرآن', 'number', 1, '2025-07-08 18:46:27'),
(13, 1, 'المجموع الكلي', 'number', 1, '2025-07-08 18:46:51'),
(14, 1, 'النسبة المئوية', 'number', 1, '2025-07-08 18:47:14'),
(15, 1, 'الترتيب', 'text', 1, '2025-07-08 18:47:33'),
(16, 1, 'التقدير', 'text', 1, '2025-07-08 18:47:46');

-- --------------------------------------------------------

--
-- بنية الجدول `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `stored_path` varchar(255) NOT NULL,
  `extracted_text` longtext DEFAULT NULL,
  `file_type` varchar(50) NOT NULL,
  `data_category` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `files`
--

INSERT INTO `files` (`id`, `file_name`, `stored_path`, `extracted_text`, `file_type`, `data_category`, `uploaded_by`, `uploaded_at`) VALUES
(2, 'قاعدة بيانات فقط اول ثانوي أ. مقدم ركن حلمي الردفاني.xlsx', 'uploads/file_6864c160125dd9.78689023.xlsx', NULL, 'xlsx', 'رقم الطالب', 1, '2025-07-02 05:19:28'),
(4, '1056.pdf', 'uploads/file_6864e6d91ec799.89427322.pdf', NULL, 'pdf', 'أخرى', 1, '2025-07-02 07:59:21'),
(5, 'رسالة الماجستير %22 آثار الإرهاب في اليمن %22.pdf', 'uploads/file_6864f0348361d0.14213235.pdf', NULL, 'pdf', 'تقارير', 1, '2025-07-02 08:39:16');

-- --------------------------------------------------------

--
-- بنية الجدول `file_protection`
--

CREATE TABLE `file_protection` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `protection_type` varchar(50) NOT NULL,
  `auth_key` varchar(255) DEFAULT NULL,
  `auth_value_hash` varchar(255) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `file_protection`
--

INSERT INTO `file_protection` (`id`, `file_id`, `protection_type`, `auth_key`, `auth_value_hash`, `description`) VALUES
(2, 2, 'username', 'رقم الطالب', NULL, NULL);

-- --------------------------------------------------------

--
-- بنية الجدول `queries_log`
--

CREATE TABLE `queries_log` (
  `id` int(11) NOT NULL,
  `query_text` mediumtext NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` mediumtext DEFAULT NULL,
  `query_time` timestamp NULL DEFAULT current_timestamp(),
  `file_id` int(11) DEFAULT NULL,
  `is_successful` tinyint(1) DEFAULT NULL,
  `auth_attempted` tinyint(1) DEFAULT 0,
  `auth_successful` tinyint(1) DEFAULT NULL,
  `auth_details` mediumtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `queries_log`
--

INSERT INTO `queries_log` (`id`, `query_text`, `ip_address`, `user_agent`, `query_time`, `file_id`, `is_successful`, `auth_attempted`, `auth_successful`, `auth_details`) VALUES
(1, 'ما نتيجة جيفارا', '185.80.140.104', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 05:58:02', NULL, 0, 0, 0, ''),
(2, 'جيفارا', '185.80.140.104', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 05:58:28', NULL, 0, 0, 0, ''),
(3, '1056', '185.80.140.104', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 05:58:38', NULL, 0, 0, 0, ''),
(4, '12345', '185.80.140.104', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 05:58:46', NULL, 0, 0, 0, ''),
(5, 'جيفارا ', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 07:56:51', NULL, 0, 0, 0, ''),
(6, 'جيفارا', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 07:56:54', NULL, 0, 0, 0, ''),
(7, 'جيفارا حلمي محسن', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 07:57:01', NULL, 0, 0, 0, ''),
(8, '1056', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 07:57:08', NULL, 0, 0, 0, ''),
(14, '1057', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:06:55', NULL, 0, 0, 0, ''),
(13, '1056', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:06:31', 4, 1, 0, 0, ''),
(12, 'جيفارا', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:06:25', NULL, 0, 0, 0, ''),
(15, '1058', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:07:01', NULL, 0, 0, 0, ''),
(16, '1056', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:07:06', 4, 1, 0, 0, ''),
(17, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:07:22', 2, 0, 1, 0, ''),
(18, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:07:37', 2, 0, 1, 0, ''),
(19, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:07:44', 2, 0, 1, 0, ''),
(20, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:07:51', 2, 0, 1, 0, ''),
(21, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:08:02', 2, 0, 1, 0, ''),
(22, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:08:14', 2, 0, 1, 0, ''),
(23, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:13:35', 2, 0, 1, 0, ''),
(24, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:13:56', 2, 0, 1, 0, ''),
(25, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:14:05', 2, 0, 1, 0, ''),
(26, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:14:16', 2, 0, 1, 0, ''),
(27, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:14:26', 2, 0, 1, 0, ''),
(28, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:14:53', 2, 0, 1, 0, ''),
(29, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:15:01', 2, 0, 1, 0, ''),
(30, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:15:13', 2, 0, 1, 0, ''),
(31, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:15:20', 2, 0, 1, 0, ''),
(32, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:15:28', 2, 0, 1, 0, ''),
(33, 'نتائج', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:30:08', 2, 0, 1, 0, ''),
(34, 'نجم', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:30:22', NULL, 0, 0, 0, ''),
(35, 'حلمي', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:30:30', 2, 0, 1, 0, ''),
(36, 'حلمي', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:30:37', 2, 0, 1, 0, ''),
(37, 'حلمي', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:30:47', 2, 0, 1, 0, ''),
(38, 'حلمي', '185.240.64.203', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Mobile Safari/537.36 EdgA/113.0.1774.63', '2025-07-02 08:30:53', 2, 0, 1, 0, ''),
(39, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:40:31', 2, 0, 1, 0, ''),
(40, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:40:49', 2, 0, 1, 0, ''),
(41, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:41:01', 2, 0, 1, 0, ''),
(42, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:41:13', 2, 0, 1, 0, ''),
(43, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:41:30', 2, 0, 1, 0, ''),
(44, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:41:33', 2, 0, 1, 0, ''),
(45, 'حلمي', '185.80.140.199', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 08:41:40', 2, 0, 1, 0, ''),
(46, 'نتيجة جيفارا', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 21:06:26', NULL, 0, 0, 0, ''),
(47, 'نتيجة جيفارا', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 21:06:30', NULL, 0, 0, 0, ''),
(48, 'نتيجة جيفارا', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 21:06:33', NULL, 0, 0, 0, ''),
(49, 'جيفارا', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 21:06:41', NULL, 0, 0, 0, ''),
(50, 'حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 21:06:50', 2, 0, 1, 0, ''),
(51, 'ماهي نتيجة جيفارا حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 22:48:13', 2, 0, 1, 0, ''),
(52, 'ماهي نتيجة جيفارا حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 22:48:31', 2, 0, 1, 0, ''),
(53, 'جيفارا ', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:42:29', NULL, 0, 0, 0, ''),
(54, 'جيفارا حلمي محسن صالح ', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:42:38', NULL, 0, 0, 0, ''),
(55, 'حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:42:48', 2, 0, 1, 0, ''),
(56, 'حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:44:00', 2, 0, 1, 0, ''),
(57, 'حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:44:02', 2, 0, 1, 0, ''),
(58, 'حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:49:40', 2, 0, 1, 0, ''),
(59, 'جيفارا حلمي محسن صالح ', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:49:59', NULL, 0, 0, 0, ''),
(60, 'حلمي', '185.240.64.189', 'Mozilla/5.0 (Linux; Android 10; POCO X2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Mobile Safari/537.36', '2025-07-02 23:50:08', 2, 0, 1, 0, '');

-- --------------------------------------------------------

--
-- بنية الجدول `record_data`
--

CREATE TABLE `record_data` (
  `id` int(11) NOT NULL,
  `record_instance_id` int(11) NOT NULL COMMENT 'معرف يجمع كل حقول سجل واحد',
  `template_id` int(11) NOT NULL,
  `field_id` int(11) NOT NULL,
  `field_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `record_instances`
--

CREATE TABLE `record_instances` (
  `record_instance_id` varchar(255) NOT NULL,
  `template_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `record_templates`
--

CREATE TABLE `record_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `record_templates`
--

INSERT INTO `record_templates` (`id`, `template_name`, `description`, `created_at`) VALUES
(1, 'طالب', 'نتائج', '2025-07-03 01:19:30');

-- --------------------------------------------------------

--
-- بنية الجدول `templates`
--

CREATE TABLE `templates` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `templates`
--

INSERT INTO `templates` (`id`, `name`, `created_at`) VALUES
(1, 'استبيان', '2025-07-07 23:18:21'),
(2, 'بيانات', '2025-07-08 00:38:00');

-- --------------------------------------------------------

--
-- بنية الجدول `template_fields`
--

CREATE TABLE `template_fields` (
  `id` int(11) NOT NULL,
  `template_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL COMMENT 'اسم الحقل (مثال: الرقم الجامعي)',
  `field_type` varchar(50) NOT NULL DEFAULT 'text' COMMENT 'نوع الحقل (text, number, date)',
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `template_fields`
--

INSERT INTO `template_fields` (`id`, `template_id`, `field_name`, `field_type`, `is_required`, `sort_order`) VALUES
(1, 1, 'رقم الطالب', 'text', 0, 0),
(2, 1, 'اسم الطالب', 'text', 0, 0),
(3, 1, 'المدرسة', 'text', 0, 0);

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'alredfani', '$2y$10$7mlLsxHqWyohJhGkQuYYo.hq3S3Kahb6L2erzTl.3eNerL5IWlWO2', 'admin', '2025-07-02 03:18:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `fields`
--
ALTER TABLE `fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `file_protection`
--
ALTER TABLE `file_protection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indexes for table `queries_log`
--
ALTER TABLE `queries_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indexes for table `record_data`
--
ALTER TABLE `record_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_record_instance` (`record_instance_id`),
  ADD KEY `fk_record_data_template_id` (`template_id`),
  ADD KEY `fk_record_data_field_id` (`field_id`);

--
-- Indexes for table `record_instances`
--
ALTER TABLE `record_instances`
  ADD PRIMARY KEY (`record_instance_id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `record_templates`
--
ALTER TABLE `record_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_name` (`template_name`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `template_fields`
--
ALTER TABLE `template_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fields`
--
ALTER TABLE `fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `file_protection`
--
ALTER TABLE `file_protection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `queries_log`
--
ALTER TABLE `queries_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `record_data`
--
ALTER TABLE `record_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `record_templates`
--
ALTER TABLE `record_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `template_fields`
--
ALTER TABLE `template_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- قيود الجداول المحفوظة
--

--
-- القيود للجدول `fields`
--
ALTER TABLE `fields`
  ADD CONSTRAINT `fields_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- القيود للجدول `record_data`
--
ALTER TABLE `record_data`
  ADD CONSTRAINT `fk_record_data_field_id` FOREIGN KEY (`field_id`) REFERENCES `fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_record_data_template_id` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE;

--
-- القيود للجدول `record_instances`
--
ALTER TABLE `record_instances`
  ADD CONSTRAINT `record_instances_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- القيود للجدول `template_fields`
--
ALTER TABLE `template_fields`
  ADD CONSTRAINT `template_fields_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `record_templates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
