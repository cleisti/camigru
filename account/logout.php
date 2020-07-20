<?php
	session_start();
	$_SESSION['logged_user'] = "";
	unset($_SESSION);
	header('Location: index.php');
?>