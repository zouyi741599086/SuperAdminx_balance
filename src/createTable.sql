CREATE TABLE `sa_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '余额',
  `integral` int(11) DEFAULT '0' COMMENT '积分',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fk_sa_balance_sa_user1_idx` (`user_id`) USING BTREE,
  CONSTRAINT `fk_sa_balance_sa_user1` FOREIGN KEY (`user_id`) REFERENCES `sa_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户的余额';



CREATE TABLE `sa_balance_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `balance_type` varchar(45) DEFAULT NULL COMMENT '提现的余额类型，就是对应balance表的字段，代表提现的哪个字段',
  `orderno` varchar(45) DEFAULT NULL COMMENT '订单号',
  `status` tinyint(1) DEFAULT '2' COMMENT '状态，2》审核中，4》审核通过，6》审核拒绝，8》已打款，10》打款失败',
  `money` decimal(10,2) DEFAULT '0.00' COMMENT '提现金额',
  `shouxufei` decimal(10,2) DEFAULT '0.00' COMMENT '手续费',
  `on_money` decimal(10,2) DEFAULT '0.00' COMMENT '到账金额',
  `bank_name` varchar(45) DEFAULT NULL COMMENT '提现-开户姓名',
  `bank_title` varchar(45) DEFAULT NULL COMMENT '提现-所属银行',
  `bank_number` varchar(45) DEFAULT NULL COMMENT '提现-银行卡号',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `audit_time` datetime DEFAULT NULL COMMENT '审核时间',
  `pay_time` datetime DEFAULT NULL COMMENT '打款时间',
  `reason` varchar(200) DEFAULT NULL COMMENT '失败原因',
  PRIMARY KEY (`id`),
  KEY `fk_sa_balance_withdraw_sa_user1_idx` (`user_id`),
  CONSTRAINT `fk_sa_balance_withdraw_sa_user1` FOREIGN KEY (`user_id`) REFERENCES `sa_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户的余额-提现';

CREATE TABLE `sa_balance_details_integral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `details_type` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL COMMENT '标题',
  `change_value` decimal(10,2) DEFAULT '0.00' COMMENT '本次变化的值',
  `change_balance` decimal(10,2) DEFAULT '0.00' COMMENT '变化后的余额',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sa_balance_details_sa_user1_idx` (`user_id`),
  CONSTRAINT `sa_balance_details_integral_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sa_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户的余额明细';

CREATE TABLE `sa_balance_details_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '所属用户',
  `details_type` varchar(50) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL COMMENT '标题',
  `change_value` decimal(10,2) DEFAULT '0.00' COMMENT '本次变化的值',
  `change_balance` decimal(10,2) DEFAULT '0.00' COMMENT '变化后的余额',
  `create_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sa_balance_details_sa_user1_idx` (`user_id`),
  CONSTRAINT `sa_balance_details_money_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sa_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户的余额明细';




