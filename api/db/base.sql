DELIMITER ;;

DROP FUNCTION IF EXISTS `hotscore`;;
CREATE FUNCTION `hotscore`(`creation` timestamp, `votes` int) RETURNS float
BEGIN
  RETURN ROUND(votes - (unix_timestamp(now()) - unix_timestamp(creation)) / 300, 3);
END;;

DROP FUNCTION IF EXISTS `uuid_reddit`;;
CREATE FUNCTION `uuid_reddit`(`uuid` binary(36)) RETURNS binary(16)
    DETERMINISTIC
RETURN UNHEX(CONCAT(SUBSTR(uuid, 15, 4),SUBSTR(uuid, 10, 4),SUBSTR(uuid, 1, 8),SUBSTR(uuid, 20, 4),SUBSTR(uuid, 25)));;

DELIMITER ;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` binary(16) NOT NULL,
  `uid` binary(16) NOT NULL,
  `votes` int(11) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `title` varchar(512) NOT NULL,
  `url` varchar(1024) NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `creation` (`creation`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE
) ENGINE=InnoDB;


DELIMITER ;;

CREATE TRIGGER `after_insert_post` AFTER INSERT ON `posts` FOR EACH ROW
BEGIN 
    INSERT INTO votes SET id = NEW.id, uid = NEW.uid; 
END;;

DELIMITER ;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` binary(16) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(128) NOT NULL,
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB;


DELIMITER ;;
CREATE TRIGGER `users_insert_uuid` BEFORE INSERT ON `users` FOR EACH ROW
BEGIN 
    SET NEW.uid = uuid_reddit(UUID()); 
END;;
DELIMITER ;

DROP TABLE IF EXISTS `votes`;
CREATE TABLE `votes` (
  `id` binary(16) NOT NULL,
  `uid` binary(16) NOT NULL,
  `vote` tinyint(1) NOT NULL DEFAULT '1',
  `creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`uid`),
  KEY `id` (`id`),
  KEY `uid` (`uid`),
  CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB;


DROP VIEW IF EXISTS `vv_posts`;
CREATE VIEW `vv_posts` AS select `posts`.`id` AS `id`,`posts`.`uid` AS `uid`,sum(ifnull(`votes`.`vote`,0)) AS `votes`,`users`.`username` AS `username`,`posts`.`title` AS `title`,`posts`.`url` AS `url`,`posts`.`creation` AS `creation`,round(`hotscore`(`posts`.`creation`,sum(ifnull(`votes`.`vote`,0))),3) AS `score` from ((`posts` left join `votes` on((`posts`.`id` = `votes`.`id`))) left join `users` on((`posts`.`uid` = `users`.`uid`))) where (`posts`.`status` = 1) group by `posts`.`id` order by round(`hotscore`(`posts`.`creation`,sum(ifnull(`votes`.`vote`,0))),3) desc;

