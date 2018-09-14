# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: localhost (MySQL 5.6.38)
# Database: directus_test
# Generation Time: 2018-09-14 15:44:46 +0000
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
  `action` varchar(45) NOT NULL,
  `action_by` int(11) unsigned NOT NULL DEFAULT '0',
  `action_on` datetime NOT NULL,
  `ip` varchar(50) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `collection` varchar(64) NOT NULL,
  `item` varchar(255) NOT NULL,
  `edited_on` datetime DEFAULT NULL,
  `comment` text,
  `comment_deleted_on` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table directus_activity_seen
# ------------------------------------------------------------

DROP TABLE IF EXISTS `directus_activity_seen`;

CREATE TABLE `directus_activity_seen` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `activity` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL DEFAULT '0',
  `seen_on` datetime DEFAULT NULL,
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

INSERT INTO `directus_collections` (`collection`, `managed`, `hidden`, `single`, `translation`, `note`, `icon`)
VALUES
    ('home',1,0,0,NULL,NULL,NULL),
    ('home_news',1,0,0,NULL,NULL,NULL),
    ('languages',1,0,0,NULL,NULL,NULL),
    ('news',1,0,0,NULL,NULL,NULL),
    ('news_translations',1,0,0,NULL,NULL,NULL);

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
	(1,'directus_activity','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(2,'directus_activity','action','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(3,'directus_activity','action_by','number','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(4,'directus_activity','action_on','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(5,'directus_activity','ip','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(6,'directus_activity','user_agent','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(7,'directus_activity','collection','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(8,'directus_activity','item','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(9,'directus_activity','comment','string','markdown',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(10,'directus_activity','edited_on','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(11,'directus_activity','comment_deleted_on','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(12,'directus_activity_seen','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(13,'directus_activity_seen','activity','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(14,'directus_activity_seen','user','number','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(15,'directus_activity_seen','seen_on','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(16,'directus_activity_seen','archived','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(17,'directus_collections','collection','string','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(18,'directus_collections','managed','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(19,'directus_collections','hidden','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(20,'directus_collections','single','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(21,'directus_collections','translation','json','code','{ \"language\": \"application/json\" }',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(22,'directus_collections','note','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(23,'directus_collections','icon','string','icon',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(24,'directus_collection_presets','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(25,'directus_collection_presets','title','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(26,'directus_collection_presets','user','number','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(27,'directus_collection_presets','role','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(28,'directus_collection_presets','collection','string','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(29,'directus_collection_presets','search_query','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(30,'directus_collection_presets','filters','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(31,'directus_collection_presets','view_options','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(32,'directus_collection_presets','view_type','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(33,'directus_collection_presets','view_query','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(34,'directus_collection_presets','translation','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(35,'directus_fields','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(36,'directus_fields','collection','string','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(37,'directus_fields','field','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(38,'directus_fields','type','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(39,'directus_fields','interface','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(40,'directus_fields','options','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(41,'directus_fields','locked','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(42,'directus_fields','translation','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(43,'directus_fields','readonly','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(44,'directus_fields','required','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(45,'directus_fields','sort','number','sort',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(46,'directus_fields','note','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(47,'directus_fields','hidden_input','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(48,'directus_fields','hidden_list','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(49,'directus_fields','view_width','number','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(50,'directus_fields','group','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(51,'directus_files','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(52,'directus_files','filename','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(53,'directus_files','title','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(54,'directus_files','description','string','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(55,'directus_files','location','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(56,'directus_files','tags','array','tags',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(57,'directus_files','width','number','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(58,'directus_files','height','number','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(59,'directus_files','filesize','number','filesize',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(60,'directus_files','duration','number','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(61,'directus_files','metadata','json','JSON',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(62,'directus_files','type','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(63,'directus_files','charset','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(64,'directus_files','embed','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(65,'directus_files','folder','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(66,'directus_files','uploaded_by','number','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(67,'directus_files','uploaded_on','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(68,'directus_files','storage_adapter','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(69,'directus_files','data','alias','alias','{ \"nameField\": \"filename\", \"sizeField\": \"filesize\", \"typeField\": \"type\" }',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(70,'directus_files','url','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(71,'directus_folders','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(72,'directus_folders','name','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(73,'directus_folders','parent_folder','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(74,'directus_roles','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(75,'directus_roles','name','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(76,'directus_roles','description','string','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(77,'directus_roles','ip_whitelist','string','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(78,'directus_roles','nav_blacklist','string','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(79,'directus_user_roles','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(80,'directus_user_roles','user','number','user',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(81,'directus_user_roles','role','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(82,'directus_users','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(83,'directus_users','status','status','status','{\"status_mapping\":{\"deleted\":{\"name\":\"Deleted\",\"published\":false},\"active\":{\"name\":\"Active\",\"published\":true},\"draft\":{\"name\":\"Draft\",\"published\":false},\"suspended\":{\"name\":\"Suspended\",\"published\":false},\"invited\":{\"name\":\"Invited\",\"published\":false}}}',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(84,'directus_users','first_name','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(85,'directus_users','last_name','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(86,'directus_users','email','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(87,'directus_users','roles','o2m','one-to-many',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(88,'directus_users','email_notifications','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(89,'directus_users','password','string','password',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(90,'directus_users','avatar','file','single-file',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(91,'directus_users','company','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(92,'directus_users','title','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(93,'directus_users','locale','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(94,'directus_users','locale_options','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(95,'directus_users','timezone','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(96,'directus_users','last_ip','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(97,'directus_users','last_access_on','datetime','datetime',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(98,'directus_users','last_page','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(99,'directus_users','token','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(100,'directus_permissions','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(101,'directus_permissions','collection','string','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(102,'directus_permissions','role','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(103,'directus_permissions','status','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(104,'directus_permissions','create','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(105,'directus_permissions','read','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(106,'directus_permissions','update','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(107,'directus_permissions','delete','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(108,'directus_permissions','explain','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(109,'directus_permissions','allow_statuses','array','tags',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(110,'directus_permissions','read_field_blacklist','string','textarea',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(111,'directus_relations','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(112,'directus_relations','collection_many','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(113,'directus_relations','field_many','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(114,'directus_relations','collection_one','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(115,'directus_relations','field_one','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(116,'directus_relations','junction_field','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(117,'directus_revisions','id','number','primary-key',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(118,'directus_revisions','activity','number','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(119,'directus_revisions','collection','string','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(120,'directus_revisions','item','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(121,'directus_revisions','data','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(122,'directus_revisions','delta','json','json',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(123,'directus_revisions','parent_item','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(124,'directus_revisions','parent_collection','string','many-to-one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(125,'directus_revisions','parent_changed','boolean','toggle',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(126,'directus_settings','auto_sign_out','number','numeric',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(127,'directus_settings','youtube_api_key','string','text-input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(128,'products','status','status','status','{\"status_mapping\":{\"1\":{\"name\":\"Published\"},\"2\":{\"name\":\"Draft\",\"published\":\"0\"}}}',0,NULL,0,0,0,4,'0',0,NULL,0,NULL),
	(129,'products','category_id','number','many_to_one',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(130,'products','images','o2m','one-to-many',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(131,'categories','id','number','primary_key',NULL,0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(132,'categories','products','O2M','one-to-many','{\"status_mapping\":{\"1\":{\"name\":\"Published\"},\"2\":{\"name\":\"Draft\",\"published\":\"0\"}}}',0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(133,'categories','name','string','text_input',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(134,'languages','code','string','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(135,'languages','name','string','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(136,'news','id','integer','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(137,'news_translations','id','integer','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(138,'news_translations','title','varchar','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(139,'news_translations','content','text','textarea','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(140,'news_translations','news','integer','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(141,'news_translations','language','lang','lang','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(142,'news','translations','translation','translation',NULL,0,NULL,0,0,NULL,4,NULL,0,NULL,0,NULL),
	(143,'home','id','primary_key','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(144,'home','title','varchar','text-input','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(145,'home','news','o2m','one-to-many','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(146,'home_news','id','primary_key','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(147,'home_news','home_id','integer','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL),
	(148,'home_news','news_id','integer','numeric','null',0,NULL,0,0,0,4,NULL,0,NULL,0,NULL);

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
  `uploaded_by` int(11) unsigned NOT NULL,
  `uploaded_on` datetime NOT NULL,
  `storage_adapter` varchar(50) NOT NULL DEFAULT 'local',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


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
	(20180220023138,'CreateActivityTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023144,'CreateActivitySeenTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023152,'CreateCollectionsPresetsTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023157,'CreateCollectionsTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023202,'CreateFieldsTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023208,'CreateFilesTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023213,'CreateFoldersTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023217,'CreateRolesTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023226,'CreatePermissionsTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023232,'CreateRelationsTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023238,'CreateRevisionsTable','2018-09-03 13:22:08','2018-09-03 13:22:08',0),
	(20180220023243,'CreateSettingsTable','2018-09-03 13:22:08','2018-09-03 13:22:09',0),
	(20180220023248,'CreateUsersTable','2018-09-03 13:22:09','2018-09-03 13:22:09',0),
	(20180426173310,'CreateUserRoles','2018-09-03 13:22:09','2018-09-03 13:22:09',0);

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
  `collection_many` varchar(64) NOT NULL,
  `field_many` varchar(45) NOT NULL,
  `collection_one` varchar(64) DEFAULT NULL,
  `field_one` varchar(64) DEFAULT NULL,
  `junction_field` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `directus_relations` WRITE;
/*!40000 ALTER TABLE `directus_relations` DISABLE KEYS */;

INSERT INTO `directus_relations` (`id`, `collection_many`, `field_many`, `collection_one`, `field_one`, `junction_field`)
VALUES
	(1,'directus_activity','action_by','directus_users',NULL,NULL),
	(2,'directus_activity_seen','user','directus_users',NULL,NULL),
	(3,'directus_activity_seen','activity','directus_activity',NULL,NULL),
	(4,'directus_collections_presets','user','directus_users',NULL,NULL),
	(5,'directus_collections_presets','group','directus_groups',NULL,NULL),
	(6,'directus_files','uploaded_by','directus_users',NULL,NULL),
	(7,'directus_files','folder','directus_folders',NULL,NULL),
	(8,'directus_folders','parent_folder','directus_folders',NULL,NULL),
	(9,'directus_permissions','group','directus_groups',NULL,NULL),
	(10,'directus_revisions','activity','directus_activity',NULL,NULL),
	(11,'directus_user_roles','user','directus_users','roles','role'),
	(12,'directus_user_roles','role','directus_roles','users','user'),
	(13,'directus_users','avatar','directus_files',NULL,NULL),
	(14, 'products', 'category_id', 'categories', 'products',NULL),
	(15, 'news_translations', 'news', 'news', 'translations',NULL),
	(16, 'products_images', 'product_id', 'products', 'images',NULL),
	(17, 'products_images', 'file_id', 'directus_files', NULL,NULL),
	(18, 'home_news', 'news_id', 'news', NULL,NULL),
	(19, 'home_news', 'home_id', 'home', 'news',NULL);


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

LOCK TABLES `directus_settings` WRITE;
/*!40000 ALTER TABLE `directus_settings` DISABLE KEYS */;

INSERT INTO `directus_settings` (`id`, `scope`, `key`, `value`)
VALUES
	(1,'global','auto_sign_out','60'),
	(2,'global','project_name','Directus'),
	(3,'global','default_limit','200'),
	(4,'global','logo',''),
	(5,'files','youtube_api_key','');

/*!40000 ALTER TABLE `directus_settings` ENABLE KEYS */;
UNLOCK TABLES;


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
  `last_access_on` datetime DEFAULT NULL,
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

INSERT INTO `directus_users` (`id`, `status`, `first_name`, `last_name`, `email`, `email_notifications`, `password`, `avatar`, `company`, `title`, `locale`, `high_contrast_mode`, `locale_options`, `timezone`, `last_access_on`, `last_page`, `token`, `external_id`)
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
