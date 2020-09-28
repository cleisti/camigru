<?php
// include 'config/connect.php';

function    fetch_email($username) {
	$pdo = connect();
	$query = "SELECT email FROM users WHERE username = :username;";
	$stmt = $pdo->prepare($query);
	$stmt->execute(array(':username' => $username));
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	$email = $res['email'];

	return ($email);
}

function	fetch_uId($username) {
	$pdo = connect();

	$query = "SELECT user_id FROM users WHERE username = :username;";
	$stmt = $pdo->prepare($query);
	$stmt->execute(array(':username' => $username));
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	$id = $res['user_id'];

	return $id;
}

function    user_exists($email, $username) {

	$pdo = connect();
	
	if ($email) {
		try {
			$query = "SELECT email FROM users
					WHERE email = :email;";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array(':email' => $email));
			$res = $stmt->fetch();
		}
		catch (PDOException $e) {
			echo "ERROR: " . $e->getMessage;
		}
		if ($res) {
			return("This email is already in use on another account.");
		}
	}
	
	if ($username) {
		try {
			$query = "SELECT username FROM users
					WHERE username = :username;";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array(':username' => $username));
			$res = $stmt->fetch();
		}
		catch (PDOException $e) {
			echo "ERROR: " . $e->getMessage;
		}
		if ($res) {
			return("User already exists.");
		}
	}
	return FALSE;
}

function    input_is_valid($email, $username, $passwd, $validate_pw) {

	if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
		echo "Invalid email.";
		return (0);
	}       

	if ($username && preg_match('/[^a-zA-Z0-9_]/', $username)) {
		echo "Username contains illegal characters.<br>Allowed characters are alphabetical characters, numerical characters and _.";
		return (0);
	}

	if ($passwd && strcmp($passwd, $validate_pw) === 0) {
		if (!preg_match('/^(?=.*\d)(?=.*[a-zA-Z])/', $passwd)) {
			echo "Password must contain at least one alphabetical character and one number.";
			return (0);
		}
	}
	else if ($passwd) {
		echo "Password doesn't match validation. Try again.";
		return (0);
	}
	return (1);
}
?>