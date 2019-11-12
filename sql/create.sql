CREATE TABLE IF NOT EXISTS `ap_coders_repository` (
 `ID` bigint(20) NOT NULL AUTO_INCREMENT,
 `public_id` varchar(32) NOT NULL,
 `name` varchar(32) NOT NULL,
 `type` varchar(12) NOT NULL,
 `storage` varchar(24) NOT NULL,
 `date_created` datetime NOT NULL,
 `date_updated` datetime NOT NULL,
 PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8