-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2024 at 08:34 AM
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
-- Database: `grace_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address_1` mediumtext NOT NULL,
  `address_2` mediumtext DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(191) NOT NULL,
  `postal_code` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `address_1`, `address_2`, `city`, `state`, `country`, `postal_code`, `user_id`, `created_at`, `updated_at`) VALUES
(39, 'nmnmnmnmn', 'dfdfdfd', 'fgfgf', 'ffdfd', 'Afghanistan', 12345, 1, '2023-12-04 16:16:40', '2023-12-04 16:16:40'),
(40, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', NULL, 'Bulgaria', 71511, 1, '2023-12-04 16:18:18', '2023-12-04 16:18:18'),
(41, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', NULL, 'Bulgaria', 71511, 1, '2023-12-04 16:24:20', '2023-12-04 16:24:20'),
(42, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', 'dddd', 'Egypt', 71511, 1, '2023-12-15 13:35:10', '2023-12-15 13:35:10'),
(43, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', 'dddd', 'Egypt', 71511, 1, '2023-12-15 13:39:31', '2023-12-15 13:39:31'),
(44, 'rewrwerwe', 'fdfsdfdsf', 'dfdsf', 'sdgfdgd', 'Aruba', 32131, 1, '2024-01-08 16:08:42', '2024-01-08 16:08:42'),
(45, 'rewrwerwe', 'fdfsdfdsf', 'dfdsf', 'sdgfdgd', 'Afghanistan', 32131, 1, '2024-01-13 11:06:40', '2024-01-13 11:06:40'),
(46, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', NULL, 'Egypt', 71511, 1, '2024-01-13 11:12:43', '2024-01-13 11:12:43'),
(47, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', NULL, 'Egypt', 71511, 1, '2024-01-20 14:20:47', '2024-01-20 14:20:47'),
(48, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', 'Assiut', 'Egypt', 71511, 2, '2024-01-25 23:45:37', '2024-01-25 23:45:37'),
(49, '5 Al-Mohandiseen ST - Asyut', NULL, 'Asyut', 'assiut', 'Egypt', 71511, 1, '2024-02-16 17:29:53', '2024-02-16 17:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_size` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `product_quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `product_id`, `product_size`, `product_quantity`, `created_at`, `updated_at`) VALUES
(76, 1, 3, 3, 1, '2024-02-17 12:46:44', '2024-02-17 12:46:44'),
(77, 1, 5, 2, 4, '2024-02-17 17:12:48', '2024-02-17 17:12:48'),
(78, 1, 5, 3, 4, '2024-02-17 17:12:48', '2024-02-17 17:12:48');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `main_image` varchar(191) NOT NULL,
  `banner_image` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `main_image`, `banner_image`, `created_at`, `updated_at`) VALUES
(1, 'Men', 'men', '169338880010.png', '169338880066.png', '2023-08-30 06:46:40', '2023-08-30 06:47:07'),
(2, 'Women', 'women', '169338884869.png', '1693388848100.png', '2023-08-30 06:47:28', '2023-08-30 06:47:28'),
(3, 'Kids', 'kids', '169338886733.png', '169338886754.png', '2023-08-30 06:47:47', '2024-04-14 19:11:04');

-- --------------------------------------------------------

--
-- Table structure for table `category_product`
--

CREATE TABLE `category_product` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_product`
--

INSERT INTO `category_product` (`category_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL),
(1, 4, NULL, NULL),
(1, 5, NULL, NULL),
(2, 4, NULL, NULL),
(2, 6, NULL, NULL),
(3, 2, NULL, NULL),
(3, 3, NULL, NULL),
(3, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_subcategory`
--

CREATE TABLE `category_subcategory` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `subcategory_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_subcategory`
--

INSERT INTO `category_subcategory` (`category_id`, `subcategory_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL),
(1, 2, NULL, NULL),
(1, 3, NULL, NULL),
(1, 6, NULL, NULL),
(2, 1, NULL, NULL),
(2, 4, NULL, NULL),
(2, 5, NULL, NULL),
(2, 6, NULL, NULL),
(3, 1, NULL, NULL),
(3, 2, NULL, NULL),
(3, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
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
  `migration` varchar(191) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2022_06_27_132434_create_addresses_table', 1),
(6, '2022_06_27_132512_create_categories_table', 1),
(7, '2022_06_27_132551_create_subcategories_table', 1),
(8, '2022_06_27_132626_create_products_table', 1),
(9, '2022_06_27_132703_create_thumb_images_table', 1),
(10, '2022_06_27_132720_create_carts_table', 1),
(11, '2022_06_27_132734_create_orders_table', 1),
(12, '2022_06_27_132850_create_product_subcategory_table', 1),
(13, '2022_07_02_105540_create_order_items_table', 1),
(14, '2022_07_05_113950_create_product_sizes_table', 1),
(15, '2022_07_05_162124_create_reviews_table', 1),
(16, '2023_06_16_115637_create_category_subcategory_table', 1),
(17, '2023_07_26_224037_create_category_product_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tracking_num` varchar(191) NOT NULL,
  `num_items` int(10) UNSIGNED NOT NULL,
  `total_cost` double UNSIGNED NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `tracking_num`, `num_items`, `total_cost`, `status`, `user_id`, `address_id`, `created_at`, `updated_at`) VALUES
(13, 'GR59219', 7, 1265, 4, 1, 45, '2024-01-13 11:06:41', '2024-01-13 11:13:01'),
(14, 'GR18628', 2, 66, 1, 1, 46, '2024-01-13 11:12:44', '2024-01-13 11:12:44'),
(15, 'GR72180', 4, 394, 3, 1, 47, '2024-01-20 14:20:47', '2024-03-29 02:22:01'),
(16, 'GR38871', 5, 525, 4, 2, 48, '2024-01-25 23:45:37', '2024-01-26 18:29:45'),
(17, 'GR96945', 1, 300, 4, 1, 49, '2024-02-16 17:29:53', '2024-02-16 17:30:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(300) NOT NULL,
  `product_main_image` varchar(191) NOT NULL,
  `product_size` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `product_quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `product_total_price` double UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `product_name`, `product_main_image`, `product_size`, `product_quantity`, `product_total_price`, `order_id`, `created_at`, `updated_at`) VALUES
(32, 'product 4', '170514896861.jpg', 2, 2, 1140, 13, '2024-01-13 11:06:41', '2024-01-13 11:06:41'),
(33, 'product oh', '170514202226.jpg', 1, 3, 75, 13, '2024-01-13 11:06:41', '2024-01-13 11:06:41'),
(34, 'product oh', '170514202226.jpg', 2, 2, 50, 13, '2024-01-13 11:06:42', '2024-01-13 11:06:42'),
(35, 'product 1', '170514205547.jpg', 1, 2, 66, 14, '2024-01-13 11:12:44', '2024-01-13 11:12:44'),
(36, 'product oh', '170514202226.jpg', 1, 1, 25, 15, '2024-01-20 14:20:47', '2024-01-20 14:20:47'),
(37, 'product 2', '170514795266.jpg', 2, 3, 369, 15, '2024-01-20 14:20:47', '2024-01-20 14:20:47'),
(38, 'product 1', '170514205547.jpg', 3, 1, 33, 16, '2024-01-25 23:45:37', '2024-01-25 23:45:37'),
(39, 'product 2', '170514795266.jpg', 2, 2, 246, 16, '2024-01-25 23:45:37', '2024-01-25 23:45:37'),
(40, 'product 2', '170514795266.jpg', 3, 2, 246, 16, '2024-01-25 23:45:37', '2024-01-25 23:45:37'),
(41, 'product 3', '170514856170.jpg', 2, 1, 300, 17, '2024-02-16 17:29:53', '2024-02-16 17:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) NOT NULL,
  `token` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(300) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `short_description` text NOT NULL,
  `long_description` longtext NOT NULL,
  `main_image` varchar(191) NOT NULL,
  `old_price` double(6,2) UNSIGNED DEFAULT NULL,
  `new_price` double(6,2) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `short_description`, `long_description`, `main_image`, `old_price`, `new_price`, `quantity`, `status`, `created_at`, `updated_at`) VALUES
(2, 'product oh', 'product-oh', 'ppppppppp', 'qqqqqqqqqqq', '170514202226.jpg', 44.00, 25.00, 44, 1, '2024-01-13 08:33:42', '2024-01-13 10:31:46'),
(3, 'product 1', 'product-1', 'gfdgfdgfdg', 'gffdgfdgfdgdfgfdgfg', '170514205547.jpg', 33.00, 33.00, 23, 1, '2024-01-13 08:34:15', '2024-01-13 08:34:15'),
(4, 'product 2', 'product-2', 'hgfhhhhhhh', 'gfhgfhghhgfhgfhgfh', '170514795266.jpg', 123.00, 123.00, 10, 1, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(5, 'product 3', 'product-3', 'fgggggggggggggggg', 'rrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrrr', '170514856170.jpg', 333.00, 300.00, 98, 1, '2024-01-13 10:22:41', '2024-04-11 16:47:25'),
(6, 'product 4', 'product-4', 'fgjkdhgkfdjhg', 'klgjflkdgjlkfd jglkgjldkfgjkjkljkjlkjklgjf lkdgjlkfdjglkgjldkfgjkjkljkjlkjkl gjflkdgjlkfdjglkg jldkfgjkjkljkjlkjklgjflkdgjlkfd jglkgjldkfgjkjkljkj lkjklgjflkdgj lkfdjglkgjld kfgjkjkljkjlkjkl gjflkd gjlkfdjglkgjldkfg jkjkljkjlkjklgjflkdgjlkf djglkgjldkfgjkjk ljkjlkjklgjflkd gjlkfdjglkgjldkfgjkjkljkjlk jklgjflkdgjlkfdjglkgjldkfgjkjkljkjlkj klgj flkdgjlkfdjglkgjldkfgjk jkljkjlkjklgjflkdgjlkfdj glkgjl dkfgjkjkljk jlkjklgjflkdg  jlkfdjglkgjld kfgjkjkljkjlkjklgj', '170514896861.jpg', 570.00, 570.00, 35, 1, '2024-01-13 10:29:28', '2024-04-11 16:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `size` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `size`, `product_id`, `created_at`, `updated_at`) VALUES
(6, 1, 3, '2024-01-13 08:34:16', '2024-01-13 08:34:16'),
(7, 1, 4, '2024-01-13 10:12:34', '2024-01-13 10:12:34'),
(8, 2, 4, '2024-01-13 10:12:34', '2024-01-13 10:12:34'),
(9, 3, 4, '2024-01-13 10:12:34', '2024-01-13 10:12:34'),
(10, 4, 4, '2024-01-13 10:12:34', '2024-01-13 10:12:34'),
(11, 5, 4, '2024-01-13 10:12:34', '2024-01-13 10:12:34'),
(18, 1, 2, '2024-01-13 10:31:47', '2024-01-13 10:31:47'),
(19, 2, 2, '2024-01-13 10:31:47', '2024-01-13 10:31:47'),
(20, 3, 2, '2024-01-13 10:31:47', '2024-01-13 10:31:47'),
(28, 2, 6, '2024-04-11 16:03:08', '2024-04-11 16:03:08'),
(29, 3, 6, '2024-04-11 16:03:08', '2024-04-11 16:03:08'),
(30, 4, 6, '2024-04-11 16:03:08', '2024-04-11 16:03:08'),
(31, 2, 5, '2024-04-11 16:47:28', '2024-04-11 16:47:28'),
(32, 3, 5, '2024-04-11 16:47:29', '2024-04-11 16:47:29'),
(33, 4, 5, '2024-04-11 16:47:29', '2024-04-11 16:47:29');

-- --------------------------------------------------------

--
-- Table structure for table `product_subcategory`
--

CREATE TABLE `product_subcategory` (
  `subcategory_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_subcategory`
--

INSERT INTO `product_subcategory` (`subcategory_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, NULL),
(2, 4, NULL, NULL),
(3, 4, NULL, NULL),
(3, 6, NULL, NULL),
(4, 4, NULL, NULL),
(5, 4, NULL, NULL),
(5, 5, NULL, NULL),
(6, 2, NULL, NULL),
(6, 3, NULL, NULL),
(6, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(70) NOT NULL,
  `body_text` text NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `rating`, `title`, `body_text`, `product_id`, `user_id`, `created_at`, `updated_at`) VALUES
(76, 4, 'hhhhhhhhhh', 'jjjjjjjj', 6, 1, '2024-02-16 17:26:33', '2024-02-16 17:26:33'),
(77, 2, 'oooooooo', 'يعععععععععععععع', 2, 1, '2024-02-16 17:28:23', '2024-02-16 18:39:29'),
(78, 3, 'cool', 'huhuhhuhuhuhuhuh', 5, 1, '2024-02-16 17:30:37', '2024-02-16 17:30:37'),
(79, 3, 'rrrrrrrrrrrrr', 'qqqqqqqqqqqqqqqqq', 4, 1, '2024-02-16 17:31:53', '2024-02-16 17:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `main_image` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `slug`, `main_image`, `created_at`, `updated_at`) VALUES
(1, 'Jackets', 'jackets', '169338934966.png', '2023-08-30 06:55:49', '2023-08-30 06:55:49'),
(2, 'Sweaters & Shirts', 'sweaters-shirts', '169338941750.png', '2023-08-30 06:56:57', '2024-04-01 16:38:00'),
(3, 'Pants', 'pants', '169338944141.png', '2023-08-30 06:57:21', '2024-02-22 19:18:16'),
(4, 'Shoes', 'shoes', '169338946230.png', '2023-08-30 06:57:42', '2024-02-22 19:18:42'),
(5, 'Bags', 'bags', '169338949527.png', '2023-08-30 06:58:15', '2024-02-22 19:18:01'),
(6, 'Accessories', 'accessories', '169338951588.png', '2023-08-30 06:58:35', '2024-02-22 19:18:53');

-- --------------------------------------------------------

--
-- Table structure for table `thumb_images`
--

CREATE TABLE `thumb_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thumb_image` varchar(191) NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thumb_images`
--

INSERT INTO `thumb_images` (`id`, `thumb_image`, `product_id`, `created_at`, `updated_at`) VALUES
(26, '170514205566.jpg', 3, '2024-01-13 08:34:15', '2024-01-13 08:34:15'),
(27, '170514205573.jpg', 3, '2024-01-13 08:34:15', '2024-01-13 08:34:15'),
(28, '170514205529.jpg', 3, '2024-01-13 08:34:15', '2024-01-13 08:34:15'),
(29, '170514205538.jpg', 3, '2024-01-13 08:34:15', '2024-01-13 08:34:15'),
(30, '170514795372.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(31, '170514795338.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(32, '170514795316.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(33, '170514795391.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(34, '170514795334.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(35, '170514795311.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(36, '170514795377.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(37, '1705147953100.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(38, '170514795322.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(39, '170514795399.jpg', 4, '2024-01-13 10:12:33', '2024-01-13 10:12:33'),
(40, '170514896916.jpg', 6, '2024-01-13 10:29:29', '2024-01-13 10:29:29'),
(41, '170514896919.jpg', 6, '2024-01-13 10:29:29', '2024-01-13 10:29:29'),
(42, '170514896913.jpg', 6, '2024-01-13 10:29:29', '2024-01-13 10:29:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(191) NOT NULL,
  `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(191) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Grace', 'Edraak', 'admin@edraakmc.com', '2024-01-26 01:43:57', '$2y$10$bM06FtVtvBl6Ja0oInBnKuCsna1pEGDsvLfqLPLwLj9bMgC3gGoaS', 1, 'h4WNRrYIchPEafWvuTO6lCR9QR2Y7pQIPtakEFs15vj6XV6SG8i9T5d73Y4n', '2022-07-10 23:29:51', '2022-07-10 23:29:51'),
(2, 'Yousif', 'Mohamed', 'yewess97@gmail.com', '2024-04-13 19:16:57', '$2y$10$SWEsmn35u8DEz8wdSoqPxOdhMV2d8otWElaiYI7B25YxURJc83ARe', 1, NULL, '2023-11-29 11:50:38', '2024-04-13 17:16:57'),
(4, 'lolo', 'loool', 'lolo@mail.com', '2024-04-13 19:12:01', '$2y$10$QT23ZsQ.LWV6YwQMFUD4weuC2RZ.tG7sCIcUzgUWckDkZKcy.It2S', 0, NULL, '2024-04-13 17:12:00', '2024-04-13 17:12:00'),
(5, 'soso', 'soso', 'soso@mail.com', '2024-04-14 17:52:32', '$2y$10$JBKaO35OpjiHBu2P/x4bKO8T1xDfXiLlmrdRTuRFR2M208iRU9mMu', 0, NULL, '2024-04-13 17:17:25', '2024-04-14 15:52:32'),
(6, 'bobo', 'bobo', 'bobo@mail.com', '2024-04-13 19:17:41', '$2y$10$rUASVmnfVOoxPBQX1nh0qevoKsWRm90kPrPfO.wD69c8clwLdcNfS', 0, NULL, '2024-04-13 17:17:41', '2024-04-13 17:17:41'),
(7, 'vovo', 'vovo', 'vovo@mail.com', '2024-04-13 19:17:59', '$2y$10$ZOhtl1JeznKjTF422ddybujCpCRJ4njY/X6F68DwA4d1HKZF.NPri', 0, NULL, '2024-04-13 17:17:59', '2024-04-13 17:17:59'),
(8, 'coco', 'coco', 'coco@mail.com', '2024-04-13 19:18:13', '$2y$10$iBupadG.gNNfE0xGCewA9enTmKUHPSQSiU.0O79VPE4MnfNW.yfym', 0, NULL, '2024-04-13 17:18:13', '2024-04-13 17:18:13'),
(9, 'fofo', 'fofo', 'fofo@mail.com', '2024-04-14 18:11:02', '$2y$10$pgOU6c0V/F8GfiOWVpc44eXl.HJi5aa1lWoeh.XeP8x4oFLpP9dGi', 1, NULL, '2024-04-13 17:18:30', '2024-04-14 16:11:02'),
(10, 'yoyo', 'yoyo', 'yoyo@mail.com', '2024-04-13 19:18:49', '$2y$10$C7ROMh/ELwRlNjspYaSwUeZ2xTaZvrnCa8HgbsOV5t9xjzhQItR0W', 0, NULL, '2024-04-13 17:18:49', '2024-04-13 17:18:49'),
(11, 'zozo', 'zozo', 'zozo@mail.com', '2024-04-13 19:19:01', '$2y$10$TUCaO7qrkugb7MToSsnfeOlcGyhhOilxwbwHXj/EYRmAGGRkBYYzy', 0, NULL, '2024-04-13 17:19:01', '2024-04-13 17:19:01'),
(12, 'wowo', 'wowo', 'wowo@mail.com', '2024-04-13 19:19:20', '$2y$10$oDjmPkJgyJZfRuX1u2L8KOTXKdfa89SoME3bpY.BeaItPqe81MP/S', 0, NULL, '2024-04-13 17:19:20', '2024-04-13 17:19:20'),
(13, 'gogo', 'gogo', 'gogo@mail.com', '2024-04-13 19:19:38', '$2y$10$OxevkjZev2WtLIQsWZSn2OMuw8HP9mDt1E31y1Z980T13NOZE/He.', 0, NULL, '2024-04-13 17:19:38', '2024-04-13 17:19:38'),
(14, 'jojo', 'jojo', 'jojo@mail.com', '2024-04-13 19:19:58', '$2y$10$HIQGJPblhZs3gVW6G2Tjr.oqr8njpEelZ1ZL0a3WG.4oy9ad/QV7a', 0, NULL, '2024-04-13 17:19:58', '2024-04-13 17:19:58'),
(15, 'koko', 'koko', 'koko@mail.com', '2024-04-13 19:20:14', '$2y$10$I0SvlMvheDALG/f9.4BYVOHrW1uK0xP2QGrRIALob0SP08PJCttq.', 0, NULL, '2024-04-13 17:20:14', '2024-04-13 17:20:14'),
(16, 'eoeo', 'eoeo', 'eoeo@mail.com', '2024-04-13 19:20:35', '$2y$10$I70LTpa1PvV3NJueTYr24.TfHkjwHeGz.HCAgg0HigBx4u9I1I9L2', 0, NULL, '2024-04-13 17:20:35', '2024-04-13 17:20:35'),
(17, 'user', 'user', 'user@mail.com', '2024-04-13 19:20:50', '$2y$10$.kDgvavLrEStO8lhYSIZ6eFo0U.wWFFNLzftSuHB097U5sQJYR.SW', 0, NULL, '2024-04-13 17:20:50', '2024-04-13 17:20:50'),
(18, '3asal', '3asal', '3asal@mail.com', '2024-04-13 19:21:13', '$2y$10$mY4VKwNYGfAL539iNh0lk.E.bY0Ej8Iw4PjpR5azDd85VCHh6ZjoO', 0, NULL, '2024-04-13 17:21:13', '2024-04-13 17:21:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`),
  ADD KEY `carts_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_name_index` (`name`);

--
-- Indexes for table `category_product`
--
ALTER TABLE `category_product`
  ADD UNIQUE KEY `category_product_category_id_product_id_unique` (`category_id`,`product_id`),
  ADD KEY `category_product_product_id_foreign` (`product_id`),
  ADD KEY `category_product_category_id_product_id_index` (`category_id`,`product_id`);

--
-- Indexes for table `category_subcategory`
--
ALTER TABLE `category_subcategory`
  ADD UNIQUE KEY `category_subcategory_category_id_subcategory_id_unique` (`category_id`,`subcategory_id`),
  ADD KEY `category_subcategory_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `category_subcategory_category_id_subcategory_id_index` (`category_id`,`subcategory_id`);

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
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_tracking_num_unique` (`tracking_num`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_address_id_foreign` (`address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_slug_unique` (`slug`),
  ADD KEY `products_name_index` (`name`);

--
-- Indexes for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_sizes_product_id_foreign` (`product_id`);

--
-- Indexes for table `product_subcategory`
--
ALTER TABLE `product_subcategory`
  ADD UNIQUE KEY `product_subcategory_subcategory_id_product_id_unique` (`subcategory_id`,`product_id`),
  ADD KEY `product_subcategory_product_id_foreign` (`product_id`),
  ADD KEY `product_subcategory_subcategory_id_product_id_index` (`subcategory_id`,`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_product_id_foreign` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subcategories_slug_unique` (`slug`),
  ADD KEY `subcategories_name_index` (`name`);

--
-- Indexes for table `thumb_images`
--
ALTER TABLE `thumb_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thumb_images_product_id_foreign` (`product_id`);

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
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `thumb_images`
--
ALTER TABLE `thumb_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `category_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `category_subcategory`
--
ALTER TABLE `category_subcategory`
  ADD CONSTRAINT `category_subcategory_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `category_subcategory_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_sizes`
--
ALTER TABLE `product_sizes`
  ADD CONSTRAINT `product_sizes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_subcategory`
--
ALTER TABLE `product_subcategory`
  ADD CONSTRAINT `product_subcategory_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_subcategory_subcategory_id_foreign` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thumb_images`
--
ALTER TABLE `thumb_images`
  ADD CONSTRAINT `thumb_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
