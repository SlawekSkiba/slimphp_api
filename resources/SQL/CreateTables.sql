CREATE TABLE IF NOT EXISTS `positions` (
	`id` int(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,    
    `is_admin` boolean NOT NULL DEFAULT 0,    
PRIMARY KEY (`id`),
UNIQUE KEY `positions_name` (`name`)
);

CREATE TABLE IF NOT EXISTS `users` (
	`id` int(20) NOT NULL AUTO_INCREMENT,
	`email` varchar(50) NOT NULL,    
    `firstName` varchar(50) NOT NULL,
    `lastName` varchar(50) NOT NULL,
    `position_id` int(20) NOT NULL,
    `password` varchar(64) NOT NULL,
    `loginAttempts` int(20) NOT NULL DEFAULT 0,
    `salt` varchar(36) NOT NULL,
PRIMARY KEY (`id`),
CONSTRAINT FK_Position_Id FOREIGN KEY (position_id) REFERENCES positions(id),
CONSTRAINT UC_Users_Email UNIQUE (email)
);

CREATE TABLE IF NOT EXISTS `absence_types` (
    `id` int(20) NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,    
    `allowed_days` int not null DEFAULT 30,
    PRIMARY KEY (`id`),
    CONSTRAINT UC_abs_types_name UNIQUE (name) 
);

CREATE TABLE IF NOT EXISTS `absence_plan` (
	`id` int(20) NOT NULL AUTO_INCREMENT,
	`user_id` int(20) NOT NULL,
	`date_from` date NOT NULL,    
    `date_to` varchar(50) NOT NULL,
    `absence_type_id` int(20) NOT NULL,
PRIMARY KEY (`id`),
CONSTRAINT FK_AbsencePlan_User_Id FOREIGN KEY (user_id) REFERENCES users(id),
CONSTRAINT FK_AbsencePlan_Type_Id FOREIGN KEY (absence_type_id) REFERENCES absence_types(id),
CONSTRAINT FK_AbsencePlan_Unique UNIQUE (user_id, date_from, date_to)
);