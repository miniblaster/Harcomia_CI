CREATE TABLE `users` (
`user_id` int NOT NULL AUTO_INCREMENT,
`email` varchar(320) NOT NULL,
`password` varchar(128) DEFAULT NULL,
`name` varchar(64) NOT NULL,
`billing` text,
`api_key` varchar(32) DEFAULT NULL,
`token_code` varchar(32) DEFAULT NULL,
`twofa_secret` varchar(16) DEFAULT NULL,
`anti_phishing_code` varchar(8) DEFAULT NULL,
`one_time_login_code` varchar(32) DEFAULT NULL,
`pending_email` varchar(128) DEFAULT NULL,
`email_activation_code` varchar(32) DEFAULT NULL,
`lost_password_code` varchar(32) DEFAULT NULL,
`type` tinyint NOT NULL DEFAULT '0',
`status` tinyint NOT NULL DEFAULT '0',
`plan_id` varchar(16) NOT NULL DEFAULT '',
`plan_expiration_date` datetime DEFAULT NULL,
`plan_settings` text,
`plan_trial_done` tinyint(4) DEFAULT '0',
`plan_expiry_reminder` tinyint(4) DEFAULT '0',
`payment_subscription_id` varchar(64) DEFAULT NULL,
`payment_processor` varchar(16) DEFAULT NULL,
`payment_total_amount` float DEFAULT NULL,
`payment_currency` varchar(4) DEFAULT NULL,
`referral_key` varchar(32) DEFAULT NULL,
`referred_by` varchar(32) DEFAULT NULL,
`referred_by_has_converted` tinyint(4) DEFAULT '0',
`language` varchar(32) DEFAULT 'english',
`timezone` varchar(32) DEFAULT 'UTC',
`datetime` datetime DEFAULT NULL,
`ip` varchar(64) DEFAULT NULL,
`country` varchar(32) DEFAULT NULL,
`last_activity` datetime DEFAULT NULL,
`last_user_agent` varchar(256) DEFAULT NULL,
`total_logins` int DEFAULT '0',
`user_deletion_reminder` tinyint(4) DEFAULT '0',
`source` varchar(32) DEFAULT 'direct',
PRIMARY KEY (`user_id`),
KEY `plan_id` (`plan_id`),
KEY `api_key` (`api_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `users` (`user_id`, `email`, `password`, `api_key`, `referral_key`, `name`, `type`, `status`, `plan_id`, `plan_expiration_date`, `plan_settings`, `datetime`, `ip`, `last_activity`)
VALUES (1,'admin','$2y$10$uFNO0pQKEHSFcus1zSFlveiPCB3EvG9ZlES7XKgJFTAl5JbRGFCWy', md5(rand()), md5(rand()), 'AltumCode',1,1,'custom','2030-01-01 12:00:00', '{"additional_global_domains":true,"custom_url":true,"deep_links":true,"no_ads":true,"removable_branding":true,"custom_branding":true,"custom_colored_links":true,"statistics":true,"qr_is_enabled":true,"custom_backgrounds":true,"verified":true,"temporary_url_is_enabled":true,"seo":true,"utm":true,"fonts":true,"password":true,"sensitive_content":true,"leap_link":true,"api_is_enabled":true,"affiliate_is_enabled":true,"dofollow_is_enabled":true,"biolink_blocks_limit":-1,"projects_limit":-1,"pixels_limit":-1,"biolinks_limit":-1,"links_limit":-1,"domains_limit":-1,"track_links_retention":-1,"enabled_biolink_blocks":{"link":true,"heading":true,"paragraph":true,"avatar":true,"image":true,"socials":true,"mail":true,"soundcloud":true,"spotify":true,"youtube":true,"twitch":true,"vimeo":true,"tiktok":true,"applemusic":true,"tidal":true,"anchor":true,"twitter_tweet":true,"instagram_media":true,"rss_feed":true,"custom_html":true,"vcard":true,"image_grid":true,"divider":true,"faq":true,"discord":true,"facebook":true,"reddit":true,"audio":true,"video":true,"file":true,"countdown":true,"cta":true,"external_item":true,"share":true,"youtube_feed":true}}', NOW(),'',NOW());

-- SEPARATOR --

CREATE TABLE `users_logs` (
`id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` int DEFAULT NULL,
`type` varchar(64) DEFAULT NULL,
`ip` varchar(64) DEFAULT NULL,
`device_type` varchar(16) DEFAULT NULL,
`os_name` varchar(16) DEFAULT NULL,
`country_code` varchar(8) DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
PRIMARY KEY (`id`),
KEY `users_logs_user_id` (`user_id`),
CONSTRAINT `users_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `projects` (
`project_id` int NOT NULL AUTO_INCREMENT,
`user_id` int NOT NULL,
`name` varchar(64) NOT NULL DEFAULT '',
`color` varchar(16) DEFAULT '#000',
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`project_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `biolinks_themes` (
`biolink_theme_id` int NOT NULL AUTO_INCREMENT,
`name` varchar(64) NOT NULL,
`image` varchar(40) DEFAULT NULL,
`settings` text,
`is_enabled` tinyint NOT NULL DEFAULT '1',
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`biolink_theme_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `links` (
`link_id` int NOT NULL AUTO_INCREMENT,
`project_id` int DEFAULT NULL,
`user_id` int NOT NULL,
`biolink_theme_id` int DEFAULT NULL,
`biolink_id` int DEFAULT NULL,
`domain_id` int DEFAULT '0',
`pixels_ids` text,
`type` varchar(32) NOT NULL DEFAULT '',
`subtype` varchar(32) DEFAULT NULL,
`url` varchar(256) NOT NULL DEFAULT '',
`location_url` varchar(2048) DEFAULT NULL,
`clicks` int NOT NULL DEFAULT '0',
`settings` text,
`start_date` datetime DEFAULT NULL,
`end_date` datetime DEFAULT NULL,
`is_verified` tinyint DEFAULT '0',
`is_enabled` tinyint NOT NULL DEFAULT '1',
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`link_id`),
KEY `project_id` (`project_id`),
KEY `user_id` (`user_id`),
KEY `url` (`url`),
KEY `links_subtype_index` (`subtype`),
KEY `links_type_index` (`type`),
KEY `links_links_link_id_fk` (`biolink_id`),
KEY `links_biolinks_themes_biolink_theme_id_fk` (`biolink_theme_id`),
CONSTRAINT `links_biolinks_themes_biolink_theme_id_fk` FOREIGN KEY (`biolink_theme_id`) REFERENCES `biolinks_themes` (`biolink_theme_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `links_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `links_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `links_links_link_id_fk` FOREIGN KEY (`biolink_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `biolinks_blocks` (
`biolink_block_id` int NOT NULL AUTO_INCREMENT,
`user_id` int NOT NULL,
`link_id` int DEFAULT NULL,
`type` varchar(32) NOT NULL DEFAULT '',
`location_url` varchar(512) DEFAULT NULL,
`clicks` int NOT NULL DEFAULT '0',
`settings` text,
`order` int NOT NULL DEFAULT '0',
`start_date` datetime DEFAULT NULL,
`end_date` datetime DEFAULT NULL,
`is_enabled` tinyint(4) NOT NULL DEFAULT '1',
`datetime` datetime NOT NULL,
PRIMARY KEY (`biolink_block_id`),
KEY `user_id` (`user_id`),
KEY `links_type_index` (`type`),
KEY `links_links_link_id_fk` (`link_id`),
CONSTRAINT `biolinks_blocks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `biolinks_blocks_ibfk_2` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `pixels` (
`pixel_id` int NOT NULL AUTO_INCREMENT,
`user_id` int NOT NULL,
`type` varchar(64) NOT NULL,
`name` varchar(64) NOT NULL,
`pixel` varchar(64) NOT NULL,
`last_datetime` datetime DEFAULT NULL,
`datetime` datetime NOT NULL,
PRIMARY KEY (`pixel_id`),
KEY `user_id` (`user_id`),
CONSTRAINT `pixels_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `plans` (
`plan_id` int NOT NULL AUTO_INCREMENT,
`name` varchar(256) NOT NULL DEFAULT '',
`description` varchar(256) NOT NULL DEFAULT '',
`monthly_price` float NULL,
`annual_price` float NULL,
`lifetime_price` float NULL,
`trial_days` int unsigned NOT NULL DEFAULT '0',
`settings` text NOT NULL,
`taxes_ids` text,
`codes_ids` text,
`color` varchar(16) DEFAULT NULL,
`status` tinyint(4) NOT NULL,
`order` int(10) unsigned DEFAULT '0',
`datetime` datetime NOT NULL,
PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- SEPARATOR --

CREATE TABLE `pages_categories` (
`pages_category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`url` varchar(256) NOT NULL DEFAULT '',
`title` varchar(64) NOT NULL DEFAULT '',
`description` varchar(128) DEFAULT NULL,
`icon` varchar(32) DEFAULT NULL,
`order` int NOT NULL DEFAULT '0',
`language` varchar(32) DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`pages_category_id`),
KEY `url` (`url`),
KEY `pages_categories_url_language_index` (`url`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- SEPARATOR --

CREATE TABLE `pages` (
`page_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`pages_category_id` bigint unsigned DEFAULT NULL,
`url` varchar(128) NOT NULL,
`title` varchar(64) NOT NULL DEFAULT '',
`description` varchar(128) DEFAULT NULL,
`editor` varchar(16) DEFAULT NULL,
`content` longtext,
`type` varchar(16) DEFAULT '',
`position` varchar(16) NOT NULL DEFAULT '',
`language` varchar(32) DEFAULT NULL,
`open_in_new_tab` tinyint DEFAULT '1',
`order` int DEFAULT '0',
`total_views` int DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`page_id`),
KEY `pages_pages_category_id_index` (`pages_category_id`),
KEY `pages_url_index` (`url`),
KEY `pages_url_language_index` (`url`,`language`),
CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`pages_category_id`) REFERENCES `pages_categories` (`pages_category_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

INSERT INTO `pages` (`pages_category_id`, `url`, `title`, `description`, `content`, `type`, `position`, `order`, `total_views`, `datetime`, `last_datetime`) VALUES
(NULL, 'https://altumcode.com/', 'Software by AltumCode', '', '', 'external', 'bottom', 1, 0, NOW(), NOW()),
(NULL, 'https://altumco.de/66biolinks', 'Built with 66biolinks', '', '', 'external', 'bottom', 0, 0, NOW(), NOW());

-- SEPARATOR --

CREATE TABLE `blog_posts_categories` (
`blog_posts_category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`url` varchar(256) NOT NULL DEFAULT '',
`title` varchar(64) NOT NULL DEFAULT '',
`description` varchar(128) DEFAULT NULL,
`order` int NOT NULL DEFAULT '0',
`language` varchar(32) DEFAULT NULL,
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`blog_posts_category_id`),
KEY `url` (`url`),
KEY `blog_posts_categories_url_language_index` (`url`,`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- SEPARATOR --

CREATE TABLE `blog_posts` (
`blog_post_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`blog_posts_category_id` bigint unsigned DEFAULT NULL,
`url` varchar(128) NOT NULL,
`title` varchar(64) NOT NULL DEFAULT '',
`description` varchar(128) DEFAULT NULL,
`image` varchar(40) DEFAULT NULL,
`editor` varchar(16) DEFAULT NULL,
`content` longtext,
`language` varchar(32) DEFAULT NULL,
`total_views` int DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`blog_post_id`),
KEY `blog_post_id_index` (`blog_post_id`),
KEY `blog_post_url_index` (`url`),
KEY `blog_post_url_language_index` (`url`,`language`),
KEY `blog_posts_category_id` (`blog_posts_category_id`),
CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`blog_posts_category_id`) REFERENCES `blog_posts_categories` (`blog_posts_category_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `track_links` (
`id` int NOT NULL AUTO_INCREMENT,
`user_id` int NOT NULL,
`link_id` int DEFAULT NULL,
`biolink_block_id` int DEFAULT NULL,
`project_id` int DEFAULT NULL,
`country_code` varchar(8) DEFAULT NULL,
`city_name` varchar(128) DEFAULT NULL,
`os_name` varchar(16) DEFAULT NULL,
`browser_name` varchar(32) DEFAULT NULL,
`referrer_host` varchar(256) DEFAULT NULL,
`referrer_path` varchar(1024) DEFAULT NULL,
`device_type` varchar(16) DEFAULT NULL,
`browser_language` varchar(16) DEFAULT NULL,
`utm_source` varchar(128) DEFAULT NULL,
`utm_medium` varchar(128) DEFAULT NULL,
`utm_campaign` varchar(128) DEFAULT NULL,
`is_unique` tinyint(4) DEFAULT '0',
`datetime` datetime NOT NULL,
PRIMARY KEY (`id`),
KEY `link_id` (`link_id`),
KEY `track_links_date_index` (`datetime`),
KEY `track_links_project_id_index` (`project_id`),
KEY `track_links_users_user_id_fk` (`user_id`),
KEY `track_links_biolink_block_id_index` (`biolink_block_id`),
CONSTRAINT `track_links_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `track_links_links_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `links` (`project_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `track_links_projects_project_id_fk` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `track_links_users_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `settings` (
`id` int NOT NULL AUTO_INCREMENT,
`key` varchar(64) NOT NULL DEFAULT '',
`value` longtext NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

SET @cron_key = MD5(RAND());

-- SEPARATOR --

INSERT INTO `settings` (`key`, `value`)
VALUES
('main', '{"title":"harcomia","default_language":"english","default_theme_style":"light","default_timezone":"UTC","index_url":"","terms_and_conditions_url":"","privacy_policy_url":"","not_found_url":"","se_indexing":true,"default_results_per_page":25,"default_order_type":"DESC","auto_language_detection_is_enabled":true,"blog_is_enabled":true,"logo_light":"","logo_dark":"","logo_email":"","opengraph":"","favicon":""}'),
('users', '{"email_confirmation":false,"register_is_enabled":true,"auto_delete_inactive_users":0,"user_deletion_reminder":0}'),
('ads', '{"header":"","footer":"","header_biolink":"","footer_biolink":""}'),
('captcha', '{"type":"basic","recaptcha_public_key":"","recaptcha_private_key":"","login_is_enabled":0,"register_is_enabled":0,"lost_password_is_enabled":0,"resend_activation_is_enabled":0}'),
('cron', concat('{\"key\":\"', @cron_key, '\"}')),
('email_notifications', '{\"emails\":\"\",\"new_user\":\"0\",\"new_payment\":\"0\"}'),
('facebook', '{\"is_enabled\":\"0\",\"app_id\":\"\",\"app_secret\":\"\"}'),
('google', '{\"is_enabled\":\"0\",\"client_id\":\"\",\"client_secret\":\"\"}'),
('twitter', '{\"is_enabled\":\"0\",\"consumer_api_key\":\"\",\"consumer_api_secret\":\"\"}'),
('discord', '{\"is_enabled\":\"0\"}'),
('plan_custom', '{\"plan_id\":\"custom\",\"name\":\"Custom\",\"status\":1}'),
('plan_free', '{"plan_id":"free","name":"Free","days":null,"status":1,"settings":{"additional_global_domains":true,"custom_url":true,"deep_links":true,"no_ads":true,"removable_branding":true,"custom_branding":true,"custom_colored_links":true,"statistics":true,"custom_backgrounds":true,"verified":true,"temporary_url_is_enabled":true,"seo":true,"utm":true,"socials":true,"fonts":true,"password":true,"sensitive_content":true,"leap_link":true,"api_is_enabled":true,"affiliate_is_enabled":true,"projects_limit":10,"pixels_limit":10,"biolinks_limit":15,"links_limit":25,"domains_limit":1,"enabled_biolink_blocks":{"link":true,"text":true,"image":true,"mail":true,"soundcloud":true,"spotify":true,"youtube":true,"twitch":true,"vimeo":true,"tiktok":true,"applemusic":true,"tidal":true,"anchor":true,"twitter_tweet":true,"instagram_media":true,"rss_feed":true,"custom_html":true,"vcard":true,"image_grid":true,"divider":true}}}'),
('payment', '{\"is_enabled\":\"0\",\"type\":\"both\",\"brand_name\":\"phpBiolinks\",\"currency\":\"USD\", \"codes_is_enabled\": false}'),
('paypal', '{\"is_enabled\":\"0\",\"mode\":\"sandbox\",\"client_id\":\"\",\"secret\":\"\"}'),
('stripe', '{\"is_enabled\":\"0\",\"publishable_key\":\"\",\"secret_key\":\"\",\"webhook_secret\":\"\"}'),
('offline_payment', '{\"is_enabled\":\"0\",\"instructions\":\"Your offline payment instructions go here..\"}'),
('coinbase', '{\"is_enabled\":\"0\"}'),
('payu', '{\"is_enabled\":\"0\"}'),
('paystack', '{\"is_enabled\":\"0\"}'),
('razorpay', '{\"is_enabled\":\"0\"}'),
('mollie', '{\"is_enabled\":\"0\"}'),
('yookassa', '{\"is_enabled\":\"0\"}'),
('crypto_com', '{\"is_enabled\":\"0\"}'),
('paddle', '{\"is_enabled\":\"0\"}'),
('smtp', '{\"host\":\"\",\"from\":\"\",\"from_name\":\"\",\"encryption\":\"tls\",\"port\":\"587\",\"auth\":\"0\",\"username\":\"\",\"password\":\"\"}'),
('custom', '{\"head_js\":\"\",\"head_css\":\"\"}'),
('socials', '{\"facebook\":\"\",\"instagram\":\"\",\"twitter\":\"\",\"youtube\":\"\"}'),
('announcements', '{"id":"","content":"","show_logged_in":"","show_logged_out":""}'),
('business', '{\"invoice_is_enabled\":\"0\",\"name\":\"\",\"address\":\"\",\"city\":\"\",\"county\":\"\",\"zip\":\"\",\"country\":\"\",\"email\":\"\",\"phone\":\"\",\"tax_type\":\"\",\"tax_id\":\"\",\"custom_key_one\":\"\",\"custom_value_one\":\"\",\"custom_key_two\":\"\",\"custom_value_two\":\"\"}'),
('webhooks', '{"user_new": "", "user_delete": ""}'),
('cookie_consent', '{}'),
('links', '{"branding":"by AltumCode","shortener_is_enabled":true,"qr_codes_is_enabled":true,"biolinks_is_enabled":true,"files_is_enabled":true,"vcards_is_enabled":true,"directory_is_enabled":true,"directory_display":"verified","domains_is_enabled":true,"main_domain_is_enabled":true,"blacklisted_domains":"","blacklisted_keywords":"","google_safe_browsing_is_enabled":false,"google_safe_browsing_api_key":"","google_static_maps_is_enabled":false,"google_static_maps_api_key":"","avatar_size_limit":2,"background_size_limit":2,"favicon_size_limit":2,"seo_image_size_limit":2,"thumbnail_image_size_limit":2,"image_size_limit":2,"audio_size_limit":2,"video_size_limit":2,"file_size_limit":2,"product_file_size_limit":2}'),
('tools', ''),
('license', '{\"license\": \"codegood.net\", \"type\": \"Extended License\"}'),
('product_info', '{\"version\":\"26.0.0\", \"code\":\"2600\"}');


-- SEPARATOR --

CREATE TABLE `domains` (
`domain_id` int NOT NULL AUTO_INCREMENT,
`user_id` int DEFAULT NULL,
`scheme` varchar(8) NOT NULL DEFAULT '',
`host` varchar(256) NOT NULL DEFAULT '',
`custom_index_url` varchar(256) DEFAULT NULL,
`custom_not_found_url` varchar(256) DEFAULT NULL,
`type` tinyint(11) DEFAULT '1',
`is_enabled` tinyint(4) DEFAULT '0',
`datetime` datetime DEFAULT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`domain_id`),
KEY `user_id` (`user_id`),
KEY `host` (`host`),
KEY `type` (`type`),
CONSTRAINT `domains_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ROW_FORMAT=DYNAMIC ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEPARATOR --

CREATE TABLE `data` (
`datum_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`biolink_block_id` int DEFAULT NULL,
`link_id` int DEFAULT NULL,
`project_id` int DEFAULT NULL,
`user_id` int NOT NULL,
`type` varchar(32) DEFAULT NULL,
`data` text,
`datetime` datetime NOT NULL,
PRIMARY KEY (`datum_id`),
UNIQUE KEY `datum_id` (`datum_id`),
KEY `link_id` (`link_id`),
KEY `project_id` (`project_id`),
KEY `user_id` (`user_id`),
KEY `biolink_block_id` (`biolink_block_id`),
CONSTRAINT `data_ibfk_1` FOREIGN KEY (`link_id`) REFERENCES `links` (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `data_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE,
CONSTRAINT `data_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `data_ibfk_4` FOREIGN KEY (`biolink_block_id`) REFERENCES `biolinks_blocks` (`biolink_block_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `qr_codes` (
`qr_code_id` bigint unsigned NOT NULL AUTO_INCREMENT,
`user_id` int NOT NULL,
`project_id` int DEFAULT NULL,
`name` varchar(64) NOT NULL,
`type` varchar(32) DEFAULT NULL,
`qr_code_logo` varchar(64) DEFAULT NULL,
`qr_code` varchar(64) NOT NULL,
`settings` text,
`datetime` datetime NOT NULL,
`last_datetime` datetime DEFAULT NULL,
PRIMARY KEY (`qr_code_id`),
KEY `user_id` (`user_id`),
KEY `project_id` (`project_id`),
CONSTRAINT `qr_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
CONSTRAINT `qr_codes_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- SEPARATOR --

INSERT INTO `links` (`link_id`, `project_id`, `user_id`, `biolink_id`, `domain_id`, `pixels_ids`, `type`, `subtype`, `url`, `location_url`, `clicks`, `settings`, `start_date`, `end_date`, `is_verified`, `is_enabled`, `datetime`) VALUES (1, NULL, 1, NULL, 0, '[]', 'biolink', NULL, 'example', NULL, 0, '{\"verified_location\":\"top\",\"background_type\":\"preset\",\"background\":\"six\",\"favicon\":null,\"text_color\":\"#fff\",\"display_branding\":true,\"branding\":{\"name\":\"\",\"url\":\"\"},\"seo\":{\"block\":false,\"title\":\"\",\"meta_description\":\"\",\"image\":\"\"},\"utm\":{\"medium\":\"\",\"source\":\"\"},\"font\":\"arial\",\"font_size\":16,\"password\":null,\"sensitive_content\":false,\"leap_link\":\"\"}', NULL, NULL, 1, 1, '2021-12-20 18:05:36');

-- SEPARATOR --

INSERT INTO `biolinks_blocks` (`user_id`, `link_id`, `type`, `location_url`, `clicks`, `settings`, `order`, `start_date`, `end_date`, `is_enabled`, `datetime`) VALUES (1, 1, 'heading', NULL, 0, '{\"heading_type\":\"h1\",\"text\":\"Example page\",\"text_color\":\"white\"}', 0, NULL, NULL, 1, '2021-12-20 18:05:52');

-- SEPARATOR --

INSERT INTO `biolinks_blocks` (`user_id`, `link_id`, `type`, `location_url`, `clicks`, `settings`, `order`, `start_date`, `end_date`, `is_enabled`, `datetime`) VALUES (1, 1, 'paragraph', NULL, 0, '{\"text\":\"This is an example description.\",\"text_color\":\"white\"}', 1, NULL, NULL, 1, '2021-12-20 18:06:09');

-- SEPARATOR --

CREATE TABLE `codes` (
  `code_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `days` int(11) DEFAULT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `discount` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `redeemed` int(11) NOT NULL DEFAULT 0,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plan_id` int(10) UNSIGNED DEFAULT NULL,
  `processor` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_id` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taxes_ids` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `base_amount` float DEFAULT NULL,
  `total_amount` float DEFAULT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` float DEFAULT NULL,
  `currency` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_proof` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `redeemed_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code_id` int(10) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

CREATE TABLE `taxes` (
  `tax_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` int(11) DEFAULT NULL,
  `value_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('inclusive','exclusive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_type` enum('personal','business','both') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countries` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEPARATOR --

ALTER TABLE `codes`
  ADD PRIMARY KEY (`code_id`),
  ADD KEY `type` (`type`),
  ADD KEY `code` (`code`);

-- SEPARATOR --

ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_user_id` (`user_id`),
  ADD KEY `plan_id` (`plan_id`);
  
-- SEPARATOR --

ALTER TABLE `redeemed_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `code_id` (`code_id`),
  ADD KEY `user_id` (`user_id`);

-- SEPARATOR --

ALTER TABLE `taxes`
  ADD PRIMARY KEY (`tax_id`);
  
-- SEPARATOR --

ALTER TABLE `codes`
  MODIFY `code_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- SEPARATOR --

ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

-- SEPARATOR --

ALTER TABLE `redeemed_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

-- SEPARATOR --

ALTER TABLE `taxes`
  MODIFY `tax_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- SEPARATOR --

ALTER TABLE `payments`
  ADD CONSTRAINT `payments_plans_plan_id_fk` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`plan_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `payments_users_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- SEPARATOR --

ALTER TABLE `redeemed_codes`
  ADD CONSTRAINT `redeemed_codes_ibfk_1` FOREIGN KEY (`code_id`) REFERENCES `codes` (`code_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `redeemed_codes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;