/*ACCOUNTS TABLE*/
CREATE TABLE `{{TABLE_PREFIX}}_coders_account` (
 `ID` int(11) NOT NULL,
 `token` varchar(32) NOT NULL,
 `name` varchar(16) NOT NULL,
 `alias` varchar(32) NOT NULL,
 `status` tinyint(4) NOT NULL,
 `email_address` varchar(128) NOT NULL,
 `address_city` varchar(255) NOT NULL,
 `address_state` varchar(128) NOT NULL,
 `address_zip` varchar(16) NOT NULL,
 `address_country` varchar(64) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

/*PAYMENTS TABLE*/
CREATE TABLE `{{TABLE_PREFIX}}_coders_checkout` (
 `ID` int(11) NOT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8

/*SUBSCRIPTIONS TABLE*/
CREATE TABLE `{{TABLE_PREFIX}}_coders_subscription` (
 `ID` int(11) NOT NULL,
 `account_id` int(11) NOT NULL,
 `tier_id` varchar(32) NOT NULL,
 `satus` tinyint(1) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 `date_terminated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

/*PROJECTS TABLE*/
CREATE TABLE `{{TABLE_PREFIX}}_coders_project` (
 `ID` varchar(12) NOT NULL,
 `title` varchar(64) NOT NULL,
 `content` longtext NOT NULL,
 `status` tinyint(1) NOT NULL,
 `image_id` bigint(20) NOT NULL COMMENT 'wordpress gallery',
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8

/*TIERS TABLE*/
CREATE TABLE `{{TABLE_PREFIX}}_coders_tier` (
 `ID` varchar(32) NOT NULL COMMENT 'project_id + tier_id',
 `title` varchar(32) NOT NULL,
 `description` longtext NOT NULL,
 `image_id` bigint(20) NOT NULL,
 `status` tinyint(1) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8