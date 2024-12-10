CREATE TABLE `sa_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '余额',
  `integral` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '积分',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sa_balance_sa_user1_idx` (`user_id`),
  CONSTRAINT `fk_sa_balance_sa_user1` FOREIGN KEY (`user_id`) REFERENCES `sa_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='用户的余额';

CREATE TABLE `sa_balance_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `balance_type` varchar(45) DEFAULT NULL COMMENT '变化的余额类型，对应balance表的字段',
  `title` varchar(200) DEFAULT NULL COMMENT '标题',
  `type` tinyint(1) DEFAULT '1' COMMENT '变化的类型，1》增加，2》减少',
  `change_value` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '本次变化的值',
  `change_balance` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '变化后的余额',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sa_balance_details_sa_user1_idx` (`user_id`),
  CONSTRAINT `fk_sa_balance_details_sa_user1` FOREIGN KEY (`user_id`) REFERENCES `sa_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='用户的余额明细';