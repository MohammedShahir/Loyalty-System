-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2025 at 05:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;
--
-- Database: `hair`
--

-- --------------------------------------------------------
--
-- Table structure for table `activity`
--

CREATE TABLE `activity` (
  `id` int(11) NOT NULL,
  `Activity_Name` varchar(20) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`id`, `Activity_Name`)
VALUES (1, 'معالجات الشعر'),
  (2, 'تجميل والعناية بالبش'),
  (3, 'كليهما');
-- --------------------------------------------------------
--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `Card_Name` varchar(20) NOT NULL,
  `Release_Date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Expiration_Date` datetime NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `cards`
--

INSERT INTO `cards` (
    `id`,
    `Card_Name`,
    `Release_Date`,
    `Expiration_Date`
  )
VALUES (
    1,
    'برونزي',
    '0000-00-00 00:00:00',
    '0000-00-00 00:00:00'
  ),
  (
    2,
    'فضي',
    '0000-00-00 00:00:00',
    '0000-00-00 00:00:00'
  ),
  (
    3,
    'ذهبي',
    '0000-00-00 00:00:00',
    '0000-00-00 00:00:00'
  );
-- --------------------------------------------------------
--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `hairdresser`
--

CREATE TABLE `hairdresser` (
  `id` int(11) NOT NULL,
  `Hairdresser_Name` varchar(100) NOT NULL,
  `Hairdresser_Owner` varchar(120) NOT NULL,
  `Call_Num` int(9) NOT NULL,
  `Whats_Num` int(9) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Type_of_Activity` int(11) NOT NULL,
  `Type_of_Card` int(11) NOT NULL,
  `Total_Points` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `hairdresser`
--

INSERT INTO `hairdresser` (
    `id`,
    `Hairdresser_Name`,
    `Hairdresser_Owner`,
    `Call_Num`,
    `Whats_Num`,
    `Address`,
    `Type_of_Activity`,
    `Type_of_Card`,
    `Total_Points`
  )
VALUES (
    3,
    'بقص',
    'قلصق',
    995999999,
    999995999,
    'سسل',
    3,
    3,
    5545
  ),
  (
    4,
    'فيروز',
    'فيروز',
    789654123,
    789654123,
    'الشيخ',
    2,
    2,
    0
  );
-- --------------------------------------------------------
--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES (1, '0001_01_01_000000_create_users_table', 1),
  (2, '0001_01_01_000001_create_cache_table', 1),
  (3, '0001_01_01_000002_create_jobs_table', 1);
-- --------------------------------------------------------
--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- --------------------------------------------------------
--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `Invoice_Num` int(11) NOT NULL,
  `Total_Sales` float NOT NULL,
  `Hairdresser_Id` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (
    `id`,
    `Invoice_Num`,
    `Total_Sales`,
    `Hairdresser_Id`
  )
VALUES (2, 445, 54454, 3),
  (4, 12, 1000, 3),
  (5, 0, 0, 4);
-- --------------------------------------------------------
--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (
    `id`,
    `user_id`,
    `ip_address`,
    `user_agent`,
    `payload`,
    `last_activity`
  )
VALUES (
    '637VxOoB8OnycXW6nTB9iK6fyrYOJgifilomlxIn',
    2,
    '127.0.0.1',
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36',
    'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVGdxdUxmMkpiSng1T2tURE4zdDJQd2QxWGpBU3JTWVJIejZHTXRVQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly9tb2hhbW1lZDo4MDAwL2NhbGN1bGF0aW5nLXBvaW50cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==',
    1758899034
  );
-- --------------------------------------------------------
--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
--
-- Dumping data for table `users`
--

INSERT INTO `users` (
    `id`,
    `name`,
    `email`,
    `email_verified_at`,
    `password`,
    `remember_token`,
    `created_at`,
    `updated_at`
  )
VALUES (
    1,
    'Test User',
    'test@example.com',
    '2025-09-24 16:25:02',
    '$2y$12$VjcWwlJBfx9PyuLvcH28gu0YXIMleoBHfBNPAqm1nPCO6oa/ZOpSe',
    'pJuIkVyfcK',
    '2025-09-24 16:25:02',
    '2025-09-24 16:25:02'
  ),
  (
    2,
    'محمد',
    'mohamad@example.com',
    NULL,
    '$2y$12$BxMKap56FQXq3Ha7hRLS1u3KTnQ7xdyXH0L4cwZGMuinNJPhTEhwa',
    'MCoFl1k4KGutK8KZ82w3PQsNcOfo9BvSZ68YndjjgJ62RYfMsddzb7JUnM5r',
    '2025-09-24 16:25:02',
    '2025-09-24 16:25:02'
  );
--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity`
--
ALTER TABLE `activity`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
ADD PRIMARY KEY (`key`);
--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
ADD PRIMARY KEY (`key`);
--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);
--
-- Indexes for table `hairdresser`
--
ALTER TABLE `hairdresser`
ADD PRIMARY KEY (`id`),
  ADD KEY `Type_of_Activity` (`Type_of_Activity`),
  ADD KEY `Type_of_Card` (`Type_of_Card`);
--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);
--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
ADD PRIMARY KEY (`id`);
--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
ADD PRIMARY KEY (`email`);
--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
ADD PRIMARY KEY (`id`),
  ADD KEY `Hairdresser_Id` (`Hairdresser_Id`);
--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);
--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);
--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity`
--
ALTER TABLE `activity`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 4;
--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 4;
--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `hairdresser`
--
ALTER TABLE `hairdresser`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 5;
--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 4;
--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 3;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `hairdresser`
--
ALTER TABLE `hairdresser`
ADD CONSTRAINT `fk_hairdresser_activity` FOREIGN KEY (`Type_of_Activity`) REFERENCES `activity` (`id`),
  ADD CONSTRAINT `fk_hairdresser_cards` FOREIGN KEY (`Type_of_Card`) REFERENCES `cards` (`id`);
--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
ADD CONSTRAINT `fk_sales_hairdresser` FOREIGN KEY (`Hairdresser_Id`) REFERENCES `hairdresser` (`id`);
COMMIT;

UPDATE `cards`
SET `Expiration_Date` = DATE_ADD(`Release_Date`, INTERVAL 1 YEAR)
WHERE `Expiration_Date` IS NULL
  OR `Expiration_Date` <> DATE_ADD(`Release_Date`, INTERVAL 1 YEAR);
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;