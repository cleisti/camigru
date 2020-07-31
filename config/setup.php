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
		`profile_pic` VARCHAR(255) NOT NULL DEFAULT '0',
		PRIMARY KEY (`user_id`)
	);";

	$images = "CREATE TABLE IF NOT EXISTS images (
		`img_id` INT NOT NULL AUTO_INCREMENT,
		`img_user_id` INT NOT NULL,
		`path` VARCHAR(255) NOT NULL,
		PRIMARY KEY (`img_id`)
	);";

	$pdo->exec($database . $users . $images);

?>