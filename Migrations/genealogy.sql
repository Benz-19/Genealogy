
--
-- Database: `genealogy`
--

-- --------------------------------------------------------

--
-- Table structure for table `genealogy_relationships`
--

DROP TABLE IF EXISTS `genealogy_relationships`;
CREATE TABLE IF NOT EXISTS `genealogy_relationships` (
  `ancestor_id` int NOT NULL,
  `descendant_id` int NOT NULL,
  `depth` int NOT NULL,
  PRIMARY KEY (`ancestor_id`,`descendant_id`),
  KEY `descendant_id` (`descendant_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genealogy_relationships`
--

INSERT INTO `genealogy_relationships` (`ancestor_id`, `descendant_id`, `depth`) VALUES
(1, 1, 0),
(1, 2, 1),
(1, 3, 2),
(2, 2, 0),
(2, 3, 1),
(3, 3, 0),
(4, 4, 0),
(1, 4, 3),
(2, 4, 2),
(3, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `genealogy_users`
--

DROP TABLE IF EXISTS `genealogy_users`;
CREATE TABLE IF NOT EXISTS `genealogy_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referral_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referrer_id` int DEFAULT NULL,
  `network_size` int DEFAULT '0',
  `network_depth` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `referral_code` (`referral_code`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `genealogy_users`
--

INSERT INTO `genealogy_users` (`id`, `username`, `email`, `referral_code`, `referrer_id`, `network_size`, `network_depth`, `created_at`) VALUES
(1, 'Alex', 'alex@test.com', 'REF-alex-A111', NULL, 3, 3, '2026-02-05 05:02:24'),
(2, 'Garry', 'garry@test.com', 'REF-garry-B222', 1, 2, 2, '2026-02-05 05:02:24'),
(3, 'Charles', 'charles@test.com', 'REF-charles-C333', 2, 1, 1, '2026-02-05 05:02:24'),
(4, 'andrew', 'andrew@gmail.com', 'REF-andrew-65A1C5', 3, 0, 0, '2026-02-05 05:03:00');
COMMIT;

