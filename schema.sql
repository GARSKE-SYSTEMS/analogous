CREATE TABLE IF NOT EXISTS `collector` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `server_id` int(11) NOT NULL,
  `name` tinytext NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collector_id` int(11) NOT NULL,
  `content` varchar(4096) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext NOT NULL,
  `ip` tinytext NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collector_id` int(11) NOT NULL,
  `content` tinytext NOT NULL,
  `created_at` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` tinytext NOT NULL,
  `password` text NOT NULL,
  `created_on` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);
