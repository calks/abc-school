CREATE TABLE  `user_marks` (
  `user_id` int(11) NOT NULL,
  `schedule_entry_id` int(11) NOT NULL,
  `schedule_entry_date` date NOT NULL DEFAULT '0000-00-00',
  `mark` integer,
  `comment` text,
  PRIMARY KEY (`user_id`,`schedule_entry_id`,`schedule_entry_date`),
  KEY `con_user_attendance_02` (`schedule_entry_id`),
  CONSTRAINT `con_user_marks_01` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `con_user_marks_02` FOREIGN KEY (`schedule_entry_id`) REFERENCES `user_group_schedule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;