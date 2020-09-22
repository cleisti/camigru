<?php
	session_start();
	$_SESSION['logged_user'] = "";
	$_SESSION['user_id'] = "";
	session_destroy();
	header("refresh:1;url=index.php?page=account/login");
?>