# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.38)
# Database: directus_test
# Generation Time: 2018-08-03 18:54:40 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;

INSERT INTO `categories` (`id`, `name`)
VALUES
	(1,'Old Category');

/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table directus_activity
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_activity`;

CREATE TABLE `directus_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(45) NOT NULL,
  `action` varchar(45) NOT NULL,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `datetime` datetime NOT NULL,
  `ip` varchar(50) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `collection` varchar(64) NOT NULL,
  `item` varchar(255) NOT NULL,
  `datetime_edited` datetime DEFAULT NULL,
  `comment` text,
  `deleted_comment` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_activity_seen
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_activity_seen`;

CREATE TABLE `directus_activity_seen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `seen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `archived` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_collection_presets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_collection_presets`;

CREATE TABLE `directus_collection_presets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `user` int(11) unsigned DEFAULT NULL,
  `role` int(11) unsigned DEFAULT NULL,
  `collection` varchar(64) NOT NULL,
  `search_query` varchar(100) DEFAULT NULL,
  `filters` text,
  `view_type` varchar(100) NOT NULL,
  `view_query` text,
  `view_options` text,
  `translation` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_collection_title` (`user`,`collection`,`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_collections
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_collections`;

CREATE TABLE `directus_collections` (
  `collection` varchar(64) NOT NULL,
  `item_name_template` varchar(255) DEFAULT NULL,
  `preview_url` varchar(255) DEFAULT NULL,
  `managed` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `hidden` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `single` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `translation` text,
  `note` varchar(255) DEFAULT NULL,
  `icon` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`collection`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_collections` WRITE;
/*!40000 ALTER TABLE `directus_collections` DISABLE KEYS */;

INSERT INTO `directus_collections` (`collection`, `item_name_template`, `preview_url`, `managed`, `hidden`, `single`, `translation`, `note`, `icon`)
VALUES
    ('home',NULL,NULL,1,0,0,NULL,NULL,NULL),
    ('home_news',NULL,NULL,1,0,0,NULL,NULL,NULL),
    ('languages',NULL,NULL,1,0,0,NULL,NULL,NULL),
    ('news',NULL,NULL,1,0,0,NULL,NULL,NULL),
    ('news_translations',NULL,NULL,1,0,0,NULL,NULL,NULL);

/*!40000 ALTER TABLE `directus_collections` ENABLE KEYS */;
UNLOCK TABLES;

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
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `translation` text,
  `readonly` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort` int(11) unsigned DEFAULT NULL,
  `view_width` int(11) unsigned NOT NULL DEFAULT '4',
  `note` varchar(1024) DEFAULT NULL,
  `hidden_input` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `validation` varchar(255) DEFAULT NULL,
  `hidden_list` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `group` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_collection_field` (`collection`,`field`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_fields` WRITE;
/*!40000 ALTER TABLE `directus_fields` DISABLE KEYS */;

INSERT INTO `directus_fields` (`id`, `collection`, `field`, `type`, `interface`, `options`, `locked`, `translation`, `readonly`, `required`, `sort`, `view_width`, `note`, `hidden_input`, `validation`, `hidden_list`, `group`)
VALUES
	(1,'directus_activity','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(2,'directus_activity','type','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(3,'directus_activity','action','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(4,'directus_activity','user','int','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(5,'directus_activity','datetime','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(6,'directus_activity','ip','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(7,'directus_activity','user_agent','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(8,'directus_activity','collection','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(9,'directus_activity','item','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(10,'directus_activity','comment','text','markdown',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(11,'directus_activity_read','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(12,'directus_activity_read','activity','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(13,'directus_activity_read','user','int','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(14,'directus_activity_read','read','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(15,'directus_activity_read','archived','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(16,'directus_collections','collection','varchar','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(17,'directus_collections','item_name_template','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(18,'directus_collections','preview_url','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(19,'directus_collections','managed','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(20,'directus_collections','hidden','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(21,'directus_collections','single','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(22,'directus_collections','translation','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(23,'directus_collections','note','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(24,'directus_collection_presets','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(25,'directus_collection_presets','title','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(26,'directus_collection_presets','user','int','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(27,'directus_collection_presets','role','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(28,'directus_collection_presets','collection','varchar','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(29,'directus_collection_presets','search_query','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(30,'directus_collection_presets','filters','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(31,'directus_collection_presets','view_options','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(32,'directus_collection_presets','view_type','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(33,'directus_collection_presets','view_query','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(34,'directus_collection_presets','translation','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(35,'directus_fields','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(36,'directus_fields','collection','varchar','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(37,'directus_fields','field','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(38,'directus_fields','type','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(39,'directus_fields','interface','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(40,'directus_fields','options','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(41,'directus_fields','locked','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(42,'directus_fields','translation','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(43,'directus_fields','readonly','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(44,'directus_fields','required','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(45,'directus_fields','sort','int','sort',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(46,'directus_fields','note','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(47,'directus_fields','hidden_input','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(48,'directus_fields','hidden_list','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(49,'directus_fields','view_width','int','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(50,'directus_fields','group','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(51,'directus_files','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(52,'directus_files','filename','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(53,'directus_files','title','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(54,'directus_files','description','text','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(55,'directus_files','location','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(56,'directus_files','tags','array','tags',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(57,'directus_files','width','int','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(58,'directus_files','height','int','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(59,'directus_files','filesize','int','filesize',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(60,'directus_files','duration','int','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(61,'directus_files','metadata','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(62,'directus_files','type','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(63,'directus_files','charset','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(64,'directus_files','embed','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(65,'directus_files','folder','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(66,'directus_files','upload_user','int','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(67,'directus_files','upload_date','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(68,'directus_files','storage_adapter','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(69,'directus_files','data','alias','alias','{ \"nameField\": \"filename\", \"sizeField\": \"filesize\", \"typeField\": \"type\" }',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(70,'directus_files','url','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(71,'directus_folders','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(72,'directus_folders','name','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(73,'directus_folders','parent_folder','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(74,'directus_roles','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(75,'directus_roles','name','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(76,'directus_roles','description','varchar','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(77,'directus_roles','ip_whitelist','text','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(78,'directus_roles','nav_blacklist','text','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(79,'directus_user_roles','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(80,'directus_user_roles','user','int','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(81,'directus_user_roles','role','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(82,'directus_users','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(83,'directus_users','status','varchar','status','{\"status_mapping\":{\"deleted\":{\"name\":\"Deleted\",\"published\":false},\"active\":{\"name\":\"Active\",\"published\":true},\"draft\":{\"name\":\"Draft\",\"published\":false},\"suspended\":{\"name\":\"Suspended\",\"published\":false},\"invited\":{\"name\":\"Invited\",\"published\":false}}}',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(84,'directus_users','first_name','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(85,'directus_users','last_name','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(86,'directus_users','email','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(87,'directus_users','roles','m2m','m2m',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(88,'directus_users','email_notifications','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(89,'directus_users','password','varchar','password',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(90,'directus_users','avatar','file','single-file',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(91,'directus_users','company','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(92,'directus_users','title','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(93,'directus_users','locale','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(94,'directus_users','locale_options','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(95,'directus_users','timezone','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(96,'directus_users','last_ip','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(97,'directus_users','last_login','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(98,'directus_users','last_access','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(99,'directus_users','last_page','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(100,'directus_users','token','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(101,'directus_permissions','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(102,'directus_permissions','collection','varchar','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(103,'directus_permissions','role','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(104,'directus_permissions','status','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(105,'directus_permissions','create','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(106,'directus_permissions','read','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(107,'directus_permissions','update','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(108,'directus_permissions','delete','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(109,'directus_permissions','navigate','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(110,'directus_permissions','explain','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(111,'directus_permissions','allow_statuses','array','tags',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(112,'directus_permissions','read_field_blacklist','varchar','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(113,'directus_relations','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(114,'directus_relations','collection_a','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(115,'directus_relations','field_a','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(116,'directus_relations','junction_key_a','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(117,'directus_relations','junction_mixed_collections','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(118,'directus_relations','junction_key_b','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(119,'directus_relations','collection_b','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(120,'directus_relations','field_b','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(121,'directus_revisions','id','int','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(122,'directus_revisions','activity','int','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(123,'directus_revisions','collection','varchar','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(124,'directus_revisions','item','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(125,'directus_revisions','data','longjson','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(126,'directus_revisions','delta','longjson','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(127,'directus_revisions','parent_item','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(128,'directus_revisions','parent_collection','varchar','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(129,'directus_revisions','parent_changed','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(130,'directus_settings','auto_sign_out','int','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(131,'directus_settings','youtube_api_key','varchar','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(132,'products','status','status','status','{\"status_mapping\":{\"1\":{\"name\":\"Published\"},\"2\":{\"name\":\"Draft\",\"published\":\"0\"}}}',0,NULL,0,0,0,4,'0',0,NULL,0,NULL),
	(133,'products','category_id','integer','many_to_one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(134,'products','images','m2m','many_to_many',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(135,'categories','id','integer','primary_key',NULL,0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(136,'categories','products','O2M','one_to_many','{\"status_mapping\":{\"1\":{\"name\":\"Published\"},\"2\":{\"name\":\"Draft\",\"published\":\"0\"}}}',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(137,'categories','name','varchar','text_input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(138,'languages','code','char','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(139,'languages','name','varchar','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(140,'news','id','integer','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(141,'news_translations','id','integer','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(142,'news_translations','title','varchar','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(143,'news_translations','content','text','textarea','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(144,'news_translations','news','integer','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(145,'news_translations','language','lang','lang','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(146,'news','translations','translation','translation',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(147,'home','id','primary_key','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(148,'home','title','varchar','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(149,'home','news','m2m','many-to-many','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(150,'home_news','id','primary_key','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(151,'home_news','home_id','integer','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(152,'home_news','news_id','integer','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL);

/*!40000 ALTER TABLE `directus_fields` ENABLE KEYS */;
UNLOCK TABLES;


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
  `filesize` int(11) unsigned NOT NULL DEFAULT '0',
  `duration` int(11) DEFAULT NULL,
  `metadata` text,
  `type` varchar(255) DEFAULT NULL,
  `charset` varchar(50) DEFAULT NULL,
  `embed` varchar(200) DEFAULT NULL,
  `folder` int(11) unsigned DEFAULT NULL,
  `upload_user` int(11) unsigned NOT NULL,
  `upload_date` datetime NOT NULL,
  `storage_adapter` varchar(50) NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_files` WRITE;
/*!40000 ALTER TABLE `directus_files` DISABLE KEYS */;

INSERT INTO `directus_files` (`id`, `filename`, `title`, `description`, `location`, `tags`, `width`, `height`, `filesize`, `duration`, `metadata`, `type`, `charset`, `embed`, `folder`, `upload_user`, `upload_date`, `storage_adapter`)
VALUES
	(1,'00000000001.jpg','Mountain Range','A gorgeous view of this wooded mountain range','Earth','trees,rocks,nature,mountains,forest',1800,1200,602058,NULL,NULL,'image/jpeg','binary',NULL,NULL,1,'2018-07-12 13:09:18','local');

/*!40000 ALTER TABLE `directus_files` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_folders
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_folders`;

CREATE TABLE `directus_folders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 NOT NULL,
  `parent_folder` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_name_parent_folder` (`name`,`parent_folder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_migrations`;

CREATE TABLE `directus_migrations` (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_migrations` WRITE;
/*!40000 ALTER TABLE `directus_migrations` DISABLE KEYS */;

INSERT INTO `directus_migrations` (`version`, `migration_name`, `start_time`, `end_time`, `breakpoint`)
VALUES
	(20180220023138,'CreateActivityTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023144,'CreateActivitySeenTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023152,'CreateCollectionsPresetsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023157,'CreateCollectionsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023202,'CreateFieldsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023208,'CreateFilesTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023213,'CreateFoldersTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023217,'CreateRolesTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023226,'CreatePermissionsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023232,'CreateRelationsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023238,'CreateRevisionsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023243,'CreateSettingsTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180220023248,'CreateUsersTable','2018-07-12 13:09:18','2018-07-12 13:09:18',0),
	(20180426173310,'CreateUserRoles','2018-07-12 13:09:18','2018-07-12 13:09:18',0);

/*!40000 ALTER TABLE `directus_migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_permissions`;

CREATE TABLE `directus_permissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collection` varchar(64) NOT NULL,
  `role` int(11) unsigned NOT NULL,
  `status` varchar(64) DEFAULT NULL,
  `status_blacklist` varchar(1000) DEFAULT NULL,
  `create` varchar(16) DEFAULT NULL,
  `read` varchar(16) DEFAULT NULL,
  `update` varchar(16) DEFAULT NULL,
  `delete` varchar(16) DEFAULT NULL,
  `navigate` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `comment` varchar(8) DEFAULT NULL,
  `explain` varchar(8) DEFAULT NULL,
  `read_field_blacklist` varchar(1000) DEFAULT NULL,
  `write_field_blacklist` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_relations` WRITE;
/*!40000 ALTER TABLE `directus_relations` DISABLE KEYS */;

INSERT INTO `directus_relations` (`id`, `collection_a`, `field_a`, `junction_key_a`, `junction_collection`, `junction_mixed_collections`, `junction_key_b`, `collection_b`, `field_b`)
VALUES
	(1,'directus_activity','user',NULL,NULL,NULL,NULL,'directus_users',NULL),
	(2,'directus_activity_read','user',NULL,NULL,NULL,NULL,'directus_users',NULL),
	(3,'directus_activity_read','activity',NULL,NULL,NULL,NULL,'directus_activity',NULL),
	(4,'directus_collections_presets','user',NULL,NULL,NULL,NULL,'directus_users',NULL),
	(5,'directus_collections_presets','group',NULL,NULL,NULL,NULL,'directus_groups',NULL),
	(6,'directus_files','upload_user',NULL,NULL,NULL,NULL,'directus_users',NULL),
	(7,'directus_files','folder',NULL,NULL,NULL,NULL,'directus_folders',NULL),
	(8,'directus_folders','parent_folder',NULL,NULL,NULL,NULL,'directus_folders',NULL),
	(9,'directus_permissions','group',NULL,NULL,NULL,NULL,'directus_groups',NULL),
	(10,'directus_revisions','activity',NULL,NULL,NULL,NULL,'directus_activity',NULL),
	(11,'directus_users','roles','user','directus_user_roles',NULL,'role','directus_roles','users'),
	(12,'directus_users','avatar',NULL,NULL,NULL,NULL,'directus_files',NULL),
	(13,'products','category_id',NULL,NULL,NULL,NULL,'categories','products'),
	(14,'products','images','product_id','products_images',NULL,'file_id','directus_files','id'),
	(15,'news','translations',NULL,NULL,NULL,NULL,'news_translations','news'),
	(16,'home','news','home_id','home_news',NULL,'news_id','news','id');

/*!40000 ALTER TABLE `directus_relations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_revisions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_revisions`;

CREATE TABLE `directus_revisions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity` int(11) unsigned NOT NULL,
  `collection` varchar(64) NOT NULL,
  `item` varchar(255) NOT NULL,
  `data` longtext NOT NULL,
  `delta` longtext,
  `parent_item` varchar(255) DEFAULT NULL,
  `parent_collection` varchar(64) DEFAULT NULL,
  `parent_changed` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_roles`;

CREATE TABLE `directus_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `external_id` varchar(255) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `ip_whitelist` text,
  `nav_blacklist` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_group_name` (`name`),
  UNIQUE KEY `idx_users_external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_roles` WRITE;
/*!40000 ALTER TABLE `directus_roles` DISABLE KEYS */;

INSERT INTO `directus_roles` (`id`, `external_id`, `name`, `description`, `ip_whitelist`, `nav_blacklist`)
VALUES
	(1,NULL,'Administrator','Admins have access to all managed data within the system by default',NULL,NULL),
	(2,NULL,'Public','This sets the data that is publicly available through the API without a token',NULL,NULL),
	(3,'3','Intern',NULL,NULL,NULL);

/*!40000 ALTER TABLE `directus_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_settings`;

CREATE TABLE `directus_settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(64) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_scope_name` (`scope`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_user_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_user_roles`;

CREATE TABLE `directus_user_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned DEFAULT NULL,
  `role` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_role` (`user`,`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_user_roles` WRITE;
/*!40000 ALTER TABLE `directus_user_roles` DISABLE KEYS */;

INSERT INTO `directus_user_roles` (`id`, `user`, `role`)
VALUES
	(1,1,1),
	(2,2,3),
	(3,3,3);

/*!40000 ALTER TABLE `directus_user_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table directus_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_users`;

CREATE TABLE `directus_users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(16) NOT NULL DEFAULT 'draft',
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `email_notifications` int(1) NOT NULL DEFAULT '1',
  `password` varchar(255) DEFAULT NULL,
  `avatar` int(11) unsigned DEFAULT NULL,
  `company` varchar(191) DEFAULT NULL,
  `title` varchar(191) DEFAULT NULL,
  `locale` varchar(8) DEFAULT 'en-US',
  `high_contrast_mode` tinyint(1) unsigned DEFAULT '0',
  `locale_options` text,
  `timezone` varchar(32) NOT NULL DEFAULT 'America/New_York',
  `last_access` datetime DEFAULT NULL,
  `last_page` varchar(45) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `external_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_users_email` (`email`),
  UNIQUE KEY `idx_users_token` (`token`),
  UNIQUE KEY `idx_users_external_id` (`external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_users` WRITE;
/*!40000 ALTER TABLE `directus_users` DISABLE KEYS */;

INSERT INTO `directus_users` (`id`, `status`, `first_name`, `last_name`, `email`, `email_notifications`, `password`, `avatar`, `company`, `title`, `locale`, `high_contrast_mode`, `locale_options`, `timezone`, `last_access`, `last_page`, `token`, `external_id`)
VALUES
    (1,'active','Admin','User','admin@getdirectus.com',1,'$2y$10$sx0.rYeNCXvJZ9LYGPZofekAq2ah7pVEWnB3YR5aNNseLBAILztc2',1,NULL,NULL,'en-US',0,NULL,'Europe/Berlin','2018-05-21 15:48:03','/collections/projects','token','00ud6pmxj4KW5F6Ua0h7'),
    (2,'active','Intern','User','intern@getdirectus.com',1,NULL,NULL,NULL,NULL,'en-US',0,NULL,'Europe/Berlin',NULL,NULL,'intern_token',NULL),
    (3,'suspended','Disabled','User','disabled@getdirectus.com',1,'$2y$10$Njtky/bsFG9qzeW7EPy8FubOay.GxRFWTlCrQEDyR9D0N2UMdxC3u',NULL,NULL,NULL,'en-US',0,NULL,'America/New_York',NULL,NULL,'disabled_token',NULL);

/*!40000 ALTER TABLE `directus_users` ENABLE KEYS */;
UNLOCK TABLES;

# Dump of table home
# ------------------------------------------------------------

DROP TABLE IF EXISTS `home`;

CREATE TABLE `home` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `home` WRITE;
/*!40000 ALTER TABLE `home` DISABLE KEYS */;

INSERT INTO `home` (`id`, `title`)
VALUES
	(1,'title 1');

/*!40000 ALTER TABLE `home` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table home_news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `home_news`;

CREATE TABLE `home_news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `home_id` int(10) unsigned DEFAULT NULL,
  `news_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `home_news` WRITE;
/*!40000 ALTER TABLE `home_news` DISABLE KEYS */;

INSERT INTO `home_news` (`id`, `home_id`, `news_id`)
VALUES
	(1,1,1);

/*!40000 ALTER TABLE `home_news` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table languages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages` (
  `code` char(2) NOT NULL DEFAULT '',
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `languages` WRITE;
/*!40000 ALTER TABLE `languages` DISABLE KEYS */;

INSERT INTO `languages` (`code`, `name`)
VALUES
	('en','English'),
	('es','Español'),
	('nl','Nederlands');

/*!40000 ALTER TABLE `languages` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;

INSERT INTO `news` (`id`)
VALUES
	(1),
	(2);

/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table news_translations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `news_translations`;

CREATE TABLE `news_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `content` text,
  `news` int(10) unsigned DEFAULT NULL,
  `language` char(2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `news_translations` WRITE;
/*!40000 ALTER TABLE `news_translations` DISABLE KEYS */;

INSERT INTO `news_translations` (`id`, `title`, `content`, `news`, `language`)
VALUES
	(1,'Title','content',1,'en'),
	(2,'Titulo','contenido',1,'es'),
	(3,'Titel','inhoud',1,'nl');

/*!40000 ALTER TABLE `news_translations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '2',
  `price` decimal(10,2) unsigned NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table products_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products_images`;

CREATE TABLE `products_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `file_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
