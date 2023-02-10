CREATE TABLE IF NOT EXISTS `players`
(
	`id`       int(10) unsigned NOT NULL AUTO_INCREMENT,
	`teamId`   int(10) unsigned DEFAULT NULL,
	`number`   varchar(3)       DEFAULT NULL,
	`name`     varchar(50)      DEFAULT NULL,
	`position` int(10) unsigned DEFAULT NULL,
	PRIMARY KEY (`id`),
	KEY `teamId` (`teamId`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `teams`
(
	`id`        int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name`      varchar(100) DEFAULT NULL,
	`address`   varchar(100) DEFAULT NULL,
	`startTime` time         DEFAULT NULL,
	`endTime`   time         DEFAULT NULL,
	`groupName` varchar(2)   DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;
