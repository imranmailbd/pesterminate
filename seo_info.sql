CREATE TABLE `seo_info` (
  `seo_info_id` int(11) NOT NULL,
  `seo_info_publish` tinyint(1) NOT NULL DEFAULT 1,
  `created_on` datetime NOT NULL,
  `last_updated` datetime NOT NULL,
  `users_id` int(11) NOT NULL,
  `seo_title` varchar(200) NOT NULL,
  `uri_value` varchar(200) NOT NULL,
  `seo_keywords` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `video_url` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `seo_info`
  ADD PRIMARY KEY (`seo_info_id`);
  
ALTER TABLE `seo_info`
  MODIFY `seo_info_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;  

INSERT INTO `seo_info` (`seo_info_id`, `seo_info_publish`, `created_on`, `last_updated`, `users_id`, `seo_title`, `uri_value`, `seo_keywords`, `description`, `video_url`) VALUES
(1, 1, '2023-08-31 00:29:00', '2023-08-31 00:29:00', 1, 'Residential Pest Control in Toronto | Pesterminate | Pest Control', 'home', 'Residential Pest Control in Toronto, Best Pest Control in Toronto, Mice removal treatment in Toronto, Rat Removal treatment in Toronto, Ants and Carpenter Ants Removal treatment in Toronto', 'Residential Pest Control in Toronto. We are a professional pest control company dedicated to helping you keep your home or business free from unwanted pests.', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(2, 1, '2023-08-31 00:43:48', '2023-08-31 00:43:48', 1, 'Termites', 'services/termites', 'Termites', 'Termites', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(3, 1, '2023-08-31 01:02:56', '2023-08-31 01:02:56', 1, 'Cockroach', 'services/cockroach', 'Cockroach', 'Cockroach', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(4, 1, '2023-08-31 01:05:11', '2023-08-31 01:05:11', 1, 'Fly Control', 'services/fly-control', 'Fly Control', 'Fly Control', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(5, 1, '2023-08-31 01:06:53', '2023-08-31 01:06:53', 1, 'Bed Bug', 'services/bed-bug', 'Bed Bug', 'Bed Bug', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(6, 1, '2023-08-31 01:08:04', '2023-08-31 01:08:04', 1, 'Wasps Nest', 'services/wasps-nest', 'Wasps Nest', 'Wasps Nest', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(7, 1, '2023-08-31 01:12:19', '2023-08-31 01:12:19', 1, 'Ant Control', 'services/ant-control', 'Ant Control', 'Ant Control', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(8, 1, '2023-08-31 01:15:31', '2023-08-31 01:15:31', 1, 'Rodents', 'services/rodents', 'Rodents', 'Rodents', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(9, 1, '2023-08-31 01:16:36', '2023-08-31 01:16:36', 1, 'Spiders', 'services/spiders', 'Spiders', 'Spiders', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(10, 1, '2023-08-31 01:43:41', '2023-08-31 01:43:41', 1, 'About Pesterminate', 'about-pesterminate', 'About Pesterminate', 'About Pesterminate', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(11, 1, '2023-08-31 01:45:24', '2023-08-31 01:45:24', 1, 'Common Pest Control', 'services/residential', 'Common Pest Control', 'Common Pest Control', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(12, 1, '2023-08-31 01:48:41', '2023-08-31 01:48:41', 1, 'WHY CHOOSE US', 'why-choose-us', 'WHY CHOOSE US', 'WHY CHOOSE US', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(13, 1, '2023-08-31 01:53:35', '2023-08-31 01:53:35', 1, 'Contact Us', 'contact-us', 'Contact Us', 'Contact Us', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(14, 1, '2023-08-31 07:24:38', '2023-08-31 07:24:38', 1, 'Pesterminate News & Articles', 'news-articles', 'Pesterminate  News  Articles', 'Pesterminate  News & Articles', 'http://www.youtube.com/watch?v=xkcoM8eUuL8'),
(15, 1, '2023-09-03 05:57:27', '2023-09-03 06:21:14', 1, 'Residential Pest Control in Toronto', 'residential-pest-control-in-toronto', 'Residential Pest Control in Toronto, Pest Control Toronto,', 'Our residential pest control in Toronto and wildlife removal experts have years of experience in safely and effectively removing pest and wildlife such as ants', 'https://www.youtube.com/watch?v=gw9RHV8utgc');

