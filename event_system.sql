-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2025 at 10:05 AM
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
-- Database: `event_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `capacity` int(11) NOT NULL DEFAULT 100,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `date`, `location`, `capacity`, `created_at`, `updated_at`) VALUES
(13, 'cooooooool', '3213', '2025-11-27', 'dwqd', 100, '2025-10-26 23:57:16', '2025-10-27 01:02:56'),
(14, 'qw', 'wq', '2025-11-10', 'wdqw', 11, '2025-10-30 00:29:09', '2025-10-30 00:29:09'),
(15, '23213', '32132', '2025-12-03', '321', 1, '2025-11-10 01:49:30', '2025-11-10 01:49:30');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_10_22_085645_add_role_to_users_table', 2),
(6, '2025_10_25_075245_create_events_table', 3),
(7, '2025_10_25_080022_create_rsvps_table', 4),
(8, '2025_10_25_083624_create_seats_table', 5),
(9, '2025_10_25_084457_add_seat_id_to_rsvps_table', 6),
(10, '2025_10_26_070420_create_events_table', 7),
(11, '2025_10_27_081038_add_price_to_seats_table', 8),
(12, '2025_10_27_082337_update_seat_status_enum_in_seats_table', 9),
(13, '2025_10_29_103756_add_email_to_rsvps_table', 10),
(14, '2025_10_30_082742_add_seat_number_to_seats_table', 11);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 2, 'auth_token', 'e3e6ad128f1012c9c8dba96b417a74443f107e8c12481b06b4836b48c822a845', '[\"*\"]', NULL, NULL, '2025-10-22 01:42:50', '2025-10-22 01:42:50'),
(2, 'App\\Models\\User', 2, 'auth_token', 'dd059d36e5d73b94a4c70324a24e79c8e1081116de06e7d4dcc92ea229cfb856', '[\"*\"]', '2025-10-24 00:00:47', NULL, '2025-10-23 23:55:32', '2025-10-24 00:00:47'),
(3, 'App\\Models\\User', 2, 'auth_token', '02530a9506eff39ac72114b5330078dffe1d1af76a177095404458f799a4d6cd', '[\"*\"]', '2025-10-24 22:56:19', NULL, '2025-10-24 00:01:35', '2025-10-24 22:56:19'),
(4, 'App\\Models\\User', 3, 'auth_token', 'cc40d1af6b20349b35f9607e4d8e5eb90cfe5923846d35c8d6ec5786b96b5303', '[\"*\"]', '2025-10-24 22:57:01', NULL, '2025-10-24 22:56:27', '2025-10-24 22:57:01'),
(5, 'App\\Models\\User', 2, 'auth_token', '058328028a39c2bfb0621135da982d3895aaae9c74c51b0d5c0f49001f15fa4f', '[\"*\"]', '2025-10-24 22:57:33', NULL, '2025-10-24 22:57:10', '2025-10-24 22:57:33'),
(6, 'App\\Models\\User', 2, 'auth_token', 'b4187f77bd59153dd45219718818884604d7ce18c9eb3357f800b0dedf64335f', '[\"*\"]', '2025-10-24 23:01:27', NULL, '2025-10-24 22:57:39', '2025-10-24 23:01:27'),
(7, 'App\\Models\\User', 2, 'auth_token', '1fcc34a50be8d0672c74fb30999494e55fdfd28d67875ef03605794862f50315', '[\"*\"]', '2025-10-24 23:13:41', NULL, '2025-10-24 23:01:34', '2025-10-24 23:13:41'),
(9, 'App\\Models\\User', 2, 'auth_token', '32f39e4aac484b1b7b484067ba83c017967fd1bd6a1373cea35ab82f2b1e6972', '[\"*\"]', '2025-10-24 23:14:43', NULL, '2025-10-24 23:14:10', '2025-10-24 23:14:43'),
(24, 'App\\Models\\User', 2, 'auth_token', 'b90c939c7f5f561eb95b73d0fd2d1ff76496771c66cdb5420457030191dc078d', '[\"*\"]', '2025-10-26 23:53:46', NULL, '2025-10-26 23:53:39', '2025-10-26 23:53:46'),
(39, 'App\\Models\\User', 4, 'auth_token', '839f4fd6fae390ee1e4b27d1f4b6c8f553585b0ccf7b0e8e2c5e5db087a09866', '[\"*\"]', '2025-10-31 01:30:06', NULL, '2025-10-30 23:23:00', '2025-10-31 01:30:06'),
(40, 'App\\Models\\User', 2, 'auth_token', 'ba60ca0268c3ebe3bf7de05fc4128dee964bca19df622453af3892e4ea8c855a', '[\"*\"]', '2025-11-01 00:55:17', NULL, '2025-11-01 00:47:43', '2025-11-01 00:55:17'),
(41, 'App\\Models\\User', 2, 'auth_token', 'b31f4d951d7a640d95dc3b1c2e98f3cc65cc23aa781a0c54d5b15c0a5c7ce0d2', '[\"*\"]', '2025-11-01 00:58:19', NULL, '2025-11-01 00:58:17', '2025-11-01 00:58:19'),
(42, 'App\\Models\\User', 4, 'auth_token', 'd6f7cea3cae9333e8eda355d2840d900ec5541ea0337e86670ce4e9413d40b34', '[\"*\"]', '2025-11-01 01:00:26', NULL, '2025-11-01 01:00:21', '2025-11-01 01:00:26'),
(43, 'App\\Models\\User', 2, 'auth_token', '75bd5c88af5f5f8a5dbe51c06d4768793eccec8ce395199e0d7658348455cdcf', '[\"*\"]', '2025-11-01 02:12:45', NULL, '2025-11-01 01:05:21', '2025-11-01 02:12:45'),
(44, 'App\\Models\\User', 4, 'auth_token', 'b6f41ed740f92990982946d7fbfcd1a56886ebc31bddef07c5ac16c314581c23', '[\"*\"]', '2025-11-01 02:26:43', NULL, '2025-11-01 02:13:00', '2025-11-01 02:26:43'),
(49, 'App\\Models\\User', 5, 'auth_token', '0eeadc9b8a306aaa2ce42531051e27e0af3477cd1c5a56297f135c4e5756be7d', '[\"*\"]', NULL, NULL, '2025-11-03 01:39:03', '2025-11-03 01:39:03'),
(50, 'App\\Models\\User', 4, 'auth_token', '9e32627c62549a25322c726ba7dc4259f1c7609f644fb522ac70d9023b7cd1c9', '[\"*\"]', '2025-11-06 01:35:33', NULL, '2025-11-06 01:31:25', '2025-11-06 01:35:33'),
(51, 'App\\Models\\User', 2, 'auth_token', 'c64394e1f67930771020a7232a2ccba7e131ab6b94173642dfcf55ccc7719b20', '[\"*\"]', NULL, NULL, '2025-11-06 01:36:04', '2025-11-06 01:36:04'),
(52, 'App\\Models\\User', 4, 'auth_token', 'd911b528c120f777d1c04ff4934cab69bbc5d8c69d2050e7ed4e64388a46087d', '[\"*\"]', '2025-11-06 01:55:56', NULL, '2025-11-06 01:55:45', '2025-11-06 01:55:56'),
(53, 'App\\Models\\User', 2, 'auth_token', '999bd00eca70171dc2c450cc5034815da15f9df83f20391865a50aff7e906d22', '[\"*\"]', '2025-11-06 01:57:07', NULL, '2025-11-06 01:56:54', '2025-11-06 01:57:07'),
(54, 'App\\Models\\User', 2, 'auth_token', 'f611cc8715fed2b128e73d3a16bbeba5413876636f2765c687e4cb641875e8d6', '[\"*\"]', NULL, NULL, '2025-11-06 02:12:14', '2025-11-06 02:12:14'),
(55, 'App\\Models\\User', 2, 'auth_token', '0b8699b44c998f976bb7b396d0dc7d76981a23b924075dd0ce6502c28998b4ff', '[\"*\"]', NULL, NULL, '2025-11-06 02:12:33', '2025-11-06 02:12:33'),
(56, 'App\\Models\\User', 2, 'auth_token', '2ea725cee3777ed451e44aa8fb93feeae00425e8929aa04032d013a2cf7c2543', '[\"*\"]', NULL, NULL, '2025-11-06 02:12:50', '2025-11-06 02:12:50'),
(57, 'App\\Models\\User', 2, 'auth_token', 'ce6cbf9690f811afa7c658134119eb4f4317d7ad308d92eb684ae44111d5aa3a', '[\"*\"]', '2025-11-06 02:18:41', NULL, '2025-11-06 02:13:25', '2025-11-06 02:18:41'),
(58, 'App\\Models\\User', 2, 'auth_token', '31325a7e7ff1d768702f583b045bd0ac198e3c70b6762ce297fefdaf9fdec7db', '[\"*\"]', NULL, NULL, '2025-11-07 01:15:39', '2025-11-07 01:15:39'),
(59, 'App\\Models\\User', 2, 'auth_token', '4f4079ff06eadefd33e7d6748a84897176ad0593ca9873f8b118eac8ae82d3f4', '[\"*\"]', '2025-11-07 01:33:18', NULL, '2025-11-07 01:15:41', '2025-11-07 01:33:18'),
(60, 'App\\Models\\User', 4, 'auth_token', '548d574c67857e9d050b5778096d32a4d1406e0dca62c5d28d76b77d40c1bfa7', '[\"*\"]', '2025-11-07 01:50:54', NULL, '2025-11-07 01:33:44', '2025-11-07 01:50:54'),
(61, 'App\\Models\\User', 2, 'auth_token', 'a737afdb600a186c0d7f55b38fd4ab256357b17ad9a3cd03120d1513daead790', '[\"*\"]', '2025-11-10 00:47:41', NULL, '2025-11-07 01:52:40', '2025-11-10 00:47:41'),
(62, 'App\\Models\\User', 4, 'auth_token', 'f86419f7c836847e163cd0853c3c301c97da1c57153da48ca0d1cfc32bd4c560', '[\"*\"]', NULL, NULL, '2025-11-10 01:05:15', '2025-11-10 01:05:15'),
(63, 'App\\Models\\User', 4, 'auth_token', 'fb044e50bb8af7f5c1ed7ca994295eb70f6dd2deaa910589fe79da1e1e37cec6', '[\"*\"]', '2025-11-10 01:07:27', NULL, '2025-11-10 01:06:12', '2025-11-10 01:07:27'),
(64, 'App\\Models\\User', 4, 'auth_token', 'a6489f55d68a822c2721d85106fb5825cfa662cbcbc93467408a810f4bd4e555', '[\"*\"]', '2025-11-10 01:11:36', NULL, '2025-11-10 01:08:39', '2025-11-10 01:11:36'),
(65, 'App\\Models\\User', 4, 'auth_token', '62d2f5e9e22dca7a069bf05dc542fd530ac65144c41179a970254cd2a5456cbe', '[\"*\"]', '2025-11-10 01:32:51', NULL, '2025-11-10 01:27:58', '2025-11-10 01:32:51'),
(66, 'App\\Models\\User', 2, 'auth_token', '26c8e0fa034e4077f4d8a4b53edf07da59ebdf3fac934907d92774ca0018ce1b', '[\"*\"]', '2025-11-10 01:50:22', NULL, '2025-11-10 01:40:30', '2025-11-10 01:50:22'),
(67, 'App\\Models\\User', 4, 'auth_token', '00628ac83f118fec6dec8ca3a8928196c30ac18c52b814feff3b378fc193b202', '[\"*\"]', '2025-11-10 01:50:48', NULL, '2025-11-10 01:50:40', '2025-11-10 01:50:48'),
(68, 'App\\Models\\User', 2, 'auth_token', '2c15593aad19e596648417551894f4ffbde5bd9f1edc7f243cf4b42e62698b59', '[\"*\"]', '2025-11-10 01:51:26', NULL, '2025-11-10 01:51:24', '2025-11-10 01:51:26'),
(69, 'App\\Models\\User', 4, 'auth_token', '3cb145318e1715102fb57396ee92c4952beb67d3f8510e3056464f67a85453ed', '[\"*\"]', '2025-11-10 01:52:13', NULL, '2025-11-10 01:51:59', '2025-11-10 01:52:13'),
(70, 'App\\Models\\User', 2, 'auth_token', '5cdddf0f28efb5fc3fe2de556cae43cf0e271d576f56f3d69f7b2d22a2677ef1', '[\"*\"]', '2025-11-15 00:56:59', NULL, '2025-11-10 01:57:31', '2025-11-15 00:56:59');

-- --------------------------------------------------------

--
-- Table structure for table `rsvps`
--

CREATE TABLE `rsvps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `status` enum('confirmed','canceled') NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `seat_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rsvps`
--

INSERT INTO `rsvps` (`id`, `event_id`, `user_id`, `email`, `guest_name`, `guest_email`, `status`, `created_at`, `updated_at`, `seat_id`) VALUES
(23, 13, NULL, NULL, 'Kenneth', 'watermelin0413@gmail.com', 'confirmed', '2025-10-30 02:36:06', '2025-11-10 01:22:15', 67),
(27, 13, 4, 'watermelin0413@gmail.com', NULL, NULL, 'confirmed', '2025-10-31 00:02:34', '2025-11-10 01:28:23', 13),
(28, 14, 4, 'watermelin0413@gmail.com', NULL, NULL, 'canceled', '2025-10-31 01:30:06', '2025-11-10 01:58:22', 101),
(29, 14, NULL, NULL, 'Kenneth', 'watermelin0413@gmail.com', 'confirmed', '2025-11-10 01:33:08', '2025-11-10 01:33:08', 110),
(30, 15, 4, 'watermelin0413@gmail.com', NULL, NULL, 'canceled', '2025-11-10 01:50:48', '2025-11-10 01:52:06', 112);

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seat_number` varchar(255) DEFAULT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(255) NOT NULL,
  `status` enum('available','processing','booked','blocked') DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `seat_number`, `event_id`, `label`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 13, 'A1', 'booked', '2025-10-26 23:57:16', '2025-10-29 02:41:10'),
(2, NULL, 13, 'A2', 'booked', '2025-10-26 23:57:16', '2025-10-29 02:47:34'),
(3, NULL, 13, 'A3', 'booked', '2025-10-26 23:57:16', '2025-10-29 02:48:20'),
(4, NULL, 13, 'A4', 'booked', '2025-10-26 23:57:16', '2025-10-29 02:49:17'),
(5, NULL, 13, 'A5', 'booked', '2025-10-26 23:57:16', '2025-10-29 02:51:02'),
(6, NULL, 13, 'A6', 'booked', '2025-10-26 23:57:16', '2025-10-29 03:09:28'),
(7, NULL, 13, 'A7', 'booked', '2025-10-26 23:57:16', '2025-10-30 23:59:14'),
(8, NULL, 13, 'A8', 'booked', '2025-10-26 23:57:16', '2025-11-01 02:20:06'),
(9, NULL, 13, 'A9', 'booked', '2025-10-26 23:57:16', '2025-10-29 03:01:38'),
(10, NULL, 13, 'A10', 'booked', '2025-10-26 23:57:16', '2025-11-01 02:20:22'),
(11, NULL, 13, 'B1', 'booked', '2025-10-26 23:57:16', '2025-11-06 01:35:33'),
(12, NULL, 13, 'B2', 'available', '2025-10-26 23:57:16', '2025-11-10 01:11:24'),
(13, NULL, 13, 'B3', 'booked', '2025-10-26 23:57:16', '2025-11-10 01:28:23'),
(14, NULL, 13, 'B4', 'processing', '2025-10-26 23:57:16', '2025-11-10 01:32:22'),
(15, NULL, 13, 'B5', 'booked', '2025-10-26 23:57:16', '2025-11-10 01:06:26'),
(16, NULL, 13, 'B6', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(17, NULL, 13, 'B7', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(18, NULL, 13, 'B8', 'booked', '2025-10-26 23:57:16', '2025-10-29 03:14:33'),
(19, NULL, 13, 'B9', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(20, NULL, 13, 'B10', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(21, NULL, 13, 'C1', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(22, NULL, 13, 'C2', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(23, NULL, 13, 'C3', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(24, NULL, 13, 'C4', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(25, NULL, 13, 'C5', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(26, NULL, 13, 'C6', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(27, NULL, 13, 'C7', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(28, NULL, 13, 'C8', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(29, NULL, 13, 'C9', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(30, NULL, 13, 'C10', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(31, NULL, 13, 'D1', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(32, NULL, 13, 'D2', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(33, NULL, 13, 'D3', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(34, NULL, 13, 'D4', 'booked', '2025-10-26 23:57:16', '2025-11-01 02:17:03'),
(35, NULL, 13, 'D5', 'booked', '2025-10-26 23:57:16', '2025-11-01 02:16:47'),
(36, NULL, 13, 'D6', 'booked', '2025-10-26 23:57:16', '2025-11-01 02:16:23'),
(37, NULL, 13, 'D7', 'booked', '2025-10-26 23:57:16', '2025-10-29 23:53:23'),
(38, NULL, 13, 'D8', 'booked', '2025-10-26 23:57:16', '2025-10-29 23:57:16'),
(39, NULL, 13, 'D9', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(40, NULL, 13, 'D10', 'booked', '2025-10-26 23:57:16', '2025-10-29 03:08:29'),
(41, NULL, 13, 'E1', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(42, NULL, 13, 'E2', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(43, NULL, 13, 'E3', 'available', '2025-10-26 23:57:16', '2025-10-30 02:52:47'),
(44, NULL, 13, 'E4', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(45, NULL, 13, 'E5', 'available', '2025-10-26 23:57:16', '2025-10-27 00:57:46'),
(46, NULL, 13, 'E6', 'booked', '2025-10-26 23:57:16', '2025-10-27 00:24:59'),
(47, NULL, 13, 'E7', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(48, NULL, 13, 'E8', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(49, NULL, 13, 'E9', 'available', '2025-10-26 23:57:16', '2025-10-30 02:36:42'),
(50, NULL, 13, 'E10', 'booked', '2025-10-26 23:57:16', '2025-10-30 02:29:56'),
(51, NULL, 13, 'F1', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(52, NULL, 13, 'F2', 'available', '2025-10-26 23:57:16', '2025-11-01 02:12:40'),
(53, NULL, 13, 'F3', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(54, NULL, 13, 'F4', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(55, NULL, 13, 'F5', 'available', '2025-10-26 23:57:16', '2025-10-30 02:36:40'),
(56, NULL, 13, 'F6', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(57, NULL, 13, 'F7', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(58, NULL, 13, 'F8', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(59, NULL, 13, 'F9', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(60, NULL, 13, 'F10', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(61, NULL, 13, 'G1', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(62, NULL, 13, 'G2', 'booked', '2025-10-26 23:57:16', '2025-10-30 00:07:58'),
(63, NULL, 13, 'G3', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(64, NULL, 13, 'G4', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(65, NULL, 13, 'G5', 'available', '2025-10-26 23:57:16', '2025-10-27 00:51:06'),
(66, NULL, 13, 'G6', 'available', '2025-10-26 23:57:16', '2025-10-27 00:43:53'),
(67, NULL, 13, 'G7', 'booked', '2025-10-26 23:57:16', '2025-11-10 01:22:15'),
(68, NULL, 13, 'G8', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(69, NULL, 13, 'G9', 'available', '2025-10-26 23:57:16', '2025-10-26 23:57:16'),
(70, NULL, 13, 'G10', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(71, NULL, 13, 'H1', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(72, NULL, 13, 'H2', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(73, NULL, 13, 'H3', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(74, NULL, 13, 'H4', 'booked', '2025-10-26 23:57:17', '2025-10-29 23:57:13'),
(75, NULL, 13, 'H5', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(76, NULL, 13, 'H6', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(77, NULL, 13, 'H7', 'booked', '2025-10-26 23:57:17', '2025-10-30 00:15:31'),
(78, NULL, 13, 'H8', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(79, NULL, 13, 'H9', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(80, NULL, 13, 'H10', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(81, NULL, 13, 'I1', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(82, NULL, 13, 'I2', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(83, NULL, 13, 'I3', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(84, NULL, 13, 'I4', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(85, NULL, 13, 'I5', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(86, NULL, 13, 'I6', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(87, NULL, 13, 'I7', 'booked', '2025-10-26 23:57:17', '2025-10-30 00:45:36'),
(88, NULL, 13, 'I8', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(89, NULL, 13, 'I9', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(90, NULL, 13, 'I10', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(91, NULL, 13, 'J1', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(92, NULL, 13, 'J2', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(93, NULL, 13, 'J3', 'booked', '2025-10-26 23:57:17', '2025-10-30 00:06:38'),
(94, NULL, 13, 'J4', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(95, NULL, 13, 'J5', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(96, NULL, 13, 'J6', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(97, NULL, 13, 'J7', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(98, NULL, 13, 'J8', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(99, NULL, 13, 'J9', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(100, NULL, 13, 'J10', 'available', '2025-10-26 23:57:17', '2025-10-26 23:57:17'),
(101, NULL, 14, 'A1', 'available', '2025-10-30 00:29:09', '2025-11-10 01:58:22'),
(102, NULL, 14, 'A2', 'booked', '2025-10-30 00:29:09', '2025-10-30 00:44:07'),
(103, NULL, 14, 'A3', 'booked', '2025-10-30 00:29:09', '2025-10-31 01:30:06'),
(104, NULL, 14, 'A4', 'booked', '2025-10-30 00:29:09', '2025-10-30 02:24:22'),
(105, NULL, 14, 'A5', 'booked', '2025-10-30 00:29:09', '2025-10-30 23:58:07'),
(106, NULL, 14, 'A6', 'booked', '2025-10-30 00:29:09', '2025-10-30 23:23:09'),
(107, NULL, 14, 'A7', 'available', '2025-10-30 00:29:09', '2025-11-06 02:18:07'),
(108, NULL, 14, 'A8', 'available', '2025-10-30 00:29:09', '2025-10-30 00:29:09'),
(109, NULL, 14, 'A9', 'available', '2025-10-30 00:29:09', '2025-10-30 00:29:09'),
(110, NULL, 14, 'A10', 'booked', '2025-10-30 00:29:09', '2025-11-10 01:33:08'),
(111, NULL, 14, 'B1', 'booked', '2025-10-30 00:29:09', '2025-10-30 00:40:04'),
(112, NULL, 15, 'A1', 'available', '2025-11-10 01:49:30', '2025-11-10 01:52:06');

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(2, 'admin', 'a1@gmail.com', NULL, '$2y$12$smnLuFI/iXhKjtNsH9G4pOE9Ofku0zWzNXCQB3fBm6TGsRNQGnO.q', NULL, '2025-10-22 01:42:42', '2025-10-24 23:15:20', 'admin'),
(3, 'user', 'u1@gmail.com', NULL, '$2y$12$oCApCqVdsWmrOLHaGahH5ulu3v3Fdtbd8zApUQst.3cVH8mHWlJdK', NULL, '2025-10-24 22:56:19', '2025-10-24 22:56:19', 'user'),
(4, 'Kenneth', 'watermelin0413@gmail.com', NULL, '$2y$12$flBrhTziIzE7KS4.WOlz7uPQ1B6Mb8tTtWY/qNxK9KEIsvlGnaoba', NULL, '2025-10-30 23:22:51', '2025-10-30 23:22:51', 'user'),
(5, 'test', 'test@gmail.com', NULL, '$2y$12$PTjvfPRxGsZ1e2s3jq/sXu7sU6eXuexenrU5kiWL.Izrf15k/BYbW', NULL, '2025-11-03 01:38:53', '2025-11-03 01:38:53', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

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
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `rsvps`
--
ALTER TABLE `rsvps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rsvps_event_id_user_id_unique` (`event_id`,`user_id`),
  ADD UNIQUE KEY `rsvps_event_id_guest_email_unique` (`event_id`,`guest_email`),
  ADD KEY `rsvps_user_id_foreign` (`user_id`),
  ADD KEY `rsvps_seat_id_foreign` (`seat_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seats_event_id_foreign` (`event_id`);

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
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `rsvps`
--
ALTER TABLE `rsvps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rsvps`
--
ALTER TABLE `rsvps`
  ADD CONSTRAINT `rsvps_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rsvps_seat_id_foreign` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `rsvps_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
