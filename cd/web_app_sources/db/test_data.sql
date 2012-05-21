-- Adminer 3.3.3 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `anwser` (`id`, `User_id`, `Question_id`, `anwser`) VALUES
(1,	15,	1,	'-1'),
(2,	15,	2,	'46');


INSERT INTO `assignment` (`id`, `name`, `description`, `created`, `assigndate`, `duedate`, `maxpoints`, `timelimit`, `autocorrect`, `Course_id`) VALUES
(1,	'úkol',	'úkol',	'2012-05-09 23:12:25',	'2012-05-16 05:30:00',	'2012-05-07 00:00:00',	999,	1,	1,	3),
(2,	'Test scitani a odcitani',	'Zaklady',	'2012-05-11 21:10:21',	'2012-05-11 21:09:00',	'2012-06-30 00:00:00',	0,	20,	0,	1),
(7,	'Test',	'Vysivani',	'2012-05-16 19:59:50',	'2012-05-16 19:59:00',	'2012-05-24 00:00:00',	0,	0,	0,	4),
(8,	'Napiš BP',	'Pořádně',	'2012-05-17 23:04:11',	'2012-05-17 00:00:00',	'2012-05-19 00:00:00',	10,	10,	1,	5),
(9,	'Test',	'Testik',	'2012-05-17 23:10:20',	'2012-05-17 23:10:00',	'2012-06-30 00:00:00',	0,	0,	0,	1),
(10,	'Tvrde a mekke i/y',	'Doplnte spravne i/y',	'2012-05-21 18:08:43',	'2012-05-21 18:08:00',	'2012-07-31 00:00:00',	100,	0,	1,	6);

INSERT INTO `comment` (`id`, `content`, `added`, `User_id`, `Lesson_id`) VALUES
(1,	'Doporučuji všem, aby si přečetli Wikipedii.',	'2012-04-06 11:52:50',	15,	1),
(4,	'Na priste si pripravte referaty !',	'2012-05-11 16:52:31',	20,	5),
(5,	'Komentar 1',	'2012-05-21 17:57:20',	20,	6),
(6,	'Komentar 2',	'2012-05-21 17:59:59',	22,	6),
(7,	'Komentar 3',	'2012-05-21 18:01:44',	21,	6);

INSERT INTO `course` (`id`, `name`, `description`, `created`) VALUES
(1,	'Matematika I',	'První základy Matematiky prvního stupně základní školy. Určeno pro úplné začátečníky.',	NULL),
(2,	'Matematika II',	'Pokročilé techniky v základoškolské matematice. Určeno pro absolventy kurzu Matematika I.',	NULL),
(3,	'Kurz',	'popis',	NULL),
(4,	'Kurz vyšívání',	'Pokročilé vyšívání pro dívky starší 12ti let',	NULL),
(5,	'Psaní bakalářky I',	'První semestr\r\nKreditů:4\r\nDotace: 4mil',	NULL),
(6,	'Cesky jazyk I',	'Mluvnice, literatura i sloh',	NULL),
(7,	'Anglicky jazyk',	'Pripravny kurz k certifikatu CAE',	NULL);

INSERT INTO `event` (`id`, `name`, `date`, `time`, `description`, `added`, `Course_id`) VALUES
(1,	'Opakování sčítání',	'2012-04-24 00:00:00',	NULL,	'Sčítání čísel do 100',	'2012-04-06 12:04:46',	1),
(2,	'Zaverecny test',	'2012-06-29 00:00:00',	NULL,	'Scitani odcitani',	'2012-05-11 21:44:29',	1),
(3,	'Test',	'2012-06-12 00:00:00',	NULL,	'Test',	'2012-05-11 21:45:01',	1),
(4,	'Exkurze',	'2012-05-22 00:00:00',	NULL,	'Popis',	'2012-05-11 21:45:32',	1),
(5,	'Gt',	'2012-05-11 00:00:00',	NULL,	'Uu',	'2012-05-11 21:54:25',	1),
(6,	'Test event',	'2012-05-12 00:00:00',	NULL,	'Test description\r\n',	'2012-05-11 21:58:20',	2),
(7,	'Test z vysivani',	'2012-05-22 00:00:00',	NULL,	'Pozor',	'2012-05-16 20:01:15',	4),
(8,	'CAE',	'2012-06-20 00:00:00',	NULL,	'Test CAE',	'2012-05-21 18:02:46',	7),
(9,	'Diktat',	'2012-06-20 00:00:00',	NULL,	'Velka a mala pismena, carky, i/y',	'2012-05-21 18:07:39',	6);


INSERT INTO `lesson` (`id`, `topic`, `description`, `number`, `Course_id`, `date`) VALUES
(1,	'První hodina',	'Čísla, sčítání násobení\r\n=======================\r\n\r\nPellentesque habitants morbi tristique senectus et netus et malesuada fames ac turpis egestas. Fusce sapien elit, porta at mattis eget, euismod sit amet nisl. Curabitur vehicula tincidunt arcu, sed venenatis ipsum consequat vel. Mauris dolor eros, gravida et euismod non, ultricies nec nisi. In nec diam ac turpis sodales consectetur. Etiam lectus lorem, sodales et pulvinar at, auctor in dolor. Maecenas quam leo, aliquam ut viverra a, elementum id dolor. Ut ac augue velit. Cras mattis dui non magna fringilla scelerisque. Nunc sagittis nibh id dolor interdum luctus.\r\n\r\nCo je to matematika ?\r\n=====================\r\n\"Wikipedie\":http://cs.wikipedia.org/wiki/Matematika',	NULL,	1,	'2012-04-01 00:00:00'),
(2,	'Druhá hodina',	'Násobení celých čísel\n===========================================\n+ěščřžýáíé=\n3*3 = ?',	NULL,	1,	'2012-04-02 00:00:00'),
(3,	'Prvni lekce',	'Quisque elementum aliquam rhoncus. In quis velit in nunc egestas bibendum et vel arcu. Quisque vitae consequat velit. Morbi ultricies libero id nibh hendrerit euismod. Nunc blandit, enim eget commodo accumsan, quam risus pulvinar elit, sit amet dapibus mauris sem vel turpis. Donec posuere, nisi et vestibulum venenatis, mauris quam bibendum risus, et condimentum felis tortor nec quam. Vivamus vitae diam lorem. Praesent condimentum iaculis congue. Quisque in neque ligula. Cras at tincidunt mauris. Proin vehicula aliquam cursus. Quisque laoreet scelerisque consectetur. Proin porta auctor libero, eu porttitor orci semper sit amet. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;',	NULL,	2,	'2012-04-18 00:00:00'),
(4,	'Lekce 2',	'**Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam dolor eros, luctus at dictum nec, hendrerit nec est. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam imperdiet sodales est, sed ultrices arcu tempor eu.**',	NULL,	2,	'2012-04-26 00:00:00'),
(5,	'Základy vyšívání',	'**Probrali jsme základy vyšívání**\r\n\r\nviz \"wiki\":http://cs.wikipedia.org/wiki/Vy%C5%A1%C3%ADv%C3%A1n%C3%AD\r\n\r\nInteger fringilla scelerisque ipsum vel tincidunt. Aliquam lacinia dapibus est, eu consectetur sapien bibendum ac. Praesent at urna magna, ut volutpat dolor. Nullam lobortis molestie magna, ut tempus nisi placerat sed. Praesent quis enim sem. Nam et vehicula lectus. Suspendisse nec diam ac leo sagittis ullamcorper sed eu lectus.\r\n\r\n\r\nNam sed justo eu massa fringilla malesuada nec a turpis. Nunc quam magna, sollicitudin non condimentum ut, lobortis sit amet purus. Mauris sit amet libero imperdiet magna commodo posuere vitae at mauris. In hendrerit velit et magna interdum mollis adipiscing metus vulputate. Phasellus metus libero, pulvinar vitae aliquam eget, rhoncus sed felis. Aliquam erat volutpat. Sed lobortis dui nec sapien tempus imperdiet. Sed convallis aliquam erat, nec porta augue ultrices et. Sed nec mauris in nulla imperdiet vehicula. Duis sed tellus massa, at imperdiet purus. Nulla ultrices rutrum tortor feugiat tincidunt.',	NULL,	4,	'2012-05-11 00:00:00'),
(6,	'Mluvnice',	'Mluvnice\r\n########\r\nDonec scelerisque aliquet justo, et vulputate diam pharetra sed. Mauris metus quam, malesuada vitae ultricies in, lobortis sit amet lectus. Proin dapibus bibendum eros, id lacinia ipsum suscipit sed. Nunc auctor congue pretium. Nullam et dui a tellus tristique blandit. Duis id nisi eu urna venenatis bibendum dignissim eget tellus. Donec velit libero, adipiscing ut tempor vitae, fringilla sit amet dui. Phasellus urna arcu, tempus blandit adipiscing sed, faucibus ac mi. Nulla tempor molestie lorem eu ultricies. Sed pulvinar, nisi id dignissim congue, diam arcu auctor massa, id sollicitudin nibh neque vitae metus.\r\n\r\nIn commodo lobortis sem sit amet imperdiet. Praesent scelerisque tellus sed nisl faucibus faucibus in ut magna. Nunc pulvinar dictum aliquam. Curabitur id volutpat libero. Donec aliquam commodo lorem, eu congue nibh pharetra sed. Curabitur feugiat erat sed orci malesuada elementum. Etiam cursus, mi sed cursus cursus, arcu lectus commodo mauris, vitae aliquam neque magna eu ante.\r\n- jedna\r\n- dve\r\n- tri',	NULL,	6,	'2012-05-21 00:00:00'),
(7,	'Sloh',	'Sloh\r\n####\r\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque enim orci, porta non laoreet et, interdum at lorem. Nam id nunc eu arcu pellentesque convallis. Vivamus sagittis tincidunt sem, vel sagittis mauris lobortis pretium. Donec consequat venenatis urna, in sagittis ante fermentum commodo. Sed eget nibh sit amet ante mattis sodales. Phasellus eget diam diam. Sed eget dolor id leo egestas consequat. Aenean pulvinar malesuada ipsum non euismod. In orci lorem, adipiscing eu rutrum vel, pulvinar sed diam. Nunc volutpat blandit erat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Aliquam tempor tortor pulvinar turpis tempus et posuere libero condimentum. Duis sollicitudin fringilla libero, ac tincidunt arcu aliquam nec.',	NULL,	6,	'2012-05-22 00:00:00'),
(8,	'Prvni hodina',	'Casy minule\r\n###########\r\n\r\nIn justo odio, euismod non congue ac, pulvinar non ante. Duis eu felis consectetur nisi lacinia ultricies. Integer quis mi non orci vulputate lacinia. Proin vel diam sit amet nulla vehicula fringilla in dapibus neque. Duis posuere ornare aliquam. Fusce cursus est a enim ullamcorper ac bibendum justo condimentum. Integer ultricies, arcu eu molestie fermentum, metus justo sagittis erat, sit amet consequat orci leo non nisl.\r\n\r\nSed fringilla magna a tortor fermentum mollis non et sem. Donec pulvinar, nisl eget rutrum tempor, lectus nibh luctus lorem, sed volutpat orci augue at risus. Aenean dignissim lorem nec neque congue vulputate. Aenean nec tortor vitae arcu vestibulum accumsan at sed ipsum. Morbi a turpis purus. Etiam pellentesque diam arcu, quis vestibulum neque. Aenean nec fermentum lacus. Morbi ac dui eu ipsum faucibus vehicula eu eget mauris. Suspendisse fringilla ante vel turpis vehicula ac eleifend orci ullamcorper. Aenean adipiscing iaculis est, eu ornare turpis tristique in. Ut varius sodales libero eu molestie. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Cras fringilla massa in ligula iaculis vel mollis nunc condimentum. Maecenas sed bibendum dolor. Etiam varius, sem et vulputate facilisis, ante mi consequat sapien, et ullamcorper sapien nisl a enim. Pellentesque vehicula sodales lacinia.',	NULL,	7,	'2012-05-22 00:00:00');

INSERT INTO `mail` (`id`, `to`, `subject`, `msg`, `sent`) VALUES
(69,	'jakub@kinst.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://localhost/CourseMan/document_root/user/check?hash=204bb152ffd93adfc22f1ae5cbe1af9f753ea818\">http://localhost/CourseMan/document_root/user/check?hash=204bb152ffd93adfc22f1ae5cbe1af9f753ea818</a>.',	'2012-04-06 11:27:35'),
(70,	'evakufnerova@seznam.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=97bb2f05ae3873555ff4c4e12f2cfd84c81748f3\">http://rp-jakub-cz.dc.starver.net/user/check?hash=97bb2f05ae3873555ff4c4e12f2cfd84c81748f3</a>.',	'2012-04-06 11:38:38'),
(71,	'evakufnerova@seznam.cz',	'You have been invited to Matematika I',	'Hi, you have been invited to course <b>Matematika I</b> at\n		CourseManager by Jakub Kinst. To accept invitation register at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a> with this e-mail address.',	'2012-04-06 11:45:04'),
(72,	'pavel.lauko@gmail.com',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=5a8af09dbfd9e1e23ec4f8cbe65b6b96e7a5fcab\">http://rp-jakub-cz.dc.starver.net/user/check?hash=5a8af09dbfd9e1e23ec4f8cbe65b6b96e7a5fcab</a>.',	'2012-04-25 11:06:15'),
(73,	'jerry90@gmail.com',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=52b0eec911b13a67c7b6170fdcbbe395b4bf9588\">http://rp-jakub-cz.dc.starver.net/user/check?hash=52b0eec911b13a67c7b6170fdcbbe395b4bf9588</a>.',	'2012-05-02 17:06:05'),
(74,	'ostan89@gmail.com',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=bce5c6afb6e1740f3cd7a342dea412a04c9b6924\">http://rp-jakub-cz.dc.starver.net/user/check?hash=bce5c6afb6e1740f3cd7a342dea412a04c9b6924</a>.',	'2012-05-09 22:49:06'),
(75,	'milena@kinst.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=ff238a56b13fb2253c2f0590202a4787342edfdb\">http://rp-jakub-cz.dc.starver.net/user/check?hash=ff238a56b13fb2253c2f0590202a4787342edfdb</a>.',	'2012-05-11 16:41:09'),
(76,	'jirka@kinst.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=cb34e14d47ab85b724aa76b479439953f51600a8\">http://rp-jakub-cz.dc.starver.net/user/check?hash=cb34e14d47ab85b724aa76b479439953f51600a8</a>.',	'2012-05-11 16:46:05'),
(77,	'josef@kinst.cz',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=f5de62e7a252c43b92238853a725b072e9903cab\">http://rp-jakub-cz.dc.starver.net/user/check?hash=f5de62e7a252c43b92238853a725b072e9903cab</a>.',	'2012-05-11 16:46:08'),
(78,	'josef@kinst.cz',	'You have been invited to Kurz vyšívání',	'Hi, you have been invited to course <b>Kurz vyšívání</b> at\n		CourseManager by Milena Kinstova. To accept invitation register at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a> with this e-mail address.',	'2012-05-11 16:48:04'),
(79,	'josef@kinst.cz',	'New Assignment added to Kurz vyšívání',	'There is a new assignment called Test in your course <b>Kurz vyšívání</b><br />\n	    You can check it at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a>.',	'2012-05-16 20:00:06'),
(80,	'tomasgrosup@gmail.com',	'Complete your registration at CourseManager',	'Welcome to CourseManager. Your registration is almost complete. To cemplete the\n		registration process, click to this link: <a href=\"http://rp-jakub-cz.dc.starver.net/user/check?hash=c25233bfbd39faa1bab8dbccf9264eb82e1479e9\">http://rp-jakub-cz.dc.starver.net/user/check?hash=c25233bfbd39faa1bab8dbccf9264eb82e1479e9</a>.',	'2012-05-17 23:01:05'),
(81,	'evakufnerova@seznam.cz',	'New Assignment added to Matematika I',	'There is a new assignment called Test in your course <b>Matematika I</b><br />\n	    You can check it at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a>.',	'2012-05-17 23:11:04'),
(82,	'josef@kinst.cz',	'Notification of upcoming assignment duedate',	'Assignment <b>Test</b> duedate is on <b>2012-05-24 00:00:00</b>. You can check it at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a>.',	'2012-05-18 10:01:06'),
(83,	'josef@kinst.cz',	'You have been invited to Cesky jazyk I',	'Hi, you have been invited to course <b>Cesky jazyk I</b> at\n		CourseManager by Milena Kinstova. To accept invitation register at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a> with this e-mail address.',	'2012-05-21 17:56:06'),
(84,	'jirka@kinst.cz',	'You have been invited to Cesky jazyk I',	'Hi, you have been invited to course <b>Cesky jazyk I</b> at\n		CourseManager by Milena Kinstova. To accept invitation register at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a> with this e-mail address.',	'2012-05-21 17:56:08'),
(85,	'jirka@kinst.cz',	'New Assignment added to Cesky jazyk I',	'There is a new assignment called Tvrde a mekke i/y in your course <b>Cesky jazyk I</b><br />\n	    You can check it at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a>.',	'2012-05-21 18:09:07'),
(86,	'josef@kinst.cz',	'New Assignment added to Cesky jazyk I',	'There is a new assignment called Tvrde a mekke i/y in your course <b>Cesky jazyk I</b><br />\n	    You can check it at <a href=\"http://rp-jakub-cz.dc.starver.net/\">http://rp-jakub-cz.dc.starver.net/</a>.',	'2012-05-21 18:09:10');

INSERT INTO `message` (`id`, `subject`, `content`, `read`, `sent`, `from`, `to`, `reply_to_id`) VALUES
(6,	'Konzultace',	'Dostavte se prosim do meho kabinetu v pondeli v 15:30.\r\n\r\nDekuji',	1,	'2012-05-21 18:12:21',	20,	22,	NULL),
(7,	'Re: Konzultace',	'>Dostavte se prosim do meho kabinetu v pondeli v 15:30.\r\n>\r\n>Dekuji\r\n\r\nBudu tam, dekuji.',	1,	'2012-05-21 18:13:15',	22,	20,	NULL);

INSERT INTO `offlinetask` (`id`, `name`, `date`, `Course_id`, `maxpoints`, `grade`) VALUES
(1,	'Diktat duben',	NULL,	6,	100,	0),
(2,	'DU 3',	NULL,	6,	0,	1);

INSERT INTO `onlinesubmission` (`id`, `started`, `submitted`, `User_id`, `Assignment_id`, `points`) VALUES
(1,	'2012-05-17 22:41:01',	NULL,	15,	2,	21);

INSERT INTO `question` (`id`, `Assignment_id`, `type`, `label`, `choices`, `rightanwser`) VALUES
(1,	2,	'text',	'2-3=',	NULL,	NULL),
(2,	2,	'radio',	'3+43',	'43#44#45#46',	NULL),
(4,	7,	'text',	'Otazka 1',	NULL,	NULL),
(5,	7,	'text',	'Otazka 2',	NULL,	NULL),
(6,	8,	'radio',	'gggg',	'a#b#c#d#e#f#g#h',	'dd'),
(7,	9,	'text',	'Test1',	NULL,	NULL),
(8,	9,	'radio',	'Yyy',	'R#T',	NULL),
(9,	10,	'radio',	'Trapi ho dobre b*dlo',	'i#y',	'y'),
(10,	10,	'radio',	'Kluci, neb*jte se',	'i#y',	'i');

INSERT INTO `reply` (`id`, `content`, `created`, `Topic_id`, `User_id`, `Reply_id`) VALUES
(1,	'Pouze 3',	'2012-05-11 16:54:23',	1,	20,	NULL),
(2,	'jen 3 ?',	'2012-05-11 17:13:54',	1,	22,	NULL),
(6,	'Bizon',	'2012-05-21 18:02:01',	4,	21,	NULL);

INSERT INTO `resource` (`id`, `name`, `description`, `filename`, `size`, `added`, `Lesson_id`, `Course_id`) VALUES
(1,	'bp_uprava.pdf',	NULL,	'03d1d9bacfa9f97d093c0037425c3350_bp_uprava.pdf',	164682,	'2012-04-06 12:29:49',	NULL,	1),
(2,	'info.php',	NULL,	'22c08debaf81d6e4889872470bbcbdce_info.php',	72,	'2012-05-17 23:21:14',	NULL,	5),
(3,	'info.php',	NULL,	'22c08debaf81d6e4889872470bbcbdce_info.php',	72,	'2012-05-17 23:21:14',	NULL,	5),
(4,	'oranzova karolinka.pdf',	NULL,	'20efe700f8107db1c19a88622775651b_oranzova karolinka.pdf',	1189802,	'2012-05-21 17:58:09',	6,	6),
(5,	'teach_your_self_java_in_21_days.pdf',	NULL,	'0e32d0a261a333880c270c3ac9c8dac1_teach_your_self_java_in_21_days.pdf',	6039939,	'2012-05-21 18:05:47',	NULL,	7);

INSERT INTO `result` (`id`, `points`, `User_id`, `OfflineTask_id`) VALUES
(1,	90,	21,	1),
(2,	78,	22,	1),
(3,	1,	21,	2),
(4,	2,	22,	2);

INSERT INTO `settings` (`User_id`, `showEmail`, `lang`, `assignment_notif_interval`) VALUES
(15,	1,	'en',	5),
(20,	1,	'en',	5),
(21,	1,	'en',	5),
(22,	1,	'en',	5);

INSERT INTO `student` (`Course_id`, `User_id`) VALUES
(6,	21),
(4,	22),
(6,	22);

INSERT INTO `teacher` (`Course_id`, `User_id`) VALUES
(1,	15),
(2,	15),
(4,	20),
(6,	20),
(7,	21);

INSERT INTO `topic` (`id`, `label`, `content`, `created`, `Course_id`, `User_id`) VALUES
(1,	'Kolik absencí je povoleno ?',	'Dobrý den, chtěl bych se zeptat, kolik absencí můžu mít, aby mi byl předmět uznán. Díky',	'2012-05-11 16:54:11',	4,	20),
(2,	'Pomoc',	'Pomuze mi prosim nekdo s lekci 1',	'2012-05-11 17:14:24',	4,	22),
(4,	'Bizon nebo Byzon ?',	'Nevite jake i/y ?\r\nDiky',	'2012-05-21 18:00:27',	6,	22);

INSERT INTO `user` (`id`, `email`, `password`, `firstname`, `lastname`, `created`, `seclink`, `checked`, `facebook`, `icq`, `phone`, `web`, `apiKey`) VALUES
(15,	'jakub@kinst.cz',	'c7290eb6d8dc2fc4edc97cc1808bbe02',	'Jakub',	'Kinst',	'2012-04-06 11:27:15',	'204bb152ffd93adfc22f1ae5cbe1af9f753ea818',	1,	NULL,	NULL,	NULL,	'http://jakub.kinst.cz',	NULL),
(20,	'milena@kinst.cz',	'48285406fe430d1389ccc284c1b1353d',	'Milena',	'Kinstova',	'2012-05-11 16:40:46',	'ff238a56b13fb2253c2f0590202a4787342edfdb',	1,	NULL,	NULL,	NULL,	'',	NULL),
(21,	'jirka@kinst.cz',	'9a6f796e1c7de0282a61a460011fd8dc',	'Jirka',	'Kinst',	'2012-05-11 16:45:01',	'cb34e14d47ab85b724aa76b479439953f51600a8',	1,	NULL,	NULL,	NULL,	'',	NULL),
(22,	'josef@kinst.cz',	'78adc74c3492df9662d09c5a17b93168',	'Josef',	'Kinst',	'2012-05-11 16:45:30',	'f5de62e7a252c43b92238853a725b072e9903cab',	1,	NULL,	NULL,	NULL,	'',	NULL);

-- 2012-05-21 18:15:10
