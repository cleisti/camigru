<?php
	include_once '../config/connect.php';
	include_once 'validation.php';
	session_start();

	function	update_username($info) {

		if ($info['new_un'] === $info['user'])
			return ("This already is your username . . .");
		else if (!$mess = user_exists(NULL, $info['new_un'])) {
			if (input_is_valid(NULL, $info['new_un'], NULL, NULL)) {
				$pdo = connect();

				try {
					$query = "UPDATE `users` SET `username` = :new_un WHERE `username` = :user;";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array(':new_un' => $info['new_un'], ':user' => $info['user']));
		
					$email = $info['email'];
					$subject = "Username changed";
					$content = 'Your username has been changed to ' . $info['new_un'] . '. If this was not you, please contact support.';
					$headers = 'From: admin@camigru.com' . "\r\n";
					mail($email, $subject, $content, $headers);

					$_SESSION['logged_user'] = $info['new_un'];
					if ($stmt)
						return "Username changed";
					else
						return "Fail";
				}
				catch (PDOException $e) {
					echo "ERROR: " . $e->getMessage();
				}
			}
		}
		else
			return $mess;
	}

	function	update_email($info) {

		if (strcmp($info['new_email'], $info['validate_email']) !== 0)
			return ("Mail address does not match validation.");
		else if (!$mess = user_exists($info['new_email'], NULL)) {
			if (input_is_valid($info['new_email'], NULL, NULL, NULL)) {
				$pdo = connect();
		
				try {
					$query = "UPDATE `users` SET `email` = :email WHERE `user_id` = :userId;";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array(':email' => $info['new_email'], ':userId' => $info['user_id']));
					return "Email changed.";
				}
				catch (PDOException $e) {
					echo "ERROR: " . $e->getMessage();
				}
			}
		}
		else
			return $mess;
	}

	function		update_password($info) {

		if (input_is_valid(NULL, NULL, $info['new_pw'], $info['validate_pw'])) {
			$pdo = connect();

			try {
				$query = "SELECT `password` FROM `users`
							WHERE `username` = :username;";
				$stmt = $pdo->prepare($query);
				$stmt->execute(array(':username' => $info['user']));
				$res = $stmt->fetch(PDO::FETCH_ASSOC);
				$pass = $res['password'];
			
				if (password_verify($info['old_pw'], $pass)) {
					$hashed = password_hash($info['new_pw'], PASSWORD_DEFAULT);
					$query = "UPDATE `users`
									SET `password` = :password
									WHERE `username` = :username;";
					$stmt = $pdo->prepare($query);
					$stmt->execute(array(':password' => $hashed, ':username' => $info['user']));
					return ("Password successfully changed.");
				}
				else
					return ("Incorrect password.");
			}
			catch (PDOException $e) {
				echo "ERROR: " . $e->getMessage();
			}
		}
	}

	function	get_checkboxinfo($info) {
		$pdo = connect();

		$query = "SELECT `verified` FROM `users` WHERE `user_id` = :userId;";
		$stmt = $pdo->prepare($query);
		$stmt->execute(array(':userId' => $info['user_id']));
		$res = $stmt->fetchColumn();

		return $res;
	}

	function	change_notifications($info) {
		$pdo = connect();

		$res = get_checkboxinfo($info);

		$v = ($res == 1) ? 2: 1;
		$query = "UPDATE `users` SET `verified` = :v WHERE `user_id` = :userId;";
		$stmt = $pdo->prepare($query);
		$stmt->execute(array(':v' => $v, ':userId' => $info['user_id']));

		return $res;
	}
?>