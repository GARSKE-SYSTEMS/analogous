CREATE TABLE IF NOT EXISTS `collectors` (
  `id` INTEGER PRIMARY KEY
    /* sqlite auto-inc */
    /*!40000 AUTO_INCREMENT */,
  `server_id` INTEGER NOT NULL,
  `name` TEXT NOT NULL
) /*!40100 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 */;

CREATE TABLE IF NOT EXISTS `lines` (
  `id` INTEGER PRIMARY KEY
    /* sqlite auto-inc */
    /*!40000 AUTO_INCREMENT */,
  `collector_id` INTEGER NOT NULL,
  `content` TEXT NOT NULL
) /*!40100 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 */;

CREATE TABLE IF NOT EXISTS `servers` (
  `id` INTEGER PRIMARY KEY
    /* sqlite auto-inc */
    /*!40000 AUTO_INCREMENT */,
  `name` TEXT NOT NULL,
  `ip` TEXT NOT NULL
) /*!40100 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 */;

CREATE TABLE IF NOT EXISTS `tokens` (
  `id` INTEGER PRIMARY KEY
    /* sqlite auto-inc */
    /*!40000 AUTO_INCREMENT */,
  `collector_id` INTEGER NOT NULL,
  `content` TEXT NOT NULL,
  `created_at` INTEGER NOT NULL
) /*!40100 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 */;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INTEGER PRIMARY KEY
    /* sqlite auto-inc */
    /*!40000 AUTO_INCREMENT */,
  `username` TEXT NOT NULL,
  `password` TEXT NOT NULL,
  `created_on` INTEGER NOT NULL
) /*!40100 ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 */;
