<?php 
	
	/**
	 * This file checks the database tables
	 * for the login module.
	 * If one of them is not present yet
	 * it will be created.
	 * 
	 * NOTE:
	 * If one of them has an incorrect structure
	 * there will be errors later when persisting data!
	 */
	
	$query =
		"CREATE TABLE IF NOT EXISTS `user_data` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'internal user id',
		  `aa_inst_id` int(11) NOT NULL COMMENT 'the app arena instance id',
		  `email` varchar(128) DEFAULT NULL COMMENT 'the users email address (not fb, g+, twitter)',
		  `fb_id` bigint(20) DEFAULT NULL COMMENT 'fk to the fb table',
		  `gplus_id` char(30) DEFAULT NULL COMMENT 'fk to the gplus table',
		  `twitter_id` varchar(20) DEFAULT NULL COMMENT 'fk to the twitter table',
		  `ip` varchar(15) DEFAULT NULL COMMENT 'the users ip address',
		  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'action timestamp',
		  PRIMARY KEY (`id`),
		  FOREIGN KEY (`fb_id`) REFERENCES `user_data_fb` (`fb_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
		  FOREIGN KEY (`gplus_id`) REFERENCES `user_data_gplus` (`gplus_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
		  FOREIGN KEY (`twitter_id`) REFERENCES `user_data_twitter` (`twitter_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
		  FOREIGN KEY (`email`) REFERENCES `user_data_email` (`email`) ON DELETE NO ACTION ON UPDATE CASCADE
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='connects all identities (email, fb, g+, twitter)' AUTO_INCREMENT=1;";
	
	mysql_query( $query );
	
	$query =
		"CREATE TABLE IF NOT EXISTS `user_data_email` (
		  `email` varchar(128) NOT NULL COMMENT 'the users email address (not fb, g+, twitter)',
		  `password` varchar(64) NOT NULL COMMENT 'the users encrypted password',
		  `display_name` varchar(64) NOT NULL COMMENT 'the users username',
		  `gender` varchar(6) NOT NULL COMMENT 'the users gender',
		  PRIMARY KEY (`email`),
		  KEY `display_name` (`display_name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users credentials disregarding fb, g+, twitter';";
	
	mysql_query( $query );
	
	$query =
		"CREATE TABLE IF NOT EXISTS `user_data_fb` (
		  `fb_id` bigint(19) NOT NULL COMMENT 'the users facebook id',
		  `email` varchar(128) NOT NULL COMMENT 'the users facebook email address',
		  `display_name` varchar(64) NOT NULL COMMENT 'the users facebook name',
		  `profile_image_url` varchar(128) NOT NULL COMMENT 'the users facebook image url',
		  `gender` varchar(6) NOT NULL COMMENT 'the users facebook gender',
		  `data` text NOT NULL COMMENT 'any additional data from facebook',
		  PRIMARY KEY (`fb_id`),
		  KEY `display_name` (`display_name`),
		  KEY `email` (`email`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users facebook data';";
	
	mysql_query( $query );
	
	$query =
		"CREATE TABLE IF NOT EXISTS `user_data_gplus` (
		  `gplus_id` varchar(30) NOT NULL COMMENT 'the users google plus id',
		  `email` varchar(128) NOT NULL COMMENT 'the users google plus email address',
		  `display_name` varchar(64) NOT NULL COMMENT 'the users google plus displayName',
		  `profile_image_url` varchar(128) NOT NULL COMMENT 'the users google plus image url',
		  `gender` varchar(6) NOT NULL COMMENT 'the users google plus gender',
		  `data` text NOT NULL COMMENT 'any additional data from google plus',
		  PRIMARY KEY (`gplus_id`),
		  KEY `email` (`email`),
		  KEY `display_name` (`display_name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users google plus data';";
	
	mysql_query( $query );
	
	$query =
		"CREATE TABLE IF NOT EXISTS `user_data_twitter` (
		  `twitter_id` varchar(20) NOT NULL COMMENT 'the users twitter id',
		  `display_name` varchar(64) NOT NULL COMMENT 'the users twitter screenName',
		  `profile_image_url` varchar(128) NOT NULL COMMENT 'the users twitter image url',
		  `data` text NOT NULL COMMENT 'any additional data from the users twitter account',
		  PRIMARY KEY (`twitter_id`),
		  KEY `email` (`email`),
		  KEY `display_name` (`display_name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='the users twitter data';";
	
	mysql_query( $query );
	
	$query =
		"CREATE TABLE IF NOT EXISTS `user_log` (
		  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'internal log id',
		  `aa_inst_id` int(11) NOT NULL COMMENT 'app arena instance id',
		  `user_id` int(11) NOT NULL COMMENT 'fk to user_data.id',
		  `data` text COMMENT 'data concerning the log action',
		  `action` varchar(32) DEFAULT NULL COMMENT 'short description, e.g. what the user was doing',
		  `ip` varchar(15) DEFAULT NULL COMMENT 'the ip address on which the action was logged',
		  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'the timestamp for this log entry',
		  PRIMARY KEY (`id`),
		  FOREIGN KEY (`user_id`) REFERENCES `user_data` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='action log of what was going on' AUTO_INCREMENT=1;";
	
	mysql_query( $query );
	
?>