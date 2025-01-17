-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2022 at 06:26 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

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
  `address_1` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_2` mediumtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `product_size` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_quantity` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Men', '2022-07-10 23:30:33', '2022-07-10 23:30:33', NULL),
(2, 'Women', '2022-07-10 23:30:40', '2022-07-10 23:30:40', NULL),
(3, 'Kids', '2022-07-10 23:30:47', '2022-07-10 23:30:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(14, '2022_07_05_113950_create_product_sizes_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tracking_num` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_items` int(10) UNSIGNED NOT NULL,
  `total_cost` double(8,2) UNSIGNED NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `address_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_main_image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_size` tinyint(4) NOT NULL,
  `product_quantity` int(10) UNSIGNED NOT NULL,
  `product_total_price` double(8,2) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `long_description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `old_price` double(8,2) UNSIGNED DEFAULT NULL,
  `new_price` double(8,2) UNSIGNED NOT NULL,
  `stock_status` tinyint(4) NOT NULL DEFAULT 0,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `short_description`, `long_description`, `main_image`, `old_price`, `new_price`, `stock_status`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Andora Full Buttoned Casual Denim Jacket - Light Blue', 'Cotton Material .. Regular Fit .. Solid Pattern .. Turn Down Neck .. Long Sleeves .. Buttons Closure .. Production Country: Egypt .. Color: Blue', 'Andora Company for trading, exportation and importation start serving the Egyptian market since 2002 as the first trading company for King Tout Clothes factory located in 6th of October City.Andora Company main activity is marketing & distribution of men, women and kids clothes that are produced by our factory or our local & international partner factories to assure the delivery of high quality products that’s fashionable with quality materials & manufacturing process at competitive & affordable prices.', '165750405012.jpg', 1199.00, 315.00, 1, 1, '2022-07-10 23:47:30', '2022-07-10 23:47:51', NULL),
(2, 'Ravin Side Pockets Zipper Leather Bomber Black Jacket', '100% PU Material .. Regular Fit .. Long Sleeves .. Solid Pattern .. Band Collar .. Zipper Closure .. Production Country: Egypt .. Color: Black', 'From running errands to taking a walk in the neighborhood, get ready for the cooler weather in style with this leather Puffer Jacket from Ravin. Made from a water- and wind-resistant material to help keep you dry and comfy, this men\'s leather puffer jacket features a nice design with banded cuffs for cozy wear. The front full-length zipper allows for easy layering and versatile styling, while the two side pockets let you carry small everyday essentials with you. In a solid hue, it makes a great layering piece over a variety of your tees, sweaters or sweatshirts to give you an array of cool-weather wear options.', '165750434376.jpg', NULL, 1200.00, 1, 1, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(3, 'M Sou Elastic Hem Hooded Trendy Black Jacket', '100 % Synthetic Fibers .. Regular fit .. Long Sleeves .. Solid Pattern .. Hooded Neck .. Zipper closure .. Color: Black', 'Top off your weekend-to-weekday with this jacket from M Sou. Made from a mid-weight fabric with plain pattern for comfy wear, this long-sleeve jacket features a front-length zipper closure for quick and easy dressing. Tailored in below waist length for convenient layering.', '165750458688.jpg', 449.00, 315.00, 1, 2, '2022-07-10 23:56:26', '2022-07-10 23:56:46', NULL),
(4, 'M Sou Hips Length Corn Flower Blue Classic Jacket', 'Linen material .. Regular fit .. Long Sleeves .. Solid Pattern .. Notched collar open neckline .. Slip-on .. Comes with belt .. Color: Blue', 'An easy lightweight layer for classic styles days in different colors -- we made this classic jacket with our iconic different ways for versatile ease. The silhouette is flattering and feminine with an open front, long sleeves, and a notched collar.', '165750475378.jpg', 559.00, 399.00, 1, 2, '2022-07-10 23:59:13', '2022-07-10 23:59:27', NULL),
(5, 'Andora Removable Head Cover Double Face Green & Beige Bomber Jacket', 'Polyester Material .. Regular Fit .. Long Sleeves .. Printed & Stitched Patch Pattern .. Zip Through Collar .. Removable Head Cover .. Zipper Closure .. Color: Multicolour', 'Andora Company for trading, exportation and importation start serving the Egyptian market since 2002 as the first trading company for King Tout Clothes factory located in 6th of October City.Andora Company main activity is marketing & distribution of men, women and kids clothes that are produced by our factory or our local & international partner factories to assure the delivery of high quality products that’s fashionable with quality materials & manufacturing process at competitive & affordable prices. Our strategy is to ensure competitive prices reach our consumers by eliminating the middleman and sales directly from the factory to consumer.Andorra products are available through 15 outlet distributed across Great Cairo, 6th of October, 10th of Ramdan and Damietta,For our consumer satisfaction more branches to come soon…', '165750497370.jpg', 1049.00, 450.00, 1, 3, '2022-07-11 00:02:53', '2022-07-11 00:03:16', NULL),
(6, 'Andora Kids Stitched Waterproof Hooded Jacket - Watermelon', 'Mix Material .. Regular fit .. Long Sleeves .. Solid Pattern .. Hooded Neck .. Zipper Closure .. Production Country: Egypt .. Color: Red', 'Andora company was established in 2002/ We provide our own collection design for Men/ Women & Kids outwear & home wear/ Our management has the experience and knows how to control the garments with high Quality and high fashionable design and very high competitive price/ we work with our partners inside Egypt and outside Egypt to maintain the successful formula to our consumer / Andora now has about 15 stores strategically located across Greater Cairo to cater for our target consumer we are on the way to expand', '165750520158.jpg', 899.00, 180.00, 1, 3, '2022-07-11 00:06:41', '2022-07-11 00:06:59', NULL),
(7, 'Fashion Men Sport Fitness Training Pants', 'Men\'s running pants .. Gender:MEN .. Material:polyester .. Origin:CN(Origin) .. Closure Type:Elastic Waist .. Fit:Fits true to size, take your normal size', 'We are factory direct s.There are  many outstanding stuffs in my store，please Use the category to view more.    Wish you have a happy shopping!  We adhere to the business philosophy of customer first, provide customers with high-quality products, preferential prices, and strive to do the best for you to choose satisfactory products. Finally, I wish you a happy shopping in our store, thank you! Thank you so much for .All dimensions are measured by hand, there may be 1-2cm deviations', '165750541435.jpg', 1200.00, 870.00, 1, 1, '2022-07-11 00:10:14', '2022-07-11 00:10:31', NULL),
(8, 'Gabardine Elegant Plain Pants - Light Beige', 'Gabardine Material .. Solid Material .. Regular Fit .. Slip-On .. Production Country: Egypt .. Color: Light Beige', 'comfortable with these products from Cottonil for men underwears and clothing. They make an excellent use of the Egyptian cotton offering a wide range of products with affordable prices.', '165750553225.jpg', 550.00, 320.00, 1, 1, '2022-07-11 00:12:12', '2022-07-11 00:12:26', NULL),
(9, 'Casual Striped Pants - Black & White', 'Mixed material .. Striped pattern .. Black & White .. Casual pants .. Production Country: Egypt .. Color: Black & White', 'Casual Striped Pants - Black & White', '165750572571.jpg', NULL, 160.00, 1, 2, '2022-07-11 00:15:25', '2022-07-11 00:15:25', NULL),
(10, 'Defacto Woman White Trousers', 'ShellFabric1 Cotton 100% .. Production Country: Turkey .. Color: White', 'Founded in 2003, DEFACTO is today one of the most popular fashion brands in Turkey and around the world with more than 500 stores. It is positioned as a pioneering brand of fashion throughout the Mediterranean world.', '165750584250.jpg', 399.00, 349.00, 1, 2, '2022-07-11 00:17:22', '2022-07-11 00:17:37', NULL),
(11, 'Izor Boys Slip On Sweatpants - Black', 'Cotton Material .. Solid Pattern .. Regular Fit .. Slip-on .. Elasticated hem .. Color: Black', 'Step up your style with our Clothes ! Featuring great design', '165750606218.jpg', NULL, 109.00, 1, 3, '2022-07-11 00:21:02', '2022-07-11 00:21:02', NULL),
(12, 'Defacto Girl Green Pants', 'ShellFabric1 Viscose 100% .. Color: Green', 'Founded in 2003, DEFACTO is today one of the most popular fashion brands in Turkey and around the world with more than 500 stores. It is positioned as a pioneering brand of fashion throughout the Mediterranean world.', '165750615271.jpg', NULL, 249.00, 1, 3, '2022-07-11 00:22:32', '2022-07-11 00:22:32', NULL),
(13, 'Coup Slim Fit Plain Polo Shirt With Short Sleeves And Button Closure', 'Slim Fit Plain Polo Shirt with Short Sleeves and Button Closure .. Production Country: Egypt .. Color: Black', 'The polo is made in a classic relaxed fit with a low hem, branded buttons, and a button placket at the neck. With foldable collar. Classic Fit Polo side split edge button hole Solstice embroidery Care Tips 100% cotton Machine washable at 40 degrees stay away from fire', '165750631714.jpg', 399.00, 199.00, 1, 1, '2022-07-11 00:25:17', '2022-07-11 00:25:32', NULL),
(14, 'Coup Slim Fit Cut And Sew Polo-Shirt With Short Sleeves And Button Closure', 'Slim Fit Cut and Sew Polo-Shirt with Short Sleeves and Button Closure .. Production Country: Egypt .. Color: N.Pink .. Main Material: 100% Cotton', 'The polo is made in a classic relaxed fit with a low hem, branded buttons, and a button placket at the neck. With foldable collar. Classic Fit Polo side split edge button hole Solstice embroidery Care Tips 100% cotton Machine washable at 40 degrees stay away from fire', '165750645646.jpg', NULL, 249.00, 1, 1, '2022-07-11 00:27:36', '2022-07-11 00:27:36', NULL),
(15, 'Sweat-shirt Hooded Zipper Sweatshirt For Women', 'Sweat-shirt Hooded Zipper Sweatshirt .. Color: black .. Main Material: mix', 'Sweat-shirt Hooded Zipper Sweatshirt', '165750676294.jpg', 220.00, 119.00, 1, 2, '2022-07-11 00:32:42', '2022-07-11 00:32:56', NULL),
(16, 'Fashion Spring Autumn Large Size Women T Shirts Casual O-Neck', 'Material:Polyester .. Item Type:Tops .. Tops Type:Tees .. Sleeve Length(cm):Three Quarter .. Sleeve Style:REGULAR .. Fabric Type:Broadcloth .. Pattern Type:Floral .. Clothing Length:REGULAR .. Decoration:Lace .. Style:Casual .. Age:Ages 18-35 Years Old .. Collar:O-Neck .. season:Spring,Summer,Autumn, .. style:Casual,Sweet,Vintage .. Fit For:Women Ladies Feminino Femme Female', 'Shipping and Packaging All items will be double checked and well packed before sending.Items will be dispatched within 2 business days after buyers pay for the order. It usually takes about 14 to 18 working days for delivering the package to the destination(for remote areas,it may take a little longer). Special Announcement:Please fill in the correct and detailed consignee, address and phone number in the order. For fast and correct delivery.If you do not receive order within 30 working days,please feel free to contact Customer Service before leaving Negative and Neutral Feedback, we will do our best to help you resolve the problem.TaxNo, you will only pay what is quoted as total cost at checkout, no more. You are not expected to pay any additional duties or taxes. If you are asked by Customs or our logistic partner to pay duties, please contact our Customer Service .ReturnIf the product is not on good condition, You can return this product within 14 working days, please contact customer service center before returning.FeedbackYour satisfaction is our first priorityIf you receive the order and it is in good condition,we would be grateful if you would leave us 5 star Positive Feedback for the transaction.', '165750698811.jpg', 976.00, 781.00, 1, 2, '2022-07-11 00:36:28', '2022-07-11 00:36:45', NULL),
(17, 'Diadora Cotton Boy Printed T-Shirt -White', 'Materila: 95%Cotton - 5% lycra .. Long sleeves .. Round neck .. Slip-on .. Front printed t-shirt .. Our Model Is Weard Size 6/7 .. Made In Egypt With Excellence From Diadora Italy .. Color: White', 'Printed T-Shirt for Boys From Diadora . 95% Cotton-5%Lycra  and more comfort . you can match it Skinny pants and simple accessory to have a perfect casual look', '165750717867.jpg', 229.00, 160.00, 1, 3, '2022-07-11 00:39:38', '2022-07-11 00:39:55', NULL),
(18, 'Defeet Defect Sweat Shirt Of Girls', 'The product is pure cotton .. Girls\' sweatshirts at the highest level .. The finest cotton materials .. It is washed at 30 degrees .. Production Country: Egypt .. Color: light blue', 'Defect offers the finest t-shirts for your children to look their best in front of the world, and this is summarized in our cotton products', '165750736242.jpg', NULL, 199.00, 1, 3, '2022-07-11 00:42:42', '2022-07-11 00:42:42', NULL),
(19, 'Trending Style Sports Shoes Breathable Trainers Sneakers For Men', 'About Size: China Size Please Check Our Size Chart Reference .. If Your Feet Are Thicker Or Wider Please Add One Size Bigger .. More Colors and Models Please Visit Our Shop .. Hard-Wearing Anti-Slip and Breathable And Hot .. Fit Well and Walking Comfortable .. Fashion Sneakers & Athletics Shoes .. Fashion Running Sports shoes .. Good Quality Walking Footwear .. Trendy & Casual Athletics Shoes', 'Dear Customers,Welcome to Our Shop,More Colors and Models Please Visit Shop!\r\nHere our Size Chart Reference Following:\r\nUS 4.5 = UK3.5 = EU/CN 35 = 225 mm;\r\nUS 5 = UK 4 = EU/CN 36 = 230 mm;\r\nUS 5.5 = UK 4.5 = EU/CN 37 = 235 mm;\r\nUS 6 = UK 5 = EU/CN 38 = 240 mm;\r\nUS 6.5 = UK 5.5 = EU/CN 39 = 245 mm;\r\nUS 7 = UK 6 = EU/CN 40 = 250 mm;\r\nUS 8 = UK 7 = EU/CN 41 = 255 mm;\r\nUS 8.5 = UK 7.5 = EU/CN 42 = 260 mm;\r\nUS 9 = UK 8 = EU/CN 43 = 265 mm;\r\nUS 10 = UK 9 = EU/CN 44 = 270 mm;\r\nUS 11 = UK 10 = EU/CN 45 = 275 mm;\r\nUS 12 = UK 11 = EU/CN 46 = 280 mm;\r\nUS 13 = UK 12 = EU/CN 47 = 285 mm;\r\nUS 14 = UK 13 = EU/CN 48 = 290 mm;\r\nPlease Choose Right Size Rely On Your Measurement Of Feet Length.\r\nFor many men, a quality pair of brogues are an absolute must have. The distinctive brogue detailing on these shoes instantly sets them apart from other styles.\r\nStylish Design:Enhance your look with the streamlined silhouette of a classic oxford shoe. A versatile style with a no seam cap-toe oxford features hand painted Argentinean leather.The shoes is as comfortable in the boardroom as it is on the dancefloor.\r\nVersatility for Effectiveness:This no seam cap-toe oxford features hand painted Argentinean leather.he classic and timeless oxford deserves a place in every man\'s closet.\r\nThey are perfectly suited to most smart casual occasions and can be paired with everything from trousers to jeans. Making them a very versatile addition to your footwear collection.\r\nRich in feel, classic in inspiration, sophisticated in looks, wingtip gives a classic, traditional aesthetic in a luxurious yet contemporary design.\r\nPlease Make 5-Star Rating if You\'re Happy With Items,And Share with Your Friends Facebook,Instagram,Whatsapp,Twitter,Youtube,Pinterest ect,Enjoy Shopping Here!', '165750972958.jpg', 1200.00, 600.00, 1, 1, '2022-07-11 01:22:09', '2022-07-11 01:22:21', NULL),
(20, 'Stylish Oxfords Shoes - Black', 'Lace-up closure .. Genuine Leather upper & inner material .. Production Country: Egypt .. Color: Black', 'Stylish Oxfords Shoes - Black', '165750983945.jpg', 500.00, 300.00, 1, 1, '2022-07-11 01:23:59', '2022-07-11 01:24:11', NULL),
(21, 'Lile BN-301 Ballerina Suede - Beige', 'Material - suede .. Sizes from 37 to 41 .. Available in all colors in store .. The sole is light and comfortable .. Production Country: Egypt .. Color: Beige', 'Lile is a brand which provides women\'s shoes and bags in a high quality materials which let you comfort all day long. It\'s product will add a touch of beauty and elegance to your look. Get your stylish casual and classic look with it.', '165751000051.jpg', 499.00, 127.00, 1, 2, '2022-07-11 01:26:40', '2022-07-11 01:26:53', NULL),
(22, 'Desert Canvas Slip On Sock Sneakers - Kashmir', 'Simply Slip on and off .. Flexible Canvas upper material .. A sturdy PVC outsole offers added comfort .. Soft footbed ensures all-day comfort .. Production Country: Egypt .. Color: Kashmir', 'D&C Footwear is one of the most professional Shoes brands in Egypt was established in 1975 .. if you seek to find a comfortable and stylish shoes for anyone D&C Footwear will be your best choice\r\n\r\nPOWERED BY DESERT FACTORIES', '165751014960.jpg', 349.00, 119.00, 1, 2, '2022-07-11 01:29:09', '2022-07-11 01:29:23', NULL),
(23, 'Desert Sportive Lace-up Leather Sneakers For Boys - Multicolor', 'Flexible Leather upper material .. Comes with secure lace-up closure .. A sturdy rubber outsole offers added comfort .. Soft footbed ensures all-day comfort .. Production Country: China, Egypt .. Color: Multicolor', 'D&C Footwear is one of the most professional Shoes brands in Egypt was established in 1975 .. if you seek to find comfortable and stylish shoes for anyone D&C Footwear will be your best choice\r\nPOWERED BY DESERT SHOES', '165751035329.jpg', 509.00, 169.00, 1, 3, '2022-07-11 01:32:33', '2022-07-11 01:35:10', NULL),
(24, 'Sneakers Comfort Shoes For Kids - White Pink', 'Main material: high-quality PU leather .. Sole: Comfortable, sturdy, and high-quality .. Foot odor texture and it is imported .. The product is Egyptian made and made by Egyptian hands .. Proudly Made in Egypt .. Color: White', 'Hard Wire Egypt is a startup in the leather market and our main goal is quality and reasonable price. You will find we have everything to suit your needs. Do not hesitate and order your products from Hard Wire Egypt. We promise that you will be fully satisfied with your experience with us. The rest of the style colors are available at Jumia store\r\nKeep yourself comfortable with Modern comfy footwear. They are manufactured with high quality and fashionable designs. Featuring a flexible exterior and smooth interior, they are comfortable and stylishAvailable sizes from 27 to 31', '165751048017.jpg', 594.00, 225.00, 1, 3, '2022-07-11 01:34:40', '2022-07-11 01:35:23', NULL),
(25, 'Fashion Men Fashion Nylon Crossbody Sports Waist Packs', '【Size and Material】- 28X15X10CM. Made of nylon material, which is durable and lightweight. The inner is well lined with soft material polyester .【Multifunction Pocket】- Small body but has large capacity, with roomy enough for your daily essential-phone,tissue,keys,wallet, etc ..【Delicate Design】- Scratch-resistant and wear-resistant, with night reflective strip, anti-theft zipper bag and adjustable shoulder strap. Each design is for you 【Multipurpose Use】- This bag can used as a chest bag, cross', 'we are committed to providing the best products, the most tasteful fashion design, and the most timely after-sales service. If you have any questions during the purchase, please feel free to contact us. Have a pleasant shopping.\r\n\r\nSize and Material: 15X26X4CM. Made of PU leathermaterial, which is durable and lightweight. The inner is well lined with soft material PUmix Polyester.Delicate Design: Scratch-resistant and wear-resistant, with High-gloss PU leather design, anti-theft zipper bag and adjustable waist strap. Each design is for you.Multifunction Pocket: Small body but has large capacity, with roomy enough for your daily essential-phone, tissue, keys, wallet, etc.Multipurpose Use: This bag can used as a chest bag, crossbody bag, shoulder bag, waist bag, etc, it can be every bags as you like.The Best Gift: The stylish waist bag is the Perfect Gift for Christmas, Thanks Giving Day, Groomsmen, Birthdays, Father\'s Day. IMPORTANT NOTE\r\nDear, We appreciate your preference and take the opportunity to let you know that your purchase is very valuable to us. Your satisfaction and positive feedback is very significant to us.If you like it, Please give us 5-star.If for any reason you are not satisfied with any of our products or have any questions regarding the use of the product, please contact us before raising a claim, Please be assured that we will be responsible for all the products of our brand. Dear, have a nice day!', '165751075248.jpg', 1105.00, 789.00, 1, 1, '2022-07-11 01:39:12', '2022-07-11 01:41:29', NULL),
(26, 'The Most Stylish Anti Theft Shoulder Bags With Its Distinctive Grey', 'Item Type: Handbags Lining material: polyester Primary Material: Nylon Number of Handles / Straps: Outside: none Shape: pillow Handbags Type: Chest Bags Inside: the interior Hardness: soft Detection number and: anti-theft chest pack Closure Type: Zipper Gender: Men Occasion: Versatile Fashion Decoration: NONE Pattern Type: Magnetic .. Production Country: China .. Color: Grey', 'Item Type: Handbags Lining material: polyester Primary Material: Nylon Number of Handles / Straps: Outside: none Shape: pillow Handbags Type: Chest Bags Inside: the interior Hardness: soft Detection number and: anti-theft chest pack Closure Type: Zipper Gender: Men Occasion: Versatile Fashion Decoration: NONE Pattern Type: Magnetic', '165751085633.jpg', 500.00, 212.00, 1, 1, '2022-07-11 01:40:56', '2022-07-11 01:41:11', NULL),
(27, 'Leather Cross Bag - Gray - Burgundy - White', 'The finest types of leather .. Wide leather arm .. Multiple external and internal pockets .. Multiple colors .. Production Country: Egypt', 'BS collection seeks to satisfy it\'s clients by providing the highest standards of quality, precision and excellence through modern designs inspired from imagination to suit the elegant looks of it\'s distinguished clients.The bag is made of the finest materials. It has multiple pockets, external pockets and internal pockets. A very practical bag and a wide arm.', '165751107879.jpg', 499.00, 170.00, 1, 2, '2022-07-11 01:44:38', '2022-07-11 01:44:52', NULL),
(28, 'Women Top Handbag And Cross Body Bag And Walet And Sholder Bag Fashion', 'LEATHER HANDBAG - SHOPPER BAG - TOTE BAG .. Leather handbag in fashionable style and classical style .. The bag is made in 100% of high quality leather Closed with a magnetic clasp .. The bag can be worn on the shoulder or in the hand .. This type of leather handbag is perfect for work, college, and every day! and very good cross body bag and clutsh bag and wallet .. The most good 4 piece .. Production Country: Egypt .. Color: white', 'LEATHER HANDBAG - SHOPPER BAG - TOTE BAG\r\nLeather handbag in fashionable style and classical style.\r\nThe bag is made in 100% of high quality leather Closed with a magnetic clasp.\r\nThe bag can be worn on the shoulder or in the hand.\r\nThis type of leather handbag is perfect for work, college, and every day!and very good cross body bag and clutsh bag and walet.the most good 4 piece.', '165751121950.jpg', 450.00, 350.00, 1, 2, '2022-07-11 01:46:59', '2022-07-11 01:47:13', NULL),
(29, 'Activ Circular Patterned Zipped Backpack - Dark Pink & Blue', 'Polyester Upper Material .. One Main Compartment .. Two External Zipped Pockets .. 39cm Length, 41cm Height, 15cm Width .. Zipper Closure .. Color: Mutlicolour', 'Activ is one of the most proprietary brands in the sports fields. We are adhering to be existed as a strong supporter of the various kinds of athletic activities. Not only we became a sponsor of many football teams, young champions, local championships, and it is not in the football game only, but also we are sponsors of basket balls, tennis and the Olympics delegations too. In Addition, Activ is very unique in providing great collection of Casual Shoes, bags, belts, wallets and under-wears.', '165751137473.jpg', 499.00, 259.00, 1, 3, '2022-07-11 01:49:34', '2022-07-11 01:52:03', NULL),
(30, 'Activ Two Main Compartments Heather Blue Backpack', 'Polyester Upper Material .. Two Main Compartments .. Two External Zipped Pockets .. 39cm Length, 41cm Height, 15cm Width .. Zipper Closure .. Color: Blue', 'Activ is one of the most proprietary brands in the sports fields. We are adhering to be existed as a strong supporter of the various kinds of athletic activities. Not only we became a sponsor of many football teams, young champions, local championships, and it is not in the football game only, but also we are sponsors of basket balls, tennis and the Olympics delegations too. In Addition, Activ is very unique in providing great collection of Casual Shoes, bags, belts, wallets and under-wears.', '165751148537.jpg', 499.00, 269.00, 1, 3, '2022-07-11 01:51:25', '2022-07-11 01:51:41', NULL),
(31, 'Gillette Skinguard Sensitive Razor For Men', 'The Skin Guard feature PROTECTS SKIN FROM THE BLADES with it\'s position between the blades to smooth skin .. LUBRICATION BEFORE AND AFTER THE BLADES for glide and comfort .. PRECISION TRIMMER on the back is perfect for hard-to-reach places and styling facial hair .. Production Country: Greece', 'MINIMIZES BLADE CONTACT WITH SENSITIVE SKIN, so you will not get as close of a shave as you would expect from our razors with 5 blades\r\nDesigned for men with skin irritation, razor bumps, and razor burn\r\nThe Skin Guard feature PROTECTS SKIN FROM THE BLADES with it\'s position between the blades to smooth skin\r\nLUBRICATION BEFORE AND AFTER THE BLADES for glide and comfort\r\nPRECISION TRIMMER on the back is perfect for hard-to-reach places and styling facial hair', '165751237276.jpg', NULL, 274.00, 1, 1, '2022-07-11 02:06:12', '2022-07-11 02:06:12', NULL),
(32, 'Dr.key Genuine Leather For Men - Bifold Wallets -2045-plain Brown', 'Brand Name: Dr.Key .. Material:Genuine Leather .. wallet made from genuine leather .. Dimensions :8*10*1cm .. Color: brown .. 100% genuine leather without linings and synthetic fabrics .. It lasts for years and can be a valuable gift', 'A luxurious leather wallet with 9 pockets of cards and one pocket for money, elegant and compact Dimensions 8 × 10 x1 you receive in a stylish envelope\r\n100% genuine leather without linings and synthetic fabrics\r\nIt lasts for years and can be a valuable gift', '165751251091.jpg', 350.00, 200.00, 1, 1, '2022-07-11 02:08:30', '2022-07-11 02:08:46', NULL),
(33, 'Women\'s Twisted Pleated Turban Hat Hijab Head Wrap Chemo White', 'Material: Polyester.Head circumference: Approx. 58 cm / 22.8 inch (Stretchable) .. Production Country: China .. Color: White', '100% Polyester, super soft and stretchable; modern style; One size fits most.\r\n\r\nFashion comfortable turban hat accessory.\r\n\r\nRetro vintage look style, 40s 50s fancy dress.\r\n\r\nVery light wear, use it all year round. Excellent hair covering in case of hair loss.\r\n\r\nOur head cover provides total head coverage for those women with hair loss due to cancer, chemotherapy, alopecia, or other hair loss conditions.\r\n\r\nSpecification:\r\n\r\nMaterial: Polyester.\r\n\r\nHead circumference: Approx. 58 cm / 22.8 inch (Stretchable)\r\n\r\nPackage Includes:\r\n\r\n1 Piece Women Turban Hat\r\n\r\nNote:\r\n\r\n1. Color might be slightly different due to the color calibration of each individual monitor.\r\n2. Please allow 1-3cm measuring deviation due to manual measurement.\r\n3. Thanks for your understanding and enjoy your shopping moment!', '165751267750.jpg', 109.00, 91.00, 1, 2, '2022-07-11 02:11:17', '2022-07-11 02:11:35', NULL),
(34, 'Fashion Women Retro Synthetic Leather Strap Bracelet Wristwatch', 'Colour:pink .. Best Material .. high-quality .. Durable and practical .. Big Discount .. Fast Delivery', 'mo cheng establishes its brand loyalty by providing our customers with the best quality products as well as the most pocket-friendly price. After several years dedication to E-commerce, mo cheng is offering thousands of products to worldwide consumers ranging from Fashion, Beauty to Automobile accessories, almost covering all aspects of today life. Give yourself a try and Buy whatever you like with mo cheng.Features\r\nHigh quality\r\n\r\n11 Colors for your choice: White, Sky blue, Red, Purple, Pink, Dark blue, Brown, Black, Yellow, Rose red, Green\r\n\r\nLength: 56 cm, 3 buckles, adjustable.\r\n\r\nDial color: White\r\n\r\nDial Window Material Type: Glass\r\n\r\nBand Material: Synthetic leather, metal, rhinestone\r\n\r\nMovement: Quartz\r\n\r\nDial Diameter:25mm/0.9\\\"\r\n\r\nStyle: Fashion & Casual\r\n\r\nGender: Women\r\n\r\nDial Display: Analog\r\n\r\nCase Shape: Round\r\n\r\nItem Type: Wristwatches\r\n\r\nNote: Due to the difference between different monitors, the picture may not reflect the actual color of the item. We guarantee the style is the same as shown in the pictures. Thank you!\r\n\r\nPackage Content:\r\n1 x Wrist Watch (with a battery inside)\r\n\r\nWhat In the Box\r\n1 xWrist Watch (with a battery inside)\r\nSpecifications of Women Retro Synthetic Leather Strap Watch Bracelet Wristwatch-Black\r\n\r\nSize (L x W x H cm)19.5  4.5  2 Weight (kg)0.033\r\nNote: - Thanks for coming to our shop,Hoping you have a happy shopping Trip in our store.- Every product sold by us has been strictly checked.selected and tested by the quality supervision department. We guarantee that every customer could purchase high quality products and get satisfied shopping experience!- Your purchase is important to us. If you have an experience that is less than exceptional we will help to make it right.Our hope is that each product we sold will help you express yourself and be known.- If you are satisfied with our products and services, please leave your positive feedback and 5 stars. and 5 stars for the detailed classification of your request!- If you have any question about the product or any other question,please feel free to contact me by email. we will try our best to solve it for you.Wish you have a good day and forever\r\nBest Regards!', '165751278278.jpg', 400.00, 255.00, 1, 2, '2022-07-11 02:13:02', '2022-07-11 02:13:15', NULL),
(35, 'AM-Shop Set Of (6) Ankle Socks - For Kids', 'High-quality industrial materials .. Flexible and comfortable with the same cotton feel .. Nice colors, it may differ slightly from the picture .. The product cannot be returned or exchanged to protect personal hygiene .. This item is non-returnable and non-refundable .. Production Country: Egypt .. Color: May Vary', 'AM-Shop is a multi-product fashion brand that has grown exponentially in the local market over the years of success.\r\n\r\nAM-Shop has a large number of important products for the daily needs of all family members, which include: -\r\n\r\n(Scarf - Underwear - socks)\r\n\r\n(Our goal is the best quality at the lowest price)', '165751299384.jpg', 199.00, 39.00, 1, 3, '2022-07-11 02:16:33', '2022-07-11 02:16:47', NULL),
(36, 'Foxford 6606 C 9 - FF Optical Frame - Oval - For Kids - Girls', 'Fox Ford Optical Frame 6606 C 9 .. Durable And Lightweight .. Material TR 90 .. For kids - Girls .. Color: Pink * Purple', 'Fox Ford Optical Frame 6606 C 9\r\nDurable And Lightweight, \r\nMaterial TR 90\r\nFor kids - Girls\r\nEyeglass Form : Oval\r\nMolded Nose Pad\r\nGlass Width (mm) 44\r\nNose (mm) 18\r\nHandle (mm) 128\r\nCountry Of Origin :- China', '165751315136.jpg', 500.00, 425.00, 1, 3, '2022-07-11 02:19:11', '2022-07-11 02:19:26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_sizes`
--

CREATE TABLE `product_sizes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `symbol_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_sizes`
--

INSERT INTO `product_sizes` (`id`, `symbol_name`, `product_id`, `created_at`, `updated_at`) VALUES
(4, 'l', 1, '2022-07-10 23:47:51', '2022-07-10 23:47:51'),
(5, 'xl', 1, '2022-07-10 23:47:51', '2022-07-10 23:47:51'),
(6, 'xxl', 1, '2022-07-10 23:47:51', '2022-07-10 23:47:51'),
(7, 's', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23'),
(8, 'm', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23'),
(9, 'l', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23'),
(10, 'xl', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23'),
(11, 'xxl', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23'),
(16, 'm', 3, '2022-07-10 23:56:47', '2022-07-10 23:56:47'),
(17, 'l', 3, '2022-07-10 23:56:47', '2022-07-10 23:56:47'),
(18, 'xl', 3, '2022-07-10 23:56:47', '2022-07-10 23:56:47'),
(19, 'xxl', 3, '2022-07-10 23:56:47', '2022-07-10 23:56:47'),
(23, 'm', 4, '2022-07-10 23:59:27', '2022-07-10 23:59:27'),
(24, 'xl', 4, '2022-07-10 23:59:27', '2022-07-10 23:59:27'),
(25, 'xxl', 4, '2022-07-10 23:59:27', '2022-07-10 23:59:27'),
(29, 's', 5, '2022-07-11 00:03:16', '2022-07-11 00:03:16'),
(30, 'm', 5, '2022-07-11 00:03:16', '2022-07-11 00:03:16'),
(31, 'l', 5, '2022-07-11 00:03:17', '2022-07-11 00:03:17'),
(35, 's', 6, '2022-07-11 00:06:59', '2022-07-11 00:06:59'),
(36, 'm', 6, '2022-07-11 00:06:59', '2022-07-11 00:06:59'),
(37, 'l', 6, '2022-07-11 00:06:59', '2022-07-11 00:06:59'),
(42, 'm', 7, '2022-07-11 00:10:31', '2022-07-11 00:10:31'),
(43, 'l', 7, '2022-07-11 00:10:31', '2022-07-11 00:10:31'),
(44, 'xl', 7, '2022-07-11 00:10:32', '2022-07-11 00:10:32'),
(45, 'xxl', 7, '2022-07-11 00:10:32', '2022-07-11 00:10:32'),
(49, 'm', 8, '2022-07-11 00:12:27', '2022-07-11 00:12:27'),
(50, 'l', 8, '2022-07-11 00:12:27', '2022-07-11 00:12:27'),
(51, 'xl', 8, '2022-07-11 00:12:27', '2022-07-11 00:12:27'),
(52, 'l', 9, '2022-07-11 00:15:25', '2022-07-11 00:15:25'),
(53, 'xl', 9, '2022-07-11 00:15:25', '2022-07-11 00:15:25'),
(54, 'xxl', 9, '2022-07-11 00:15:26', '2022-07-11 00:15:26'),
(59, 'm', 10, '2022-07-11 00:17:37', '2022-07-11 00:17:37'),
(60, 'l', 10, '2022-07-11 00:17:37', '2022-07-11 00:17:37'),
(61, 'xl', 10, '2022-07-11 00:17:37', '2022-07-11 00:17:37'),
(62, 'xxl', 10, '2022-07-11 00:17:37', '2022-07-11 00:17:37'),
(63, 's', 11, '2022-07-11 00:21:02', '2022-07-11 00:21:02'),
(64, 'm', 11, '2022-07-11 00:21:03', '2022-07-11 00:21:03'),
(65, 'l', 11, '2022-07-11 00:21:03', '2022-07-11 00:21:03'),
(66, 's', 12, '2022-07-11 00:22:32', '2022-07-11 00:22:32'),
(67, 'm', 12, '2022-07-11 00:22:32', '2022-07-11 00:22:32'),
(68, 'l', 12, '2022-07-11 00:22:32', '2022-07-11 00:22:32'),
(72, 'l', 13, '2022-07-11 00:25:32', '2022-07-11 00:25:32'),
(73, 'xl', 13, '2022-07-11 00:25:32', '2022-07-11 00:25:32'),
(74, 'xxl', 13, '2022-07-11 00:25:32', '2022-07-11 00:25:32'),
(75, 's', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36'),
(76, 'm', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36'),
(77, 'l', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36'),
(78, 'xl', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36'),
(79, 'xxl', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36'),
(83, 'l', 15, '2022-07-11 00:32:56', '2022-07-11 00:32:56'),
(84, 'xl', 15, '2022-07-11 00:32:56', '2022-07-11 00:32:56'),
(85, 'xxl', 15, '2022-07-11 00:32:56', '2022-07-11 00:32:56'),
(91, 's', 16, '2022-07-11 00:36:45', '2022-07-11 00:36:45'),
(92, 'm', 16, '2022-07-11 00:36:45', '2022-07-11 00:36:45'),
(93, 'l', 16, '2022-07-11 00:36:45', '2022-07-11 00:36:45'),
(94, 'xl', 16, '2022-07-11 00:36:45', '2022-07-11 00:36:45'),
(95, 'xxl', 16, '2022-07-11 00:36:45', '2022-07-11 00:36:45'),
(99, 's', 17, '2022-07-11 00:39:55', '2022-07-11 00:39:55'),
(100, 'm', 17, '2022-07-11 00:39:55', '2022-07-11 00:39:55'),
(101, 'l', 17, '2022-07-11 00:39:55', '2022-07-11 00:39:55'),
(102, 's', 18, '2022-07-11 00:42:42', '2022-07-11 00:42:42'),
(103, 'm', 18, '2022-07-11 00:42:42', '2022-07-11 00:42:42'),
(104, 'l', 18, '2022-07-11 00:42:42', '2022-07-11 00:42:42'),
(108, 'm', 19, '2022-07-11 01:22:21', '2022-07-11 01:22:21'),
(109, 'l', 19, '2022-07-11 01:22:21', '2022-07-11 01:22:21'),
(110, 'xl', 19, '2022-07-11 01:22:21', '2022-07-11 01:22:21'),
(113, 'l', 20, '2022-07-11 01:24:11', '2022-07-11 01:24:11'),
(114, 'xl', 20, '2022-07-11 01:24:11', '2022-07-11 01:24:11'),
(117, 'm', 21, '2022-07-11 01:26:53', '2022-07-11 01:26:53'),
(118, 'l', 21, '2022-07-11 01:26:53', '2022-07-11 01:26:53'),
(121, 'm', 22, '2022-07-11 01:29:23', '2022-07-11 01:29:23'),
(122, 'l', 22, '2022-07-11 01:29:23', '2022-07-11 01:29:23'),
(127, 's', 23, '2022-07-11 01:35:10', '2022-07-11 01:35:10'),
(128, 'm', 23, '2022-07-11 01:35:10', '2022-07-11 01:35:10'),
(129, 's', 24, '2022-07-11 01:35:23', '2022-07-11 01:35:23'),
(130, 'm', 24, '2022-07-11 01:35:23', '2022-07-11 01:35:23'),
(136, 'm', 26, '2022-07-11 01:41:11', '2022-07-11 01:41:11'),
(137, 'l', 26, '2022-07-11 01:41:11', '2022-07-11 01:41:11'),
(138, 's', 25, '2022-07-11 01:41:29', '2022-07-11 01:41:29'),
(139, 'm', 25, '2022-07-11 01:41:29', '2022-07-11 01:41:29'),
(140, 'l', 25, '2022-07-11 01:41:29', '2022-07-11 01:41:29'),
(143, 'm', 27, '2022-07-11 01:44:52', '2022-07-11 01:44:52'),
(144, 'l', 27, '2022-07-11 01:44:52', '2022-07-11 01:44:52'),
(147, 'm', 28, '2022-07-11 01:47:13', '2022-07-11 01:47:13'),
(148, 'l', 28, '2022-07-11 01:47:13', '2022-07-11 01:47:13'),
(153, 'm', 30, '2022-07-11 01:51:41', '2022-07-11 01:51:41'),
(154, 'l', 30, '2022-07-11 01:51:41', '2022-07-11 01:51:41'),
(155, 'm', 29, '2022-07-11 01:52:03', '2022-07-11 01:52:03'),
(156, 'l', 29, '2022-07-11 01:52:03', '2022-07-11 01:52:03'),
(157, 's', 31, '2022-07-11 02:06:12', '2022-07-11 02:06:12'),
(158, 'm', 31, '2022-07-11 02:06:12', '2022-07-11 02:06:12'),
(161, 's', 32, '2022-07-11 02:08:46', '2022-07-11 02:08:46'),
(162, 'm', 32, '2022-07-11 02:08:46', '2022-07-11 02:08:46'),
(165, 's', 33, '2022-07-11 02:11:35', '2022-07-11 02:11:35'),
(166, 'm', 33, '2022-07-11 02:11:35', '2022-07-11 02:11:35'),
(169, 's', 34, '2022-07-11 02:13:15', '2022-07-11 02:13:15'),
(170, 'm', 34, '2022-07-11 02:13:15', '2022-07-11 02:13:15'),
(173, 's', 35, '2022-07-11 02:16:47', '2022-07-11 02:16:47'),
(174, 'm', 35, '2022-07-11 02:16:47', '2022-07-11 02:16:47'),
(177, 's', 36, '2022-07-11 02:19:26', '2022-07-11 02:19:26'),
(178, 'm', 36, '2022-07-11 02:19:26', '2022-07-11 02:19:26');

-- --------------------------------------------------------

--
-- Table structure for table `product_subcategory`
--

CREATE TABLE `product_subcategory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subcategory_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_subcategory`
--

INSERT INTO `product_subcategory` (`id`, `subcategory_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 2, NULL, NULL),
(3, 7, 3, NULL, NULL),
(4, 7, 4, NULL, NULL),
(5, 13, 5, NULL, NULL),
(6, 13, 6, NULL, NULL),
(7, 2, 7, NULL, NULL),
(8, 2, 8, NULL, NULL),
(9, 8, 9, NULL, NULL),
(10, 8, 10, NULL, NULL),
(11, 14, 11, NULL, NULL),
(12, 14, 12, NULL, NULL),
(13, 3, 13, NULL, NULL),
(14, 3, 14, NULL, NULL),
(15, 9, 15, NULL, NULL),
(16, 9, 16, NULL, NULL),
(17, 15, 17, NULL, NULL),
(18, 15, 18, NULL, NULL),
(19, 4, 19, NULL, NULL),
(20, 4, 20, NULL, NULL),
(21, 10, 21, NULL, NULL),
(22, 10, 22, NULL, NULL),
(23, 16, 23, NULL, NULL),
(24, 16, 24, NULL, NULL),
(25, 5, 25, NULL, NULL),
(26, 5, 26, NULL, NULL),
(27, 11, 27, NULL, NULL),
(28, 11, 28, NULL, NULL),
(29, 17, 29, NULL, NULL),
(30, 17, 30, NULL, NULL),
(31, 6, 31, NULL, NULL),
(32, 6, 32, NULL, NULL),
(33, 12, 33, NULL, NULL),
(34, 12, 34, NULL, NULL),
(35, 18, 35, NULL, NULL),
(36, 18, 36, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `name`, `category_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Jackets', 1, '2022-07-10 23:31:01', '2022-07-10 23:31:01', NULL),
(2, 'Pants', 1, '2022-07-10 23:31:09', '2022-07-10 23:31:09', NULL),
(3, 'Sweaters & Shirts', 1, '2022-07-10 23:31:24', '2022-07-10 23:31:24', NULL),
(4, 'Shoes', 1, '2022-07-10 23:31:48', '2022-07-10 23:31:48', NULL),
(5, 'Bags', 1, '2022-07-10 23:31:55', '2022-07-10 23:31:55', NULL),
(6, 'Accessories', 1, '2022-07-10 23:32:05', '2022-07-10 23:32:05', NULL),
(7, 'Jackets', 2, '2022-07-10 23:32:16', '2022-07-10 23:32:16', NULL),
(8, 'Pants', 2, '2022-07-10 23:32:33', '2022-07-10 23:32:33', NULL),
(9, 'Sweaters & Shirts', 2, '2022-07-10 23:32:45', '2022-07-10 23:32:45', NULL),
(10, 'Shoes', 2, '2022-07-10 23:32:56', '2022-07-10 23:32:56', NULL),
(11, 'Bags', 2, '2022-07-10 23:33:08', '2022-07-10 23:33:08', NULL),
(12, 'Accessories', 2, '2022-07-10 23:33:19', '2022-07-10 23:33:19', NULL),
(13, 'Jackets', 3, '2022-07-10 23:33:33', '2022-07-10 23:33:33', NULL),
(14, 'Pants', 3, '2022-07-10 23:33:45', '2022-07-10 23:33:45', NULL),
(15, 'Sweaters & Shirts', 3, '2022-07-10 23:33:56', '2022-07-10 23:33:56', NULL),
(16, 'Shoes', 3, '2022-07-10 23:34:09', '2022-07-10 23:34:09', NULL),
(17, 'Bags', 3, '2022-07-10 23:34:24', '2022-07-10 23:34:24', NULL),
(18, 'Accessories', 3, '2022-07-10 23:34:35', '2022-07-10 23:34:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `thumb_images`
--

CREATE TABLE `thumb_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `thumb_image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `thumb_images`
--

INSERT INTO `thumb_images` (`id`, `thumb_image`, `product_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '165750405030.jpg', 1, '2022-07-10 23:47:30', '2022-07-10 23:47:30', NULL),
(2, '165750405060.jpg', 1, '2022-07-10 23:47:30', '2022-07-10 23:47:30', NULL),
(3, '165750405079.jpg', 1, '2022-07-10 23:47:31', '2022-07-10 23:47:31', NULL),
(4, '165750405130.jpg', 1, '2022-07-10 23:47:31', '2022-07-10 23:47:31', NULL),
(5, '165750434370.jpg', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(6, '165750434349.jpg', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(7, '165750434361.jpg', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(8, '165750434313.jpg', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(9, '165750434349.jpg', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(10, '165750434374.jpg', 2, '2022-07-10 23:52:23', '2022-07-10 23:52:23', NULL),
(11, '165750434436.jpg', 2, '2022-07-10 23:52:24', '2022-07-10 23:52:24', NULL),
(12, '165750458656.jpg', 3, '2022-07-10 23:56:26', '2022-07-10 23:56:26', NULL),
(13, '165750458676.jpg', 3, '2022-07-10 23:56:26', '2022-07-10 23:56:26', NULL),
(14, '165750458630.jpg', 3, '2022-07-10 23:56:26', '2022-07-10 23:56:26', NULL),
(15, '165750458650.jpg', 3, '2022-07-10 23:56:26', '2022-07-10 23:56:26', NULL),
(16, '165750475347.jpg', 4, '2022-07-10 23:59:13', '2022-07-10 23:59:13', NULL),
(17, '165750475367.jpg', 4, '2022-07-10 23:59:13', '2022-07-10 23:59:13', NULL),
(18, '165750475367.jpg', 4, '2022-07-10 23:59:13', '2022-07-10 23:59:13', NULL),
(19, '165750475323.jpg', 4, '2022-07-10 23:59:13', '2022-07-10 23:59:13', NULL),
(20, '165750475391.jpg', 4, '2022-07-10 23:59:13', '2022-07-10 23:59:13', NULL),
(21, '165750497367.jpg', 5, '2022-07-11 00:02:53', '2022-07-11 00:02:53', NULL),
(22, '165750497342.jpg', 5, '2022-07-11 00:02:53', '2022-07-11 00:02:53', NULL),
(23, '165750497322.jpg', 5, '2022-07-11 00:02:54', '2022-07-11 00:02:54', NULL),
(24, '165750497415.jpg', 5, '2022-07-11 00:02:54', '2022-07-11 00:02:54', NULL),
(25, '165750497495.jpg', 5, '2022-07-11 00:02:54', '2022-07-11 00:02:54', NULL),
(26, '165750520124.jpg', 6, '2022-07-11 00:06:41', '2022-07-11 00:06:41', NULL),
(27, '165750520143.jpg', 6, '2022-07-11 00:06:41', '2022-07-11 00:06:41', NULL),
(28, '165750520149.jpg', 6, '2022-07-11 00:06:41', '2022-07-11 00:06:41', NULL),
(29, '165750520190.jpg', 6, '2022-07-11 00:06:41', '2022-07-11 00:06:41', NULL),
(30, '165750520251.jpg', 6, '2022-07-11 00:06:42', '2022-07-11 00:06:42', NULL),
(31, '165750520213.jpg', 6, '2022-07-11 00:06:42', '2022-07-11 00:06:42', NULL),
(32, '165750520214.jpg', 6, '2022-07-11 00:06:42', '2022-07-11 00:06:42', NULL),
(33, '165750520265.jpg', 6, '2022-07-11 00:06:42', '2022-07-11 00:06:42', NULL),
(34, '165750541597.jpg', 7, '2022-07-11 00:10:15', '2022-07-11 00:10:15', NULL),
(35, '165750584276.jpg', 10, '2022-07-11 00:17:22', '2022-07-11 00:17:22', NULL),
(36, '165750584256.jpg', 10, '2022-07-11 00:17:22', '2022-07-11 00:17:22', NULL),
(37, '165750584239.jpg', 10, '2022-07-11 00:17:22', '2022-07-11 00:17:22', NULL),
(38, '165750584273.jpg', 10, '2022-07-11 00:17:22', '2022-07-11 00:17:22', NULL),
(39, '165750584210.jpg', 10, '2022-07-11 00:17:22', '2022-07-11 00:17:22', NULL),
(40, '165750606388.jpg', 11, '2022-07-11 00:21:03', '2022-07-11 00:21:03', NULL),
(41, '165750606338.jpg', 11, '2022-07-11 00:21:03', '2022-07-11 00:21:03', NULL),
(42, '165750606385.jpg', 11, '2022-07-11 00:21:03', '2022-07-11 00:21:03', NULL),
(43, '165750615212.jpg', 12, '2022-07-11 00:22:32', '2022-07-11 00:22:32', NULL),
(44, '165750615264.jpg', 12, '2022-07-11 00:22:32', '2022-07-11 00:22:32', NULL),
(45, '165750631746.jpg', 13, '2022-07-11 00:25:17', '2022-07-11 00:25:17', NULL),
(46, '165750631715.jpg', 13, '2022-07-11 00:25:17', '2022-07-11 00:25:17', NULL),
(47, '165750631759.jpg', 13, '2022-07-11 00:25:17', '2022-07-11 00:25:17', NULL),
(48, '165750645611.jpg', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36', NULL),
(49, '165750645671.jpg', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36', NULL),
(50, '165750645612.jpg', 14, '2022-07-11 00:27:36', '2022-07-11 00:27:36', NULL),
(51, '165750698828.jpg', 16, '2022-07-11 00:36:28', '2022-07-11 00:36:28', NULL),
(52, '165750698889.jpg', 16, '2022-07-11 00:36:28', '2022-07-11 00:36:28', NULL),
(53, '165750718087.jpg', 17, '2022-07-11 00:39:40', '2022-07-11 00:39:40', NULL),
(54, '165750718034.jpg', 17, '2022-07-11 00:39:40', '2022-07-11 00:39:40', NULL),
(55, '165750718041.jpg', 17, '2022-07-11 00:39:40', '2022-07-11 00:39:40', NULL),
(56, '165750718086.jpg', 17, '2022-07-11 00:39:40', '2022-07-11 00:39:40', NULL),
(57, '165750972955.jpg', 19, '2022-07-11 01:22:09', '2022-07-11 01:22:09', NULL),
(58, '165750972914.jpg', 19, '2022-07-11 01:22:09', '2022-07-11 01:22:09', NULL),
(59, '165750972935.jpg', 19, '2022-07-11 01:22:09', '2022-07-11 01:22:09', NULL),
(60, '165750972966.jpg', 19, '2022-07-11 01:22:09', '2022-07-11 01:22:09', NULL),
(61, '165750972978.jpg', 19, '2022-07-11 01:22:09', '2022-07-11 01:22:09', NULL),
(62, '165751014947.jpg', 22, '2022-07-11 01:29:09', '2022-07-11 01:29:09', NULL),
(63, '165751014930.jpg', 22, '2022-07-11 01:29:09', '2022-07-11 01:29:09', NULL),
(64, '165751015099.jpg', 22, '2022-07-11 01:29:10', '2022-07-11 01:29:10', NULL),
(65, '165751015070.jpg', 22, '2022-07-11 01:29:10', '2022-07-11 01:29:10', NULL),
(66, '165751035363.jpg', 23, '2022-07-11 01:32:33', '2022-07-11 01:32:33', NULL),
(67, '165751035351.jpg', 23, '2022-07-11 01:32:33', '2022-07-11 01:32:33', NULL),
(68, '165751035391.jpg', 23, '2022-07-11 01:32:33', '2022-07-11 01:32:33', NULL),
(69, '165751035352.jpg', 23, '2022-07-11 01:32:33', '2022-07-11 01:32:33', NULL),
(70, '165751035321.jpg', 23, '2022-07-11 01:32:33', '2022-07-11 01:32:33', NULL),
(71, '165751048088.jpg', 24, '2022-07-11 01:34:40', '2022-07-11 01:34:40', NULL),
(72, '165751048043.jpg', 24, '2022-07-11 01:34:40', '2022-07-11 01:34:40', NULL),
(73, '165751048036.jpg', 24, '2022-07-11 01:34:40', '2022-07-11 01:34:40', NULL),
(74, '165751048095.jpg', 24, '2022-07-11 01:34:40', '2022-07-11 01:34:40', NULL),
(75, '165751048084.jpg', 24, '2022-07-11 01:34:40', '2022-07-11 01:34:40', NULL),
(76, '165751075212.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(77, '165751075217.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(78, '165751075247.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(79, '165751075249.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(80, '165751075210.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(81, '165751075244.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(82, '165751075243.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(83, '165751075262.jpg', 25, '2022-07-11 01:39:12', '2022-07-11 01:39:12', NULL),
(84, '165751085655.jpg', 26, '2022-07-11 01:40:56', '2022-07-11 01:40:56', NULL),
(85, '165751085631.jpg', 26, '2022-07-11 01:40:56', '2022-07-11 01:40:56', NULL),
(86, '165751085682.jpg', 26, '2022-07-11 01:40:56', '2022-07-11 01:40:56', NULL),
(87, '165751085661.jpg', 26, '2022-07-11 01:40:56', '2022-07-11 01:40:56', NULL),
(88, '165751085625.jpg', 26, '2022-07-11 01:40:56', '2022-07-11 01:40:56', NULL),
(89, '165751107868.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(90, '165751107827.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(91, '165751107811.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(92, '165751107896.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(93, '165751107835.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(94, '165751107871.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(95, '165751107876.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(96, '165751107825.jpg', 27, '2022-07-11 01:44:38', '2022-07-11 01:44:38', NULL),
(97, '165751137494.jpg', 29, '2022-07-11 01:49:34', '2022-07-11 01:49:34', NULL),
(98, '165751137416.jpg', 29, '2022-07-11 01:49:34', '2022-07-11 01:49:34', NULL),
(99, '165751137435.jpg', 29, '2022-07-11 01:49:34', '2022-07-11 01:49:34', NULL),
(100, '165751137492.jpg', 29, '2022-07-11 01:49:34', '2022-07-11 01:49:34', NULL),
(101, '165751148626.jpg', 30, '2022-07-11 01:51:26', '2022-07-11 01:51:26', NULL),
(102, '165751148696.jpg', 30, '2022-07-11 01:51:26', '2022-07-11 01:51:26', NULL),
(103, '165751148651.jpg', 30, '2022-07-11 01:51:26', '2022-07-11 01:51:26', NULL),
(104, '165751148642.jpg', 30, '2022-07-11 01:51:26', '2022-07-11 01:51:26', NULL),
(105, '165751251038.jpg', 32, '2022-07-11 02:08:30', '2022-07-11 02:08:30', NULL),
(106, '165751251030.jpg', 32, '2022-07-11 02:08:30', '2022-07-11 02:08:30', NULL),
(107, '165751251032.jpg', 32, '2022-07-11 02:08:30', '2022-07-11 02:08:30', NULL),
(108, '165751251042.jpg', 32, '2022-07-11 02:08:30', '2022-07-11 02:08:30', NULL),
(109, '165751251168.jpg', 32, '2022-07-11 02:08:31', '2022-07-11 02:08:31', NULL),
(110, '165751251145.jpg', 32, '2022-07-11 02:08:31', '2022-07-11 02:08:31', NULL),
(111, '165751251148.jpg', 32, '2022-07-11 02:08:31', '2022-07-11 02:08:31', NULL),
(112, '165751267828.jpg', 33, '2022-07-11 02:11:18', '2022-07-11 02:11:18', NULL),
(113, '165751267825.jpg', 33, '2022-07-11 02:11:18', '2022-07-11 02:11:18', NULL),
(114, '165751267882.jpg', 33, '2022-07-11 02:11:18', '2022-07-11 02:11:18', NULL),
(115, '165751267844.jpg', 33, '2022-07-11 02:11:18', '2022-07-11 02:11:18', NULL),
(116, '165751299388.jpg', 35, '2022-07-11 02:16:33', '2022-07-11 02:16:33', NULL),
(117, '1657512993100.jpg', 35, '2022-07-11 02:16:33', '2022-07-11 02:16:33', NULL),
(118, '165751315135.jpg', 36, '2022-07-11 02:19:11', '2022-07-11 02:19:11', NULL),
(119, '165751315185.jpg', 36, '2022-07-11 02:19:11', '2022-07-11 02:19:11', NULL),
(120, '165751315180.jpg', 36, '2022-07-11 02:19:11', '2022-07-11 02:19:11', NULL),
(121, '165751315125.jpg', 36, '2022-07-11 02:19:11', '2022-07-11 02:19:11', NULL),
(122, '165751315161.jpg', 36, '2022-07-11 02:19:11', '2022-07-11 02:19:11', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint(4) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Grace', 'Edraak', 'admin@edraakmc.com', '2022-07-11 01:30:08', '$2y$10$bM06FtVtvBl6Ja0oInBnKuCsna1pEGDsvLfqLPLwLj9bMgC3gGoaS', 1, NULL, '2022-07-10 23:29:51', '2022-07-10 23:29:51', NULL);

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
  ADD UNIQUE KEY `categories_name_unique` (`name`);

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
  ADD KEY `products_category_id_foreign` (`category_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_subcategory_subcategory_id_foreign` (`subcategory_id`),
  ADD KEY `product_subcategory_product_id_foreign` (`product_id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategories_category_id_foreign` (`category_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_sizes`
--
ALTER TABLE `product_sizes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT for table `product_subcategory`
--
ALTER TABLE `product_subcategory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `thumb_images`
--
ALTER TABLE `thumb_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_address_id_foreign` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `thumb_images`
--
ALTER TABLE `thumb_images`
  ADD CONSTRAINT `thumb_images_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
