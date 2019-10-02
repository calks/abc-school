ALTER TABLE `user` MODIFY COLUMN `role` ENUM('admin','manager','teacher','student', 'director') DEFAULT NULL;
