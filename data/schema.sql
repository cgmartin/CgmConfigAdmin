CREATE TABLE IF NOT EXISTS `configadminvalue` (
  `configvalue_id` varchar(255) PRIMARY KEY NOT NULL,
  `value`          text         NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
