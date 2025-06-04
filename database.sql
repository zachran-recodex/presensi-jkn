-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 04, 2025 at 08:22 PM
-- Server version: 8.0.35
-- PHP Version: 8.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `presensi_jkn`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED DEFAULT NULL,
  `type` enum('clock_in','clock_out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_time` time NOT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_valid_location` tinyint(1) NOT NULL DEFAULT '0',
  `distance_from_office` decimal(8,2) DEFAULT NULL,
  `status` enum('success','failed','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `device_info` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `face_recognition_result` json DEFAULT NULL,
  `face_similarity_score` decimal(3,2) DEFAULT NULL,
  `is_late` tinyint(1) NOT NULL DEFAULT '0',
  `late_minutes` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `failure_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `location_id` bigint UNSIGNED NOT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `join_date` date NOT NULL,
  `work_start_time` time NOT NULL DEFAULT '08:00:00',
  `work_end_time` time NOT NULL DEFAULT '17:00:00',
  `is_flexible_time` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `location_id`, `employee_id`, `phone`, `position`, `department`, `join_date`, `work_start_time`, `work_end_time`, `is_flexible_time`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 6, 1, 'EMP000', '081112354044', 'General Manager', 'Human Capital', '2020-01-15', '08:00:00', '17:00:00', 0, 'active', 'Alvie tukanng ngocok', '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(2, 7, 2, 'EMP001', '081234567890', 'Software Developer', 'IT', '2024-01-15', '08:00:00', '17:00:00', 0, 'active', 'Senior developer dengan pengalaman 5 tahun', '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(3, 8, 3, 'EMP002', '081234567891', 'Marketing Manager', 'Marketing', '2024-02-01', '08:30:00', '17:30:00', 1, 'active', 'Manager marketing dengan track record excellent', '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(4, 9, 4, 'EMP003', '081234567892', 'Accountant', 'Finance', '2024-01-03', '08:00:00', '16:00:00', 0, 'active', 'Staff accounting berpengalaman', '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(5, 10, 5, 'EMP004', '081234567893', 'HR Specialist', 'Human Resources', '2024-03-10', '08:00:00', '17:00:00', 0, 'active', 'Spesialis recruitment dan employee relations', '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(6, 11, 1, 'EMP005', '081234567894', 'Sales Executive', 'Sales', '2024-02-20', '09:00:00', '18:00:00', 1, 'active', 'Sales executive dengan target tinggi', '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(7, 12, 2, 'EMP006', '081234567895', 'Graphic Designer', 'Creative', '2023-12-01', '08:30:00', '17:30:00', 0, 'inactive', 'Designer grafis yang sedang cuti panjang', '2025-06-04 13:08:13', '2025-06-04 13:08:13');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `radius` int NOT NULL DEFAULT '100',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Jakarta',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `address`, `latitude`, `longitude`, `radius`, `timezone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Kantor Pusat Jakarta', 'Jl. Sudirman No. 123, Jakarta Pusat, DKI Jakarta', -6.20880000, 106.84560000, 100, 'Asia/Jakarta', 1, '2025-06-04 13:08:10', '2025-06-04 13:08:10'),
(2, 'Cabang Bandung', 'Jl. Braga No. 45, Bandung, Jawa Barat', -6.91750000, 107.61910000, 150, 'Asia/Jakarta', 1, '2025-06-04 13:08:10', '2025-06-04 13:08:10'),
(3, 'Cabang Surabaya', 'Jl. Tunjungan No. 67, Surabaya, Jawa Timur', -7.25750000, 112.75210000, 120, 'Asia/Jakarta', 1, '2025-06-04 13:08:10', '2025-06-04 13:08:10'),
(4, 'Kantor Regional Medan', 'Jl. Imam Bonjol No. 89, Medan, Sumatera Utara', 3.59520000, 98.67220000, 100, 'Asia/Jakarta', 1, '2025-06-04 13:08:10', '2025-06-04 13:08:10'),
(5, 'Warehouse Tangerang', 'Jl. Raya Serpong No. 234, Tangerang, Banten', -6.22930000, 106.68940000, 200, 'Asia/Jakarta', 1, '2025-06-04 13:08:10', '2025-06-04 13:08:10');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2025_06_01_084146_create_locations_table', 1),
(5, '2025_06_01_084217_create_employees_table', 1),
(6, '2025_06_01_084249_create_attendances_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `face_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`, `role`, `face_id`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'super-admin', 'Administrator', 'admin@jakakuasanusantara.web.id', '$2y$12$mt1ZzO1IJ.mrgaWU1pURhO6Nw3LCg1lwfF5wHJ/rZNz1JTy0iwcLC', 'admin', NULL, 1, NULL, '2025-06-04 13:08:10', '2025-06-04 13:08:10'),
(2, 'admin', 'Admin Backup', 'backup@jakakuasanusantara.web.id', '$2y$12$IIpqfjbcSs.z8HapBeA/b.VK8Q/iJw.xCLgBn04gbeVmpVHsuXmka', 'admin', NULL, 1, NULL, '2025-06-04 13:08:10', '2025-06-04 13:08:10'),
(3, 'hr-manager', 'HR Manager', 'hr@jakakuasanusantara.web.id', '$2y$12$.zMGPU9nADXtGxdug9vDAO12eYmpv1BxLAcl3iYerUh5UQjgMTM7i', 'admin', NULL, 1, NULL, '2025-06-04 13:08:11', '2025-06-04 13:08:11'),
(4, 'zachranraze', 'Zachran Razendra', 'zachranraaze@jakakuasanusantara.web.id', '$2y$12$VG5PQ7eEjieHFTzlRgHG2OccNYsEfmHPBZIzxyQcRPWLhfDDc3W9.', 'admin', NULL, 1, NULL, '2025-06-04 13:08:11', '2025-06-04 13:08:11'),
(5, 'sinyo', 'Sinyo Simpers', 'sinyo@jakakuasanusantara.web.id', '$2y$12$u./DBmgE2uwBiZ2uuSi8yeATCsLcRgSIDNAOz7t7S2ZhEaoV0SJoC', 'admin', NULL, 1, NULL, '2025-06-04 13:08:11', '2025-06-04 13:08:11'),
(6, 'alvie', 'Alvie Dharia Safaraz', 'alive@jakakuasanusantara.web.id', '$2y$12$5poZxGrwgB8Qdrmh.M0s.OfLyPXyWcYixbMhlCiMGOFJloXVy0tJi', 'user', NULL, 1, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(7, 'budi.santoso', 'Budi Santoso', 'budi.santoso@jakakuasanusantara.web.id', '$2y$12$l1UBgPs6DwMoBrkWCGSRDeB2DnjpYN1RCZqtvErimKIw5TWUo3c7W', 'user', NULL, 1, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(8, 'siti.nurhaliza', 'Siti Nurhaliza', 'siti.nurhaliza@jakakuasanusantara.web.id', '$2y$12$FIYDe6QNwifZZNTFEYyCx.Zx0MTiOO0Axd2Pap9QhVpZRdo1wdcDy', 'user', NULL, 1, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(9, 'ahmad.fauzi', 'Ahmad Fauzi', 'ahmad.fauzi@jakakuasanusantara.web.id', '$2y$12$UXLpooXjaNmKVmycVm83mO0mFeAaSF5QsPU2mFXl2yBDfbcY70b9q', 'user', NULL, 1, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(10, 'dewi.lestari', 'Dewi Lestari', 'dewi.lestari@jakakuasanusantara.web.id', '$2y$12$9WmHY5UGd5jiHIYe0YTNj.1Qr3AcwmIvgbWNH3z47uVo9GPEEk/mC', 'user', NULL, 1, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(11, 'rudi.hermawan', 'Rudi Hermawan', 'rudi.hermawan@jakakuasanusantara.web.id', '$2y$12$AAzSr4N5PLPBEKRO/h8Bz.OdSpjQZC65xax.x1uY8wUwmYKfRQMUe', 'user', NULL, 1, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13'),
(12, 'maria.gonzales', 'Maria Gonzales', 'maria.gonzales@jakakuasanusantara.web.id', '$2y$12$XUx.BlY0mzBdQGN66aLmO.J5VKfnoVtQUGMv3pG7cMJfhbibfSO4K', 'user', NULL, 0, NULL, '2025-06-04 13:08:13', '2025-06-04 13:08:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_daily_attendance` (`user_id`,`type`,`attendance_date`),
  ADD KEY `attendances_location_id_foreign` (`location_id`),
  ADD KEY `attendances_user_id_attendance_date_index` (`user_id`,`attendance_date`),
  ADD KEY `attendances_attendance_date_type_index` (`attendance_date`,`type`),
  ADD KEY `attendances_status_index` (`status`),
  ADD KEY `attendances_user_id_type_attendance_date_index` (`user_id`,`type`,`attendance_date`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  ADD KEY `employees_user_id_foreign` (`user_id`),
  ADD KEY `employees_employee_id_index` (`employee_id`),
  ADD KEY `employees_status_index` (`status`),
  ADD KEY `employees_location_id_status_index` (`location_id`,`status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locations_latitude_longitude_index` (`latitude`,`longitude`),
  ADD KEY `locations_is_active_index` (`is_active`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
