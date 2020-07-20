<!DOCTYPE html>
<html>
<head>
</head>
    <body>
        <h2>Change password</h2>
        <form action="" method="post">
            Old password: <input type="password" name="old_pw" minlength="8" maxlength="20" value="" required />
			<br />
			New password: <input type="password" name="new_pw" minlength="8" maxlength="20" value="" required />
			<br />
            Validate password: <input type="password" name="validate_pw" minlength="8" maxlength="20" value="" required />
            <input class="button" type="submit" name="submit" value="Change" />
        </form>
        <br />
    </body>
</html>

<?php
	include '../config/connect.php';
	session_start();

	$info['submit'] = $_POST['submit'];
	$info['old_pw'] = filter_var($_POST['old_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	$info['new_pw'] = filter_var($_POST['new_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	$info['validate_pw'] = filter_var($_POST['validate_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	$info['user'] = $_SESSION['logged_user'];

	function		change_password($info, $pdo) {
		try {
			$get_pass = "SELECT password FROM users
						WHERE username = :username;";
			$stmt = $pdo->prepare($get_pass);
			$stmt->execute(array(':username' => $info['user']));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$pass = $res['password'];
		
			if (password_verify($info['old_pw'], $pass)) {
				$hashed = password_hash($info['new_pw'], PASSWORD_DEFAULT);
				$change_pass = "UPDATE users
								SET password = :password
								WHERE username = :username;";
				$stmt = $pdo->prepare($change_pass);
				$stmt->execute(array(':password' => $hashed, ':username' => $info['user']));
				echo "Password successfully changed.";
			}
			else {
				echo "Wrong password";
				exit;
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
	}

	if ($info['submit'] === 'Change' && $info['old_pw'] && $info['new_pw'] && $info['validate_pw'] && $info['user']) {
		$pdo = connect();

		if ($info['new_pw'] === $info['validate_pw']) {
			change_password($info, $pdo);
			unset($_POST);
			unset($info);
		}
		else {
			echo "New password doesn't match validation.";
		}
	}
?>