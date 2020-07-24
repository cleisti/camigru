<?php
	include 'connect.php';
	$pdo = connect();

	$database = "CREATE DATABASE IF NOT EXISTS camigru; USE camigru;";

	$users = "CREATE TABLE IF NOT EXISTS users (
		`user_id` INT NOT NULL AUTO_INCREMENT,
		`email` VARCHAR(100) NOT NULL,
		`username` VARCHAR(25) NOT NULL,
		`password` VARCHAR(255) NOT NULL,
		`verified` TINYINT(1) NOT NULL DEFAULT '0',
		`token` VARCHAR(255) DEFAULT NULL,
		`reset` VARCHAR(255) DEFAULT NULL,
		`profile_pic` VARCHAR(80) NOT NULL,
		PRIMARY KEY (`user_id`)
	);";

	$pdo->exec($database . $users);

?>