<?php
include_once 'config/connect.php';

if (!isset($_SESSION))
	session_start();

if (isset($_POST['username'])) {
	echo json_encode(get_user($_SESSION['logged_user']));
}

function	get_user($username) {
	$pdo = connect();
	$query = "SELECT * FROM users WHERE username = :username;";
	$stmt = $pdo->prepare($query);
	$stmt->execute(array(':username' => $username));
	$res = $stmt->fetch(PDO::FETCH_ASSOC);

	return $res;
}

?>