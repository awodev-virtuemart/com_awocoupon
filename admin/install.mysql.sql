CREATE TABLE IF NOT EXISTS #__awocoupon_vm (
	`id` int(16) NOT NULL auto_increment,
	`coupon_code` varchar(32) NOT NULL default '',
	`num_of_uses` INT NOT NULL DEFAULT 0,
	`coupon_value_type` enum('percent','total') NOT NULL default 'percent',
	`coupon_value` decimal(12,2) NOT NULL default '0.00',
	`min_value` decimal(12,2),
	`discount_type` enum('specific','overall') NOT NULL DEFAULT 'overall',
	`function_type` enum('coupon','giftcert') NOT NULL DEFAULT 'coupon',
	`function_type2` enum('product','category'),
	`startdate` DATE,
	`expiration` DATE,
	`published` TINYINT NOT NULL DEFAULT 1,
	PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS #__awocoupon_vm_product (
	`id` int(16) NOT NULL auto_increment,
	`coupon_id` varchar(32) NOT NULL default '',
	`product_id` INT NOT NULL,
	PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS #__awocoupon_vm_category (
	`id` int(16) NOT NULL auto_increment,
	`coupon_id` varchar(32) NOT NULL default '',
	`category_id` INT NOT NULL,
	PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS #__awocoupon_vm_user (
	`id` int(16) NOT NULL auto_increment,
	`coupon_id` varchar(32) NOT NULL default '',
	`user_id` INT NOT NULL,
	PRIMARY KEY  (`id`)
);

CREATE TABLE IF NOT EXISTS #__awocoupon_vm_history (
	`coupon_id` varchar(32) NOT NULL default '',
	`user_id` INT NOT NULL,
	`num` INT NOT NULL DEFAULT 0,
	PRIMARY KEY  (`coupon_id`,`user_id`)
);

