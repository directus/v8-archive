# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.35)
# Database: directus
# Generation Time: 2018-02-15 23:24:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table directus_activity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_activity`;

CREATE TABLE `directus_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `action` varchar(45) NOT NULL,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `datetime` datetime DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `collection` varchar(64) NOT NULL,
  `item` varchar(255) DEFAULT NULL,
  `message` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_activity_read
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_activity_read`;

CREATE TABLE `directus_activity_read` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `read` tinyint(1) DEFAULT '0',
  `archived` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_collection_presets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_collection_presets`;

CREATE TABLE `directus_collection_presets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `user` int(11) unsigned NOT NULL,
  `group` int(11) unsigned DEFAULT NULL,
  `collection` varchar(64) NOT NULL,
  `fields` varchar(255) DEFAULT NULL,
  `statuses` varchar(64) DEFAULT NULL,
  `sort` varchar(255) DEFAULT NULL,
  `search_string` text,
  `filters` text,
  `view_options` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_collection_title` (`user`,`collection`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_collections
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_collections`;

CREATE TABLE `directus_collections` (
  `collection` varchar(64) NOT NULL DEFAULT '',
  `item_name_template` varchar(255) DEFAULT NULL,
  `preview_url` varchar(255) DEFAULT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `single` tinyint(1) NOT NULL DEFAULT '0',
  `status_mapping` text,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_fields
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_fields`;

CREATE TABLE `directus_fields` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection` varchar(64) NOT NULL,
  `field` varchar(64) NOT NULL,
  `type` varchar(64) NOT NULL,
  `interface` varchar(64) NOT NULL,
  `options` text,
  `locked` tinyint(4) NOT NULL DEFAULT '0',
  `translation` text,
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `sort` int(11) unsigned DEFAULT NULL,
  `comment` varchar(1024) DEFAULT NULL,
  `hidden_input` tinyint(4) NOT NULL DEFAULT '0',
  `hidden_list` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `collection-field` (`collection`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_files
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_files`;

CREATE TABLE `directus_files` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `location` varchar(200) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `width` int(11) unsigned DEFAULT NULL,
  `height` int(11) unsigned DEFAULT NULL,
  `filesize` int(11) unsigned DEFAULT '0',
  `duration` int(11) unsigned DEFAULT NULL,
  `metadata` text,
  `type` varchar(255) DEFAULT NULL,
  `charset` varchar(50) DEFAULT NULL,
  `embed` varchar(200) DEFAULT NULL,
  `folder` int(11) unsigned DEFAULT NULL,
  `upload_user` int(11) unsigned NOT NULL,
  `upload_date` datetime NOT NULL,
  `storage_adapter` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `directus_files` WRITE;
/*!40000 ALTER TABLE `directus_files` DISABLE KEYS */;

INSERT INTO `directus_files` (`id`, `filename`, `title`, `description`, `location`, `tags`, `width`, `height`, `filesize`, `duration`, `metadata`, `type`, `charset`, `embed`, `folder`, `upload_user`, `upload_date`, `storage_adapter`)
VALUES
	(1,'00000000001.jpg','Mountain Range','A gorgeous view of this wooded mountain range','Earth','trees,rocks,nature,mountains,forest',1800,1200,602058,NULL,NULL,'image/jpeg','binary',NULL,NULL,1,'2018-02-15 23:20:55','local');

/*!40000 ALTER TABLE `directus_files` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_folders`;

CREATE TABLE `directus_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `parent_folder` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name_parent_folder` (`name`,`parent_folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_groups`;

CREATE TABLE `directus_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ip_whitelist` text,
  `nav_blacklist` text,
  `nav_override` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `directus_users_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `directus_groups` WRITE;
/*!40000 ALTER TABLE `directus_groups` DISABLE KEYS */;

INSERT INTO `directus_groups` (`id`, `name`, `description`, `ip_whitelist`, `nav_blacklist`, `nav_override`)
VALUES
	(1,'Administrator','Admins have access to all managed data within the system by default',NULL,NULL,NULL),
	(2,'Public','This sets the data that is publicly available through the API without a token',NULL,NULL,NULL);

/*!40000 ALTER TABLE `directus_groups` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_migrations`;

CREATE TABLE `directus_migrations` (
  `version` varchar(255) DEFAULT NULL,
  UNIQUE KEY `idx_directus_migrations_version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_migrations` WRITE;
/*!40000 ALTER TABLE `directus_migrations` DISABLE KEYS */;

INSERT INTO `directus_migrations` (`version`)
VALUES
	('20150203221946'),
	('20150203235646'),
	('20150204003426'),
	('20150204015251'),
	('20150204023325'),
	('20150204024327'),
	('20150204031412'),
	('20150204041007'),
	('20150204042725'),
	('20180131165011'),
	('20180131165022'),
	('20180131165033'),
	('20180131165044');

/*!40000 ALTER TABLE `directus_migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_permissions`;

CREATE TABLE `directus_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection` varchar(64) NOT NULL,
  `group` int(11) unsigned NOT NULL,
  `status` int(11) DEFAULT NULL,
  `create` tinyint(1) NOT NULL DEFAULT '0',
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `update` tinyint(1) NOT NULL DEFAULT '0',
  `delete` tinyint(1) NOT NULL DEFAULT '0',
  `navigate` tinyint(1) NOT NULL DEFAULT '1',
  `read_field_blacklist` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `write_field_blacklist` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_relations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_relations`;

CREATE TABLE `directus_relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection_a` varchar(64) NOT NULL,
  `field_a` varchar(45) NOT NULL,
  `junction_key_a` varchar(64) DEFAULT NULL,
  `junction_collection` varchar(64) DEFAULT NULL,
  `junction_mixed_collections` varchar(64) DEFAULT NULL,
  `junction_key_b` varchar(64) DEFAULT NULL,
  `collection_b` varchar(64) DEFAULT NULL,
  `field_b` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_revisions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_revisions`;

CREATE TABLE `directus_revisions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity` int(11) unsigned NOT NULL,
  `collection` varchar(64) NOT NULL,
  `item` varchar(255) DEFAULT NULL,
  `data` text,
  `delta` text,
  `parent_item` varchar(255) DEFAULT NULL,
  `parent_collection` varchar(64) NOT NULL,
  `parent_changed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



# Dump of table directus_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_settings`;

CREATE TABLE `directus_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(64) DEFAULT NULL,
  `group` varchar(64) DEFAULT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_scope_group_key` (`scope`,`group`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `directus_settings` WRITE;
/*!40000 ALTER TABLE `directus_settings` DISABLE KEYS */;

INSERT INTO `directus_settings` (`id`, `scope`, `group`, `key`, `value`)
VALUES
	(1,'global',NULL,'cms_user_auto_sign_out','60'),
	(2,'global',NULL,'project_name','Directus'),
	(3,'global',NULL,'project_url','http://localhost/'),
	(4,'global',NULL,'rows_per_page','200'),
	(5,'files',NULL,'thumbnail_quality','100'),
	(6,'files',NULL,'thumbnail_size','200'),
	(7,'global',NULL,'cms_thumbnail_url',''),
	(8,'files',NULL,'file_naming','file_id'),
	(9,'files',NULL,'thumbnail_crop_enabled','1'),
	(10,'files',NULL,'youtube_api_key','');

/*!40000 ALTER TABLE `directus_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_users`;

CREATE TABLE `directus_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) unsigned DEFAULT '2',
  `first_name` varchar(50) DEFAULT '',
  `last_name` varchar(50) DEFAULT '',
  `email` varchar(128) NOT NULL DEFAULT '',
  `email_notifications` tinyint(1) DEFAULT '1',
  `group` int(11) unsigned DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `avatar` int(11) unsigned DEFAULT NULL,
  `company` varchar(191) DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `locale` varchar(8) DEFAULT 'en-US',
  `locale_options` text,
  `timezone` varchar(32) DEFAULT 'America/New_York',
  `last_ip` varchar(50) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_access` datetime DEFAULT NULL,
  `last_page` varchar(45) DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `invite_token` varchar(255) DEFAULT NULL,
  `invite_accepted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `directus_users_email_unique` (`email`),
  UNIQUE KEY `directus_users_token_unique` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOCK TABLES `directus_users` WRITE;
/*!40000 ALTER TABLE `directus_users` DISABLE KEYS */;

INSERT INTO `directus_users` (`id`, `status`, `first_name`, `last_name`, `email`, `email_notifications`, `group`, `password`, `avatar`, `company`, `title`, `locale`, `locale_options`, `timezone`, `last_ip`, `last_login`, `last_access`, `last_page`, `token`, `invite_token`, `invite_accepted`)
VALUES
	(1,1,'Admin','User','admin@getdirectus.com',1,1,'$2y$10$yVbAAia36vEx2g9zvbA0L.ioHlQ0n1lxIhPUYuEw9.VuxkwFKLTYe',NULL,NULL,NULL,'en-US',NULL,'America/New_York',NULL,NULL,NULL,NULL,'igETSuIIT0VlTKDOjJdprSxEWvXahB7r',NULL,NULL);

/*!40000 ALTER TABLE `directus_users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
