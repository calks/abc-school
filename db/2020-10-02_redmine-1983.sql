CREATE TABLE `classwork` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schedule_entry_id` int(11) NOT NULL,
  `schedule_entry_date` date DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `con_classwork_01` (`schedule_entry_id`),
  CONSTRAINT `con_classwork_01` FOREIGN KEY (`schedule_entry_id`) REFERENCES `user_group_schedule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;