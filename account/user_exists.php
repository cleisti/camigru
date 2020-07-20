<?php

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
			echo "This email is already in use on another account.";
			return TRUE;
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
			echo "User already exists.";
			return TRUE;
		}
	}
	return FALSE;
}

?>