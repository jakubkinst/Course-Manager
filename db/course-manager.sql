-- Adminer 3.1.0 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP DATABASE IF EXISTS `course-manager`;
CREATE DATABASE `course-manager` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `course-manager`;

DROP TABLE IF EXISTS `anwser`;
CREATE TABLE `anwser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `User_id` int(11) NOT NULL,
  `Question_id` int(11) NOT NULL,
  `anwser` text,
  PRIMARY KEY (`id`),
  KEY `User_id` (`User_id`),
  KEY `Question_id` (`Question_id`),
  CONSTRAINT `anwser_ibfk_3` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `anwser_ibfk_4` FOREIGN KEY (`Question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;

INSERT INTO `anwser` (`id`, `User_id`, `Question_id`, `anwser`) VALUES
(44,	1,	81,	'help'),
(45,	1,	84,	'tst'),
(46,	1,	85,	'testttakjsdkajdhkajsdhkajsdhk'),
(47,	1,	86,	'existuje#neexistuje'),
(48,	1,	74,	'Klaus'),
(49,	1,	75,	'Vira je vira'),
(50,	1,	76,	'4'),
(51,	1,	77,	'2#4'),
(52,	2,	74,	'Medvedev'),
(53,	2,	75,	'Tralali'),
(54,	2,	76,	'2'),
(55,	2,	77,	'1#2#5'),
(56,	6,	102,	'1'),
(57,	6,	103,	'asd'),
(58,	2,	106,	'Klaus'),
(59,	2,	107,	'2'),
(60,	2,	108,	'xzcmzx,mnzxmc'),
(61,	2,	109,	'13');

DROP TABLE IF EXISTS `anwserfile`;
CREATE TABLE `anwserfile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `anwserfile` (`id`, `filename`, `size`) VALUES
(1,	'869e06b6d9d5831835c1c78f5c353661_readme (1).txt',	64),
(2,	'e25bcb82877f28b96c05645aedab41a4_PO auto translator.html',	2977);

DROP TABLE IF EXISTS `assignment`;
CREATE TABLE `assignment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

INSERT INTO `assignment` (`id`, `name`, `description`, `created`, `assigndate`, `duedate`, `maxpoints`, `timelimit`, `autocorrect`, `Course_id`) VALUES
(13,	'Test znalosti',	'Kontrola latky',	'2011-11-13 18:13:51',	'2011-11-14 00:00:00',	'2011-11-17 00:00:00',	20,	0,	0,	1),
(14,	'asdas',	'asda',	'2011-11-14 12:33:27',	'2011-11-14 00:00:00',	'2011-11-18 00:00:00',	0,	0,	0,	1),
(15,	'Test 2',	'',	'2011-11-14 18:13:16',	'2011-11-14 00:00:00',	'2011-11-15 00:00:00',	0,	100,	0,	1),
(16,	'sasdf',	'adsfasdf',	'2011-11-14 19:21:07',	'2011-11-14 00:00:00',	'2011-11-16 00:00:00',	0,	0,	0,	1),
(17,	'testsssts',	'sfasfs',	'2011-11-14 19:28:03',	'2011-11-15 10:13:00',	'2011-11-16 14:47:00',	0,	0,	0,	1),
(18,	'Makroekonomie',	'Test z prvni lekce',	'2011-11-16 10:35:19',	'2011-11-16 10:34:00',	'2011-12-03 14:00:00',	0,	8,	0,	1),
(19,	'AutoCorrect 1',	'asdasd',	'2011-11-16 16:25:25',	'2011-11-16 00:00:00',	'2011-11-19 00:00:00',	20,	0,	0,	1),
(20,	'AC',	'asd',	'2011-11-16 16:30:55',	'2011-11-16 00:00:00',	'2011-11-25 00:00:00',	30,	0,	0,	1),
(21,	'asd',	'asd',	'2011-11-16 16:31:47',	'2011-11-11 00:00:00',	'2011-11-24 00:00:00',	21,	0,	0,	1),
(22,	'AAACCC',	'asdas',	'2011-11-16 16:42:47',	'2011-11-16 16:42:00',	'2011-11-30 00:00:00',	20,	0,	1,	1),
(23,	'asd',	'asdaasd',	'2011-11-16 16:49:33',	'2011-11-16 16:49:00',	'2011-11-11 00:00:00',	200,	0,	0,	1),
(24,	'sdfa',	'sd\\fasdfasdf',	'2011-11-21 10:59:03',	'2011-11-23 11:10:00',	'2011-11-30 17:20:00',	0,	0,	0,	1),
(25,	'File test',	'sadads',	'2011-11-23 15:21:39',	'2011-11-23 00:00:00',	'2011-11-30 00:00:00',	0,	0,	0,	1),
(26,	'Test mail',	'asdasd',	'2011-11-24 10:52:54',	'2011-11-29 00:00:00',	'2011-11-30 00:00:00',	0,	0,	0,	1),
(27,	'Test mail',	'asdasd',	'2011-11-24 10:53:09',	'2011-11-29 00:00:00',	'2011-11-30 00:00:00',	0,	0,	0,	1),
(28,	'Testik X',	'',	'2011-11-28 16:27:04',	'2011-11-17 07:09:00',	'2011-11-30 00:00:00',	0,	1,	0,	1);

DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `added` datetime DEFAULT NULL,
  `User_id` int(11) NOT NULL,
  `Lesson_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Comment_User1` (`User_id`),
  KEY `fk_Comment_Lesson1` (`Lesson_id`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`Lesson_id`) REFERENCES `lesson` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_Comment_User1` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

INSERT INTO `comment` (`id`, `content`, `added`, `User_id`, `Lesson_id`) VALUES
(1,	'ahooj',	'2011-06-14 20:37:28',	2,	1),
(2,	'blabla\r\n',	'2011-06-14 20:37:33',	2,	1),
(3,	'Supeer',	'2011-06-18 12:23:18',	1,	1),
(8,	'asgajdshdsfdas',	'2011-06-28 14:10:03',	9,	1),
(10,	'asd',	'2011-07-02 09:55:07',	2,	5),
(11,	'asd',	'2011-07-02 09:55:22',	2,	5),
(12,	'asd',	'2011-07-02 10:42:38',	1,	3),
(15,	'dfsaf',	'2011-12-02 11:33:24',	1,	15),
(16,	'asd',	'2011-12-02 11:48:15',	1,	15),
(17,	'asd',	'2011-12-02 11:53:00',	1,	15),
(18,	'sad',	'2011-12-02 11:53:47',	1,	15),
(19,	'asdf',	'2011-12-02 11:54:48',	1,	15),
(20,	'gjhgk',	'2011-12-02 12:02:25',	1,	15),
(21,	'KKK',	'2011-12-02 12:04:20',	1,	15),
(22,	'asd\r\n',	'2011-12-02 12:04:39',	1,	15),
(23,	'asdfsdf',	'2011-12-02 12:05:25',	1,	15),
(24,	'asdf',	'2011-12-02 12:05:42',	1,	15),
(25,	'sdfsad',	'2011-12-02 12:05:52',	1,	15),
(26,	'sdfasf',	'2011-12-02 12:06:18',	1,	15),
(27,	'esf',	'2011-12-02 12:06:48',	1,	15),
(28,	'saf',	'2011-12-02 12:12:30',	1,	15),
(29,	'asdf',	'2011-12-02 12:13:49',	1,	15),
(30,	'asd',	'2011-12-02 12:14:12',	1,	15),
(31,	'adsf',	'2011-12-02 12:14:49',	1,	15);

DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` text,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

INSERT INTO `course` (`id`, `name`, `description`, `created`) VALUES
(1,	'Jakubuv Kurz 1',	'Kubuv kurz popis 1',	NULL),
(2,	'Mileny Kurz 1',	'popis',	NULL),
(3,	'Jakubuv kurz 2',	'kjadshlfkjadsklfhadslfkjahsdklfjhasdklfjhasf\r\nadsfsdfasdfadsf alsfjlfj la askfh',	NULL),
(4,	'Jakubuv kurz cislo 3',	'test',	NULL),
(6,	'Kurz 5',	'asdas',	NULL),
(7,	'Ekonomie I',	'Zakladni kurz Ekonomie pro stredoskolaky',	NULL);

DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `time` time DEFAULT NULL,
  `description` text,
  `added` datetime DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Event_Course1` (`Course_id`),
  CONSTRAINT `event_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

INSERT INTO `event` (`id`, `name`, `date`, `time`, `description`, `added`, `Course_id`) VALUES
(3,	'asdasd',	'2011-10-18 00:00:00',	NULL,	'asd',	'2011-10-28 12:26:54',	1),
(4,	'asdasd',	'2011-10-18 00:00:00',	NULL,	'asd',	'2011-10-28 12:27:36',	1),
(5,	'asdasd',	'2011-10-18 00:00:00',	NULL,	'asd',	'2011-10-28 12:27:50',	1),
(6,	'asdasd',	'2011-10-25 00:00:00',	NULL,	'asd',	'2011-10-28 12:27:56',	1),
(7,	'asdasd',	'2011-10-05 00:00:00',	NULL,	'asd',	'2011-10-28 12:28:05',	1),
(8,	'Test z ekonomie',	'2011-11-22 00:00:00',	NULL,	'Testik',	'2011-11-21 10:55:08',	1),
(9,	'Logika',	'2011-11-23 11:33:00',	NULL,	'adasd',	'2011-11-21 11:01:02',	1);

DROP TABLE IF EXISTS `invite`;
CREATE TABLE `invite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) NOT NULL,
  `invitedBy` int(11) NOT NULL,
  `Course_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invitedBy` (`invitedBy`),
  KEY `Course_id` (`Course_id`),
  CONSTRAINT `invite_ibfk_1` FOREIGN KEY (`invitedBy`) REFERENCES `user` (`id`),
  CONSTRAINT `invite_ibfk_3` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

INSERT INTO `invite` (`id`, `email`, `invitedBy`, `Course_id`) VALUES
(5,	'frantisek@kinst.cz',	1,	1),
(6,	'jirka@kinst.cz',	1,	1),
(7,	'jirka@kinst.cz',	1,	1),
(8,	'mek@kinst.cz',	1,	1),
(9,	'mek@kinst.cz',	1,	1),
(10,	'kaja@kinst.cz',	1,	1);

DROP TABLE IF EXISTS `lesson`;
CREATE TABLE `lesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic` varchar(45) DEFAULT NULL,
  `description` text,
  `number` int(11) DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Lesson_Course1` (`Course_id`),
  CONSTRAINT `lesson_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

INSERT INTO `lesson` (`id`, `topic`, `description`, `number`, `Course_id`, `date`) VALUES
(1,	'Prvni lekce',	'Co jsme vsechni delali na prvni lekci ?\r\n1]kskjs\r\n2]asdjks\r\n...',	NULL,	1,	'2011-06-14 20:35:12'),
(3,	'Lesson by Jakub',	'Kjdckscdnkjdsc scjdscnkdscjnsd cksdjcndskjcnsdkn csdkn csdkjsdc nksdnc ksdckdscn csdkn scdknnsdfksnjdsnf sdjf sdjfn sdfjn sfdj nsdjfn sdknf kfsn skdcnscd kndcs kdcsn scdkn sdcknscdksdcn cskd scdm ksdcn',	NULL,	3,	'2011-06-18 14:29:51'),
(5,	'Test',	'Lesson1',	NULL,	2,	'2011-07-02 09:53:26'),
(15,	'Texy Test',	'Header\r\n######\r\n- asd\r\n- sad\r\n- asd\r\n/--code\r\nsadasdasddsfafa\r\nsdfadsf\r\n\\--\r\n\"gmail\":http://gmail.com',	NULL,	1,	'2011-12-05 00:00:00');

DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(45) NOT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `msg` text,
  `sent` datetime DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=latin1;

INSERT INTO `mail` (`id`, `to`, `subject`, `msg`, `sent`) VALUES
(1,	'jakub@kinst.cz',	'sub',	'msg',	'2011-11-24 10:53:31'),
(2,	'milena@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Test mail in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-24 10:53:31'),
(3,	'evicka@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Test mail in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-24 10:53:31'),
(4,	'tonda@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Test mail in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-24 10:53:31'),
(5,	'jirina@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Test mail in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-24 10:53:31'),
(6,	'jerry90@gmail.com',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Test mail in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-24 10:53:31'),
(7,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 22:57:19'),
(8,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 22:57:21'),
(9,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 22:57:22'),
(10,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 22:57:24'),
(11,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 22:57:26'),
(12,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:00:16'),
(13,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:00:19'),
(14,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:00:21'),
(15,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:00:23'),
(16,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:00:24'),
(17,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:07:17'),
(18,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:07:19'),
(19,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:07:20'),
(20,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:07:22'),
(21,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:07:24'),
(22,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:13:17'),
(23,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:13:19'),
(24,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:13:20'),
(25,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:13:22'),
(26,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:13:24'),
(27,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:17'),
(28,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:19'),
(29,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:21'),
(30,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:23'),
(31,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:25'),
(32,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:27'),
(33,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:29'),
(34,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:31'),
(35,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:33'),
(36,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:16:35'),
(37,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:17:18'),
(38,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:17:20'),
(39,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:17:22'),
(40,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:17:23'),
(41,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:17:25'),
(42,	'milena@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:19:18'),
(43,	'evicka@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:19:20'),
(44,	'tonda@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:19:22'),
(45,	'jirina@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:19:23'),
(46,	'jerry90@gmail.com',	'Notification of upcoming assignment duedate',	'Assignment <b>Makroekonomie</b> duedate is on <b>2011-12-03 14:00:00</b>. You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-27 23:19:26'),
(47,	'milena@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Testik X in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-28 16:27:20'),
(48,	'evicka@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Testik X in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-28 16:27:22'),
(49,	'tonda@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Testik X in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-28 16:27:25'),
(50,	'jirina@kinst.cz',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Testik X in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-28 16:27:27'),
(51,	'jerry90@gmail.com',	'New Assignment added to Jakubuv Kurz 1',	'There is a new assignment called Testik X in your course <b>Jakubuv Kurz 1</b><br />\n	    You can check it at <a href=\"http://localhost/CourseMan/www/\">http://localhost/CourseMan/www/</a>.',	'2011-11-28 16:27:29'),
(52,	'jirka@kropacek.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://localhost/CourseMan/www/user/check?hash=f137a46cb68d4e79c90f5ffdf17fb6ddb1aeadd7\">http://localhost/CourseMan/www/user/check?hash=f137a46cb68d4e79c90f5ffdf17fb6ddb1aeadd7</a>.',	'2011-12-06 11:36:18'),
(53,	'frantisek@kinst.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://localhost/CourseMan/www/user/check?hash=f7273d5f9e486b28d0467d643db7c47ff4d59aeb\">http://localhost/CourseMan/www/user/check?hash=f7273d5f9e486b28d0467d643db7c47ff4d59aeb</a>.',	'2011-12-06 11:50:17');

DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(45) DEFAULT NULL,
  `content` text,
  `read` bit(1) DEFAULT b'0',
  `sent` datetime DEFAULT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `reply_to_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Message_User1` (`from`),
  KEY `fk_Message_User2` (`to`),
  KEY `fk_Message_Message1` (`reply_to_id`),
  CONSTRAINT `fk_Message_Message1` FOREIGN KEY (`reply_to_id`) REFERENCES `message` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Message_User1` FOREIGN KEY (`from`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_Message_User2` FOREIGN KEY (`to`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

INSERT INTO `message` (`id`, `subject`, `content`, `read`, `sent`, `from`, `to`, `reply_to_id`) VALUES
(3,	'Ahoj',	'Zkouska zpravy',	'1',	'2011-11-02 18:04:32',	1,	2,	NULL),
(4,	'Cus',	'askjdhflasjfalkfhlkjfhlksdjfhkladshflkadsjhfkladsjhflkajdshflkajdshfkladshlfkjsaf\r\nasdf\r\nasdf\r\nasdf\r\nasdfsdfasdfasdkjfgakdsfygkdsufy',	'1',	'2011-11-02 18:12:20',	2,	1,	NULL),
(5,	'Cus 2',	'Blablabla',	'1',	'2011-11-02 18:13:06',	2,	1,	NULL),
(6,	'Test',	'ahoj jak se mas ?',	'1',	'2011-11-03 08:32:15',	1,	1,	NULL),
(7,	'Ahoj',	'sdffsd',	'1',	'2011-11-03 09:34:51',	1,	2,	NULL),
(8,	'Ahoj',	'asdasdasd',	'1',	'2011-11-07 13:44:02',	1,	2,	NULL),
(9,	'Ahoj',	'askjhdkasjdhkadshkshdfadsf',	'1',	'2011-11-13 10:57:25',	1,	1,	NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1 COMMENT='Napriklad test, ukol, aktivita z realne hodiny.';

INSERT INTO `offlinetask` (`id`, `name`, `date`, `Course_id`, `maxpoints`, `grade`) VALUES
(1,	'Pisemka 1',	NULL,	1,	10,	0),
(2,	'Pisemka 2',	NULL,	1,	20,	0),
(3,	'Test',	NULL,	1,	10,	1),
(4,	'Test 2',	NULL,	1,	10,	1),
(5,	'asdf',	NULL,	1,	23,	0),
(6,	'asdf',	NULL,	1,	23,	0),
(7,	'safa',	NULL,	1,	22,	0),
(8,	'Test 3',	NULL,	1,	10,	0),
(9,	'Znamky 2',	NULL,	1,	5,	0),
(10,	'Znamky 3',	NULL,	1,	5,	1),
(11,	'Znamky 5',	NULL,	1,	0,	1),
(12,	'askj',	NULL,	1,	1,	0),
(13,	'M',	NULL,	3,	10,	0),
(14,	'Pisemka 2',	NULL,	1,	0,	0),
(15,	'Test 7',	NULL,	1,	10,	0),
(16,	'Test 8',	NULL,	1,	10,	0),
(17,	'Znamky 6',	NULL,	1,	0,	0),
(18,	'Znamky 5',	NULL,	1,	0,	1),
(19,	'Test 21',	NULL,	1,	20,	0);

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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=latin1;

INSERT INTO `onlinesubmission` (`id`, `started`, `submitted`, `User_id`, `Assignment_id`, `points`) VALUES
(34,	'2011-11-14 13:24:55',	NULL,	1,	13,	NULL),
(35,	'2011-11-14 18:13:36',	NULL,	1,	15,	NULL),
(36,	'2011-11-16 10:38:15',	NULL,	1,	18,	NULL),
(37,	'2011-11-16 14:29:47',	NULL,	2,	13,	20),
(40,	'2011-11-16 18:06:06',	NULL,	2,	22,	13),
(41,	'2011-11-19 14:54:28',	NULL,	1,	22,	13),
(42,	'2011-11-23 15:26:29',	NULL,	6,	25,	NULL),
(43,	'2011-11-28 16:29:05',	NULL,	2,	28,	NULL);

DROP TABLE IF EXISTS `question`;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Assignment_id` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `label` text,
  `choices` text,
  `rightanwser` text,
  PRIMARY KEY (`id`),
  KEY `Assignment_id` (`Assignment_id`),
  CONSTRAINT `question_ibfk_2` FOREIGN KEY (`Assignment_id`) REFERENCES `assignment` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=latin1;

INSERT INTO `question` (`id`, `Assignment_id`, `type`, `label`, `choices`, `rightanwser`) VALUES
(74,	13,	'text',	'Jmeno prezidenta',	NULL,	NULL),
(75,	13,	'textarea',	'Esej na tema \"vira\"',	NULL,	NULL),
(76,	13,	'radio',	'2+2',	'4#8#1#2#3',	NULL),
(77,	13,	'multi',	'2+2',	'1#2#3#4#5',	NULL),
(78,	14,	'text',	'asd',	NULL,	NULL),
(79,	14,	'radio',	'asd',	'asds#asddgfsgfh#asdgjgfkfkhjkgh',	NULL),
(81,	15,	'text',	'test',	NULL,	NULL),
(83,	17,	'textarea',	'asfdfs',	NULL,	NULL),
(84,	18,	'text',	'Urokova sazba ?',	NULL,	NULL),
(85,	18,	'textarea',	'Sloh',	NULL,	NULL),
(86,	18,	'multi',	'Parita kupni ceny',	'existuje#existuje vzdy#neexistuje#neni mozne urcit',	NULL),
(87,	23,	'text',	'asdasd',	NULL,	NULL),
(88,	23,	'text',	'asdasdasd',	NULL,	NULL),
(89,	23,	'text',	'asdasdasddfgfdsg',	NULL,	NULL),
(90,	23,	'text',	'tralali',	NULL,	NULL),
(91,	23,	'text',	'kuku',	NULL,	NULL),
(92,	23,	'text',	'tralaliasd',	NULL,	NULL),
(95,	22,	'text',	'kalala',	NULL,	'30'),
(96,	22,	'radio',	'2+2',	'5#6#4#3',	'4'),
(97,	22,	'multi',	'2+2',	'5#4#3#2',	'4#2'),
(101,	16,	'text',	'asdasd',	NULL,	NULL),
(102,	25,	'file',	'file test',	NULL,	NULL),
(103,	25,	'text',	'test',	NULL,	NULL),
(104,	25,	'file',	'sdfsdf',	NULL,	NULL),
(105,	25,	'file',	'dfas',	NULL,	NULL),
(106,	28,	'text',	'Kdo je prezident ?',	NULL,	NULL),
(107,	28,	'file',	'Nahraj soubor',	NULL,	NULL),
(108,	28,	'textarea',	'Esej',	NULL,	NULL),
(109,	28,	'radio',	'2+12',	'13#14#15',	NULL);

DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;

INSERT INTO `reply` (`id`, `content`, `created`, `Topic_id`, `User_id`, `Reply_id`) VALUES
(5,	'Nevime',	'2011-10-26 16:53:42',	4,	1,	NULL),
(6,	'asasfsaf',	'2011-10-26 17:13:13',	4,	1,	NULL),
(7,	'asasfsafasdfasf',	'2011-10-26 17:13:16',	4,	1,	NULL),
(8,	'ahooj',	'2011-11-03 10:11:53',	8,	1,	NULL),
(9,	'dsfs',	'2011-11-03 10:12:21',	8,	1,	NULL),
(10,	'proto',	'2011-11-13 11:03:08',	11,	1,	NULL),
(11,	'protoze',	'2011-11-13 11:03:32',	11,	1,	NULL),
(12,	'asdf',	'2011-12-22 11:53:24',	4,	1,	NULL),
(13,	'asdf',	'2011-12-22 11:53:54',	4,	1,	NULL),
(14,	'asdfasdf',	'2011-12-22 11:53:59',	4,	1,	NULL),
(15,	'asdfasdfadsf',	'2011-12-22 11:54:03',	4,	1,	NULL),
(16,	'asdfasdfadasdfsf',	'2011-12-22 11:54:07',	4,	1,	NULL),
(17,	'asdfasdfadasdfsf',	'2011-12-22 11:54:10',	4,	1,	NULL),
(18,	'asdfasdfadasdfsf',	'2011-12-22 11:54:13',	4,	1,	NULL),
(19,	'asdfasdfadasdfsf',	'2011-12-22 11:54:14',	4,	1,	NULL),
(20,	'asdfasdfadasdfsf',	'2011-12-22 11:54:16',	4,	1,	NULL),
(21,	'asdfasdfadasdfsf',	'2011-12-22 11:54:18',	4,	1,	NULL),
(22,	'asdfasdfadasdfsf',	'2011-12-22 11:54:19',	4,	1,	NULL),
(23,	'asdfasdfadasdfsf',	'2011-12-22 11:54:21',	4,	1,	NULL),
(24,	'asdfasdfadasdfsf',	'2011-12-22 11:54:23',	4,	1,	NULL),
(25,	'asdfasdfadasdfsf',	'2011-12-22 11:54:24',	4,	1,	NULL),
(26,	'asdfasdfadasdfsf',	'2011-12-22 11:54:27',	4,	1,	NULL),
(27,	'asdfasdfadasdfsf',	'2011-12-22 11:54:28',	4,	1,	NULL),
(28,	'asdfasdfadasdfsf',	'2011-12-22 11:54:30',	4,	1,	NULL),
(29,	'asdfasdfadasdfsf',	'2011-12-22 11:54:32',	4,	1,	NULL),
(30,	'asdfasdfadasdfsf',	'2011-12-22 11:54:34',	4,	1,	NULL);

DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

INSERT INTO `resource` (`id`, `name`, `description`, `filename`, `size`, `added`, `Lesson_id`, `Course_id`) VALUES
(1,	'asdasdasdasdasd',	'asdasdasdasdasasd',	'3d606950be92ea61b55c4af48d3c6eca_MY STOCK EXCHANGE.ppt',	907776,	'2011-10-24 12:13:18',	NULL,	1),
(2,	'Stock Exchange',	'PDF dokumentace',	'48c4854b320587aee648aafc5bf0e295_MY STOCK EXCHANGE.pdf',	95847,	'2011-10-24 12:39:27',	NULL,	1),
(3,	'asdasd',	'asdasd',	'904b3388495db6b49e84f616328d01a8_soap_example_buyStocks.xml',	738,	'2011-10-24 12:59:05',	NULL,	3),
(4,	'05_WS_ADDRESSING.pptx',	NULL,	'd954ab1a2ee87f9c51e6ac5a42c1a46d_05_WS_ADDRESSING.pptx',	464338,	'2011-11-21 12:45:43',	NULL,	1),
(5,	'05_WS_ADDRESSING.pptx',	NULL,	'd954ab1a2ee87f9c51e6ac5a42c1a46d_05_WS_ADDRESSING.pptx',	464338,	'2011-11-21 12:45:43',	NULL,	1),
(11,	'Jellyfish.jpg',	NULL,	'e01472921fbe0fb355276b306173d3e6_Jellyfish.jpg',	775702,	'2011-11-24 10:35:52',	NULL,	1),
(12,	'Chrysanthemum.jpg',	NULL,	'e01472921fbe0fb355276b306173d3e6_Chrysanthemum.jpg',	879394,	'2011-11-24 10:35:52',	NULL,	1);

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
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;

INSERT INTO `result` (`id`, `points`, `User_id`, `OfflineTask_id`) VALUES
(1,	8,	2,	1),
(2,	7,	2,	2),
(3,	1,	2,	8),
(4,	2,	4,	8),
(5,	3,	6,	8),
(6,	4,	9,	8),
(7,	1,	2,	9),
(8,	2,	4,	9),
(9,	3,	6,	9),
(10,	4,	9,	9),
(11,	1,	2,	10),
(12,	2,	4,	10),
(13,	2,	6,	10),
(14,	3,	9,	10),
(15,	0,	2,	11),
(16,	0,	4,	11),
(17,	0,	6,	11),
(18,	0,	9,	11),
(19,	1,	2,	12),
(20,	1,	4,	12),
(21,	1,	6,	12),
(22,	1,	9,	12),
(23,	10,	2,	13),
(24,	12,	2,	14),
(25,	0,	4,	14),
(26,	0,	6,	14),
(27,	0,	9,	14),
(28,	6,	2,	15),
(29,	4,	4,	15),
(30,	2,	6,	15),
(31,	2,	9,	15),
(32,	3.5,	2,	16),
(33,	2.3,	4,	16),
(34,	1.2,	6,	16),
(35,	1.3,	9,	16),
(36,	3,	2,	17),
(37,	3,	4,	17),
(38,	2,	6,	17),
(39,	1,	9,	17),
(40,	1,	2,	18),
(41,	1,	4,	18),
(42,	1,	6,	18),
(43,	1,	9,	18),
(44,	2,	2,	19),
(45,	6,	4,	19),
(46,	13,	6,	19),
(47,	22,	9,	19);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `User_id` int(11) NOT NULL,
  `showEmail` tinyint(1) NOT NULL DEFAULT '1',
  `lang` varchar(2) NOT NULL DEFAULT 'en',
  `assignment_notif_interval` int(11) NOT NULL DEFAULT '5',
  KEY `User_id` (`User_id`),
  CONSTRAINT `settings_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `settings` (`User_id`, `showEmail`, `lang`, `assignment_notif_interval`) VALUES
(1,	1,	'en',	5),
(2,	1,	'en',	5),
(3,	1,	'en',	5),
(4,	1,	'en',	5),
(5,	1,	'en',	5),
(6,	1,	'en',	5),
(7,	1,	'en',	5),
(8,	1,	'en',	5),
(9,	1,	'en',	5),
(10,	1,	'en',	5),
(11,	1,	'en',	5);

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `Course_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`Course_id`,`User_id`),
  KEY `fk_Course_has_User_User1` (`User_id`),
  KEY `fk_Course_has_User_Course` (`Course_id`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `student_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `student` (`Course_id`, `User_id`) VALUES
(2,	1),
(1,	2),
(3,	2),
(1,	4),
(1,	6),
(1,	9),
(1,	11);

DROP TABLE IF EXISTS `teacher`;
CREATE TABLE `teacher` (
  `Course_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`Course_id`,`User_id`),
  KEY `fk_Course_has_User_User2` (`User_id`),
  KEY `fk_Course_has_User_Course1` (`Course_id`),
  CONSTRAINT `teacher_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `teacher_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `teacher` (`Course_id`, `User_id`) VALUES
(1,	1),
(3,	1),
(4,	1),
(6,	1),
(2,	2),
(7,	10);

DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(45) DEFAULT NULL,
  `content` text,
  `created` datetime DEFAULT NULL,
  `Course_id` int(11) NOT NULL,
  `User_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Topic_Course1` (`Course_id`),
  KEY `fk_Topic_User1` (`User_id`),
  CONSTRAINT `topic_ibfk_1` FOREIGN KEY (`Course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `topic_ibfk_2` FOREIGN KEY (`User_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

INSERT INTO `topic` (`id`, `label`, `content`, `created`, `Course_id`, `User_id`) VALUES
(4,	'Dotaz',	'Nevite jak to tu funguje ??',	'2011-10-26 16:53:27',	1,	1),
(5,	'asd',	'asdad',	'2011-10-26 16:54:15',	3,	1),
(6,	'adsa',	'sadf',	'2011-10-26 17:09:43',	1,	1),
(7,	'adsa',	'sadf',	'2011-10-26 17:09:46',	1,	1),
(8,	'dsfas',	'asdfsdfsdf',	'2011-10-26 17:09:49',	1,	1),
(9,	'as',	'Adsad',	'2011-10-28 12:33:57',	4,	1),
(10,	'Testa',	'dasdasd',	'2011-11-03 10:21:13',	1,	1),
(11,	'Ukol 1',	'Proc ?',	'2011-11-13 11:02:59',	1,	1);

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

INSERT INTO `user` (`id`, `email`, `password`, `firstname`, `lastname`, `created`, `seclink`, `checked`, `facebook`, `icq`, `phone`, `web`) VALUES
(1,	'jakub@kinst.cz',	'c7290eb6d8dc2fc4edc97cc1808bbe02',	'Jakub',	'Kinst',	'2011-10-03 22:15:23',	'25f32c020a6efe345e1eac1436444faf23df3453',	1,	NULL,	NULL,	NULL,	'http://jakub.kinst.cz'),
(2,	'milena@kinst.cz',	'48285406fe430d1389ccc284c1b1353d',	'Milena',	'Kinstova',	'2011-11-10 22:15:19',	'5a2dc0cab52aa5601bf585685fa95511e0038206',	1,	NULL,	NULL,	NULL,	''),
(3,	'franta@kinst.cz',	'800c62fbc146e12b38a5dc6586645f60',	'Franta',	'Kinst',	'2011-11-26 22:15:16',	'660db7f7f0b21561942d9a6b62d1884602a64c94',	1,	NULL,	NULL,	NULL,	''),
(4,	'evicka@kinst.cz',	'a5b0c2d8eaa78697154e5690575092d7',	'Evicka',	'Kinstova',	'2011-11-12 22:15:11',	'3026ee0deb0cb9155dd83dfbf8a562c9e1a0ced1',	1,	NULL,	NULL,	NULL,	''),
(5,	'janicka@kinst.cz',	'57a16d19006b79ec5335f1c35f5b3ef1',	'Janicka',	'Kinstova',	'2011-11-04 22:15:07',	'c11608cab7e7b2e3deb9a10ca2d3335cc91f451c',	1,	NULL,	NULL,	NULL,	''),
(6,	'tonda@kinst.cz',	'598d9e00baa66586eadcb8af32c069a9',	'Tonda',	'Kinst',	'2011-11-01 22:15:04',	'd7f2db2276521684f0db4aa9b7f807a47cb29849',	1,	NULL,	NULL,	NULL,	''),
(7,	'mirek@kinst.cz',	'28ea7edf726f5e612cd980afb7c93cf2',	'Mirek',	'Kinst',	'2011-11-10 22:15:00',	'a5f463aeb7f4e342fb0e33e8e9208ef9186c2272',	1,	NULL,	NULL,	NULL,	''),
(8,	'jirka@kinst.cz',	'9a6f796e1c7de0282a61a460011fd8dc',	'Jirka',	'Kinst',	'2011-11-01 22:14:56',	'5bf42cd4ea4f2b93e65ffe1629c6900d4dc5f901',	1,	NULL,	NULL,	NULL,	''),
(9,	'jirina@kinst.cz',	'a95cd7cad2c7e7a74d49fc7d442b0faa',	'Jirina',	'Kinstova',	'2011-11-01 22:14:51',	'98114cc97bc229cde4d5acdbd894aab443cf4080',	1,	NULL,	NULL,	NULL,	''),
(10,	'milan@kinst.cz',	'5a920d3997b1d9c096656f24d08524c4',	'Milan',	'Kinst',	'2011-11-02 22:14:45',	'2318de2e49728c47ca79789b223cd65f26287e53',	1,	NULL,	NULL,	NULL,	'http://www.milan.cz'),
(11,	'jerry90@gmail.com',	'cb7f9bc7ffeb30e49343592027655810',	'Jerry',	'Key',	'2011-11-01 22:14:39',	'39743a72fb31cbd96fe747e836d77bc632f03fcf',	1,	NULL,	NULL,	NULL,	''),
(12,	'jirka@kropacek.cz',	'9a6f796e1c7de0282a61a460011fd8dc',	'Jiri',	'Kropacek',	'2011-12-06 11:35:39',	'f137a46cb68d4e79c90f5ffdf17fb6ddb1aeadd7',	0,	NULL,	NULL,	NULL,	'');

-- 2012-01-25 17:29:45
