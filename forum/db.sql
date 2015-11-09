-- Adminer 4.2.0 MySQL dump

SET NAMES utf8mb4;
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
  `icon` varchar(150) COLLATE utf8_bin NOT NULL,
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
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_eighth`;
CREATE TABLE `forum_replies_eighth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_fifth`;
CREATE TABLE `forum_replies_fifth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_forth`;
CREATE TABLE `forum_replies_forth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_nth`;
CREATE TABLE `forum_replies_nth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `nthreply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_second`;
CREATE TABLE `forum_replies_second` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_seventh`;
CREATE TABLE `forum_replies_seventh` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_sixth`;
CREATE TABLE `forum_replies_sixth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_replies_third`;
CREATE TABLE `forum_replies_third` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `reply_id` int(10) unsigned NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edited` tinyint(3) unsigned NOT NULL,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `deleted` tinyint(3) unsigned NOT NULL,
  `deleted_time` timestamp NULL DEFAULT NULL,
  `deleted_user_id` int(10) unsigned NOT NULL,
  `score` int(11) NOT NULL,
  `user_group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_reply_votes`;
CREATE TABLE `forum_reply_votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reply_id` int(10) unsigned NOT NULL,
  `level` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `vote` tinyint(3) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reply_id_level_user_id` (`reply_id`,`level`,`user_id`),
  KEY `reply_id_level` (`reply_id`,`level`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `forum_sections`;
CREATE TABLE `forum_sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_bin NOT NULL,
  `default_visitors_group_id` int(10) unsigned NOT NULL,
  `default_members_group_id` int(10) unsigned NOT NULL,
  `owner_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_subcategories`;
CREATE TABLE `forum_subcategories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(200) COLLATE utf8_bin NOT NULL,
  `url_friendly_title` varchar(200) COLLATE utf8_bin NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `last_active_thread_id` int(10) unsigned NOT NULL,
  `last_activity` enum('create','edit','reply') COLLATE utf8_bin NOT NULL,
  `last_activity_time` timestamp NULL DEFAULT NULL,
  `last_active_user_id` int(10) unsigned NOT NULL,
  `icon` varchar(150) COLLATE utf8_bin NOT NULL,
  `number_of_threads` int(10) unsigned NOT NULL,
  `number_of_replies` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_threads`;
CREATE TABLE `forum_threads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `subcategory_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 COLLATE utf8_romanian_ci NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `keywords` varchar(250) COLLATE utf8_bin NOT NULL,
  `score` int(11) NOT NULL,
  `replies` int(10) unsigned NOT NULL,
  `first_level_replies` int(10) unsigned NOT NULL,
  `views` int(10) unsigned NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `edit_time` timestamp NULL DEFAULT NULL,
  `edit_user_id` int(10) unsigned NOT NULL,
  `sticky` tinyint(3) unsigned NOT NULL,
  `order` int(10) unsigned NOT NULL,
  `closed` tinyint(3) unsigned NOT NULL,
  `last_reply_id` int(10) unsigned DEFAULT NULL,
  `last_reply_user_id` int(10) unsigned DEFAULT NULL,
  `last_reply_level` int(10) unsigned DEFAULT NULL,
  `last_reply_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `forum_threads`
ADD `deleted` tinyint unsigned NOT NULL,
ADD `deleted_time` timestamp NULL AFTER `deleted`,
ADD `deleted_user_id` int unsigned NOT NULL AFTER `deleted_time`;


DROP TABLE IF EXISTS `forum_thread_tags`;
CREATE TABLE `forum_thread_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `thread_id` int(10) unsigned NOT NULL,
  `word` varchar(100) CHARACTER SET utf8 COLLATE utf8_romanian_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `forum_thread_votes`;
CREATE TABLE `forum_thread_votes` (
  `thread_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `vote` int(10) unsigned NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`thread_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `forum_titles`;
CREATE TABLE `forum_titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(10) unsigned NOT NULL,
  `title` varchar(120) NOT NULL,
  `icon` varchar(120) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `forum_userhiddensubcategories`;
CREATE TABLE `forum_userhiddensubcategories` (
  `user_id` int(10) unsigned NOT NULL,
  `subcategory_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`subcategory_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `forum_users2sections`;
CREATE TABLE `forum_users2sections` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `section_id` int(10) unsigned NOT NULL,
  `muted` tinyint(3) unsigned NOT NULL,
  `banned` tinyint(3) unsigned NOT NULL,
  `title_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `member_since` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `signature` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_section_id` (`user_id`,`section_id`),
  KEY `group_id` (`group_id`),
  KEY `section_id` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


DROP TABLE IF EXISTS `forum_users_subscriptions`;
CREATE TABLE `forum_users_subscriptions` (
  `user_id` int(10) unsigned NOT NULL,
  `thread_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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


-- 2015-05-07 11:11:41