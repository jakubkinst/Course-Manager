-- Adminer 3.1.0 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `anwser`;
CREATE TABLE `anwser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `User_id` int(11) NOT NULL,
  `Question_id` int(11) NOT NULL,
  `anwser` mediumtext,
  PRIMARY KEY (`id`),
  KEY `User_id` (`User_id`),
  KEY `Question_id` (`Question_id`),
  CONSTRAINT `anwser_ibfk_3` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `anwser_ibfk_4` FOREIGN KEY (`Question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `anwserfile`;
CREATE TABLE `anwserfile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `assignment`;
CREATE TABLE `assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` mediumtext,
  `created` datetime DEFAULT NULL,
  `assigndate` datetime DEFAULT NULL,
  `duedate` datetime DEFAULT NULL,
  `maxpoints` int(11) DEFAULT NULL,
  `timelimit` int(11) NOT NULL DEFAULT '0',
  `autocorrect` tinyint(1) NOT NULL DEFAULT '0',
  `Course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Assignment_Course1` (`Course_id`),
  CONSTRAINT `assignment_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` mediumtext,
  `added` datetime DEFAULT NULL,
  `User_id` int(11) NOT NULL,
  `Lesson_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Comment_User1` (`User_id`),
  KEY `fk_Comment_Lesson1` (`Lesson_id`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`Lesson_id`) REFERENCES `lesson` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` mediumtext,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `time` time DEFAULT NULL,
  `description` mediumtext,
  `added` datetime DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Event_Course1` (`Course_id`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `invite`;
CREATE TABLE `invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) NOT NULL,
  `invitedBy` int(11) NOT NULL,
  `Course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invitedBy` (`invitedBy`),
  KEY `Course_id` (`Course_id`),
  CONSTRAINT `invite_ibfk_3` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invite_ibfk_4` FOREIGN KEY (`invitedBy`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `lesson`;
CREATE TABLE `lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic` varchar(45) DEFAULT NULL,
  `description` mediumtext,
  `number` int(11) DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Lesson_Course1` (`Course_id`),
  CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(45) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `msg` mediumtext,
  `sent` datetime DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(45) DEFAULT NULL,
  `content` mediumtext,
  `read` tinyint(1) DEFAULT '0',
  `sent` datetime DEFAULT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `reply_to_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Message_User1` (`from`),
  KEY `fk_Message_User2` (`to`),
  KEY `fk_Message_Message1` (`reply_to_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`reply_to_id`) REFERENCES `message` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `message_ibfk_2` FOREIGN KEY (`from`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `message_ibfk_3` FOREIGN KEY (`to`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `offlinetask`;
CREATE TABLE `offlinetask` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  `maxpoints` float DEFAULT NULL,
  `grade` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_RealAssignment_Course1` (`Course_id`),
  CONSTRAINT `offlinetask_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Napriklad test, ukol, aktivita z realne hodiny.';


DROP TABLE IF EXISTS `onlinesubmission`;
CREATE TABLE `onlinesubmission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `started` datetime DEFAULT NULL,
  `submitted` datetime DEFAULT NULL,
  `User_id` int(11) NOT NULL,
  `Assignment_id` int(11) NOT NULL,
  `points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Submission_User1` (`User_id`),
  KEY `fk_Submission_Assignment1` (`Assignment_id`),
  CONSTRAINT `onlinesubmission_ibfk_1` FOREIGN KEY (`Assignment_id`) REFERENCES `assignment` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `onlinesubmission_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Assignment_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `label` mediumtext,
  `choices` mediumtext,
  `rightanwser` mediumtext,
  PRIMARY KEY (`id`),
  KEY `Assignment_id` (`Assignment_id`),
  CONSTRAINT `question_ibfk_2` FOREIGN KEY (`Assignment_id`) REFERENCES `assignment` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` mediumtext,
  `created` datetime DEFAULT NULL,
  `Topic_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  `Reply_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Reply_Topic1` (`Topic_id`),
  KEY `fk_Reply_User1` (`User_id`),
  KEY `fk_Reply_Reply1` (`Reply_id`),
  CONSTRAINT `fk_Reply_Reply1` FOREIGN KEY (`Reply_id`) REFERENCES `reply` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `reply_ibfk_1` FOREIGN KEY (`Topic_id`) REFERENCES `topic` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `reply_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` mediumtext,
  `filename` varchar(100) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `added` datetime DEFAULT NULL,
  `Lesson_id` int(11) DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Resource_Lesson1` (`Lesson_id`),
  KEY `Course_id` (`Course_id`),
  CONSTRAINT `resource_ibfk_2` FOREIGN KEY (`Lesson_id`) REFERENCES `lesson` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `resource_ibfk_3` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `result`;
CREATE TABLE `result` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `points` float DEFAULT NULL,
  `User_id` int(11) NOT NULL,
  `OfflineTask_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Result_User1` (`User_id`),
  KEY `fk_Result_RealTask1` (`OfflineTask_id`),
  CONSTRAINT `result_ibfk_1` FOREIGN KEY (`OfflineTask_id`) REFERENCES `offlinetask` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `result_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `User_id` int(11) NOT NULL,
  `showEmail` tinyint(1) NOT NULL DEFAULT '1',
  `lang` varchar(2) NOT NULL DEFAULT 'en',
  `assignment_notif_interval` int(11) NOT NULL DEFAULT '5',
  KEY `User_id` (`User_id`),
  CONSTRAINT `settings_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `Course_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`Course_id`,`User_id`),
  KEY `fk_Course_has_User_User1` (`User_id`),
  KEY `fk_Course_has_User_Course` (`Course_id`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `student_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
  `Course_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`Course_id`,`User_id`),
  KEY `fk_Course_has_User_User2` (`User_id`),
  KEY `fk_Course_has_User_Course1` (`Course_id`),
  CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `teacher_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(45) DEFAULT NULL,
  `content` mediumtext,
  `created` datetime DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Topic_Course1` (`Course_id`),
  KEY `fk_Topic_User1` (`User_id`),
  CONSTRAINT `topic_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `topic_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `seclink` varchar(45) DEFAULT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0',
  `facebook` varchar(40) DEFAULT NULL,
  `icq` varchar(9) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `web` varchar(50) DEFAULT NULL,
  `apiKey` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;


-- 2012-04-10 22:57:51
