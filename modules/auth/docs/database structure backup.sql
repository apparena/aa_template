-- phpMyAdmin SQL Dump
-- version 3.3.10
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 13. Mai 2013 um 15:44
-- Server Version: 5.1.66
-- PHP-Version: 5.3.2-1ubuntu4.18

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `apps_aa_template`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_data`
--

CREATE TABLE IF NOT EXISTS `user_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'internal user id',
  `aa_inst_id` int(11) NOT NULL COMMENT 'the app arena instance id',
  `email` varchar(128) DEFAULT NULL COMMENT 'the users email address (not fb, g+, twitter)',
  `fb_id` bigint(20) DEFAULT NULL COMMENT 'fk to the fb table',
  `gplus_id` char(30) DEFAULT NULL COMMENT 'fk to the gplus table',
  `twitter_id` varchar(20) DEFAULT NULL COMMENT 'fk to the twitter table',
  `ip` varchar(15) DEFAULT NULL COMMENT 'the users ip address',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'action timestamp',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `fb_id` (`fb_id`),
  KEY `gplus_id` (`gplus_id`),
  KEY `twitter_id` (`twitter_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='connects all identities (email, fb, g+, twitter)' AUTO_INCREMENT=10 ;

--
-- Daten für Tabelle `user_data`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_data_email`
--

CREATE TABLE IF NOT EXISTS `user_data_email` (
  `email` varchar(128) NOT NULL COMMENT 'the users email address (not fb, g+, twitter)',
  `password` varchar(64) NOT NULL COMMENT 'the users encrypted password',
  `display_name` varchar(64) NOT NULL COMMENT 'the users username',
  `gender` varchar(6) NOT NULL COMMENT 'the users gender',
  PRIMARY KEY (`email`),
  KEY `display_name` (`display_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users credentials disregarding fb, g+, twitter';

--
-- Daten für Tabelle `user_data_email`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_data_fb`
--

CREATE TABLE IF NOT EXISTS `user_data_fb` (
  `fb_id` bigint(19) NOT NULL COMMENT 'the users facebook id',
  `email` varchar(128) NOT NULL COMMENT 'the users facebook email address',
  `display_name` varchar(64) NOT NULL COMMENT 'the users facebook name',
  `profile_image_url` varchar(128) NOT NULL COMMENT 'the users facebook image url',
  `gender` varchar(6) NOT NULL COMMENT 'the users facebook gender',
  `data` blob NOT NULL COMMENT 'any additional data from facebook',
  PRIMARY KEY (`fb_id`),
  KEY `display_name` (`display_name`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users facebook data';

--
-- Daten für Tabelle `user_data_fb`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_data_gplus`
--

CREATE TABLE IF NOT EXISTS `user_data_gplus` (
  `gplus_id` varchar(30) NOT NULL COMMENT 'the users google plus id',
  `email` varchar(128) NOT NULL COMMENT 'the users google plus email address',
  `display_name` varchar(64) NOT NULL COMMENT 'the users google plus displayName',
  `profile_image_url` varchar(128) NOT NULL COMMENT 'the users google plus image url',
  `gender` varchar(6) NOT NULL COMMENT 'the users google plus gender',
  `data` blob NOT NULL COMMENT 'any additional data from google plus',
  PRIMARY KEY (`gplus_id`),
  KEY `email` (`email`),
  KEY `display_name` (`display_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users google plus data';

--
-- Daten für Tabelle `user_data_gplus`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_data_twitter`
--

CREATE TABLE IF NOT EXISTS `user_data_twitter` (
  `twitter_id` varchar(20) NOT NULL COMMENT 'the users twitter id',
  `email` varchar(128) NOT NULL COMMENT 'the users twitter email address',
  `display_name` varchar(64) NOT NULL COMMENT 'the users twitter screenName',
  `profile_image_url` varchar(128) NOT NULL COMMENT 'the users twitter image url',
  `data` blob NOT NULL COMMENT 'any additional data from the users twitter account',
  PRIMARY KEY (`twitter_id`),
  KEY `email` (`email`),
  KEY `display_name` (`display_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users twitter data';

--
-- Daten für Tabelle `user_data_twitter`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `user_log`
--

CREATE TABLE IF NOT EXISTS `user_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'internal log id',
  `aa_inst_id` int(11) NOT NULL COMMENT 'app arena instance id',
  `user_id` int(11) NOT NULL COMMENT 'fk to user_data.id',
  `data` text COMMENT 'data concerning the log action',
  `action` varchar(32) DEFAULT NULL COMMENT 'short description, e.g. what the user was doing',
  `ip` varchar(15) DEFAULT NULL COMMENT 'the ip address on which the action was logged',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'the timestamp for this log entry',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='action log of what was going on' AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `user_log`
--


--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `user_data`
--
ALTER TABLE `user_data`
  ADD CONSTRAINT `user_data_ibfk_4` FOREIGN KEY (`fb_id`) REFERENCES `user_data_fb` (`fb_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `user_data_ibfk_1` FOREIGN KEY (`gplus_id`) REFERENCES `user_data_gplus` (`gplus_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `user_data_ibfk_2` FOREIGN KEY (`twitter_id`) REFERENCES `user_data_twitter` (`twitter_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `user_data_ibfk_3` FOREIGN KEY (`email`) REFERENCES `user_data_email` (`email`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints der Tabelle `user_log`
--
ALTER TABLE `user_log`
  ADD CONSTRAINT `user_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
