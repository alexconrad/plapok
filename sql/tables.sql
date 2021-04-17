CREATE TABLE `rooms` (
	`room_id` INT(10) NOT NULL AUTO_INCREMENT,
	`room_key` CHAR(6) NOT NULL DEFAULT '0' COLLATE 'latin1_swedish_ci',
	`is_resetting` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`room_id`) USING BTREE,
	UNIQUE INDEX `roomKey` (`room_key`) USING BTREE
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB;

CREATE TABLE `people` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`room_id` INT(10) UNSIGNED NOT NULL,
	`name` VARCHAR(50) NOT NULL COLLATE 'latin1_swedish_ci',
	`participant_status` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	`number` CHAR(1) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
	`ack_reset` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id`) USING BTREE,
	UNIQUE INDEX `room_id_name` (`room_id`, `name`) USING BTREE
)
ENGINE=InnoDB;
