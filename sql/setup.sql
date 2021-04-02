/*ACCOUNTS TABLE*/
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_account` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `token` varchar(32) NOT NULL,
 `name` varchar(16) NOT NULL DEFAULT '',
 /*`alias` varchar(32) NOT NULL,*/
 `status` tinyint(1) NOT NULL,
 `email_address` varchar(128) NOT NULL,
 `address_city` varchar(255) NOT NULL,
 `address_state` varchar(128) NOT NULL,
 `address_zip` varchar(16) NOT NULL,
 `address_country` varchar(64) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*PAYMENTS TABLE*/
/*CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_checkout` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `account_id` int(11) NOT NULL,
 `subscription_id` int(11) NOT NULL,
 `token` varchar(32) NOT NULL,
 `amount` int(11) NOT NULL,
 `status` tinyint(1) NOT NULL,
 `first_name` varchar(64) NOT NULL,
 `last_name` varchar(128) NOT NULL,
 `address_city` varchar(255) NOT NULL,
 `address_state` varchar(128) NOT NULL,
 `address_zip` varchar(16) NOT NULL,
 `address_country` varchar(64) NOT NULL,
 `email_address` varchar(128) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;*/

/*SUBSCRIPTIONS TABLE*/
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_subscription` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `account_id` int(11) NOT NULL,
 `tier_id` varchar(24) NOT NULL,
 `status` tinyint(1) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 `date_terminated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*PROJECTS TABLE*/
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_project` (
 `ID` varchar(12) NOT NULL,
 `title` varchar(64) NOT NULL,
 `content` longtext,
 `access_level` VARCHAR(12) NOT NULL DEFAULT 'private',
 `status` tinyint(1) DEFAULT '0',
 `image_id` bigint(20) DEFAULT '0' COMMENT 'wordpress gallery',
 `connect_patreon` tinyint(1) DEFAULT '0',
 `connect_wc` tinyint(1) DEFAULT '0',
 /*`collection_id` int(11) DEFAULT '0' COMMENT 'repository collection (deprecated, now handled by collections in 1-n rel)',*/
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `{{TABLE_PREFIX}}_post` (
 `ID` int(11) NOT NULL AUTO_INCREMENT,
 `public_id` varchar(32) NOT NULL,
 `parent_id` int(11) NOT NULL DEFAULT '0',
 `name` varchar(32) NOT NULL,
 `type` varchar(12) NOT NULL,
 /*`collection` varchar(24) NOT NULL,*/
 `title` varchar(64) NOT NULL,
 `content` longtext NOT NULL,
 `tier_id` varchar(12) NOT NULL DEFAULT '' COMMENT 'combination of project + tier-id',
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8

/*TIERS TABLE*/
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_tier` (
 `project_id` varchar(12) NOT NULL COMMENT 'project_id',
 `tier_id` varchar(12) NOT NULL COMMENT 'project_id + tier_id',
 `title` varchar(16) NOT NULL,
 `level` tinyint(1) NOT NULL DEFAULT '1',
 `description` longtext NOT NULL,
 `image_id` bigint(20) NOT NULL DEFAULT '0',
 `status` tinyint(1) NOT NULL DEFAULT '0',
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`project_id`,`tier_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_token` (
 `ID` varchar(32) NOT NULL,
 `type` varchar(16) NOT NULL,
 `target` int(11) NOT NULL,
 `status` tinyint(1) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_expired` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*LOGS TABLE*/
CREATE TABLE IF NOT EXISTS `{{TABLE_PREFIX}}_logs` (
 `timestamp` datetime NOT NULL,
 `source` varchar(32) NOT NULL,
 `message` varchar(128) NOT NULL,
 `type` tinyint(1) NOT NULL,
 `status` tinyint(1) NOT NULL DEFAULT 0,
 `account_id` varchar(32) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
