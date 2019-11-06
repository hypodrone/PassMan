SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;

USE legionix_passman;

DROP TABLE IF EXISTS `passmandata`;
DROP TABLE IF EXISTS `passmanusers`;

CREATE TABLE `passmanusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) NOT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `created` DATETIME NOT NULL,
  `last_login` DATETIME DEFAULT NULL,
  `resettoken` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `passmandata` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `service` varchar(255) NOT NULL UNIQUE,
  `srvpsswd` varchar(255) DEFAULT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `passmanusers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

COMMIT;
