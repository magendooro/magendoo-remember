<?php


$this->startSetup();

$sql=<<<SQLTEXT
CREATE TABLE IF NOT EXISTS `{$this->getTable('remember/remember')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(10) unsigned NOT NULL,
  `token` char(40) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `nonce` char(40) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `lastip` varchar(255) NOT NULL,
  `useragent` varchar(255) NOT NULL,
  PRIMARY KEY (`entity_id`),
  UNIQUE KEY `token` (`customer_id`,`token`,`nonce`),
  CONSTRAINT `MAGENDOO_REMEMBER_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SQLTEXT;

$this->run($sql);

$this->endSetup();
