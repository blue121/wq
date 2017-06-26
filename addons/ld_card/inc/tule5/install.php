<?php
pdo_query("DROP TABLE IF EXISTS `ims_ld_card_cards`;
CREATE TABLE IF NOT EXISTS `ims_ld_card_cards` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`weid` varchar(10) NOT NULL,
`userid` varchar(10) NOT NULL,
`card_id` varchar(50) NOT NULL,
`title` varchar(255) NOT NULL,
`minhb` varchar(10),
`maxhb` varchar(10),
`hbnum` varchar(10),
`sendhb` varchar(255),
`sendnum` tinyint(10),
`sign` varchar(255) DEFAULT '',
`category` int(3),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ld_card_cardticket`;
CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ld_card_carousel`;
CREATE TABLE IF NOT EXISTS `ims_ld_card_carousel` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`title` varchar(255),
`img` varchar(255),
`href` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ld_card_category`;
CREATE TABLE IF NOT EXISTS `ims_ld_card_category` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(255) NOT NULL,
`thumb` varchar(255) NOT NULL,
`displayorder` int(3) NOT NULL DEFAULT '0',
`link` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ld_card_log`;
CREATE TABLE IF NOT EXISTS `ims_ld_card_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`weid` varchar(10) NOT NULL,
`userid` varchar(10) NOT NULL,
`card_id` varchar(255) NOT NULL,
`cardcode` varchar(255) NOT NULL,
`card_user` varchar(255),
`friend` varchar(255),
`card_consume` varchar(255),
`isfriend` tinyint(2),
`status` varchar(255),
`time` text,
`sendhb` int(10),
`hbopenid` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ims_ld_card_users`;
CREATE TABLE IF NOT EXISTS `ims_ld_card_users` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10) NOT NULL,
`shopname` varchar(255) NOT NULL,
`username` varchar(255) NOT NULL,
`tel` varchar(20) NOT NULL,
`add` varchar(255) NOT NULL,
`openid` varchar(255) NOT NULL,
`logo` varchar(255),
`lng` varchar(255),
`lat` varchar(255),
`yyzz` varchar(255),
`status` tinyint(4) DEFAULT '1',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");
