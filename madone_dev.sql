# ************************************************************
# Sequel Pro SQL dump
# Version 3348
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.5.9)
# Database: madone_forsait
# Generation Time: 2011-07-21 20:57:43 +0800
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table madonefeedbackmessage
# ------------------------------------------------------------



# Dump of table madonegalleryimage
# ------------------------------------------------------------



# Dump of table madonegallerysection
# ------------------------------------------------------------



# Dump of table madonemodule
# ------------------------------------------------------------

CREATE TABLE `madonemodule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `classname` varchar(100) DEFAULT NULL,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name__idx` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

LOCK TABLES `madonemodule` WRITE;
/*!40000 ALTER TABLE `madonemodule` DISABLE KEYS */;

INSERT INTO `madonemodule` (`id`, `title`, `name`, `classname`, `enabled`, `position`)
VALUES
	(1,'Страницы сайта','pages','PagesModule',1,1),
	(2,'Текстовые блоки','text-blocks','TextBlocksModule',1,2),
	(5,'Портфолио','portfolio','GalleryModule',1,4),
	(6,'Новости','news','NewsModule',1,3),
	(7,'Витрина','showcase','ShowcaseModule',0,5),
	(8,'Обратная связь','feedback','FeedbackModule',0,6);

/*!40000 ALTER TABLE `madonemodule` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table madonenews
# ------------------------------------------------------------



# Dump of table madonepage
# ------------------------------------------------------------

CREATE TABLE `madonepage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `uri` varchar(700) DEFAULT NULL,
  `text_ruru_utf8` longtext,
  `type` int(10) unsigned NOT NULL,
  `menu` tinyint(3) unsigned DEFAULT NULL,
  `enabled_ruru_utf8` tinyint(3) unsigned DEFAULT NULL,
  `meta_title_ruru_utf8` longtext,
  `meta_keywords_ruru_utf8` longtext,
  `meta_description_ruru_utf8` longtext,
  `lk` int(11) NOT NULL,
  `rk` int(11) NOT NULL,
  `lvl` int(11) NOT NULL,
  `app_settings` longtext,
  PRIMARY KEY (`id`),
  KEY `name__idx` (`name`),
  KEY `uri__idx` (`uri`(333)),
  KEY `type__idx` (`type`),
  KEY `menu__idx` (`menu`),
  KEY `enabled_ruru_utf8__idx` (`enabled_ruru_utf8`),
  KEY `lk__rk__lvl__idx` (`lk`,`rk`,`lvl`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

LOCK TABLES `madonepage` WRITE;
/*!40000 ALTER TABLE `madonepage` DISABLE KEYS */;

INSERT INTO `madonepage` (`id`, `title_ruru_utf8`, `name`, `uri`, `text_ruru_utf8`, `type`, `menu`, `enabled_ruru_utf8`, `meta_title_ruru_utf8`, `meta_keywords_ruru_utf8`, `meta_description_ruru_utf8`, `lk`, `rk`, `lvl`, `app_settings`)
VALUES
	(1,'Стартовая','/','/','',4,1,1,NULL,NULL,NULL,1,10,1,NULL),
	(15,'Новости и события','news','/news','',2,1,1,NULL,NULL,NULL,4,5,2,NULL),
	(4,'Контакты','contacts','/contacts','',10,1,1,NULL,NULL,NULL,8,9,2,NULL),
	(16,'Услуги','service','/service','',1,1,1,NULL,NULL,NULL,6,7,2,NULL),
	(14,'О компании','about','/about','',1,1,1,NULL,NULL,NULL,2,3,2,NULL);

/*!40000 ALTER TABLE `madonepage` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table madonepagetype
# ------------------------------------------------------------

CREATE TABLE `madonepagetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `app_classname` varchar(100) DEFAULT NULL,
  `settings` text,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `has_text` tinyint(3) unsigned DEFAULT NULL,
  `has_meta` tinyint(3) unsigned DEFAULT NULL,
  `has_subpages` tinyint(3) unsigned DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_classname__idx` (`app_classname`),
  KEY `priority__idx` (`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

LOCK TABLES `madonepagetype` WRITE;
/*!40000 ALTER TABLE `madonepagetype` DISABLE KEYS */;

INSERT INTO `madonepagetype` (`id`, `title`, `app_classname`, `settings`, `enabled`, `position`, `has_text`, `has_meta`, `has_subpages`, `priority`)
VALUES
	(1,'Обычная страница (текст с изображениями)','TextPageApplication',NULL,1,1,1,1,0,1),
	(2,'Новости','NewsApplication',NULL,1,3,0,0,1,2),
	(3,'Карта сайта','Madone_Application',NULL,1,9,0,1,0,3),
	(4,'Главная страница','IndexPageApplication',NULL,1,11,1,1,0,1),
	(5,'Фотогалерея','GalleryApplication',NULL,1,4,0,1,1,2),
	(9,'Содержание раздела','TableofcontentsApplication',NULL,1,2,1,1,0,2),
	(10,'Обратная связь','FeedbackApplication',NULL,1,10,1,1,1,2),
	(11,'Каталог','ShowcaseApplication',NULL,1,6,0,1,1,2),
	(12,'Каталог: поиск по каталогу','ShowcaseSearchApplication',NULL,1,8,0,1,1,2),
	(13,'Подписка','SubscriptionApplication',NULL,0,12,1,1,1,2),
	(14,'Фотогалерея: раздел фотогалереи','GallerySectionApplication','{\"model\": \"MadoneGallerySection\", \"filter\": \"id\"}',1,5,0,1,1,2),
	(15,'Каталог: раздел каталога','ShowcaseSectionApplication','{\"model\": \"MadoneShowcaseSection\", \"filter\": \"id\"}',1,7,0,1,1,2),
	(16,'Поиск по сайту','SearchApplication',NULL,1,13,0,0,0,2);

/*!40000 ALTER TABLE `madonepagetype` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table madoneshowcaseimage
# ------------------------------------------------------------



# Dump of table madoneshowcaseitem
# ------------------------------------------------------------



# Dump of table madoneshowcasemovie
# ------------------------------------------------------------



# Dump of table madoneshowcasesection
# ------------------------------------------------------------



# Dump of table madonesubscriptionrecipient
# ------------------------------------------------------------



# Dump of table madonetempfile
# ------------------------------------------------------------



# Dump of table madonetempimage
# ------------------------------------------------------------



# Dump of table madonetextblock
# ------------------------------------------------------------



# Dump of table madoneuser
# ------------------------------------------------------------

CREATE TABLE `madoneuser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `setting_module` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login__password__idx` (`login`,`password`),
  KEY `setting_module__idx` (`setting_module`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

LOCK TABLES `madoneuser` WRITE;
/*!40000 ALTER TABLE `madoneuser` DISABLE KEYS */;

INSERT INTO `madoneuser` (`id`, `login`, `password`, `setting_module`)
VALUES
	(1,'admin','21232f297a57a5a743894a0e4a801fc3',NULL);

/*!40000 ALTER TABLE `madoneuser` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
