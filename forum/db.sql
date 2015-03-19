-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `forum_categories`;
CREATE TABLE `forum_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `url_friendly_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `order` smallint(5) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_friendly_name_section_id` (`url_friendly_name`,`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_groups2categories`;
CREATE TABLE `forum_groups2categories` (
  `group_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL,
  `moderator` tinyint(3) unsigned NOT NULL,
  `newthread` tinyint(3) unsigned NOT NULL,
  `threadreply` tinyint(3) unsigned NOT NULL,
  `canread` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies`;
CREATE TABLE `forum_replies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_sections`;
CREATE TABLE `forum_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `default_user_group_id` int(10) unsigned NOT NULL,
  `owner_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_subcategories`;
CREATE TABLE `forum_subcategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `last_thread_created_id` int(10) unsigned NOT NULL,
  `last_thread_updated_id` int(10) unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `last_update_time` timestamp NULL DEFAULT NULL,
  `last_response_time` timestamp NULL DEFAULT NULL,
  `last_active_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_threads`;
CREATE TABLE `forum_threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `subcategory_id` int(10) unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8_bin NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `score` int(11) NOT NULL,
  `replies` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `sticky` tinyint(3) unsigned NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `closed` tinyint(3) unsigned NOT NULL,
  `last_reply_id` int(10) unsigned DEFAULT NULL,
  `last_reply_user_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_users2groups`;
CREATE TABLE `forum_users2groups` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_user_groups`;
CREATE TABLE `forum_user_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(10) unsigned NOT NULL,
  `full_name` varchar(150) COLLATE utf8_bin NOT NULL,
  `html_class` varchar(50) COLLATE utf8_bin NOT NULL,
  `admin` tinyint(3) unsigned NOT NULL,
  `moderator` tinyint(3) unsigned NOT NULL,
  `newthread` tinyint(3) unsigned NOT NULL,
  `threadreply` tinyint(3) unsigned NOT NULL,
  `canread` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- 2015-03-13 09:59:06
