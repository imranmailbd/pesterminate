-- phpMyAdmin SQL Dump
-- version 4.9.11
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 30, 2023 at 09:13 PM
-- Server version: 10.5.19-MariaDB-cll-lve
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skitsbd_pesterminate`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_feed`
--

CREATE TABLE `activity_feed` (
  `activity_feed_id` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `users_id` tinyint(1) NOT NULL,
  `activity_feed_title` varchar(255) NOT NULL,
  `activity_feed_name` text NOT NULL,
  `activity_feed_link` varchar(255) NOT NULL,
  `uri_table_name` varchar(255) NOT NULL,
  `uri_table_field_name` varchar(255) NOT NULL,
  `field_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `activity_feed`
--

INSERT INTO `activity_feed` (`activity_feed_id`, `created_on`, `users_id`, `activity_feed_title`, `activity_feed_name`, `activity_feed_link`, `uri_table_name`, `uri_table_field_name`, `field_value`) VALUES
(1, '2023-03-19 18:47:04', 1, 'Menu was edited', 'HOME', '/Manage_Data/front_menu/view/2', 'front_menu', 'front_menu_publish', '1'),
(2, '2023-03-19 18:47:27', 1, 'Menu was edited', 'PESTS', '/Manage_Data/front_menu/view/3', 'front_menu', 'front_menu_publish', '1'),
(3, '2023-03-19 21:06:46', 1, 'Menu was edited', 'PESTS', '/Manage_Data/front_menu/view/3', 'front_menu', 'front_menu_publish', '1'),
(4, '2023-03-19 21:07:26', 1, 'Menu was edited', 'SERVICES', '/Manage_Data/front_menu/view/10', 'front_menu', 'front_menu_publish', '1'),
(5, '2023-03-19 21:07:33', 1, 'Menu was edited', 'PESTS', '/Manage_Data/front_menu/view/3', 'front_menu', 'front_menu_publish', '1'),
(6, '2023-03-19 21:14:30', 1, 'Menu was edited', 'NEWS', '/Manage_Data/front_menu/view/4', 'front_menu', 'front_menu_publish', '1'),
(7, '2023-03-19 21:14:51', 1, 'Menu was edited', 'CONTACTS', '/Manage_Data/front_menu/view/5', 'front_menu', 'front_menu_publish', '1'),
(8, '2023-03-19 21:15:10', 1, 'Menu was edited', 'CONTACTS', '/Manage_Data/front_menu/view/5', 'front_menu', 'front_menu_publish', '1'),
(9, '2023-03-19 22:00:40', 1, 'Pages was edited', 'Top Header Email', '/Manage_Data/pages/view/2', 'pages', 'pages_publish', '1'),
(10, '2023-03-19 22:00:47', 1, 'Pages was edited', 'Top Header Phone', '/Manage_Data/pages/view/1', 'pages', 'pages_publish', '1'),
(11, '2023-03-19 23:20:06', 1, 'Videos was edited', 'Ant Invasions', '/Manage_Data/videos/view/3', 'videos', 'videos_publish', '1'),
(12, '2023-03-19 23:20:59', 1, 'Videos was edited', 'LEPTOSPIROSIS', '/Manage_Data/videos/view/2', 'videos', 'videos_publish', '1'),
(13, '2023-03-19 23:22:09', 1, 'Videos was edited', 'Cockroach Health Risks', '/Manage_Data/videos/view/1', 'videos', 'videos_publish', '1'),
(14, '2023-03-20 21:16:22', 1, 'branches was edited', 'Main Branch', '/Settings/branches/view/1', 'branches', 'branches_publish', '1'),
(15, '2023-03-20 21:32:35', 1, 'User was edited', 'Info Pesterminate', '/Settings/users_setup/view/3', 'users', 'users_id', '3'),
(16, '2023-03-20 21:32:55', 1, 'User was edited', 'Abdus Shobhan', '/Settings/users_setup/view/2', 'users', 'users_id', '2'),
(17, '2023-03-21 20:10:40', 1, 'Pages was edited', 'Top Header Phone', '/Manage_Data/pages/view/1', 'pages', 'pages_publish', '1'),
(18, '2023-04-15 04:25:20', 1, 'Pages was edited', 'Pests', '/Manage_Data/pages/view/3', 'pages', 'pages_publish', '1'),
(19, '2023-04-16 02:24:17', 1, 'Menu was edited', 'SERVICES', '/Manage_Data/front_menu/view/10', 'front_menu', 'front_menu_publish', '1'),
(20, '2023-04-16 03:58:35', 1, 'Menu was edited', 'NEWS', '/Manage_Data/front_menu/view/4', 'front_menu', 'front_menu_publish', '1'),
(21, '2023-04-21 00:12:28', 1, 'Menu was edited', 'CONTACTS', '/Manage_Data/front_menu/view/5', 'front_menu', 'front_menu_publish', '1'),
(22, '2023-04-21 00:15:41', 1, 'Menu was edited', 'CONTACTS', '/Manage_Data/front_menu/view/5', 'front_menu', 'front_menu_publish', '1'),
(23, '2023-04-30 22:05:22', 1, 'Pages was edited', 'Top Header Phone', '/Manage_Data/pages/view/1', 'pages', 'pages_publish', '1'),
(24, '2023-05-01 02:34:08', 1, 'branches was edited', 'Dalmatian', '/Settings/branches/view/2', 'branches', 'branches_publish', '1'),
(25, '2023-05-01 02:34:23', 1, 'branches was edited', 'Main Branch', '/Settings/branches/view/1', 'branches', 'branches_publish', '1'),
(26, '2023-05-01 04:08:47', 1, 'Pages was edited', 'About Pesterminate', '/Manage_Data/pages/view/3', 'pages', 'pages_publish', '1'),
(27, '2023-05-01 04:09:04', 1, 'Menu was edited', 'PESTS', '/Manage_Data/front_menu/view/3', 'front_menu', 'front_menu_publish', '1'),
(28, '2023-05-01 06:16:54', 3, 'Pages was edited', 'Best Pest Control in Toronto', '/Manage_Data/pages/view/12', 'pages', 'pages_publish', '1'),
(29, '2023-05-01 06:17:31', 3, 'Pages was edited', 'Residential Pest Control in Toronto', '/Manage_Data/pages/view/11', 'pages', 'pages_publish', '1'),
(30, '2023-05-01 06:36:20', 3, 'Pages was edited', 'Ants and Carpenter Ants Removal treatment in Toronto', '/Manage_Data/pages/view/15', 'pages', 'pages_publish', '1'),
(31, '2023-05-09 02:39:31', 1, 'Pages was edited', 'Rat Removal treatment in Toronto', '/Manage_Data/pages/view/14', 'pages', 'pages_publish', '1'),
(32, '2023-05-29 23:05:56', 2, 'Pages was edited', 'Best Pest Control in Toronto', '/Manage_Data/pages/view/12', 'pages', 'pages_publish', '1'),
(33, '2023-05-29 23:46:46', 2, 'Pages was edited', 'About Pesterminate', '/Manage_Data/pages/view/3', 'pages', 'pages_publish', '1'),
(34, '2023-05-29 23:57:52', 2, 'Pages was edited', 'Residential Pest Control in Toronto', '/Manage_Data/pages/view/11', 'pages', 'pages_publish', '1'),
(35, '2023-05-30 00:03:43', 2, 'Pages was edited', 'Mice removal treatment in Toronto', '/Manage_Data/pages/view/13', 'pages', 'pages_publish', '1'),
(36, '2023-05-30 00:05:16', 2, 'Pages was edited', 'Mice removal treatment in Toronto', '/Manage_Data/pages/view/13', 'pages', 'pages_publish', '1'),
(37, '2023-05-30 00:06:49', 2, 'Menu was edited', 'Rat Removal treatment in Toronto', '/Manage_Data/front_menu/view/18', 'front_menu', 'front_menu_publish', '1'),
(38, '2023-05-30 00:13:36', 2, 'Pages was edited', 'Rat Removal treatment in Toronto', '/Manage_Data/pages/view/14', 'pages', 'pages_publish', '1'),
(39, '2023-05-30 00:15:39', 2, 'Menu was edited', 'Ants and Carpenter Ants Removal treatment in Toronto', '/Manage_Data/front_menu/view/19', 'front_menu', 'front_menu_publish', '1'),
(40, '2023-05-30 00:36:16', 2, 'Pages was edited', 'Ants and Carpenter Ants Removal treatment in Toronto', '/Manage_Data/pages/view/15', 'pages', 'pages_publish', '1'),
(41, '2023-05-30 00:38:09', 2, 'Pages was edited', 'Ants and Carpenter Ants Removal treatment in Toronto', '/Manage_Data/pages/view/15', 'pages', 'pages_publish', '1'),
(42, '2023-05-30 00:39:30', 2, 'Pages was edited', 'Ants and Carpenter Ants Removal treatment in Toronto', '/Manage_Data/pages/view/15', 'pages', 'pages_publish', '1'),
(43, '2023-05-30 00:41:59', 2, 'Pages was edited', 'About Pesterminate', '/Manage_Data/pages/view/3', 'pages', 'pages_publish', '1');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointments_id` int(11) NOT NULL,
  `appointments_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `appointments_no` int(11) NOT NULL,
  `services_id` int(11) NOT NULL,
  `customers_id` int(11) NOT NULL,
  `services_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `appointments_date` datetime NOT NULL,
  `notifications` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointments_id`, `appointments_publish`, `created_on`, `last_updated`, `users_id`, `appointments_no`, `services_id`, `customers_id`, `services_type`, `description`, `appointments_date`, `notifications`) VALUES
(1, 1, '2023-03-19 03:52:49', '2023-03-19 03:52:49', 0, 1, 1, 1, 'RESIDENTIAL', 'I am fetching some problems for Cockroach. Please set appointment as soon as you can.', '2023-03-19 03:52:49', 0),
(2, 1, '2023-03-21 17:18:05', '2023-03-21 17:18:05', 0, 2, 8, 2, '', '', '2023-03-21 00:00:00', 0),
(3, 1, '2023-03-21 17:21:56', '2023-03-21 17:21:56', 0, 3, 5, 2, '', '', '2023-03-21 00:00:00', 0),
(4, 1, '2023-03-21 17:22:10', '2023-03-21 17:22:10', 0, 4, 5, 2, '', '', '2023-03-21 00:00:00', 0),
(5, 1, '2023-03-21 20:12:36', '2023-03-21 20:12:36', 0, 5, 5, 4, '', '', '2023-03-21 00:00:00', 0),
(6, 1, '2023-04-07 22:35:04', '2023-04-07 22:35:04', 0, 6, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(7, 1, '2023-04-07 22:35:07', '2023-04-07 22:35:07', 0, 7, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(8, 1, '2023-04-07 22:35:08', '2023-04-07 22:35:08', 0, 8, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(9, 1, '2023-04-07 22:35:09', '2023-04-07 22:35:09', 0, 9, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(10, 1, '2023-04-07 22:35:09', '2023-04-07 22:35:09', 0, 10, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(11, 1, '2023-04-07 22:35:10', '2023-04-07 22:35:10', 0, 11, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(12, 1, '2023-04-07 22:35:11', '2023-04-07 22:35:11', 0, 12, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(13, 1, '2023-04-07 22:35:12', '2023-04-07 22:35:12', 0, 13, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(14, 1, '2023-04-07 22:35:12', '2023-04-07 22:35:12', 0, 14, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(15, 1, '2023-04-07 22:35:13', '2023-04-07 22:35:13', 0, 15, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(16, 1, '2023-04-07 22:35:14', '2023-04-07 22:35:14', 0, 16, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(17, 1, '2023-04-07 22:35:15', '2023-04-07 22:35:15', 0, 17, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(18, 1, '2023-04-07 22:35:15', '2023-04-07 22:35:15', 0, 18, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(19, 1, '2023-04-07 22:35:15', '2023-04-07 22:35:15', 0, 19, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(20, 1, '2023-04-07 22:35:16', '2023-04-07 22:35:16', 0, 20, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(21, 1, '2023-04-07 22:35:16', '2023-04-07 22:35:16', 0, 21, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(22, 1, '2023-04-07 22:35:17', '2023-04-07 22:35:17', 0, 22, 7, 5, '', '', '2023-04-07 00:00:00', 0),
(23, 1, '2023-04-16 04:50:11', '2023-04-16 04:50:11', 1, 23, 7, 8, '', '', '2023-04-16 00:00:00', 2),
(24, 1, '2023-04-16 05:16:45', '2023-04-16 05:16:45', 1, 24, 7, 9, '', 'test desc', '2023-04-16 00:00:00', 2),
(25, 1, '2023-04-16 05:20:09', '2023-04-16 05:20:09', 1, 25, 7, 10, '', 'test description', '2023-04-16 00:00:00', 2),
(26, 1, '2023-05-17 12:29:51', '2023-05-17 12:29:51', 1, 26, 7, 12, '', '', '2023-05-17 00:00:00', 2),
(27, 1, '2023-05-17 12:30:17', '2023-05-17 12:30:17', 1, 27, 7, 12, '', '', '2023-05-17 00:00:00', 2),
(28, 1, '2023-05-17 22:34:12', '2023-05-17 22:34:12', 1, 28, 8, 2, '', '', '2023-05-17 00:00:00', 2),
(29, 1, '2023-05-25 06:28:01', '2023-05-25 06:28:01', 1, 29, 7, 13, '', 'test dhaka', '2023-05-25 00:00:00', 2),
(30, 1, '2023-05-25 06:44:32', '2023-05-25 06:44:32', 1, 30, 7, 13, '', 'test dhaka', '2023-05-25 00:00:00', 2),
(31, 1, '2023-05-25 06:49:08', '2023-05-25 06:49:08', 1, 31, 7, 13, '', 'test dhaka', '2023-05-25 00:00:00', 2),
(32, 1, '2023-05-25 06:51:09', '2023-05-25 06:51:09', 1, 32, 7, 13, '', 'test dhaka', '2023-05-25 00:00:00', 2),
(33, 1, '2023-05-29 01:58:09', '2023-05-29 01:58:09', 1, 33, 7, 2, '', 'This is testing', '2023-05-29 00:00:00', 2),
(34, 1, '2023-05-29 02:16:49', '2023-05-29 02:16:49', 1, 34, 7, 2, '', 'This is a testing note.', '2023-05-29 00:00:00', 2),
(35, 1, '2023-05-29 02:17:44', '2023-05-29 02:17:44', 1, 35, 8, 2, '', 'sadf sadfasd fasdf', '2023-05-29 00:00:00', 2),
(36, 1, '2023-05-29 03:05:31', '2023-05-29 03:05:31', 1, 36, 7, 2, '', 'Test asdfasdf', '2023-05-29 00:00:00', 2);

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `banners_id` int(11) NOT NULL,
  `banners_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`banners_id`, `banners_publish`, `created_on`, `last_updated`, `users_id`, `name`, `description`) VALUES
(1, 1, '2023-03-19 19:19:52', '2023-03-19 19:19:52', 1, 'Say Goodbye to Pests', 'Effective Pest Control Solutions with Pesterminate.'),
(2, 1, '2023-03-19 19:20:37', '2023-03-19 19:20:37', 1, 'Your Ultimate Pest Control Partner', 'Get Rid of Pests for Good with Pesterminate.'),
(3, 1, '2023-03-19 19:21:03', '2023-03-19 19:21:03', 1, 'Pesterminate', 'Your One-Stop Solution for Safe and Reliable Pest Control.'),
(4, 1, '2023-03-19 19:21:24', '2023-03-19 19:21:24', 1, 'Protecting Your Home and Family', 'Expert Pest Control Services with Pesterminate'),
(5, 1, '2023-03-19 19:22:42', '2023-03-19 19:22:42', 1, 'The Pest Control Solution', 'Pesterminate  Trusted by Homeowners and Businesses');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `branches_id` int(11) NOT NULL,
  `branches_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `google_map` text NOT NULL,
  `working_hours` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`branches_id`, `branches_publish`, `created_on`, `last_updated`, `users_id`, `name`, `address`, `google_map`, `working_hours`) VALUES
(1, 1, '2023-03-20 02:06:18', '2023-05-01 02:34:23', 1, 'Main Branch', '3098 Danforth Ave, Unit 204\r\nToronto, ON M1L1B1', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2884.916944563348!2d-79.290207049875!3d43.6914900578711!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4ce9e66387eaf%3A0xaaef2b750681afb1!2s3098%20Danforth%20Ave%20%23204%2C%20Scarborough%2C%20ON%20M1L%201B1!5e0!3m2!1sen!2sca!4v1679292302785!5m2!1sen!2sca\\\" width=\\\"100%\\\" height=\\\"300\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\"></iframe>', 'Monday to Friday \r\n9am - 6pm'),
(2, 1, '2023-03-21 20:40:18', '2023-05-01 02:34:08', 1, 'Dalmatian', '78 Dalmatian Crescent, Scarborough, ON M1C 4W', '<iframe src=\\\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2880.1209516531235!2d-79.16484009999999!3d43.791103199999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89d4da3795c7b2c9%3A0x254e4600836184bd!2s78%20Dalmatian%20Crescent%2C%20Scarborough%2C%20ON%20M1C%204W6!5e0!3m2!1sen!2sca!4v1679445577048!5m2!1sen!2sca\\\" width=\\\"100%\\\" height=\\\"300\\\" style=\\\"border:0;\\\" allowfullscreen=\\\"\\\" loading=\\\"lazy\\\" referrerpolicy=\\\"no-referrer-when-downgrade\\\"></iframe>', 'Monday to Friday\r\n9:00 AM to 7:30 PM');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customers_id` int(11) NOT NULL,
  `customers_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `offers_email` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customers_id`, `customers_publish`, `created_on`, `last_updated`, `users_id`, `name`, `phone`, `email`, `address`, `offers_email`) VALUES
(1, 1, '2023-03-19 03:30:58', '2023-03-19 03:30:58', 1, 'Md. Abdus Shobhan', '416-509-1935', 'mdshobhancse@gmail.com', '2942 Denforth Avenew, Toronto, Canada', 1),
(2, 1, '2023-03-19 11:27:17', '2023-03-19 11:28:57', 1, 'Md. Abdus Shobhan', '4165091935', 'shobhancse@gmail.com', '2942 Danforth Ave,', 1),
(3, 0, '2023-03-19 18:37:30', '2023-03-19 18:37:30', 1, 'Test', '', '', '', 0),
(4, 1, '2023-03-21 20:12:36', '2023-03-21 20:12:36', 0, 'Ruhul Amin', '09332453-05932', 'info@perterminate.ca', '', 1),
(5, 1, '2023-04-07 22:35:04', '2023-04-07 22:35:04', 0, 'Shaker Hossain', '+8801612554925', 'shaker.hossain87@gmail.com', '', 1),
(6, 1, '2023-04-16 04:43:10', '2023-04-16 04:43:10', 0, 'Md', 'Karim', 'karim@gmail.com', '', 1),
(7, 1, '2023-04-16 04:45:24', '2023-04-16 04:45:24', 0, 'Md Karim', '34323', 'karim@gmail.com', '', 1),
(8, 1, '2023-04-16 04:50:11', '2023-04-16 04:50:11', 0, 'Md Karim', '4656567', 'karim@gmail.com', '', 1),
(9, 1, '2023-04-16 05:16:45', '2023-04-16 05:16:45', 0, 'Md Ismail', '2345', 'ismail@gmail.com', '', 1),
(10, 1, '2023-04-16 05:20:09', '2023-04-16 05:20:09', 0, 'Md Imran', '23434234', 'imran@gmail.com', '', 1),
(11, 1, '2023-04-30 04:48:16', '2023-04-30 04:48:16', 0, '', '', '', '', 1),
(12, 1, '2023-05-17 12:29:51', '2023-05-17 12:29:51', 0, 'Tareq', '5149662137', 'tareqrahim@gmail.com', '', 1),
(13, 1, '2023-05-25 06:28:01', '2023-05-25 06:28:01', 0, 'Muhammed', '2343423', 'imranmailbd@gmail.com', 'Dhaka', 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer_reviews`
--

CREATE TABLE `customer_reviews` (
  `customer_reviews_id` int(11) NOT NULL,
  `customer_reviews_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `reviews_date` date NOT NULL,
  `reviews_rating` double NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `customer_reviews`
--

INSERT INTO `customer_reviews` (`customer_reviews_id`, `customer_reviews_publish`, `created_on`, `last_updated`, `users_id`, `name`, `address`, `reviews_date`, `reviews_rating`, `description`) VALUES
(1, 1, '2023-03-19 22:17:32', '2023-03-19 22:17:32', 1, 'Home Stars', 'Toronto', '2017-01-17', 5, 'I called Ruhul Amin to clean all Roaches from our kitchen and cabinet. He did the job in April 2017 and now is November, last eight months I didn\\\'t see anything. Before I called other companies but I am not satisfied. Ruhul Amin did excellent work. we are really happy with his work.'),
(2, 1, '2023-03-19 22:19:05', '2023-03-19 22:19:05', 1, 'RODENT INVESTATION', 'Pickering', '2028-02-02', 5, 'We had a rat visiting us in the house, and within hours of having placed the rat poison and traps the rodent was caught. With his help, we found an access hole in the house. For which we are grateful. Great job.'),
(3, 1, '2023-03-19 22:20:32', '2023-03-19 22:20:32', 1, 'Mariam', 'Toronto', '2018-03-05', 5, 'We had sleepless nights, encountering walking/tapping sounds of rats inside our wall! The expert guy was here, investigated with a camera inside the walls, quickly identified the entry point, set up a trap, etc. within a day we got results! Happy & satisfied with the service! Go for it!!'),
(4, 1, '2023-03-19 22:21:39', '2023-03-19 22:21:39', 1, 'Saad', 'Toronto', '2019-04-08', 5, 'Ruhul Amin did Excellent work for me he got rid of all the Roaches in my kitchen cape. if you got Roaches In the house he is the Wright Guy to call for the job.'),
(5, 1, '2023-03-19 22:25:34', '2023-03-19 22:25:34', 1, 'Allisa', 'Ajax, ON', '2022-04-10', 5, 'Ruhul was so easy to deal with and able to accommodate us quickly. We had a potential mouse issue and wanted to have it looked at as soon as possible. He was in and out and made sure to explain to process to us so we understood what was going on. Thank you so much!'),
(6, 1, '2023-03-19 22:28:08', '2023-03-19 22:28:08', 1, 'Shahin in Etobicoke', 'Toronto, ON', '2022-12-19', 5, 'Experienced, long time in business, very professional, highly recommended. The cost is a bit high but worthy it!'),
(7, 1, '2023-03-19 22:30:16', '2023-03-19 22:30:16', 1, 'Private User', 'Toronto, ON', '2009-01-13', 5, 'Last spring I had an ant problem in my house. Ruhul came and dealt with it quickly and professionally, instructing me to call him back if the problem wasn\\\'t solved. Pretty soon, those ants were gone. Now, I have a mice problem and I was able to book him within a couple of days. He came and laid out his bait traps in various locations in a matter of minutes, and instructed me once again to call him back if I still see activity after 10 days. Very quick, very efficient, very knowledgeable, very well equipped, and very reasonably priced. Highly recommend.'),
(8, 1, '2023-03-19 22:32:49', '2023-03-19 22:32:49', 1, 'Private User', 'Markham, ON', '2010-05-02', 5, 'We have had a mice problem. Rahul came on time, provided wonderful service, and was available to answer our further concerns. He even came today to address an issue and provided me with a good solution. I recommend this service 100%'),
(9, 1, '2023-03-19 22:35:52', '2023-03-19 22:35:52', 1, 'Sknath in Vaughan', 'Toronto, ON', '2016-01-23', 5, 'Around my property, a lot of ant making problems in our daily life. I called Pesterminate Inc. and Mr. Ruhul visited my place and made an appointment for saving my kids from the ant. The next day, he did a treatment, and the ants gone within 2 days. Now my property is ant free and we are good to move around the house. Mr. Ruhul is an expert pest control guy having exceptional knowledge about pests, insects, and small animals.\r\n\r\nThank you so much for your excellent services.'),
(10, 1, '2023-03-19 22:36:53', '2023-03-19 22:36:53', 1, 'Linda from Durham Region', 'Whitby, ON', '2015-06-04', 5, 'We saw a rat feeding at the base of our bird feeder and were mortified. My husband discovered that they were burrowing under our shed. We got quotes from 3 different companies to exterminate them. Ruhul from Pesterminate seemed to be the only one who could come the next day and help us. We had questions about the poison (would it hurt the other wildlife?) and he was very informative and knew his stuff. He answered all our questions (no, it won\\\'t!). About a week later we still saw activity and let him know. He came back and put more bait down immediately. That did the trick! Have not seen another in over 2 weeks and no activity around the shed. I would not hesitate to recommend Ruhul and his company to get rid of nasty pests. Very responsive and very professional.');

-- --------------------------------------------------------

--
-- Table structure for table `front_menu`
--

CREATE TABLE `front_menu` (
  `front_menu_id` smallint(6) NOT NULL,
  `front_menu_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `last_updated` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `users_id` smallint(6) NOT NULL,
  `root_menu_id` smallint(6) NOT NULL,
  `sub_menu_id` smallint(6) NOT NULL,
  `name` varchar(255) NOT NULL,
  `menu_uri` varchar(255) NOT NULL,
  `menu_position` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `front_menu`
--

INSERT INTO `front_menu` (`front_menu_id`, `front_menu_publish`, `created_on`, `last_updated`, `users_id`, `root_menu_id`, `sub_menu_id`, `name`, `menu_uri`, `menu_position`) VALUES
(1, 1, '2023-03-19 18:42:51', '2023-03-19 18:42:51', 1, 0, 0, 'Header Menu', 'header-menu', 1),
(2, 1, '2023-03-19 18:43:44', '2023-03-19 18:47:04', 1, 1, 0, 'HOME', 'home', 1),
(3, 1, '2023-03-19 18:45:12', '2023-05-01 04:09:04', 1, 1, 0, 'PESTS', 'about-pesterminate', 2),
(4, 1, '2023-03-19 18:49:43', '2023-04-16 03:58:34', 1, 1, 0, 'NEWS', 'news-articles', 4),
(5, 1, '2023-03-19 18:55:27', '2023-04-21 00:15:40', 1, 1, 0, 'CONTACTS', 'contact-us', 5),
(6, 0, '2023-03-19 18:56:20', '2023-03-19 18:56:20', 1, 1, 3, 'COCKROACHES', 'cockroach-control', 3),
(7, 0, '2023-03-19 18:56:56', '2023-03-19 18:56:56', 1, 1, 3, 'MICE', 'mice-control', 4),
(8, 0, '2023-03-19 18:57:41', '2023-03-19 18:57:41', 1, 1, 3, 'FLIES', 'fly-control', 5),
(9, 0, '2023-03-19 18:58:22', '2023-03-19 18:58:22', 1, 1, 3, 'WASPS', 'wasp-nest-removal', 6),
(10, 1, '2023-03-19 21:03:14', '2023-04-16 02:24:17', 1, 1, 0, 'SERVICES', 'services-main', 3),
(11, 1, '2023-03-19 21:08:53', '2023-03-19 21:08:53', 1, 1, 10, 'RESIDENTIAL', 'residential', 1),
(12, 1, '2023-03-19 21:09:05', '2023-03-19 21:09:05', 1, 1, 10, 'COMMERCIAL', 'commercial', 2),
(13, 1, '2023-05-03 01:10:19', '2023-05-03 01:10:19', 1, 0, 0, 'Sidebar Menu', 'sidebar-menu', 1),
(14, 1, '2023-05-03 01:23:51', '2023-05-03 01:23:51', 1, 13, 0, 'Award Winning Toronto Pest Con', 'award-winning-toronto-pest-con', 1),
(15, 1, '2023-05-03 01:28:16', '2023-05-03 01:28:16', 1, 13, 14, 'Residential Pest Control in Toronto', 'residential-pest-control-in-toronto', 1),
(16, 1, '2023-05-03 01:28:39', '2023-05-03 01:28:39', 1, 13, 14, 'Best Pest Control in Toronto', 'best-pest-control-in-toronto', 2),
(17, 1, '2023-05-03 01:28:57', '2023-05-03 01:28:57', 1, 13, 14, 'Mice removal treatment in Toronto', 'mice-removal-treatment-in-toronto', 3),
(18, 1, '2023-05-03 01:29:24', '2023-05-30 00:06:49', 2, 13, 14, 'Rat Removal treatment in Toronto', 'rat-removal-treatment-in-toronto', 4),
(19, 1, '2023-05-03 01:29:43', '2023-05-30 00:15:39', 2, 13, 14, 'Ants and Carpenter Ants Removal treatment in Toronto', 'ants-and-carpenter-ants-removal-treatment-in-toronto', 5);

-- --------------------------------------------------------

--
-- Table structure for table `news_articles`
--

CREATE TABLE `news_articles` (
  `news_articles_id` int(11) NOT NULL,
  `news_articles_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `uri_value` varchar(100) NOT NULL,
  `news_articles_date` date NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `short_description` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `news_articles`
--

INSERT INTO `news_articles` (`news_articles_id`, `news_articles_publish`, `created_on`, `last_updated`, `users_id`, `name`, `uri_value`, `news_articles_date`, `created_by`, `short_description`, `description`) VALUES
(1, 1, '2023-03-20 01:54:56', '2023-04-26 00:38:10', 1, 'The Dangers of DIY Pest Control', 'the-dangers-of-diy-pest-control', '2023-03-13', 'Admin', 'This blog post could discuss the potential dangers of attempting to control pests without the help of a professional pest control service. The article could highlight the risks of using store-bought pesticides and other DIY methods that may not be effective in getting rid of pests or may even be harmful to humans and pets.', 'This blog post could discuss the potential dangers of attempting to control pests without the help of a professional pest control service. The article could highlight the risks of using store-bought pesticides and other DIY methods that may not be effective in getting rid of pests or may even be harmful to humans and pets.'),
(2, 1, '2023-03-20 02:03:30', '2023-04-26 00:37:19', 1, '10 Common Household Pests and How to Get Rid of Them', '10-common-household-pests-and-how-to-get-rid-of-them', '2016-07-19', 'Admin', 'This article could provide readers with information about common household pests, such as ants, cockroaches, and rodents, and offer tips and tricks for getting rid of them. The article could also cover preventative measures that homeowners can take to keep pests from entering their homes in the first place.', 'This article could provide readers with information about common household pests, such as ants, cockroaches, and rodents, and offer tips and tricks for getting rid of them. The article could also cover preventative measures that homeowners can take to keep pests from entering their homes in the first place.'),
(3, 1, '2023-03-20 19:30:35', '2023-04-26 00:36:15', 1, 'The Importance of Regular Pest Inspections', 'the-importance-of-regular-pest-inspections', '2020-10-02', 'Admin', 'This article could emphasize the importance of regular pest inspections for both homeowners and business owners. The post could explain how routine inspections can help identify potential pest problems early on and prevent them from becoming more serious and costly to address later. The article could also offer tips for finding a reputable pest control company to conduct the inspections.', 'This article could emphasize the importance of regular pest inspections for both homeowners and business owners. The post could explain how routine inspections can help identify potential pest problems early on and prevent them from becoming more serious and costly to address later. The article could also offer tips for finding a reputable pest control company to conduct the inspections.'),
(4, 1, '2023-03-20 19:31:49', '2023-04-26 00:37:45', 1, 'How to Choose the Right Pest Control Service', 'how-to-choose-the-right-pest-control-service', '2021-05-26', 'Admin', 'This post could provide readers with tips for selecting a reliable and effective pest control service. The article could cover topics such as what to look for in a pest control company, how to compare pricing and services, and what questions to ask before hiring a pest control service.', 'This post could provide readers with tips for selecting a reliable and effective pest control service. The article could cover topics such as what to look for in a pest control company, how to compare pricing and services, and what questions to ask before hiring a pest control service.'),
(5, 1, '2023-03-20 19:48:55', '2023-04-26 00:37:57', 1, 'How Climate Change is Affecting Pest Populations', 'how-climate-change-is-affecting-pest-populations', '2010-08-04', 'Admin', 'This article could discuss the ways in which climate change is impacting pest populations around the world. The post could explain how rising temperatures, changing precipitation patterns, and other environmental factors are affecting the behavior and distribution of pests. The article could also offer tips for homeowners and businesses on how to adapt to these changes and prevent pest infestations.', 'This article could discuss the ways in which climate change is impacting pest populations around the world. The post could explain how rising temperatures, changing precipitation patterns, and other environmental factors are affecting the behavior and distribution of pests. The article could also offer tips for homeowners and businesses on how to adapt to these changes and prevent pest infestations.');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `notes_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `note_for` varchar(20) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `note` mediumtext NOT NULL,
  `publics` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`notes_id`, `table_id`, `note_for`, `created_on`, `last_updated`, `users_id`, `note`, `publics`) VALUES
(0, 1, 'appointments', '2023-03-18 23:58:04', '2023-03-18 23:58:04', 1, 'This is a testing note.', 1),
(0, 1, 'customers', '2023-03-19 03:56:53', '2023-03-19 03:56:53', 1, 'Testasad fasdfa sfasdf', 0),
(0, 3, 'customers', '2023-03-19 18:37:53', '2023-03-19 18:37:53', 1, 'Customers archived successfully Test', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `pages_id` int(11) NOT NULL,
  `pages_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `uri_value` varchar(255) NOT NULL,
  `short_description` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`pages_id`, `pages_publish`, `created_on`, `last_updated`, `users_id`, `name`, `uri_value`, `short_description`, `description`) VALUES
(1, 1, '2023-03-19 21:21:01', '2023-04-30 22:05:22', 1, 'Top Header Phone', 'top-header-phone', '(647)-956-5868', ''),
(2, 1, '2023-03-19 21:21:25', '2023-03-19 22:00:40', 1, 'Top Header Email', 'top-header-email', 'info@pesterminate.ca', ''),
(3, 1, '2023-03-19 22:04:14', '2023-05-30 00:41:59', 2, 'About Pesterminate', 'about-pesterminate', 'Welcome to Pesterminate! We are a professional pest control company dedicated to helping you keep your home or business free from unwanted pests. With 15 years of experience and a commitment to customer satisfaction, we are your go-to experts for all your pest control needs. You can find us in any search engine platform by the following criteria:', 'At our company, we believe that prevention is key when it comes to pest control. That\\\'s why we offer a wide range of services designed to help you identify and prevent pest infestations before they become a major problem. Whether you\\\'re dealing with rodents, insects, or other pests, we have the tools and expertise to help you take back control of your space.\r\n\r\nOur team of skilled technicians is fully trained and equipped to handle all types of pest control issues. We use the latest techniques and products to ensure that your property is protected from pests without causing harm to your health or the environment. We are committed to using only the most effective and safe pest control methods to keep your home or business pest-free.'),
(4, 1, '2023-03-21 00:48:08', '2023-03-21 00:48:08', 1, 'Footer About Company', 'footer-about-company', 'Welcome to Pesterminate! We are a professional pest control company dedicated to helping you keep your home or business free from unwanted pests. With 15 years of experience and a commitment to customer satisfaction, we are your go-to experts for all your pest control needs.', ''),
(5, 1, '2023-03-21 01:22:20', '2023-03-21 01:22:20', 1, 'Terms of Service', 'terms-of-service', 'Pesterminate Inc. is committed to respecting the privacy of individuals and recognizes a need for the appropriate management and protection of any personal information that you agree to provide to us. We will not share your information with any third party outside of our organization, other than as necessary to fulfill your request.', 'Your privacy is extremely important to us. The trust placed in us by our customers is absolutely essential to our success. We understand that and do all we can to earn and protect that trust. We do not share your personal information with any outside companies or collect any information.\r\n\r\nOur Commitment To Privacy:\r\n\r\nWe take customer privacy seriously and do not sell or give out any customer information. We do not keep a mailing list nor distribute a newsletter.\r\n\r\nAs required we follow the Privacy policy of the following:\r\n\r\nGovernment of Canada – Canada Business Network\r\n\r\nhttp://www.canadabusiness.ca/eng/page/2764/\r\n\r\nPrivacy Guide for Small Businesses: The Basics – Office of the Privacy Commissioner of Canada\r\n\r\nhttps://www.priv.gc.ca/information/pub/guide_sb_e.asp\r\n\r\nPrivacy Toolkit – Office of the Privacy Commissioner of Canada\r\n\r\nhttps://www.priv.gc.ca/information/pub/guide_org_e.pdf'),
(6, 1, '2023-03-21 01:23:01', '2023-03-21 01:23:01', 1, 'Privacy Policy', 'privacy-policy', 'Pesterminate Inc. is committed to respecting the privacy of individuals and recognizes a need for the appropriate management and protection of any personal information that you agree to provide to us. We will not share your information with any third party outside of our organization, other than as necessary to fulfill your request.', 'Your privacy is extremely important to us. The trust placed in us by our customers is absolutely essential to our success. We understand that and do all we can to earn and protect that trust. We do not share your personal information with any outside companies nor collect any information.\r\n\r\nOur Commitment To Privacy:\r\n\r\nWe take customer privacy seriously and do not sell or give out any customer information. We do not keep a mailing list nor distribute a newsletter.\r\n\r\nAs per required we follow the Privacy policy of the following:\r\n\r\nGovernment of Canada – Canada Business Network\r\n\r\nhttp://www.canadabusiness.ca/eng/page/2764/\r\n\r\nPrivacy Guide for Small Businesses: The Basics – Office of the Privacy Commissioner of Canada\r\n\r\nhttps://www.priv.gc.ca/information/pub/guide_sb_e.asp\r\n\r\nPrivacy Toolkit – Office of the Privacy Commissioner of Canada\r\n\r\nhttps://www.priv.gc.ca/information/pub/guide_org_e.pdf'),
(7, 1, '2023-04-21 00:34:19', '2023-04-21 00:34:19', 1, 'What our customer says !', 'what-our-customer-says-', 'Clients Testimonials', 'Clients Testimonials'),
(8, 1, '2023-04-21 00:35:10', '2023-04-21 00:35:10', 1, 'Contact us Address', 'contact-us-address', 'House # B/153, Road # 22, New DOHS, Mohakhali, Dhaka-1206, Bangladesh.', 'House # B/153, Road # 22, New DOHS, Mohakhali, Dhaka-1206, Bangladesh.'),
(9, 1, '2023-04-21 00:35:34', '2023-04-21 00:35:34', 1, 'Contact us Email', 'contact-us-email', '<a href=\\\"mailto:info@skitsbd.com.com\\\"> info@skitsbd.com</a>', '<a href=\\\"mailto:info@skitsbd.com.com\\\"> info@skitsbd.com</a>'),
(10, 1, '2023-04-21 00:36:06', '2023-04-21 00:36:06', 1, 'Contact us Phone', 'contact-us-phone', 'Canada Office # +1416-509-1935, Bangladesh Office # +88 019 1171 8043', 'Canada Office # +1416-509-1935, Bangladesh Office # +88 019 1171 8043'),
(11, 1, '2023-05-01 06:10:16', '2023-05-29 23:57:52', 2, 'Residential Pest Control in Toronto', 'residential-pest-control-in-toronto', 'We offer year-round defense against more than 15 of the most prevalent home pests. You can count on us to take care of any pest issues you may have throughout the year at no additional expense to you.', 'The interior and exterior of your property will be carefully inspected during our initial visit in order to look for any present infestations or possible entry points. On our second visit, we\\\'ll take care of any infections and offer precautions.'),
(12, 1, '2023-05-01 06:16:00', '2023-05-29 23:05:56', 2, 'Best Pest Control in Toronto', 'best-pest-control-in-toronto', 'Pesterminate Professional Pest Control is one of the leading pest control companies in the Toronto area. It is the best and most experienced pest control company in Greater Toronto that can help you get rid of beetles, roaches, earwigs, mites, silverfish, carpet beetles, wasps, bees, centipedes, flies, rodents, and other vermin that can create unsanitary conditions and damage to your home.', 'We effectively remove bugs like spiders, cockroaches, and hornets. We even get rid of bed bugs, which can be notoriously hard to remove and can proliferate quickly in your home and furniture. We are accredited by the National Pest Management Association. Our most experienced Pesterminate team uses up-to-date and standard methods certified by the Canadian Pest Management Association. \r\n\r\nOur technicians are government-licensed, fully insured, certified, highly trained, and experienced. We take great pride in being the most trusted pest exterminator in this area. \r\nOur Pesterminate team specializes in both commercial and residential pest control services. Our staff is always ready to help with your general pest issues with fully equipped trucks.  Our technician team can also effectively remove raccoons, rats, mice, squirrels, and other animals from your property. We handle complex scenarios, such as removing rodents that are trapped in the walls or under your deck. We can also deal with infestations of mice and rats that can quickly get out of control if left unchecked.\r\n\r\nPesterminate Professional Pest Control\\\'s team always follows integrated pest management methods for your peace of mind. We give free quotations and 100% money-back guarantee services. If you have faced any pest problems, we will provide the best pest control service to keep your home and living environment free of pests.'),
(13, 1, '2023-05-01 06:20:59', '2023-05-30 00:05:16', 2, 'Mice removal treatment in Toronto', 'mice-removal-treatment-in-toronto', 'Mice are the most unwelcoming pests in Toronto to find in your home or the workplace. They can cause substantial damage to property structures via their eating capability after erecting their nests. They\\\'ve got cute and fuzzy faces, but they aren\\\'t the brutes you want in your home in Toronto. They can put the effects in your home at risk.', 'You\\\'ll surely not like someone biting your cabinetwork all day and leaving feces all over the kitchen in your home or eating electrical wiring in your walls to destroy your heating and cooling system, but nonetheless, the mice can do that in no time. They also carry colorful conditions, so you need someone trained in mouse control in Toronto.\r\n\r\n<b>The Common Mouse Species:</b> The mice are nocturnal creatures who can be rarely seen by homeowners as they remain active during the night or dusk when the majority of homeowners are asleep or workplaces are silent. The three common mouse species in Toronto are the house mouse, the field mouse (deer mouse), and the white-footed mouse. \r\n\r\n<b>Health Risks Associated with Mice:</b> The mice are known to disseminate more than 35 diseases in Toronto. Their feces can spark disinclinations and spread food-borne ails. So you won\\\'t be saved from Salmonella if there are mice feces in your home. A deer mouse can transfer Hantavirus that can revolt against you and beget your order, blood, or respiratory affections. A house mouse can infect you with LCMV ( Lymphocytic Chorio- Meningitis Contagion) which can beget you infections, particularly, in the colder months. \r\n\r\nAccording to the scientists ‘ A white-footed mouse is a primary carrier of Lyme conditions, as it hosts black-lawful ticks making it a crucial malefactor in the spread of Lyme Disease. ’ So you\\\'re at threat of health hazards if the mouse species have made their entry into your home or plant. So you should be serious about mice control in Toronto.\r\n\r\n<b>Here is an outline of our mice control process in Toronto:</b>\r\n<ul class=\\\"list\\\"><li>Our team of mice and rodent exterminators will provide a free quote over the phone to give you a basic understanding of the cost and how we implement our game plan strategy to eliminate your rodent problem.</li><li>Once onsite, we will be able to snappily identify the type of rodent species that has raided your home.</li><li>Our rodent control experts will conduct a detailed analysis of the property by looking for reflective signs such as oil painting or urine marks around the entrance holes.</li><li>Once we have an understanding of the general movement of the rodent exertion in and around your property, we will easily explain to you how we will resolve your problem.</li><li>With your blessing, we will begin the rodent decimation process.</li></ul>\r\nSo if you face any mice problems, we will provide the best pest control service to keep your home and living environment free of any mice. \r\nWe are a professional pest control team, and we have lots of experience to remove it. We give you a 100% satisfaction guarantee that we will provide the best service to remove it.'),
(14, 1, '2023-05-01 06:26:40', '2023-05-30 00:13:36', 2, 'Rat Removal treatment in Toronto', 'rat-removal-treatment-in-toronto', 'Rats mainly look for food and shelter before they start a full-fledged infestation. They invade homes, corporate offices, warehouses, factories, granaries, and even farms. They can gnaw through materials such as wood and plastic, and this makes it easy for them to gain entry into places. There are certain obvious signs that will tell you if you have a rat infestation and the most obvious would be rat sightings.', 'You will also notice long cylindrical-shaped feces on the floor. Rat teeth marks on the edges of your doors, kitchen cabinets and furniture are another sign of an infestation. Remember, when you notice even one of these signs, it is best to contact professional exterminators to tackle the rat invasion.\r\n\r\n<b>Rat Problem at Home:</b> Health concerns, potential damage, and many sleepless nights. We can effectively deal with a rat infestation, big or small. Home remedies do not work. Trapping rats does. Any rat pest control treatment should include trapping indoors and baiting outdoors to get your home rat free.\r\n\r\nThe presence of rats in your home can cause major concerns. Aside from the fact that they are unsightly and sometimes frightening, they can also pose significant risks. For starters, rats may spread diseases to humans. They may also bite or chew on helpless individuals, such as the very young, elderly, and disabled individuals.\r\n\r\nIf that isn’t bad enough, consider the fact that rats can also create significant damage to your property. They have very sharp teeth, which they often use to chew through different items and objects. They may chew through walls, wood, and wiring. If a rat happens to chew up the wiring in your home, it can create a fire hazard.\r\n\r\nEven if there are rats outside your home rather than inside, it can still create problems. Rats have no problem tearing up your gardens or fruit trees in search of food. To avoid unnecessary damage and illness, it is best to make sure to get ahead of the problem. Our Pesterminate-qualified professional team can assist you with your rat control needs in Toronto.\r\n\r\nWe make sure we treat your home like ours, which means we leave it the same way we found it. There is never any mess for you to clean up. We also work hard to make sure we eradicate your rat problems. Pesterminate team firmly believes no one should have to live in discomfort, and we understand just how uncomfortable a rat problem can be for people.\r\n\r\n<b>Rat Control and Removal Services:</b> Pesterminate Professional Pest Control Service is undoubtedly the No.1 Rat control and elimination service provider in the Greater Toronto Area. We\\\'re just a call away for home and commercial rat and mice control services. Rats can become a very serious problem once they move in and begin to multiply, eradication is needed immediately. \r\n\r\nYou may be familiar with the constant creepy sounds of running, scratching, and squeaking that these pests can cause in your home or restaurant. Then there is the lingering stench from rat droppings that can make breathing a chore. If you look closely you will find them living under the shelves, inside walls, and cupboards, but we suggest you leave that part to us.\r\n\r\nOur Pesterminate team uses high-quality products, and our staff is experts in pest removal, including specific training in the use of eco-friendly, low-toxic materials along with up-to-date know-how for the best treatment. Most rodents have a tendency to multiply at a very fast pace, so the sooner the problem is dealt with, the more effective we can be both time-wise and cost-wise.\r\n\r\n We make every effort to ensure that they are completely eliminated from your residential or commercial site. The cleaner you keep things, the less appealing they are for rats and mice. We often see garbage loosely stored and carelessly tossed, and this is an invitation for rodents. To be safe, ensure that you get a regular professional examination of your location to make sure that there is no infestation developing. \r\n\r\n<b>Top exterminators in Toronto Pesterminate for rats and mice:</b>\r\nOur mouse exterminators in the Greater Toronto Area are highly trained pros who are well-versed in the techniques of effective, long-term rat removal. The knowledge comes from our considerable number of years of experience cleaning up Ontario of unwanted pests. \r\n\r\nThe first step towards controlling rats is to be aware of their presence and detect their location at the very first sign of infestation. Secure garbage properly, avoid litter of any kind, rats aren\\\'t fussy, make sure you don’t leave edibles, and avoid stagnating water indoors or out. \r\n\r\nOur extermination service is great value for money, as you get the benefit of top pest control with everyday pricing.  We will not only meet your expectations, but we will also strive to exceed them. Our Pesterminate team will use \\\'people-safe\\\' pesticides or catch-and-release methods, which our exterminators are highly familiar with. Give us a call, we\\\'ll be right there.'),
(15, 1, '2023-05-01 06:34:43', '2023-05-30 00:39:30', 2, 'Ants and Carpenter Ants Removal treatment in Toronto', 'ants-and-carpenter-ants-removal-treatment-in-toronto', '<b>Pesterminate is a highly reliable pest control service with esteem for ant control in Toronto.</b>\r\nHave carpenter ants, pavement ants, Pharaoh ants, or other ant species infested your home or workplace in Toronto? No worries! Our pukka and well-trained pest control technicians can detect ant nests, track the queen ant, and abolish the ant problem. We\\\'re an estimable pest control company for ant junking in the GTA, including Toronto, Mississauga, Vaughan, Ajax, and others. Our pest control services, including ant control, are quick and effective. You can get in touch with Pesterminate Moment for ant control in Toronto to help us resolve your ant problem.', 'An ant infestation in your home shouldn\\\'t surprise you when you live near the natural niche of ants. also, calling professionals will clearly help you get relief from the ant problem for good when you\\\'re dealing with a large ant infestation. nonetheless, you can also circumscribe ants from overrunning your home if you take the right way beforehand to forestall ants.\r\n\r\nYou can discourage ants from overrunning your home if you block the entry points for ants. Fixing the cracks or gaps in the walls or bottoms of your house can help you forestall an ant infestation. also, you may use cornmeal to kill ants if ants have overrun your home formerly. Place cornmeal around ant colonies in your home. Ants will eat the cornmeal and also take it back to their colony, which will ultimately kill all ants.\r\n\r\nStill, cornmeal takes longer to work than fungicides, as ants can not digest cornmeal. Still, it\\\'s a safe and effective remedy to kill ants when you have children and faves. In addition, you may place basil leaves around the ant colony or sprinkle cinnamon around your house to repel ants.\r\n\r\nIn any case, it\\\'s stylish that you contact pest control experts to attack an ant infestation effectively. Pest control experts can annihilate the ant problem from your home for good and also help unborn ant infestations. It won’t only help you, your children, and your faves from health pitfalls but also baffle property damage.\r\n\r\n<b>Remedies for Ant Control in Toronto That Can Help You</b>\r\nThe key to deploying remedies for ant control is to understand that different ant species will respond to them differently. Counting on pest control technicians is always your best bet for ant control in Toronto. You may give us a call to make use of our pest control service for ant control on your property. \r\n\r\nOur technician can tell you right away, after examining your property, the type of ant species you\\\'re dealing with. Our expert technician can detect ant nests, track the queen, and eradicate the ant problem from your property for good.\r\n\r\nFurther, trying to get rid of an ant infestation yourself will likely make the problem worse for you. Nevertheless, some remedies can thwart an ant infestation in your home, which we have mentioned below:<ul class=\\\"list\\\"><li>Store food, such as sugar, swabs, etc., in sealed or watertight holders that may attract ants</li><li>Seal any holes you have in your home to prevent ants from spreading in your domestic space.</li><li>Use cornmeal to your advantage to kill ants, including those in the colony, if ants have previously overrun your home.</li><li>Use cinnamon or basil leaves as an ant repellent to discourage ants from overrunning your home.</li></ul>\r\nStill, it is best that you call the Pesterminate team for guaranteed and long-term results for ant control in your home. We won’t only clear your home from ants completely but also help you avert future ant infestations.\r\n\r\n<b>Pesterminate is a highly reliable pest control service with esteem for Carpenter ant control in Toronto.</b>\r\nCarpenter ants are common pests found in homes throughout Toronto. They\\\'re frequently set up to slice or groove through decayed wood to produce a lair where they live, hence the name carpenter ants. Carpenter ants only survive where there’s acceptable humidity. That’s why you’ll find them in wet surroundings, similar to garrets and crawlspaces, hiding in wet timbers and inadequately ventilated spaces. Most people mistake carpenter ants for termites.\r\n\r\n<b>Carpenter ants usually enter the property via the following access points:</b><ul class=\\\"list\\\"><li>Clogged drains</li><li>Gutters</li><li>Holes which are present in the building’s foundation</li><li>Firewood that’s carried from outside to the house</li><li>Fencing that’s too close to the house</li><li>Heating ducts and air conditioners</li><li>Plumbing</li></ul>\r\n<b>Carpenter Ants Infestation:</b> Carpenter ants typically nest in wooden structures like window frames, door frames, and furniture, and they can wreak havoc on decks and gazebos. They travel and spread via electrical wiring, tubes, and plumbing. Pavement ants are less common than carpenter ants but are far more invasive. \r\n\r\nThey thrive on all kinds of foods that are generally set up in a domestic or ménage environment. Along with humidity prevention, the junking of food remnants and cooking grease is the surest way to help ant infestations. Carpenter ants generally nest in rustic structures like window frames, door frames, and cabinetwork, and they can inflict annihilation on balconies and belvederes. They travel and spread via electrical wiring, tubes, and plumbing.\r\n\r\n<b>Top 5 Reasons to Choose Us for Ants and Carpenter Ant Control in Toronto:</b><ul class=\\\"list\\\"><li>Our ant control procedures are professional and work every time. They are designed to exterminate ants from your residential or commercial property completely.</li><li>Each ant species requires a unique approach to treatment. For illustration, carpenter ants are the most common in Toronto, so we developed a result that specifically targets and exterminates all carpenter ants on your property.</li><li>Our carpenter ant service is one of the smallest priced in the assiduity. It\\\'ll bring you lower to hire a professional carpenter ant exterminator from Pesterminate than it\\\'ll be to try to exclude the ant problem yourself.</li><li>A 100 plutocrat Back Guarantee backs our ant control and decimation services. We\\\'ll re-treat the area again if we can not completely abolish all carpenter ants from your property. If you aren\\\'t happy with our services we will reimburse all your plutocrats.</li><li>Pesterminate Professional Pest Control is an original Canadian company serving the Greater Toronto Area. Our services are accessible and our ant decimation experts are flexible and will work around your busy schedule.</li></ul>\r\n\r\nSo, it will be best that you call our Pesterminate team for guaranteed and long-term results for any type of ant control in your home. We won’t only clear your home from ants completely but also help you avert future ant infestations.');

-- --------------------------------------------------------

--
-- Table structure for table `photo_gallery`
--

CREATE TABLE `photo_gallery` (
  `photo_gallery_id` int(11) NOT NULL,
  `photo_gallery_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `photo_gallery`
--

INSERT INTO `photo_gallery` (`photo_gallery_id`, `photo_gallery_publish`, `created_on`, `last_updated`, `users_id`, `name`) VALUES
(6, 1, '2023-05-07 01:57:27', '2023-05-07 01:57:27', 1, 'test');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `services_id` int(11) NOT NULL,
  `services_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `font_awesome` varchar(50) NOT NULL,
  `uri_value` varchar(255) NOT NULL,
  `short_description` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`services_id`, `services_publish`, `created_on`, `last_updated`, `users_id`, `name`, `font_awesome`, `uri_value`, `short_description`, `description`) VALUES
(1, 1, '2023-03-19 03:37:06', '2023-05-18 02:32:51', 1, 'Cockroach', 'cockroach', 'cockroach', 'How to Prevent Infestations made by Cockroaches movement', 'Cockroaches are a common household pest that can be difficult to get rid of once they infest your home. These insects are known for their resilience and ability to adapt to a variety of environments, which makes them a particularly challenging pest to control.\r\n\r\nIdentification: Cockroaches are typically brown or black in color and have a flat, oval-shaped body. They have six legs and two long antennae that they use to navigate their environment. There are several different species of cockroaches, including the German cockroach, American cockroach, and Oriental cockroach, all of which have slightly different physical characteristics.\r\n\r\nBehavior: Cockroaches are primarily nocturnal and prefer to hide in dark, moist areas during the day. They are attracted to food and can often be found in kitchens, pantries, and other areas where food is stored. Cockroaches can also spread bacteria and disease, making them a health hazard in addition to being a nuisance.\r\n\r\nPrevention: To prevent cockroach infestations, it\\\'s important to keep your home clean and free of food debris. This includes wiping down counters and surfaces, storing food in airtight containers, and regularly taking out the garbage. Sealing up any cracks or gaps in your home\\\'s foundation can also help prevent cockroaches from entering.\r\n\r\nTreatment: If you do have a cockroach infestation, it\\\'s important to take action quickly to prevent the problem from getting worse. This may include using cockroach baits or traps, applying insecticides, or hiring a professional pest control company to eliminate the infestation.\r\n\r\nOverall, cockroaches are a pest that no homeowner wants to deal with. By taking preventative measures and seeking professional help when necessary, you can keep your home free of these pesky insects and protect your family\\\'s health and safety.'),
(2, 1, '2023-03-20 20:31:12', '2023-05-18 03:06:25', 1, 'Spiders', 'spiders', 'spiders', 'Understanding these Common Arachnids Spiders', 'Spiders are a common sight in homes and businesses around the world. While some people may find these eight-legged creatures creepy or frightening, others appreciate them for their role in controlling other pests, such as flies and mosquitoes. So, are spiders friend or foe? Let\\\'s take a closer look.\r\nFirst, it\\\'s important to understand that not all spiders are the same. In fact, there are more than 45,000 known species of spiders, each with their own unique characteristics and behaviors. While some spiders are venomous and can pose a threat to humans and pets, most species are harmless and prefer to avoid contact with people altogether.\r\n\r\nTo determine if you have a spider infestation in your home or business, it\\\'s important to know what to look for. Common signs of a spider infestation include spider webs, egg sacs, and spider sightings. If you suspect you have a spider problem, it\\\'s best to contact a pest control professional for an inspection and treatment plan.\r\n\r\nIf you\\\'re wondering whether or not you should try to get rid of spiders in your home, the answer depends on your personal preferences and the specific species of spider. If you have a harmless species of spider in your home, it may be best to leave them alone or relocate them outside. However, if you have a venomous species of spider, such as the black widow or brown recluse, it\\\'s important to seek professional pest control services to eliminate the infestation and prevent potential health risks.\r\n\r\nIn conclusion, while spiders may not be everyone\\\'s favorite creatures, they can play an important role in controlling other pests and maintaining the balance of ecosystems. By understanding these common arachnids and taking appropriate pest control measures when necessary, we can coexist with spiders in a safe and harmonious way.'),
(3, 1, '2023-03-20 20:35:06', '2023-05-18 03:11:21', 1, 'Termites', 'termites', 'termites', 'Control The Termites The Silent Destroyers', 'Termites are often called \\\"silent destroyers\\\" because they can cause significant damage to a home or business without being detected until it\\\'s too late. These wood-eating insects are responsible for billions of dollars in property damage each year, making them a serious threat to homeowners and business owners alike.\r\nTermites are social insects that live in large colonies and feed on wood and other cellulose-based materials. They are typically found in warm, humid climates and can be difficult to detect due to their underground tunnels and hidden nesting sites.\r\n\r\nTo protect your home or business from termite damage, it\\\'s important to know what signs to look for. Common signs of a termite infestation include mud tubes on exterior walls, damaged or hollow-sounding wood, and discarded termite wings. If you suspect you have a termite problem, it\\\'s crucial to contact a pest control professional as soon as possible to prevent further damage.\r\n\r\nPreventing termite infestations starts with taking proactive measures to make your property less attractive to these pests. Some tips for preventing termites include reducing moisture levels in and around your home or business, keeping firewood and other wood-based materials away from the building, and sealing any cracks or gaps in the foundation or walls.\r\n\r\nIf you do end up with a termite infestation, there are several treatment options available. A pest control professional may recommend a variety of methods, including liquid treatments, baits, and fumigation. The best treatment option will depend on the severity of the infestation and other factors unique to your property.\r\n\r\nIn conclusion, termites are a serious threat to the structural integrity of homes and businesses. By taking proactive measures to prevent infestations and contacting a pest control professional at the first sign of trouble, you can protect your property from these silent destroyers.'),
(4, 1, '2023-03-20 20:37:54', '2023-05-18 03:04:10', 1, 'Rodents', 'rodents', 'rodents', 'Rodents The Unwelcome House Guests', 'Rodents, such as mice and rats, are a common pest problem for homeowners and businesses. These small, furry creatures are known for their ability to squeeze through even the tiniest of openings and can cause significant damage to property if left unchecked.\r\nOne of the biggest concerns with rodents is their ability to spread disease. Rodents can carry a variety of diseases, including hantavirus, salmonellosis, and leptospirosis. They can also contaminate food and surfaces with their urine and droppings, posing a health risk to humans and pets.\r\n\r\nTo prevent a rodent infestation, it\\\'s important to take proactive measures to make your property less attractive to these pests. Some tips for preventing rodents include sealing any cracks or gaps in the foundation or walls, keeping food stored in airtight containers, and eliminating any sources of standing water.\r\n\r\nIf you suspect you have a rodent problem, there are several signs to look for. Common signs of a rodent infestation include gnaw marks on wires or other materials, droppings, and the sound of scurrying or scratching in the walls or ceilings. If you notice any of these signs, it\\\'s important to contact a pest control professional as soon as possible to prevent further damage and potential health risks.\r\n\r\nA pest control professional can provide a variety of treatment options for rodent infestations, including traps and baits. They can also offer advice on how to prevent future infestations and provide ongoing monitoring and maintenance services to ensure that your property remains rodent-free.\r\n\r\nIn conclusion, rodents can pose a serious health risk to humans and pets and can cause significant damage to property if left unchecked. By taking proactive measures to prevent infestations and contacting a pest control professional at the first sign of trouble, you can protect your property from these unwelcome house guests.'),
(5, 1, '2023-03-20 20:44:37', '2023-05-18 02:51:55', 1, 'Fly Control', 'fly-control', 'fly-control', 'Keep Your Home or Business Fly Pest-Free', 'Flies are a common pest problem that can be a nuisance for homeowners and businesses alike. These flying insects are known for their ability to spread disease and contaminate food and surfaces, making them a health risk to humans and pets.\r\n\r\nTo prevent a fly infestation, it\\\'s important to take proactive measures to make your property less attractive to these pests. Some tips for preventing flies include keeping food stored in airtight containers, cleaning up spills and crumbs immediately, and eliminating any sources of standing water.\r\n\r\nIf you already have a fly problem, there are several treatment options available. A pest control professional may recommend a variety of methods, including fly traps and baits, insecticides, and light traps. The best treatment option will depend on the severity of the infestation and other factors unique to your property.\r\n\r\nIn addition to prevention and treatment, it\\\'s also important to educate yourself and others about fly control. This can include learning about the different types of flies and their habits, as well as how to properly dispose of food waste and other potential attractants.\r\n\r\nBy taking proactive measures to prevent and control fly infestations, you can keep your home or business pest-free and reduce the risk of disease and contamination. A pest control professional can provide further advice and assistance in developing a comprehensive fly control plan tailored to your specific needs and concerns.\r\n\r\nIn conclusion, fly control is an important aspect of maintaining a healthy and pest-free environment for both humans and pets. By taking steps to prevent infestations and seeking professional assistance when needed, you can ensure that your property remains free from these annoying and potentially dangerous pests.'),
(6, 1, '2023-03-20 21:00:49', '2023-05-18 03:14:03', 1, 'Wasps Nest', 'wasps-nest', 'wasps-nest', 'Tips for Safe and Effective Wasps Nest Removal Service', 'Wasps Nest Removal are an important part of our ecosystem, but when they establish their hives in or around our homes or businesses, they can pose a serious risk to our health and safety. While it\\\'s important to respect these beneficial insects and their role in pollinating our crops and flowers, it\\\'s also important to know how to safely and effectively remove them when they become a nuisance.\r\n\r\nThe first step in bee control is to properly identify the species of bee and the location of the hive. Different species of bees have different behaviors and require different removal methods. For example, honey bees are social insects that form large colonies and are often found in cavities such as walls, while carpenter bees are solitary insects that burrow into wood.\r\n\r\nOnce you\\\'ve identified the bee species and hive location, it\\\'s important to take the appropriate safety precautions. Bees can become aggressive when their hive is disturbed, so it\\\'s important to wear protective clothing, avoid making sudden movements, and keep children and pets at a safe distance.\r\n\r\nIf the hive is small and accessible, it may be possible to remove it yourself using a vacuum or insecticide spray. However, if the hive is large or located in a difficult-to-reach area, it\\\'s best to seek professional help. A pest control professional can provide safe and effective removal methods that will minimize harm to the bees and prevent future infestations.\r\n\r\nIn addition to removal, it\\\'s also important to take preventive measures to reduce the risk of future bee infestations. This can include sealing up any gaps or cracks in your home or business, removing sources of standing water, and avoiding planting flowering plants near the building.\r\n\r\nBy taking a respectful and cautious approach to bee control, you can ensure the safety of yourself and others while also preserving the important role that bees play in our environment. A pest control professional can provide further advice and assistance in developing a comprehensive bee control plan tailored to your specific needs and concerns.\r\n\r\nIn conclusion, bee control is an important aspect of maintaining a safe and pest-free environment for both humans and bees. By taking steps to identify, remove, and prevent bee infestations, you can ensure the safety of yourself and others while also preserving the vital role that these beneficial insects play in our ecosystem.'),
(7, 1, '2023-03-20 21:07:00', '2023-05-18 02:32:07', 1, 'Ant Control', 'ant-control', 'ant-control', 'Tips for Effective Control and Prevention of Ant', 'Ants are a common household pest that can quickly become a nuisance if left unchecked. These tiny insects are known for their ability to form large colonies and invade homes and businesses in search of food and water. If you\\\'re dealing with an ant infestation, it\\\'s important to take proactive measures to remove them and prevent future infestations.\r\n\r\nThe first step in ant control is to properly identify the species of ant and the location of their colony. Different species of ants have different behaviors and require different removal methods. For example, carpenter ants are known for burrowing into wood, while pavement ants are often found nesting in cracks and crevices in sidewalks and driveways.\r\n\r\nOnce you\\\'ve identified the ant species and colony location, it\\\'s important to take the appropriate removal measures. This can include using bait traps, insecticides, and physical removal methods such as vacuuming or sweeping. It\\\'s important to follow the instructions on any removal products carefully and use caution when handling them, as some can be harmful to humans and pets.\r\n\r\nIn addition to removal, it\\\'s important to take preventative measures to reduce the risk of future ant infestations. This can include keeping food stored in airtight containers, cleaning up spills and crumbs immediately, and sealing up any gaps or cracks in your home or business that ants may use as entry points.\r\n\r\nAnother effective preventative measure is to eliminate any sources of standing water and moisture, as ants are attracted to damp environments. This can include fixing leaky pipes and faucets, using dehumidifiers, and ensuring proper ventilation in your home or business.\r\n\r\nBy taking proactive measures to remove and prevent ant infestations, you can keep your property pest-free and reduce the risk of contamination and damage. A pest control professional can provide further advice and assistance in developing a comprehensive ant control plan tailored to your specific needs and concerns.\r\n\r\nIn conclusion, ant control is an important aspect of maintaining a safe and pest-free environment for your home or business. By identifying and removing ant colonies, and taking proactive measures to prevent future infestations, you can keep your property free from these pesky insects and reduce the risk of harm to yourself, your family, and your business.'),
(8, 1, '2023-03-20 21:08:42', '2023-05-18 02:38:41', 1, 'Bed Bug', 'bed-bug', 'bed-bug', 'How to Identify, Treat Infestations spread by Bed Bug', 'Bed bugs are a common household pest that can be difficult to control once they establish a presence. These tiny, wingless insects are attracted to warmth and carbon dioxide, making human dwellings an ideal habitat for them. If left untreated, bed bug infestations can quickly spread throughout a home, leading to uncomfortable bites and sleepless nights.\r\n\r\nThe first step in bed bug control is to properly identify the pests. Bed bugs are reddish-brown in color and are about the size of an apple seed when fully grown. They are typically found in mattresses, box springs, and other areas where people sleep, but can also be found in furniture, curtains, and even behind electrical outlets.\r\n\r\nOnce bed bugs have been identified, it\\\'s important to take immediate action to eliminate the infestation. This can include using a combination of methods such as vacuuming, steam cleaning, and applying insecticides. It\\\'s important to follow all product instructions carefully and to use only products that are specifically labeled for bed bug control.\r\n\r\nIn addition to treating the infestation, it\\\'s important to take preventive measures to avoid future bed bug problems. This can include regularly inspecting mattresses and furniture for signs of bed bugs, washing and drying bedding at high temperatures, and sealing up any cracks or crevices in walls and furniture where bed bugs can hide.\r\n\r\nIf you suspect that you have a bed bug infestation, it\\\'s important to act quickly to avoid the problem from becoming worse. A professional pest control company can provide expert advice and assistance in identifying and treating bed bug infestations, as well as developing a customized prevention plan to keep bed bugs out of your home.\r\n\r\nIn conclusion, bed bug infestations are a common problem that can be difficult to control without professional help. By properly identifying the pests, treating the infestation with effective methods, and taking preventive measures to avoid future problems, you can successfully eliminate bed bugs from your home and enjoy a restful night\\\'s sleep.');

-- --------------------------------------------------------

--
-- Table structure for table `track_edits`
--

CREATE TABLE `track_edits` (
  `track_edits_id` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `record_for` varchar(20) NOT NULL,
  `record_id` int(11) NOT NULL,
  `details` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `track_edits`
--

INSERT INTO `track_edits` (`track_edits_id`, `created_on`, `users_id`, `record_for`, `record_id`, `details`) VALUES
(1, '2023-03-19 00:13:52', 1, 'appointments', 1, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(2, '2023-03-19 00:23:23', 1, 'appointments', 1, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(3, '2023-03-19 00:26:57', 1, 'appointments', 1, '{\"changed\":{\"\":\"Changed this Appointment from Cancel to Approved\"},\"moreInfo\":[]}'),
(4, '2023-03-19 00:39:45', 1, 'appointments', 1, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(5, '2023-03-19 11:27:38', 1, 'customers', 2, '{\"changed\":{\"name\":[\"Oyazur\",\"Oyazur Rahman\"]},\"moreInfo\":[]}'),
(6, '2023-03-19 11:28:57', 1, 'customers', 2, '{\"changed\":{\"name\":[\"Oyazur Rahman\",\"Md. Abdus Shobhan\"],\"address\":[\"\",\"2942 Danforth Ave,\"]},\"moreInfo\":[]}'),
(7, '2023-03-20 02:00:20', 1, 'news_articles', 1, '{\"changed\":{\"news_articles_date\":[\"2016-07-19\",\"2023-03-13\"]},\"moreInfo\":[]}'),
(8, '2023-03-20 02:00:44', 1, 'news_articles', 1, '{\"changed\":{\"uri_value\":[\"WHAT YOU NEED TO KNOW ABOUT ANTS IN YOUR HOUSE\",\"what-you-need-to-know-about-ants-in-your-house\"]},\"moreInfo\":[]}'),
(9, '2023-03-20 12:41:13', 1, 'appointments', 1, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(10, '2023-03-20 12:41:29', 1, 'appointments', 1, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(11, '2023-03-20 19:28:27', 1, 'news_articles', 2, '{\"changed\":{\"name\":[\"HOW TO CHOOSE YOUR GREATER TORONTO AREA (GTA) PEST CONTROL COMPANY\",\"10 Common Household Pests and How to Get Rid of Them\"],\"uri_value\":[\"how-to-choose-your-greater-toronto-area-gta-pest-control-company\",\"10-common-household-pests-and-how-to-get-rid-of-them\"],\"short_description\":[\"There\\u2019s no worse way to start the spring and summer months than getting bugged by pesky pests. If you know you have a pest problem, it can be a knee-jerk response to call up the same pest control expert that your family has been using for years.\\r\\n\\r\\nBut is it really the right idea to use the same pest control service over and over simply because you\\u2019ve always used them? To make sure that you\\u2019re getting the best service possible, you should choose a pest control company that perfectly fits your needs.\\r\\n\\r\\nKnowledge is the first thing to arm yourself with in your battle against pests! Here\\u2019s our ultimate guide on how to choose your Seattle pest control company.\",\"This article could provide readers with information about common household pests, such as ants, cockroaches, and rodents, and offer tips and tricks for getting rid of them. The article could also cover preventative measures that homeowners can take to keep pests from entering their homes in the first place.\"]},\"moreInfo\":[]}'),
(12, '2023-03-20 19:28:45', 1, 'news_articles', 2, '{\"changed\":{\"description\":[\"LICENSING AND QUALIFICATIONS\\r\\nWhen you choose a pest control company, there can be a lot of options to choose from. However, some of these options do not have the correct licensing and registration to effectively handle your pest problems.\\r\\n\\r\\nHaving a pest control company that is licensed in your local municipality ensures that your service will be in accordance with pest control best practices, and that the team will be comprised of true pest control experts.\\r\\n\\r\\nYou can check here to see if your GTA pest control company is fully licensed. When you work with Pesterminate, you know that you\\u2019re working with one of the most well-recognized and licensed pest control companies in the GTA area.\\r\\n\\r\\nEXCELLENT CUSTOMER SERVICE\\r\\nPest control needs vary situation by situation, and it\\u2019s imperative that you work with a pest control expert who knows how to listen to your needs.\\r\\n\\r\\nWhen your home is invaded by pests, such as the dreaded rats, mice, bedbugs,  it\\u2019s easy for emotions to run a little high. Ideally, you\\u2019ll want to work with a GTA licensed pest control company that takes the time to explain its full process to you and make sure that the treatment plan fits your needs and budget.\\r\\n\\r\\nProviding this type of customer service is something that Pesterminate is extraordinarily passionate about. We\\u2019ve been recognized by many different organizations for our superior service, and we take the pride serving in Toronto and having earned HomeStars : Best of 2019 award. Say what customers have to say about us in HomeStars.\\r\\n\\r\\nSAFETY\\r\\nExterminations and poison seem to go hand in hand. When you work with a pest control expert, it\\u2019s likely that they\\u2019ll be using pesticides of some sort to eliminate your pest woes. But are they handling the materials safely and cautiously?\\r\\n\\r\\nMake sure that your GTA pest control company exercises the following tenets of safe pest control:\\r\\n\\r\\nDo they use environmentally-friendly pesticides when possible?\\r\\nDo they wear protective equipment when appropriate?\\r\\nAre they insured in order to cover themselves, you, and your property?\\r\\nAre they cautious and careful as they traverse your property?\\r\\nPesterminate is dedicated to providing safe and effective pest control for its customers, and always take measures to ensure that the job is completed to maximum satisfaction while exercises the utmost caution.\\r\\n\\r\\nVALUE\\r\\nValue may be the most important part of the equation when working with GTA pest control companies.\\r\\n\\r\\nMany professional pest control services will overcharge for what they provide, simply because they know you\\u2019re in a difficult bargaining position. Rodent services in GTA are one example of this, as people are often in a big hurry to eliminate their rat troubles. This can lead to hasty decisions and overpaying for what you receive.\\r\\n\\r\\nWhen you work with Pesterminate, you have flexibility in your treatment options. We offer a pest control maintenance plan that covers all of your pest needs and prevents issues in the future. Whatever sort of GTA pest control services you\\u2019re in need of, we can find a solution that fits your unique situation\",\"\"]},\"moreInfo\":[]}'),
(13, '2023-03-20 19:29:37', 1, 'news_articles', 1, '{\"changed\":{\"name\":[\"WHAT YOU NEED TO KNOW ABOUT ANTS IN YOUR HOUSE\",\"The Dangers of DIY Pest Control\"],\"uri_value\":[\"what-you-need-to-know-about-ants-in-your-house\",\"the-dangers-of-diy-pest-control\"],\"short_description\":[\"You may be able to exterminate these pests without calling in a professional, though more severe infestations will require a stronger approach. Some ant species, like carpenter ants, can cause structural damage to your house if not treated immediately. Other infestations might be harder to eliminate due to a colony resisting natural and chemical extermination solutions.\",\"This blog post could discuss the potential dangers of attempting to control pests without the help of a professional pest control service. The article could highlight the risks of using store-bought pesticides and other DIY methods that may not be effective in getting rid of pests or may even be harmful to humans and pets.\"],\"description\":[\"GET RID OF ANTS IN YOUR HOME BY ELIMINATING THE COLONY\\r\\nTake note of where you\\u2019re seeing the ants. They may run along your baseboards, emerge from holes under cabinets, or pop out of cracks in your walls or floors, particularly if your house is older. There may be other exterior entry points to your home, including visible ant colonies in your front or back yard or cracks in your driveway.\\r\\n\\r\\nThe key to getting rid of ants in your home is to take out the colony, and finding the colony is easier when you can identify the type of ant invading your space. Here are some common ants, their differentiating characteristics, and where you might locate their colonies:\\r\\n\\r\\nSugar ants\\u2014These ants typically feed on sweet or greasy foods and can be found around your kitchen or places where you store food. They distribute food and water to the rest of their colony. They have brownish-black bodies with black heads and their nests are typically found in old wood or dark, moist areas.\\r\\nCarpenter ants\\u2014Carpenter ants are either black or red and typically 3\\/16 inch to \\u00bd inch long and prefer to build colonies in moist wood, such as tree stumps, around bathtubs, showers,or dishwashers, or behind bathroom tiles. They are most easily identified by their thorax, which is rounded and smooth. Carpenter ants will tunnel in wood, creating smooth channels and leaving behind wood shavings, so if you notice wood shavings concentrated in a specific location, the colony may be close by. If you do investigate and find tunnels that are dirty and filled with material, the culprit may be termites.\\r\\nPavement ants\\u2014These ants are also black or reddish brown with pale legs and antennae and are typically \\u215b inch long. Pavement ants prefer to nest in soil covered by solid material like rocks or pavement and are often found under driveways, sidewalk slabs, or concrete foundations of houses. Pavement ants are most likely to enter your home through cracks in the wall.\\r\\nMoisture ants (yellow ants)\\u2014These ants are longer and yellow or reddish brown in color. When they are crushed, they give off a citronella scent. Moisture ants tend to build their colonies against the foundation of homes or outdoors under rocks and logs. As their name suggests, they are attracted to high-moisture areas and are often found in bathrooms.\\r\\nWhen you locate the colony\\u2014the source of your ant infestation\\u2014the next step is to eliminate any pheromone trails made by the ants. Pheromone trails are basic scent trails that ants leave behind for other ants to join them in finding food and water. When you identify the source of the ants, you can eliminate the entire ant colony by getting rid of the existing trail. Here\\u2019s how.\\r\\n\\r\\n \\r\\n\\r\\nYou may be able to exterminate these pests without calling in a professional, though more severe infestations will require a stronger approach. Some ant species, like carpenter ants, can cause structural damage to your house if not treated immediately. Other infestations might be harder to eliminate due to a colony resisting natural and chemical extermination solutions.\\r\\n\\r\\nGET RID OF ANTS IN YOUR HOME BY ELIMINATING THE COLONY\\r\\nTake note of where you\\u2019re seeing the ants. They may run along your baseboards, emerge from holes under cabinets, or pop out of cracks in your walls or floors, particularly if your house is older. There may be other exterior entry points to your home, including visible ant colonies in your front or back yard or cracks in your driveway.\\r\\n\\r\\nThe key to getting rid of ants in your home is to take out the colony, and finding the colony is easier when you can identify the type of ant invading your space. Here are some common ants, their differentiating characteristics, and where you might locate their colonies:\\r\\n\\r\\nSugar ants\\u2014These ants typically feed on sweet or greasy foods and can be found around your kitchen or places where you store food. They distribute food and water to the rest of their colony. They have brownish-black bodies with black heads and their nests are typically found in old wood or dark, moist areas.\\r\\nCarpenter ants\\u2014Carpenter ants are either black or red and typically 3\\/16 inch to \\u00bd inch long and prefer to build colonies in moist wood, such as tree stumps, around bathtubs, showers,or dishwashers, or behind bathroom tiles. They are most easily identified by their thorax, which is rounded and smooth. Carpenter ants will tunnel in wood, creating smooth channels and leaving behind wood shavings, so if you notice wood shavings concentrated in a specific location, the colony may be close by. If you do investigate and find tunnels that are dirty and filled with material, the culprit may be termites.\\r\\nPavement ants\\u2014These ants are also black or reddish brown with pale legs and antennae and are typically \\u215b inch long. Pavement ants prefer to nest in soil covered by solid material like rocks or pavement and are often found under driveways, sidewalk slabs, or concrete foundations of houses. Pavement ants are most likely to enter your home through cracks in the wall.\\r\\nMoisture ants (yellow ants)\\u2014These ants are longer and yellow or reddish brown in color. When they are crushed, they give off a citronella scent. Moisture ants tend to build their colonies against the foundation of homes or outdoors under rocks and logs. As their name suggests, they are attracted to high-moisture areas and are often found in bathrooms.\\r\\nWhen you locate the colony\\u2014the source of your ant infestation\\u2014the next step is to eliminate any pheromone trails made by the ants. Pheromone trails are basic scent trails that ants leave behind for other ants to join them in finding food and water. When you identify the source of the ants, you can eliminate the entire ant colony by getting rid of the existing trail. Here\\u2019s how.\\r\\n\\r\\nHOW TO KEEP ANTS OUT OF YOUR HOME\\r\\nOnce ants are in your house, they can become a pesky and recurring nuisance. Follow these tips to prevent ants from infesting your home in the future:\\r\\n\\r\\nKeep your house clean\\u2014By putting food away, cleaning off countertops and floors, and emptying the trash daily, you reduce the risk of ants in your home. Try to regularly vacuum, mop, and wipe down counters, especially in areas of your house where you prepare or store food.\\r\\nBe vigilant during the hotter months\\u2014Ants are more prevalent in warm and humid conditions than they are in colder temperatures. They tend to appear more often during the summer months than they do in the winter months. Especially during warmer periods, keep your house clean.\\r\\nFind possible entry points and seal them off\\u2014Spray natural pesticides along the perimeter of your home to prevent ants from coming inside.\\r\\nFix leaks in your house\\u2014Ants are attracted to food and water sources. By fixing leaky pipes and cleaning up damp areas around your house, you will lessen the chance of allowing ants to find a source of water for the whole colony.\\r\\nKill ants in the yard\\u2014If you see nests outdoors, spot-treat the area with an insecticide. Spray in the morning and late afternoon when ants are most active. Insecticides that contain bifenthrin work especially well in dismantling ant mounds and getting rid of most ants.\\r\\nCall in a professional exterminator\\u2014Some colonies are extremely hard to eliminate despite your best effort. In this case, call in a professional to get the job done. Exterminators have tougher chemicals to get rid of the ants and can save you the time from checking every crack and crevice for ants.\",\"\"]},\"moreInfo\":[]}'),
(14, '2023-03-20 20:54:57', 1, 'services', 2, '{\"changed\":{\"short_description\":[\"Spiders are a common sight in homes and businesses around the world. While some people may find these eight-legged creatures creepy or frightening, others appreciate them for their role in controlling other pests, such as flies and mosquitoes. So, are spiders friend or foe? Let\\\\\'s take a closer look.\",\"Spiders: Friend or Foe? Understanding these Common Arachnids\"],\"description\":[\"First, it\\\\\'s important to understand that not all spiders are the same. In fact, there are more than 45,000 known species of spiders, each with their own unique characteristics and behaviors. While some spiders are venomous and can pose a threat to humans and pets, most species are harmless and prefer to avoid contact with people altogether.\\r\\n\\r\\nTo determine if you have a spider infestation in your home or business, it\\\\\'s important to know what to look for. Common signs of a spider infestation include spider webs, egg sacs, and spider sightings. If you suspect you have a spider problem, it\\\\\'s best to contact a pest control professional for an inspection and treatment plan.\\r\\n\\r\\nIf you\\\\\'re wondering whether or not you should try to get rid of spiders in your home, the answer depends on your personal preferences and the specific species of spider. If you have a harmless species of spider in your home, it may be best to leave them alone or relocate them outside. However, if you have a venomous species of spider, such as the black widow or brown recluse, it\\\\\'s important to seek professional pest control services to eliminate the infestation and prevent potential health risks.\\r\\n\\r\\nIn conclusion, while spiders may not be everyone\\\\\'s favorite creatures, they can play an important role in controlling other pests and maintaining the balance of ecosystems. By understanding these common arachnids and taking appropriate pest control measures when necessary, we can coexist with spiders in a safe and harmonious way.\",\"Spiders are a common sight in homes and businesses around the world. While some people may find these eight-legged creatures creepy or frightening, others appreciate them for their role in controlling other pests, such as flies and mosquitoes. So, are spiders friend or foe? Let\\\\\'s take a closer look.\\r\\nFirst, it\\\\\'s important to understand that not all spiders are the same. In fact, there are more than 45,000 known species of spiders, each with their own unique characteristics and behaviors. While some spiders are venomous and can pose a threat to humans and pets, most species are harmless and prefer to avoid contact with people altogether.\\r\\n\\r\\nTo determine if you have a spider infestation in your home or business, it\\\\\'s important to know what to look for. Common signs of a spider infestation include spider webs, egg sacs, and spider sightings. If you suspect you have a spider problem, it\\\\\'s best to contact a pest control professional for an inspection and treatment plan.\\r\\n\\r\\nIf you\\\\\'re wondering whether or not you should try to get rid of spiders in your home, the answer depends on your personal preferences and the specific species of spider. If you have a harmless species of spider in your home, it may be best to leave them alone or relocate them outside. However, if you have a venomous species of spider, such as the black widow or brown recluse, it\\\\\'s important to seek professional pest control services to eliminate the infestation and prevent potential health risks.\\r\\n\\r\\nIn conclusion, while spiders may not be everyone\\\\\'s favorite creatures, they can play an important role in controlling other pests and maintaining the balance of ecosystems. By understanding these common arachnids and taking appropriate pest control measures when necessary, we can coexist with spiders in a safe and harmonious way.\"]},\"moreInfo\":[]}'),
(15, '2023-03-20 20:55:55', 1, 'services', 3, '{\"changed\":{\"short_description\":[\"Termites are often called \\\\\\\"silent destroyers\\\\\\\" because they can cause significant damage to a home or business without being detected until it\\\\\'s too late. These wood-eating insects are responsible for billions of dollars in property damage each year, making them a serious threat to homeowners and business owners alike.\",\"Termites: The Silent Destroyers\"],\"description\":[\"Termites are social insects that live in large colonies and feed on wood and other cellulose-based materials. They are typically found in warm, humid climates and can be difficult to detect due to their underground tunnels and hidden nesting sites.\\r\\n\\r\\nTo protect your home or business from termite damage, it\\\\\'s important to know what signs to look for. Common signs of a termite infestation include mud tubes on exterior walls, damaged or hollow-sounding wood, and discarded termite wings. If you suspect you have a termite problem, it\\\\\'s crucial to contact a pest control professional as soon as possible to prevent further damage.\\r\\n\\r\\nPreventing termite infestations starts with taking proactive measures to make your property less attractive to these pests. Some tips for preventing termites include reducing moisture levels in and around your home or business, keeping firewood and other wood-based materials away from the building, and sealing any cracks or gaps in the foundation or walls.\\r\\n\\r\\nIf you do end up with a termite infestation, there are several treatment options available. A pest control professional may recommend a variety of methods, including liquid treatments, baits, and fumigation. The best treatment option will depend on the severity of the infestation and other factors unique to your property.\\r\\n\\r\\nIn conclusion, termites are a serious threat to the structural integrity of homes and businesses. By taking proactive measures to prevent infestations and contacting a pest control professional at the first sign of trouble, you can protect your property from these silent destroyers.\",\"Termites are often called \\\\\\\"silent destroyers\\\\\\\" because they can cause significant damage to a home or business without being detected until it\\\\\'s too late. These wood-eating insects are responsible for billions of dollars in property damage each year, making them a serious threat to homeowners and business owners alike.\\r\\nTermites are social insects that live in large colonies and feed on wood and other cellulose-based materials. They are typically found in warm, humid climates and can be difficult to detect due to their underground tunnels and hidden nesting sites.\\r\\n\\r\\nTo protect your home or business from termite damage, it\\\\\'s important to know what signs to look for. Common signs of a termite infestation include mud tubes on exterior walls, damaged or hollow-sounding wood, and discarded termite wings. If you suspect you have a termite problem, it\\\\\'s crucial to contact a pest control professional as soon as possible to prevent further damage.\\r\\n\\r\\nPreventing termite infestations starts with taking proactive measures to make your property less attractive to these pests. Some tips for preventing termites include reducing moisture levels in and around your home or business, keeping firewood and other wood-based materials away from the building, and sealing any cracks or gaps in the foundation or walls.\\r\\n\\r\\nIf you do end up with a termite infestation, there are several treatment options available. A pest control professional may recommend a variety of methods, including liquid treatments, baits, and fumigation. The best treatment option will depend on the severity of the infestation and other factors unique to your property.\\r\\n\\r\\nIn conclusion, termites are a serious threat to the structural integrity of homes and businesses. By taking proactive measures to prevent infestations and contacting a pest control professional at the first sign of trouble, you can protect your property from these silent destroyers.\"]},\"moreInfo\":[]}'),
(16, '2023-03-20 20:57:49', 1, 'services', 4, '{\"changed\":{\"short_description\":[\"Rodents, such as mice and rats, are a common pest problem for homeowners and businesses. These small, furry creatures are known for their ability to squeeze through even the tiniest of openings and can cause significant damage to property if left unchecked.\",\"Rodents: The Unwelcome House Guests\"],\"description\":[\"One of the biggest concerns with rodents is their ability to spread disease. Rodents can carry a variety of diseases, including hantavirus, salmonellosis, and leptospirosis. They can also contaminate food and surfaces with their urine and droppings, posing a health risk to humans and pets.\\r\\n\\r\\nTo prevent a rodent infestation, it\\\\\'s important to take proactive measures to make your property less attractive to these pests. Some tips for preventing rodents include sealing any cracks or gaps in the foundation or walls, keeping food stored in airtight containers, and eliminating any sources of standing water.\\r\\n\\r\\nIf you suspect you have a rodent problem, there are several signs to look for. Common signs of a rodent infestation include gnaw marks on wires or other materials, droppings, and the sound of scurrying or scratching in the walls or ceilings. If you notice any of these signs, it\\\\\'s important to contact a pest control professional as soon as possible to prevent further damage and potential health risks.\\r\\n\\r\\nA pest control professional can provide a variety of treatment options for rodent infestations, including traps and baits. They can also offer advice on how to prevent future infestations and provide ongoing monitoring and maintenance services to ensure that your property remains rodent-free.\\r\\n\\r\\nIn conclusion, rodents can pose a serious health risk to humans and pets and can cause significant damage to property if left unchecked. By taking proactive measures to prevent infestations and contacting a pest control professional at the first sign of trouble, you can protect your property from these unwelcome house guests.\",\"Rodents, such as mice and rats, are a common pest problem for homeowners and businesses. These small, furry creatures are known for their ability to squeeze through even the tiniest of openings and can cause significant damage to property if left unchecked.\\r\\nOne of the biggest concerns with rodents is their ability to spread disease. Rodents can carry a variety of diseases, including hantavirus, salmonellosis, and leptospirosis. They can also contaminate food and surfaces with their urine and droppings, posing a health risk to humans and pets.\\r\\n\\r\\nTo prevent a rodent infestation, it\\\\\'s important to take proactive measures to make your property less attractive to these pests. Some tips for preventing rodents include sealing any cracks or gaps in the foundation or walls, keeping food stored in airtight containers, and eliminating any sources of standing water.\\r\\n\\r\\nIf you suspect you have a rodent problem, there are several signs to look for. Common signs of a rodent infestation include gnaw marks on wires or other materials, droppings, and the sound of scurrying or scratching in the walls or ceilings. If you notice any of these signs, it\\\\\'s important to contact a pest control professional as soon as possible to prevent further damage and potential health risks.\\r\\n\\r\\nA pest control professional can provide a variety of treatment options for rodent infestations, including traps and baits. They can also offer advice on how to prevent future infestations and provide ongoing monitoring and maintenance services to ensure that your property remains rodent-free.\\r\\n\\r\\nIn conclusion, rodents can pose a serious health risk to humans and pets and can cause significant damage to property if left unchecked. By taking proactive measures to prevent infestations and contacting a pest control professional at the first sign of trouble, you can protect your property from these unwelcome house guests.\"]},\"moreInfo\":[]}'),
(17, '2023-03-20 20:58:55', 1, 'services', 1, '{\"changed\":{\"short_description\":[\"Cockroaches are a common household pest that can be difficult to get rid of once they infest your home. These insects are known for their resilience and ability to adapt to a variety of environments, which makes them a particularly challenging pest to control.\",\"Cockroaches: How to Identify, Prevent, and Control Infestations\"],\"description\":[\"Identification: Cockroaches are typically brown or black in color and have a flat, oval-shaped body. They have six legs and two long antennae that they use to navigate their environment. There are several different species of cockroaches, including the German cockroach, American cockroach, and Oriental cockroach, all of which have slightly different physical characteristics.\\r\\n\\r\\nBehavior: Cockroaches are primarily nocturnal and prefer to hide in dark, moist areas during the day. They are attracted to food and can often be found in kitchens, pantries, and other areas where food is stored. Cockroaches can also spread bacteria and disease, making them a health hazard in addition to being a nuisance.\\r\\n\\r\\nPrevention: To prevent cockroach infestations, it\'s important to keep your home clean and free of food debris. This includes wiping down counters and surfaces, storing food in airtight containers, and regularly taking out the garbage. Sealing up any cracks or gaps in your home\'s foundation can also help prevent cockroaches from entering.\\r\\n\\r\\nTreatment: If you do have a cockroach infestation, it\'s important to take action quickly to prevent the problem from getting worse. This may include using cockroach baits or traps, applying insecticides, or hiring a professional pest control company to eliminate the infestation.\\r\\n\\r\\nOverall, cockroaches are a pest that no homeowner wants to deal with. By taking preventative measures and seeking professional help when necessary, you can keep your home free of these pesky insects and protect your family\'s health and safety.\",\"Cockroaches are a common household pest that can be difficult to get rid of once they infest your home. These insects are known for their resilience and ability to adapt to a variety of environments, which makes them a particularly challenging pest to control.\\r\\n\\r\\nIdentification: Cockroaches are typically brown or black in color and have a flat, oval-shaped body. They have six legs and two long antennae that they use to navigate their environment. There are several different species of cockroaches, including the German cockroach, American cockroach, and Oriental cockroach, all of which have slightly different physical characteristics.\\r\\n\\r\\nBehavior: Cockroaches are primarily nocturnal and prefer to hide in dark, moist areas during the day. They are attracted to food and can often be found in kitchens, pantries, and other areas where food is stored. Cockroaches can also spread bacteria and disease, making them a health hazard in addition to being a nuisance.\\r\\n\\r\\nPrevention: To prevent cockroach infestations, it\\\\\'s important to keep your home clean and free of food debris. This includes wiping down counters and surfaces, storing food in airtight containers, and regularly taking out the garbage. Sealing up any cracks or gaps in your home\\\\\'s foundation can also help prevent cockroaches from entering.\\r\\n\\r\\nTreatment: If you do have a cockroach infestation, it\\\\\'s important to take action quickly to prevent the problem from getting worse. This may include using cockroach baits or traps, applying insecticides, or hiring a professional pest control company to eliminate the infestation.\\r\\n\\r\\nOverall, cockroaches are a pest that no homeowner wants to deal with. By taking preventative measures and seeking professional help when necessary, you can keep your home free of these pesky insects and protect your family\\\\\'s health and safety.\"]},\"moreInfo\":[]}'),
(18, '2023-03-21 20:16:09', 1, 'services', 6, '{\"changed\":{\"name\":[\"Bee Control\",\"Wasps Nest Removal\"],\"uri_value\":[\"bee-control\",\"wasps-nest-removal\"],\"short_description\":[\"Bee Control: Tips for Safe and Effective Removal\",\"Wasps Nest Removal: Tips for Safe and Effective Removal\"],\"description\":[\"Bees are an important part of our ecosystem, but when they establish their hives in or around our homes or businesses, they can pose a serious risk to our health and safety. While it\\\\\'s important to respect these beneficial insects and their role in pollinating our crops and flowers, it\\\\\'s also important to know how to safely and effectively remove them when they become a nuisance.\\r\\n\\r\\nThe first step in bee control is to properly identify the species of bee and the location of the hive. Different species of bees have different behaviors and require different removal methods. For example, honey bees are social insects that form large colonies and are often found in cavities such as walls, while carpenter bees are solitary insects that burrow into wood.\\r\\n\\r\\nOnce you\\\\\'ve identified the bee species and hive location, it\\\\\'s important to take the appropriate safety precautions. Bees can become aggressive when their hive is disturbed, so it\\\\\'s important to wear protective clothing, avoid making sudden movements, and keep children and pets at a safe distance.\\r\\n\\r\\nIf the hive is small and accessible, it may be possible to remove it yourself using a vacuum or insecticide spray. However, if the hive is large or located in a difficult-to-reach area, it\\\\\'s best to seek professional help. A pest control professional can provide safe and effective removal methods that will minimize harm to the bees and prevent future infestations.\\r\\n\\r\\nIn addition to removal, it\\\\\'s also important to take preventive measures to reduce the risk of future bee infestations. This can include sealing up any gaps or cracks in your home or business, removing sources of standing water, and avoiding planting flowering plants near the building.\\r\\n\\r\\nBy taking a respectful and cautious approach to bee control, you can ensure the safety of yourself and others while also preserving the important role that bees play in our environment. A pest control professional can provide further advice and assistance in developing a comprehensive bee control plan tailored to your specific needs and concerns.\\r\\n\\r\\nIn conclusion, bee control is an important aspect of maintaining a safe and pest-free environment for both humans and bees. By taking steps to identify, remove, and prevent bee infestations, you can ensure the safety of yourself and others while also preserving the vital role that these beneficial insects play in our ecosystem.\",\"Wasps Nest Removal are an important part of our ecosystem, but when they establish their hives in or around our homes or businesses, they can pose a serious risk to our health and safety. While it\\\\\'s important to respect these beneficial insects and their role in pollinating our crops and flowers, it\\\\\'s also important to know how to safely and effectively remove them when they become a nuisance.\\r\\n\\r\\nThe first step in bee control is to properly identify the species of bee and the location of the hive. Different species of bees have different behaviors and require different removal methods. For example, honey bees are social insects that form large colonies and are often found in cavities such as walls, while carpenter bees are solitary insects that burrow into wood.\\r\\n\\r\\nOnce you\\\\\'ve identified the bee species and hive location, it\\\\\'s important to take the appropriate safety precautions. Bees can become aggressive when their hive is disturbed, so it\\\\\'s important to wear protective clothing, avoid making sudden movements, and keep children and pets at a safe distance.\\r\\n\\r\\nIf the hive is small and accessible, it may be possible to remove it yourself using a vacuum or insecticide spray. However, if the hive is large or located in a difficult-to-reach area, it\\\\\'s best to seek professional help. A pest control professional can provide safe and effective removal methods that will minimize harm to the bees and prevent future infestations.\\r\\n\\r\\nIn addition to removal, it\\\\\'s also important to take preventive measures to reduce the risk of future bee infestations. This can include sealing up any gaps or cracks in your home or business, removing sources of standing water, and avoiding planting flowering plants near the building.\\r\\n\\r\\nBy taking a respectful and cautious approach to bee control, you can ensure the safety of yourself and others while also preserving the important role that bees play in our environment. A pest control professional can provide further advice and assistance in developing a comprehensive bee control plan tailored to your specific needs and concerns.\\r\\n\\r\\nIn conclusion, bee control is an important aspect of maintaining a safe and pest-free environment for both humans and bees. By taking steps to identify, remove, and prevent bee infestations, you can ensure the safety of yourself and others while also preserving the vital role that these beneficial insects play in our ecosystem.\"]},\"moreInfo\":[]}'),
(19, '2023-04-26 00:36:15', 1, 'news_articles', 3, '{\"changed\":{\"description\":[\"\",\"This article could emphasize the importance of regular pest inspections for both homeowners and business owners. The post could explain how routine inspections can help identify potential pest problems early on and prevent them from becoming more serious and costly to address later. The article could also offer tips for finding a reputable pest control company to conduct the inspections.\"]},\"moreInfo\":[]}'),
(20, '2023-04-26 00:36:51', 1, 'news_articles', 2, '{\"changed\":{\"description\":[\"\",\"This article could provide readers with information about common household pests, such as ants, cockroaches, and rodents, and offer tips and tricks for getting rid of them. The article could also cover preventative measures that homeowners can take to keep pests from entering their homes in the first place.\"]},\"moreInfo\":[]}'),
(21, '2023-04-26 00:37:45', 1, 'news_articles', 4, '{\"changed\":{\"description\":[\"\",\"This post could provide readers with tips for selecting a reliable and effective pest control service. The article could cover topics such as what to look for in a pest control company, how to compare pricing and services, and what questions to ask before hiring a pest control service.\"]},\"moreInfo\":[]}'),
(22, '2023-04-26 00:37:58', 1, 'news_articles', 5, '{\"changed\":{\"description\":[\"\",\"This article could discuss the ways in which climate change is impacting pest populations around the world. The post could explain how rising temperatures, changing precipitation patterns, and other environmental factors are affecting the behavior and distribution of pests. The article could also offer tips for homeowners and businesses on how to adapt to these changes and prevent pest infestations.\"]},\"moreInfo\":[]}'),
(23, '2023-04-26 00:38:10', 1, 'news_articles', 1, '{\"changed\":{\"description\":[\"\",\"This blog post could discuss the potential dangers of attempting to control pests without the help of a professional pest control service. The article could highlight the risks of using store-bought pesticides and other DIY methods that may not be effective in getting rid of pests or may even be harmful to humans and pets.\"]},\"moreInfo\":[]}'),
(24, '2023-04-30 06:04:27', 1, 'appointments', 25, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(25, '2023-04-30 06:08:30', 1, 'appointments', 25, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(26, '2023-04-30 06:09:50', 1, 'appointments', 25, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(27, '2023-04-30 06:21:35', 1, 'appointments', 25, '{\"changed\":{\"\":\"Approved this Appointment\"},\"moreInfo\":[]}'),
(28, '2023-04-30 20:54:10', 1, 'appointments', 25, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(29, '2023-04-30 20:54:15', 1, 'appointments', 24, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(30, '2023-04-30 20:54:20', 1, 'appointments', 23, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(31, '2023-05-01 02:12:14', 1, 'services', 3, '{\"changed\":{\"font_awesome\":[\"flaticon-termite\",\"termite\"]},\"moreInfo\":[]}'),
(32, '2023-05-01 02:18:07', 1, 'services', 3, '{\"changed\":{\"font_awesome\":[\"termite\",\"bug\"]},\"moreInfo\":[]}'),
(33, '2023-05-01 02:19:57', 1, 'services', 7, '{\"changed\":{\"font_awesome\":[\"flaticon-ant\",\"bug\"]},\"moreInfo\":[]}'),
(34, '2023-05-01 02:20:57', 1, 'services', 7, '{\"changed\":{\"font_awesome\":[\"bug\",\"bugs\"]},\"moreInfo\":[]}'),
(35, '2023-05-01 02:21:05', 1, 'services', 7, '{\"changed\":{\"font_awesome\":[\"bugs\",\"bug\"]},\"moreInfo\":[]}'),
(36, '2023-05-01 02:21:16', 1, 'services', 5, '{\"changed\":{\"font_awesome\":[\"flaticon-fly\",\"bugs\"]},\"moreInfo\":[]}'),
(37, '2023-05-01 02:21:43', 1, 'services', 8, '{\"changed\":{\"font_awesome\":[\"flaticon-mosquito\",\"mosquito\"]},\"moreInfo\":[]}'),
(38, '2023-05-01 02:22:47', 1, 'services', 2, '{\"changed\":{\"font_awesome\":[\"flaticon-tarantula\",\"spider\"]},\"moreInfo\":[]}'),
(39, '2023-05-01 02:24:50', 1, 'services', 6, '{\"changed\":{\"font_awesome\":[\"flaticon-bee\",\"mosquito\"]},\"moreInfo\":[]}'),
(40, '2023-05-01 02:26:11', 1, 'services', 1, '{\"changed\":{\"font_awesome\":[\"flaticon-cockroach\",\"spider\"]},\"moreInfo\":[]}'),
(41, '2023-05-01 02:26:52', 1, 'services', 4, '{\"changed\":{\"font_awesome\":[\"flaticon-squirrel\",\"cat\"]},\"moreInfo\":[]}'),
(42, '2023-05-02 23:37:30', 1, 'services', 8, '{\"changed\":{\"font_awesome\":[\"mosquito\",\"fa-spider\"]},\"moreInfo\":[]}'),
(43, '2023-05-02 23:37:47', 1, 'services', 8, '{\"changed\":{\"font_awesome\":[\"fa-spider\",\"spider\"]},\"moreInfo\":[]}'),
(44, '2023-05-03 00:24:02', 1, 'services', 8, '{\"changed\":{\"font_awesome\":[\"spider\",\"logo-image-bedbug\"]},\"moreInfo\":[]}'),
(45, '2023-05-03 00:29:03', 1, 'services', 7, '{\"changed\":{\"font_awesome\":[\"bug\",\"logo-image-ant\"]},\"moreInfo\":[]}'),
(46, '2023-05-03 00:49:52', 1, 'services', 1, '{\"changed\":{\"font_awesome\":[\"spider\",\"logo-image-cockroach\"]},\"moreInfo\":[]}'),
(47, '2023-05-03 00:51:00', 1, 'services', 5, '{\"changed\":{\"font_awesome\":[\"bugs\",\"logo-image-fleas\"]},\"moreInfo\":[]}'),
(48, '2023-05-03 00:51:46', 1, 'services', 4, '{\"changed\":{\"font_awesome\":[\"cat\",\"logo-image-mice\"]},\"moreInfo\":[]}'),
(49, '2023-05-03 00:52:25', 1, 'services', 2, '{\"changed\":{\"font_awesome\":[\"spider\",\"logo-image-spiders\"]},\"moreInfo\":[]}'),
(50, '2023-05-03 00:52:58', 1, 'services', 3, '{\"changed\":{\"font_awesome\":[\"bug\",\"logo-image-termite\"]},\"moreInfo\":[]}'),
(51, '2023-05-03 00:55:31', 1, 'services', 6, '{\"changed\":{\"font_awesome\":[\"mosquito\",\"logo-image-fleas\"]},\"moreInfo\":[]}'),
(52, '2023-05-06 03:22:32', 1, 'services', 6, '{\"changed\":{\"name\":[\"Wasps Nest Removal\",\"Wasps Nest\"],\"uri_value\":[\"wasps-nest-removal\",\"wasps-nest\"]},\"moreInfo\":[]}'),
(53, '2023-05-06 03:24:15', 1, 'services', 6, '{\"changed\":{\"short_description\":[\"Wasps Nest Removal: Tips for Safe and Effective Removal\",\"Tips for Safe and Effective Wasps Nest Removal Service\"]},\"moreInfo\":[]}'),
(54, '2023-05-06 03:25:23', 1, 'services', 7, '{\"changed\":{\"short_description\":[\"Ant Control: Tips for Effective Removal and Prevention\",\"Tips for Effective Control and Prevention of Ant\"]},\"moreInfo\":[]}'),
(55, '2023-05-06 03:26:10', 1, 'services', 8, '{\"changed\":{\"short_description\":[\"Bed Bug Control: How to Identify, Treat, and Prevent Infestations\",\"How to Identify, Treat, and Prevent Infestations to control Bed Bug\"]},\"moreInfo\":[]}'),
(56, '2023-05-06 03:27:15', 1, 'services', 1, '{\"changed\":{\"short_description\":[\"Cockroaches: How to Identify, Prevent, and Control Infestations\",\"How to Identify, Prevent, and Control Infestations made by Cockroaches movement\"]},\"moreInfo\":[]}'),
(57, '2023-05-06 03:27:55', 1, 'services', 5, '{\"changed\":{\"short_description\":[\"Fly Control: Tips for Keeping Your Home or Business Pest-Free\",\"Tips for Keeping Your Home or Business Fly Pest-Free\"]},\"moreInfo\":[]}'),
(58, '2023-05-06 03:28:28', 1, 'services', 4, '{\"changed\":{\"short_description\":[\"Rodents: The Unwelcome House Guests\",\"Rodents The Unwelcome House Guests\"]},\"moreInfo\":[]}'),
(59, '2023-05-06 03:29:16', 1, 'services', 2, '{\"changed\":{\"short_description\":[\"Spiders: Friend or Foe? Understanding these Common Arachnids\",\"Friend or Foe? Understanding these Common Arachnids Spiders\"]},\"moreInfo\":[]}'),
(60, '2023-05-06 03:29:49', 1, 'services', 3, '{\"changed\":{\"short_description\":[\"Termites: The Silent Destroyers\",\"Termites The Silent Destroyers\"]},\"moreInfo\":[]}'),
(61, '2023-05-06 03:30:43', 1, 'services', 1, '{\"changed\":{\"uri_value\":[\"Cockroach\",\"cockroach\"],\"short_description\":[\"How to Identify, Prevent, and Control Infestations made by Cockroaches movement\",\"How to Prevent and Control Infestations made by Cockroaches movement\"]},\"moreInfo\":[]}'),
(62, '2023-05-06 03:31:22', 1, 'services', 1, '{\"changed\":{\"short_description\":[\"How to Prevent and Control Infestations made by Cockroaches movement\",\"How to Prevent Infestations made by Cockroaches movement\"]},\"moreInfo\":[]}'),
(63, '2023-05-06 03:32:10', 1, 'services', 3, '{\"changed\":{\"short_description\":[\"Termites The Silent Destroyers\",\"Control The Termites The Silent Destroyers\"]},\"moreInfo\":[]}'),
(64, '2023-05-06 03:33:24', 1, 'services', 5, '{\"changed\":{\"short_description\":[\"Tips for Keeping Your Home or Business Fly Pest-Free\",\"Keep Your Home or Business Fly Pest-Free\"]},\"moreInfo\":[]}'),
(65, '2023-05-06 03:35:22', 1, 'services', 8, '{\"changed\":{\"short_description\":[\"How to Identify, Treat, and Prevent Infestations to control Bed Bug\",\"How to Identify, Treat Infestations spread by Bed Bug\"]},\"moreInfo\":[]}'),
(66, '2023-05-06 03:36:26', 1, 'services', 2, '{\"changed\":{\"short_description\":[\"Friend or Foe? Understanding these Common Arachnids Spiders\",\"Understanding these Common Arachnids Spiders\"]},\"moreInfo\":[]}'),
(67, '2023-05-29 22:58:41', 2, 'appointments', 26, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(68, '2023-05-29 22:58:47', 2, 'appointments', 27, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(69, '2023-05-29 22:58:49', 2, 'appointments', 28, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(70, '2023-05-29 22:58:52', 2, 'appointments', 29, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(71, '2023-05-29 22:58:55', 2, 'appointments', 30, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(72, '2023-05-29 22:58:57', 2, 'appointments', 31, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(73, '2023-05-29 22:59:02', 2, 'appointments', 32, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(74, '2023-05-29 22:59:05', 2, 'appointments', 33, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(75, '2023-05-29 22:59:08', 2, 'appointments', 34, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(76, '2023-05-29 22:59:11', 2, 'appointments', 35, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}'),
(77, '2023-05-29 22:59:13', 2, 'appointments', 36, '{\"changed\":{\"\":\"Cancel this Appointment\"},\"moreInfo\":[]}');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `users_id` int(11) NOT NULL,
  `password_hash` varchar(64) NOT NULL,
  `changepass_link` varchar(32) NOT NULL,
  `employee_number` varchar(20) NOT NULL,
  `pin` varchar(10) NOT NULL,
  `branches_id` tinyint(1) NOT NULL,
  `users_first_name` varchar(12) NOT NULL,
  `users_last_name` varchar(17) NOT NULL,
  `users_email` varchar(100) NOT NULL,
  `users_publish` tinyint(1) NOT NULL DEFAULT 1,
  `is_admin` tinyint(1) NOT NULL,
  `users_roll` varchar(255) NOT NULL,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `lastlogin` datetime NOT NULL,
  `minute_to_logout` smallint(6) NOT NULL DEFAULT 60,
  `popup_message` mediumtext NOT NULL,
  `login_message` text NOT NULL,
  `login_ck_id` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`users_id`, `password_hash`, `changepass_link`, `employee_number`, `pin`, `branches_id`, `users_first_name`, `users_last_name`, `users_email`, `users_publish`, `is_admin`, `users_roll`, `created_on`, `last_updated`, `lastlogin`, `minute_to_logout`, `popup_message`, `login_message`, `login_ck_id`) VALUES
(1, '$2y$10$ODEjMtZw4559Kf6zTW5pJ.Q8IyzmVi7CdnHiVk//Z7Yx3fgbTqwIK', '', '', '', 0, 'Super', 'Administrator', 'mdshobhancse@gmail.com', 1, 1, '[]', '2011-01-12 00:00:00', '2023-05-09 04:05:49', '2023-03-20 21:15:46', 60, '<h4><br></h4>', '', ''),
(2, '$2y$10$mDvBy1YFrT5NzBtGRT565.X4DkSU.H8QMhUUO9/ZO8gl1JwWVUcZC', '45be180323bb65cac44414eb31dc4038', '', '', 1, 'Abdus', 'Shobhan', 'shobhancse@gmail.com', 1, 1, '[]', '2011-01-12 00:00:00', '2023-05-29 22:58:07', '2023-05-29 22:58:07', 60, '<h4><br></h4>', '', '5df9da76b83791c'),
(3, '$2y$10$Vt9qPgLXgaTyjjYhacQzWu1C7dgoW0CdoVAJ3h5yRDyZ5OEbjOfcW', '', '', '', 0, 'Info', 'Pesterminate', 'info@pesterminate.ca', 1, 0, '[]', '2021-04-30 10:15:01', '2023-05-27 02:46:20', '2023-05-27 02:46:20', 60, '', '', 'fd8d0c1308d0148');

-- --------------------------------------------------------

--
-- Table structure for table `users_login_history`
--

CREATE TABLE `users_login_history` (
  `users_login_history_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `login_datetime` datetime NOT NULL,
  `logout_datetime` datetime NOT NULL,
  `login_ip` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users_login_history`
--

INSERT INTO `users_login_history` (`users_login_history_id`, `users_id`, `login_datetime`, `logout_datetime`, `login_ip`) VALUES
(1, 3, '2023-03-21 20:41:15', '2023-03-21 20:41:15', '127.0.0.1'),
(2, 3, '2023-05-01 04:47:05', '2023-05-01 04:47:12', '103.175.130.18'),
(3, 3, '2023-05-01 04:49:14', '2023-05-01 04:49:39', '103.175.130.18'),
(4, 3, '2023-05-01 04:49:56', '2023-05-01 04:49:56', '103.175.130.18'),
(5, 3, '2023-05-01 06:06:42', '2023-05-01 06:06:42', '103.252.226.12'),
(6, 3, '2023-05-02 01:33:29', '2023-05-02 01:33:29', '49.0.33.130'),
(7, 3, '2023-05-02 01:34:02', '2023-05-02 01:34:02', '49.0.33.130'),
(8, 3, '2023-05-02 01:34:25', '2023-05-02 01:34:25', '49.0.33.130'),
(9, 3, '2023-05-03 05:41:17', '2023-05-03 05:41:17', '49.0.33.130'),
(10, 3, '2023-05-03 05:45:03', '2023-05-03 05:45:03', '49.0.33.130'),
(11, 3, '2023-05-03 23:41:24', '2023-05-03 23:41:24', '49.0.33.130'),
(12, 3, '2023-05-06 00:37:28', '2023-05-06 00:37:28', '49.0.33.130'),
(13, 3, '2023-05-26 12:11:42', '2023-05-26 12:11:42', '174.89.251.252'),
(14, 3, '2023-05-27 02:45:15', '2023-05-27 02:45:15', '49.0.33.130'),
(15, 3, '2023-05-27 02:46:20', '2023-05-27 02:46:20', '49.0.33.130'),
(16, 2, '2023-05-29 07:13:08', '2023-05-29 07:13:08', '49.0.33.130'),
(17, 2, '2023-05-29 07:29:03', '2023-05-29 07:29:03', '49.0.33.130'),
(18, 2, '2023-05-29 07:37:05', '2023-05-29 07:37:05', '49.0.33.130'),
(19, 2, '2023-05-29 07:37:12', '2023-05-29 07:37:12', '49.0.33.130'),
(20, 2, '2023-05-29 07:38:46', '2023-05-29 07:38:46', '49.0.33.130'),
(21, 2, '2023-05-29 07:40:14', '2023-05-29 07:40:14', '49.0.33.130'),
(22, 2, '2023-05-29 07:43:03', '2023-05-29 07:43:03', '49.0.33.130'),
(23, 2, '2023-05-29 07:43:54', '2023-05-29 07:43:54', '49.0.33.130'),
(24, 2, '2023-05-29 07:45:13', '2023-05-29 07:45:13', '49.0.33.130'),
(25, 2, '2023-05-29 22:58:07', '2023-05-29 22:58:07', '49.0.33.130');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `videos_id` int(11) NOT NULL,
  `videos_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `youtube_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`videos_id`, `videos_publish`, `created_on`, `last_updated`, `users_id`, `name`, `youtube_url`) VALUES
(1, 1, '2023-03-19 22:55:04', '2023-03-19 23:22:09', 1, 'Cockroach Health Risks', 'https://www.youtube.com/embed/oQpXTwaLwfw'),
(2, 1, '2023-03-19 22:59:28', '2023-03-19 23:20:59', 1, 'LEPTOSPIROSIS', 'https://www.youtube.com/embed/oXh9SMuVPtk'),
(3, 1, '2023-03-19 23:00:15', '2023-03-19 23:20:06', 1, 'Ant Invasions', 'https://www.youtube.com/embed/3cPwLevDMe8');

-- --------------------------------------------------------

--
-- Table structure for table `why_choose_us`
--

CREATE TABLE `why_choose_us` (
  `why_choose_us_id` int(11) NOT NULL,
  `why_choose_us_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `why_choose_us`
--

INSERT INTO `why_choose_us` (`why_choose_us_id`, `why_choose_us_publish`, `created_on`, `last_updated`, `users_id`, `name`, `description`) VALUES
(1, 1, '2023-03-20 23:06:29', '2023-03-20 23:06:29', 1, 'Experienced and Professional Technicians', 'Our technicians are highly trained and experienced in pest control, ensuring that they provide the best service possible.'),
(2, 1, '2023-03-20 23:06:48', '2023-03-20 23:06:48', 1, 'Customized Treatment Plans', 'We understand that each pest problem is unique, which is why we create customized treatment plans tailored to your specific needs.'),
(3, 1, '2023-03-20 23:07:02', '2023-03-20 23:07:02', 1, 'Eco-Friendly Solutions', 'We prioritize using eco-friendly solutions that are safe for you, your family, and the environment.'),
(4, 1, '2023-03-20 23:07:20', '2023-03-20 23:07:20', 1, 'Quick Response Time', 'We know that pest problems can be urgent, which is why we offer a fast response time to ensure that your pest problem is addressed quickly and efficiently.'),
(5, 1, '2023-03-20 23:07:41', '2023-03-20 23:07:41', 1, 'Affordable Prices', 'We believe that pest control should be affordable, which is why we offer competitive prices without compromising on quality.'),
(6, 1, '2023-03-20 23:07:56', '2023-03-20 23:07:56', 1, 'Guaranteed Results', 'We stand behind our work and offer a satisfaction guarantee to ensure that you are happy with the results of our pest control services.'),
(7, 1, '2023-03-20 23:08:10', '2023-03-20 23:08:10', 1, 'Excellent Customer Service', 'We value our customers and strive to provide the best customer service possible, answering any questions you may have and addressing any concerns in a timely and professional manner.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_feed`
--
ALTER TABLE `activity_feed`
  ADD PRIMARY KEY (`activity_feed_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointments_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`banners_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`branches_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customers_id`);

--
-- Indexes for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  ADD PRIMARY KEY (`customer_reviews_id`);

--
-- Indexes for table `front_menu`
--
ALTER TABLE `front_menu`
  ADD PRIMARY KEY (`front_menu_id`),
  ADD KEY `menu_uri` (`menu_uri`) USING BTREE;

--
-- Indexes for table `news_articles`
--
ALTER TABLE `news_articles`
  ADD PRIMARY KEY (`news_articles_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`pages_id`);

--
-- Indexes for table `photo_gallery`
--
ALTER TABLE `photo_gallery`
  ADD PRIMARY KEY (`photo_gallery_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`services_id`);

--
-- Indexes for table `track_edits`
--
ALTER TABLE `track_edits`
  ADD PRIMARY KEY (`track_edits_id`),
  ADD KEY `created_by` (`record_for`,`record_id`) USING BTREE;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`users_id`);

--
-- Indexes for table `users_login_history`
--
ALTER TABLE `users_login_history`
  ADD PRIMARY KEY (`users_login_history_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`videos_id`);

--
-- Indexes for table `why_choose_us`
--
ALTER TABLE `why_choose_us`
  ADD PRIMARY KEY (`why_choose_us_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_feed`
--
ALTER TABLE `activity_feed`
  MODIFY `activity_feed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointments_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `banners_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `branches_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `customer_reviews`
--
ALTER TABLE `customer_reviews`
  MODIFY `customer_reviews_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `front_menu`
--
ALTER TABLE `front_menu`
  MODIFY `front_menu_id` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `news_articles`
--
ALTER TABLE `news_articles`
  MODIFY `news_articles_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `pages_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `photo_gallery`
--
ALTER TABLE `photo_gallery`
  MODIFY `photo_gallery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `services_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `track_edits`
--
ALTER TABLE `track_edits`
  MODIFY `track_edits_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `users_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_login_history`
--
ALTER TABLE `users_login_history`
  MODIFY `users_login_history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `videos_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `why_choose_us`
--
ALTER TABLE `why_choose_us`
  MODIFY `why_choose_us_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
