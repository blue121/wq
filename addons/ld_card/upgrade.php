<?php
pdo_query("
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


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket_copy` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket_copy` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_carousel` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`title` varchar(255),
`img` varchar(255),
`href` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket_copy` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_carousel` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`title` varchar(255),
`img` varchar(255),
`href` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_category` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(255) NOT NULL,
`thumb` varchar(255) NOT NULL,
`displayorder` int(3) NOT NULL DEFAULT '0',
`link` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket_copy` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_carousel` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`title` varchar(255),
`img` varchar(255),
`href` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_category` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(255) NOT NULL,
`thumb` varchar(255) NOT NULL,
`displayorder` int(3) NOT NULL DEFAULT '0',
`link` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_cardticket_copy` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`userid` int(10),
`createtime` varchar(20),
`ticket` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_carousel` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10),
`title` varchar(255),
`img` varchar(255),
`href` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `ims_ld_card_category` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`uniacid` int(10) NOT NULL,
`title` varchar(255) NOT NULL,
`thumb` varchar(255) NOT NULL,
`displayorder` int(3) NOT NULL DEFAULT '0',
`link` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


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


CREATE TABLE IF NOT EXISTS `ims_ld_card_users` (
`id` int(10) NOT NULL AUTO_INCREMENT,
`weid` int(10) NOT NULL,
`shopname` varchar(255) NOT NULL,
`username` varchar(255) NOT NULL,
`tel` varchar(20) NOT NULL,
`add` varchar(255) NOT NULL,
`openid` varchar(255) NOT NULL,
`logo` varchar(255),
`appid` varchar(255),
`appsecret` varchar(255),
`access_token` varchar(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

");
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `weid` varchar(10) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'userid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `userid` varchar(10) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'card_id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `card_id` varchar(50) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'title')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `title` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'minhb')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `minhb` varchar(10);");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'maxhb')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `maxhb` varchar(10);");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'hbnum')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `hbnum` varchar(10);");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'sendhb')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `sendhb` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'sendnum')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `sendnum` tinyint(10);");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'sign')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `sign` varchar(255) DEFAULT '';");
	}	
}
if(pdo_tableexists('ld_card_cards')) {
	if(!pdo_fieldexists('ld_card_cards',  'category')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cards')." ADD `category` int(3);");
	}	
}
if(pdo_tableexists('ld_card_cardticket')) {
	if(!pdo_fieldexists('ld_card_cardticket',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket')." ADD `id` int(10) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_cardticket')) {
	if(!pdo_fieldexists('ld_card_cardticket',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket')." ADD `weid` int(10);");
	}	
}
if(pdo_tableexists('ld_card_cardticket')) {
	if(!pdo_fieldexists('ld_card_cardticket',  'userid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket')." ADD `userid` int(10);");
	}	
}
if(pdo_tableexists('ld_card_cardticket')) {
	if(!pdo_fieldexists('ld_card_cardticket',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket')." ADD `createtime` varchar(20);");
	}	
}
if(pdo_tableexists('ld_card_cardticket')) {
	if(!pdo_fieldexists('ld_card_cardticket',  'ticket')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket')." ADD `ticket` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_cardticket_copy')) {
	if(!pdo_fieldexists('ld_card_cardticket_copy',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket_copy')." ADD `id` int(10) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_cardticket_copy')) {
	if(!pdo_fieldexists('ld_card_cardticket_copy',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket_copy')." ADD `weid` int(10);");
	}	
}
if(pdo_tableexists('ld_card_cardticket_copy')) {
	if(!pdo_fieldexists('ld_card_cardticket_copy',  'userid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket_copy')." ADD `userid` int(10);");
	}	
}
if(pdo_tableexists('ld_card_cardticket_copy')) {
	if(!pdo_fieldexists('ld_card_cardticket_copy',  'createtime')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket_copy')." ADD `createtime` varchar(20);");
	}	
}
if(pdo_tableexists('ld_card_cardticket_copy')) {
	if(!pdo_fieldexists('ld_card_cardticket_copy',  'ticket')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_cardticket_copy')." ADD `ticket` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_carousel')) {
	if(!pdo_fieldexists('ld_card_carousel',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_carousel')." ADD `id` int(10) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_carousel')) {
	if(!pdo_fieldexists('ld_card_carousel',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_carousel')." ADD `weid` int(10);");
	}	
}
if(pdo_tableexists('ld_card_carousel')) {
	if(!pdo_fieldexists('ld_card_carousel',  'title')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_carousel')." ADD `title` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_carousel')) {
	if(!pdo_fieldexists('ld_card_carousel',  'img')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_carousel')." ADD `img` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_carousel')) {
	if(!pdo_fieldexists('ld_card_carousel',  'href')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_carousel')." ADD `href` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_category')) {
	if(!pdo_fieldexists('ld_card_category',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_category')." ADD `id` int(10) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_category')) {
	if(!pdo_fieldexists('ld_card_category',  'uniacid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_category')." ADD `uniacid` int(10) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_category')) {
	if(!pdo_fieldexists('ld_card_category',  'title')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_category')." ADD `title` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_category')) {
	if(!pdo_fieldexists('ld_card_category',  'thumb')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_category')." ADD `thumb` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_category')) {
	if(!pdo_fieldexists('ld_card_category',  'displayorder')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_category')." ADD `displayorder` int(3) NOT NULL DEFAULT '0';");
	}	
}
if(pdo_tableexists('ld_card_category')) {
	if(!pdo_fieldexists('ld_card_category',  'link')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_category')." ADD `link` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `weid` varchar(10) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'userid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `userid` varchar(10) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'card_id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `card_id` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'cardcode')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `cardcode` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'card_user')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `card_user` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'friend')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `friend` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'card_consume')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `card_consume` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'isfriend')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `isfriend` tinyint(2);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'status')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `status` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'time')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `time` text;");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'sendhb')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `sendhb` int(10);");
	}	
}
if(pdo_tableexists('ld_card_log')) {
	if(!pdo_fieldexists('ld_card_log',  'hbopenid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_log')." ADD `hbopenid` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'id')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `id` int(10) NOT NULL AUTO_INCREMENT;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'weid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `weid` int(10) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'shopname')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `shopname` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'username')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `username` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'tel')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `tel` varchar(20) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'add')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `add` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'openid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `openid` varchar(255) NOT NULL;");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'logo')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `logo` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'appid')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `appid` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'appsecret')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `appsecret` varchar(255);");
	}	
}
if(pdo_tableexists('ld_card_users')) {
	if(!pdo_fieldexists('ld_card_users',  'access_token')) {
		pdo_query("ALTER TABLE ".tablename('ld_card_users')." ADD `access_token` varchar(255);");
	}	
}
