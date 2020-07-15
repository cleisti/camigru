<?php
	include 'database.php';
	function connect() {
		global $DB_DSN, $DB_USER, $DB_PASSWORD;
		try {
			$connection = new PDO($DB_DSN, $DB_USER, $DB_PASSWORD);
			$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch (PDOException $e) {
			echo 'Connection failed: ' . $e->getMessage();
		}
		return ($connection);
	}
?>