<?php
	include_once 'connect.php';

	try {
		$pdo = firstConnect();
		$database = "CREATE DATABASE camigru; USE camigru;";
		$pdo->exec($users . $images . $likes . $comments);
	}
	catch (PDOException $e) {
		echo 'ERROR: ' . $e->getMessage;
	}

	$pdo = NULL;

	try {
		$pdo = connect();
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
		`created` DATETIME NOT NULL,
		PRIMARY KEY (`img_id`)
	);";

	$likes = "CREATE TABLE IF NOT EXISTS likes (
		`like_id` INT NOT NULL AUTO_INCREMENT,
		`like_user_id` INT NOT NULL,
		`like_img_id` INT NOT NULL,
		PRIMARY KEY (`like_id`)
	);";

	$comments = "CREATE TABLE IF NOT EXISTS comments (
		`comment_id` INT NOT NULL AUTO_INCREMENT,
		`comment_user_id` INT NOT NULL,
		`comment_img_id` INT NOT NULL,
		`comment` VARCHAR(255) NOT NULL,
		`date` DATETIME NOT NULL,
		PRIMARY KEY (`comment_id`)
	);";
	
	$pdo->exec($users . $images . $likes . $comments);
	}
	catch (PDOException $e) {
		echo 'ERROR: ' . $e->getMessage;
	}

?>