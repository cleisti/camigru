<?php

function    fetch_email($username) {
	$pdo = connect();
	$fetch_email = "SELECT email FROM users WHERE username = :username;";
	$stmt = $pdo->prepare($fetch_email);
	$stmt->execute(array(':username' => $username));
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	$email = $res['email'];
	return ($email);
}

function    user_exists($email, $username, $pdo) {
	
	if ($email) {
		try {
			$check_email = "SELECT email FROM users
					WHERE email = :email;";

			$stmt = $pdo->prepare($check_email);
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
			$check_user = "SELECT username FROM users
					WHERE username = :username;";

			$stmt = $pdo->prepare($check_user);
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

	if ($passwd && $passwd === $validate_pw) {
		if (!preg_match('/^(?=.*\d)(?=.*[a-zA-Z])/', $passwd)) {
			echo "Password must contain at least one alphabetical character and one number.";
			return (0);
		}
	}
	else {
		echo "Password doesn't match validation. Try again.";
		return (0);
	}
	return (1);
}

function	fetch_uId($username) {
	$pdo = connect();
	$get_id = "SELECT user_id FROM users WHERE username = :username;";
	$stmt = $pdo->prepare($get_id);
	$stmt->execute(array(':username' => $username));
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	$id = $res['user_id'];
	return $id;
}

?>