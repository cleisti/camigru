<?php
	session_start();
	$_SESSION['logged_user'] = "";
	$_SESSION['user_id'] = "";
	unset($_SESSION);
	header('Location: index.php?page=login');
?>