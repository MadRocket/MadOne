-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 19, 2011 at 12:02 AM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `madone_dev`
--

-- --------------------------------------------------------

--
-- Table structure for table `madonefeedbackmessage`
--

CREATE TABLE IF NOT EXISTS `madonefeedbackmessage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `text` longtext,
  `answermd5` varchar(32) DEFAULT NULL,
  `answer` longtext,
  `date` datetime DEFAULT NULL,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date__idx` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonefeedbackmessage`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonegalleryimage`
--

CREATE TABLE IF NOT EXISTS `madonegalleryimage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section` int(10) unsigned DEFAULT NULL,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `image` text,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section__idx` (`section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonegalleryimage`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonegallerysection`
--

CREATE TABLE IF NOT EXISTS `madonegallerysection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `uri` varchar(600) DEFAULT NULL,
  `text_ruru_utf8` longtext,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  `lk` int(11) NOT NULL,
  `rk` int(11) NOT NULL,
  `lvl` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name__idx` (`name`),
  KEY `uri__idx` (`uri`(333)),
  KEY `enabled__idx` (`enabled`),
  KEY `lk__rk__lvl__idx` (`lk`,`rk`,`lvl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonegallerysection`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonemodule`
--

CREATE TABLE IF NOT EXISTS `madonemodule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `classname` varchar(100) DEFAULT NULL,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name__idx` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonemodule`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonenews`
--

CREATE TABLE IF NOT EXISTS `madonenews` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `text_ruru_utf8` longtext,
  `announce_ruru_utf8` longtext,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date__idx` (`date`),
  KEY `enabled__idx` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonenews`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonepage`
--

CREATE TABLE IF NOT EXISTS `madonepage` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `madonepage`
--

INSERT INTO `madonepage` (`id`, `title_ruru_utf8`, `name`, `uri`, `text_ruru_utf8`, `type`, `menu`, `enabled_ruru_utf8`, `meta_title_ruru_utf8`, `meta_keywords_ruru_utf8`, `meta_description_ruru_utf8`, `lk`, `rk`, `lvl`, `app_settings`) VALUES
(1, 'Главная страница', '/', '/', '', 4, 1, 1, NULL, NULL, NULL, 1, 18, 1, NULL),
(12, 'Новая страница', 'novaya_stranica', '/gallery/novaya_stranica', '', 1, 1, 1, NULL, NULL, NULL, 9, 10, 3, NULL),
(8, 'Обратная связь', 'feedback', '/feedback', '', 10, 1, 1, NULL, NULL, NULL, 12, 15, 2, NULL),
(5, 'Новости', 'news', '/news', '', 2, 1, 1, NULL, NULL, NULL, 16, 17, 2, NULL),
(4, 'Контакты', 'contacts', '/feedback/contacts', '', 10, 1, 1, NULL, NULL, NULL, 13, 14, 3, NULL),
(9, 'Фотогалерея', 'gallery', '/gallery', '', 5, 1, 1, NULL, NULL, NULL, 6, 11, 2, NULL),
(10, 'Раздел каталога', 'catalog_section', '/catalog_section', '', 15, 1, 1, NULL, NULL, NULL, 4, 5, 2, '2'),
(11, 'Поиск по сайту', 'search', '/search', '', 16, 1, 1, NULL, NULL, NULL, 2, 3, 2, NULL),
(13, 'Старница', 'page_name', '/gallery/page_name', '', 1, 1, 1, '', '', '', 7, 8, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `madonepagetype`
--

CREATE TABLE IF NOT EXISTS `madonepagetype` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `madonepagetype`
--

INSERT INTO `madonepagetype` (`id`, `title`, `app_classname`, `settings`, `enabled`, `position`, `has_text`, `has_meta`, `has_subpages`, `priority`) VALUES
(1, 'Обычная страница (текст с изображениями)', 'TextPageApplication', NULL, 1, 1, 1, 1, 0, 1),
(2, 'Новости', 'NewsApplication', NULL, 1, 3, 0, 0, 1, 2),
(3, 'Карта сайта', 'AbstractApplication', NULL, 1, 9, 0, 1, 0, 3),
(4, 'Главная страница', 'IndexPageApplication', NULL, 1, 11, 1, 1, 0, 1),
(5, 'Фотогалерея', 'GalleryApplication', NULL, 1, 4, 0, 1, 1, 2),
(9, 'Содержание раздела', 'TableofcontentsApplication', NULL, 1, 2, 1, 1, 0, 2),
(10, 'Обратная связь', 'FeedbackApplication', NULL, 1, 10, 1, 1, 1, 2),
(11, 'Каталог', 'ShowcaseApplication', NULL, 1, 6, 0, 1, 1, 2),
(12, 'Каталог: поиск по каталогу', 'ShowcaseSearchApplication', NULL, 1, 8, 0, 1, 1, 2),
(13, 'Подписка', 'SubscriptionApplication', NULL, 0, 12, 1, 1, 1, 2),
(14, 'Фотогалерея: раздел фотогалереи', 'GallerySectionApplication', '{"model": "MadoneGallerySection", "filter": "id"}', 1, 5, 0, 1, 1, 2),
(15, 'Каталог: раздел каталога', 'ShowcaseSectionApplication', '{"model": "MadoneShowcaseSection", "filter": "id"}', 1, 7, 0, 1, 1, 2),
(16, 'Поиск по сайту', 'SearchApplication', NULL, 1, 13, 0, 0, 0, 2);

-- --------------------------------------------------------

--
-- Table structure for table `madoneshowcaseimage`
--

CREATE TABLE IF NOT EXISTS `madoneshowcaseimage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section` int(10) unsigned DEFAULT NULL,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `image` text,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section__idx` (`section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madoneshowcaseimage`
--


-- --------------------------------------------------------

--
-- Table structure for table `madoneshowcaseitem`
--

CREATE TABLE IF NOT EXISTS `madoneshowcaseitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `title_fts_ruru_utf8` varchar(255) DEFAULT NULL,
  `section` int(10) unsigned DEFAULT NULL,
  `description_ruru_utf8` longtext,
  `description_fts_ruru_utf8` longtext,
  `short_description_ruru_utf8` longtext,
  `price` float DEFAULT NULL,
  `in_stock` int(11) DEFAULT NULL,
  `enabled_ruru_utf8` tinyint(3) unsigned DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `linked_items` longtext,
  `views_counter` int(11) DEFAULT NULL,
  `added_to_cart_counter` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section__idx` (`section`),
  KEY `price__idx` (`price`),
  KEY `enabled_ruru_utf8__idx` (`enabled_ruru_utf8`),
  KEY `date_added__idx` (`date_added`),
  KEY `date_modified__idx` (`date_modified`),
  FULLTEXT KEY `title_ruru_utf8__ftidx` (`title_ruru_utf8`),
  FULLTEXT KEY `title_ruru_utf8__title_fts_ruru_utf8__ftidx` (`title_ruru_utf8`,`title_fts_ruru_utf8`),
  FULLTEXT KEY `description_ruru_utf8__ftidx` (`description_ruru_utf8`),
  FULLTEXT KEY `85bf7803dc18230bec84a022e64eda30` (`description_ruru_utf8`,`description_fts_ruru_utf8`),
  FULLTEXT KEY `title_ruru_utf8__description_ruru_utf8__ftidx` (`title_ruru_utf8`,`description_ruru_utf8`),
  FULLTEXT KEY `5129b7cf82c915523d00588a491e654f` (`title_ruru_utf8`,`title_fts_ruru_utf8`,`description_ruru_utf8`,`description_fts_ruru_utf8`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madoneshowcaseitem`
--


-- --------------------------------------------------------

--
-- Table structure for table `madoneshowcasemovie`
--

CREATE TABLE IF NOT EXISTS `madoneshowcasemovie` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `section` int(10) unsigned DEFAULT NULL,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `movie` text,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `section__idx` (`section`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madoneshowcasemovie`
--


-- --------------------------------------------------------

--
-- Table structure for table `madoneshowcasesection`
--

CREATE TABLE IF NOT EXISTS `madoneshowcasesection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title_ruru_utf8` varchar(255) DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL,
  `uri` varchar(900) DEFAULT NULL,
  `text_ruru_utf8` longtext,
  `enabled_ruru_utf8` tinyint(3) unsigned DEFAULT NULL,
  `lk` int(11) NOT NULL,
  `rk` int(11) NOT NULL,
  `lvl` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name__idx` (`name`),
  KEY `uri__idx` (`uri`(333)),
  KEY `enabled_ruru_utf8__idx` (`enabled_ruru_utf8`),
  KEY `lk__rk__lvl__idx` (`lk`,`rk`,`lvl`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madoneshowcasesection`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonesubscriptionrecipient`
--

CREATE TABLE IF NOT EXISTS `madonesubscriptionrecipient` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date__idx` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonesubscriptionrecipient`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonetempfile`
--

CREATE TABLE IF NOT EXISTS `madonetempfile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date__idx` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonetempfile`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonetempimage`
--

CREATE TABLE IF NOT EXISTS `madonetempimage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` text,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date__idx` (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonetempimage`
--


-- --------------------------------------------------------

--
-- Table structure for table `madonetextblock`
--

CREATE TABLE IF NOT EXISTS `madonetextblock` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `text_ruru_utf8` longtext,
  `preview_ruru_utf8` varchar(100) DEFAULT NULL,
  `enabled` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name__idx` (`name`),
  KEY `enabled__idx` (`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `madonetextblock`
--


-- --------------------------------------------------------

--
-- Table structure for table `madoneuser`
--

CREATE TABLE IF NOT EXISTS `madoneuser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `setting_module` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login__password__idx` (`login`,`password`),
  KEY `setting_module__idx` (`setting_module`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `madoneuser`
--

INSERT INTO `madoneuser` (`id`, `login`, `password`, `setting_module`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', NULL);
