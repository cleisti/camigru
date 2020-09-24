<?php
	include 'database.php';

	function firstConnect() {
		global $DB_DSN_FIRST, $DB_USER, $DB_PASSWORD;
		try {
			$connection = new PDO($DB_DSN_FIRST, $DB_USER, $DB_PASSWORD);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return ($connection);
		}
		catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}

	function connect() {
		global $DB_DSN, $DB_USER, $DB_PASSWORD;
		try {
			$connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return ($connection);
		}
		catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
	}
?>