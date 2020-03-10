-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('administratorzy',	'7',	1529786891);

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_polish_ci,
  `rule_name` varchar(64) COLLATE utf8_polish_ci DEFAULT NULL,
  `data` text COLLATE utf8_polish_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('acl|backend|add',	2,	NULL,	NULL,	NULL,	1457012529,	1457012529),
('acl|backend|delete',	2,	NULL,	NULL,	NULL,	1457012756,	1457012756),
('acl|backend|edit',	2,	NULL,	NULL,	NULL,	1457012529,	1457012529),
('acl|backend|index',	2,	NULL,	NULL,	NULL,	1457013229,	1457013229),
('administratorzy',	1,	'Administratorzy',	NULL,	NULL,	1458133279,	1458133279),
('admins|backend|add',	2,	NULL,	NULL,	NULL,	1457452106,	1457452106),
('admins|backend|block',	2,	NULL,	NULL,	NULL,	1458663111,	1458663111),
('admins|backend|delete',	2,	NULL,	NULL,	NULL,	1457452106,	1457452106),
('admins|backend|edit',	2,	NULL,	NULL,	NULL,	1457452106,	1457452106),
('admins|backend|index',	2,	NULL,	NULL,	NULL,	1457452106,	1457452106),
('materialcms|backend|403',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('materialcms|backend|index',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('materialcms|backend|sidebar',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('news|backend|add',	2,	NULL,	NULL,	NULL,	1459844358,	1459844358),
('news|backend|delete',	2,	NULL,	NULL,	NULL,	1459844358,	1459844358),
('news|backend|edit',	2,	NULL,	NULL,	NULL,	1459844358,	1459844358),
('news|backend|index',	2,	NULL,	NULL,	NULL,	1459844358,	1459844358),
('photos|backend|add',	2,	NULL,	NULL,	NULL,	1459347947,	1459347947),
('photos|backend|display',	2,	NULL,	NULL,	NULL,	1459347947,	1459347947),
('photos|backend|edit',	2,	NULL,	NULL,	NULL,	1459347947,	1459347947),
('photos|backend|save',	2,	NULL,	NULL,	NULL,	1459754621,	1459754621),
('photos|backend|upload',	2,	NULL,	NULL,	NULL,	1459347947,	1459347947),
('users|backend|active',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('users|backend|add',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('users|backend|delete',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('users|backend|details',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('users|backend|edit',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905),
('users|backend|index',	2,	NULL,	NULL,	NULL,	1583842905,	1583842905);

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('administratorzy',	'acl|backend|add'),
('administratorzy',	'acl|backend|delete'),
('administratorzy',	'acl|backend|edit'),
('administratorzy',	'acl|backend|index'),
('administratorzy',	'admins|backend|add'),
('administratorzy',	'admins|backend|block'),
('administratorzy',	'admins|backend|delete'),
('administratorzy',	'admins|backend|edit'),
('administratorzy',	'admins|backend|index'),
('administratorzy',	'materialcms|backend|403'),
('administratorzy',	'materialcms|backend|index'),
('administratorzy',	'materialcms|backend|sidebar'),
('administratorzy',	'photos|backend|add'),
('administratorzy',	'photos|backend|display'),
('administratorzy',	'photos|backend|edit'),
('administratorzy',	'photos|backend|save'),
('administratorzy',	'photos|backend|upload'),
('administratorzy',	'users|backend|active'),
('administratorzy',	'users|backend|add'),
('administratorzy',	'users|backend|delete'),
('administratorzy',	'users|backend|details'),
('administratorzy',	'users|backend|edit'),
('administratorzy',	'users|backend|index');

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `g_access_tokens`;
CREATE TABLE `g_access_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(300) COLLATE utf8_polish_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `g_access_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `g_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


DROP TABLE IF EXISTS `g_admin`;
CREATE TABLE `g_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `realname` text COLLATE utf8_polish_ci NOT NULL,
  `avatar` varchar(250) COLLATE utf8_polish_ci DEFAULT NULL,
  `mail` text COLLATE utf8_polish_ci NOT NULL,
  `password` text COLLATE utf8_polish_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `salt` varchar(5) COLLATE utf8_polish_ci NOT NULL,
  `access_token` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `role` text COLLATE utf8_polish_ci NOT NULL,
  `banner` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(10) COLLATE utf8_polish_ci NOT NULL DEFAULT 'backend',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

INSERT INTO `g_admin` (`id`, `realname`, `avatar`, `mail`, `password`, `active`, `salt`, `access_token`, `created`, `role`, `banner`, `type`, `visible`) VALUES
(1,	'Superuser',	NULL,	'tech@cmsuser.pl',	'$2y$13$hwGQY66cFdCpQv09nKNga.gNG13gSj9r3Hqx1XxRZOOljTAiDwp3y',	1,	'BrzxW',	'',	'2020-03-10 11:00:49',	'su',	0,	'backend',	0),
(7,	'Sample',	NULL,	'sample@example.com',	'$2y$13$hwGQY66cFdCpQv09nKNga.gNG13gSj9r3Hqx1XxRZOOljTAiDwp3y',	1,	'BrzxW',	'',	'2020-03-10 10:37:23',	'administratorzy',	0,	'backend',	1);

DROP TABLE IF EXISTS `g_admin_sidebars`;
CREATE TABLE `g_admin_sidebars` (
  `uid` int(11) NOT NULL,
  `sbLeft` tinyint(1) NOT NULL DEFAULT '1',
  `sbRight` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

INSERT INTO `g_admin_sidebars` (`uid`, `sbLeft`, `sbRight`) VALUES
(1,	1,	1);

DROP TABLE IF EXISTS `g_photos`;
CREATE TABLE `g_photos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `code` varchar(15) COLLATE utf8_polish_ci NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `rotate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


DROP TABLE IF EXISTS `g_seo`;
CREATE TABLE `g_seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `params` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `readableName` text COLLATE utf8_polish_ci,
  `title` varchar(200) COLLATE utf8_polish_ci DEFAULT NULL,
  `description` text COLLATE utf8_polish_ci,
  `keywords` text COLLATE utf8_polish_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`params`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

INSERT INTO `g_seo` (`id`, `name`, `params`, `readableName`, `title`, `description`, `keywords`) VALUES
(8,	'news',	'{\"id\":\"index\"}',	'Aktualności',	'Lorem ipsum dolor sit amet',	'Lorem ipsum dolor sit amet',	'Lorem ipsum dolor sit amet');

DROP TABLE IF EXISTS `g_users`;
CREATE TABLE `g_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `surname` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `mail` varchar(150) COLLATE utf8_polish_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_polish_ci NOT NULL,
  `salt` varchar(5) COLLATE utf8_polish_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `nip` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `base_address` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL,
  `extra_data` json DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `role` varchar(100) COLLATE utf8_polish_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_polish_ci NOT NULL DEFAULT 'frontend',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8_polish_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;


ALTER TABLE `g_users`
ADD `phone` varchar(20) COLLATE 'utf8_polish_ci' NOT NULL AFTER `mail`;

-- 2018-06-23 23:51:33
