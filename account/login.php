<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<h2>Log in</h2>
		<form action="" method="post">
			Username: <input type="text" name="login" value="" />
			<br />
			Password: <input type="password" name="passwd" value="" />
			<input type="submit" name="connect" value="Log in" />
		</form>
		<a href="index.php?page=account/reset_pass">Forgot your password?</a>
	</body>
</html>

<?php
	include '../config/connect.php';
	session_start();

	$submit = $_POST['connect'];

	function    auth($username, $passwd, $pdo) {
		try {
			$get_hash = "SELECT `password`, `verified` FROM users
						WHERE username = :username;";

			$stmt = $pdo->prepare($get_hash);
			$stmt->execute(array(':username' => $username));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$v = $res['verified'];
			$hash = $res['password'];
			$stmt->close();

			if ($v !== '1') {
				echo "You haven't activated your account yet.<br>Follow the link that has been sent to your email or<br>send a new link.";
				return FALSE;
			}
			else if (!password_verify($passwd, $hash)) {
				echo "Wrong username or password.";
				return FALSE;
			}
			else {
				return TRUE;
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
	}

	if ($submit === 'Log in' && isset($_POST['login']) && isset($_POST['passwd'])) {
		
		$pdo = connect();

		$username = filter_var($_POST['login'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

		if (auth($username, $passwd, $pdo) === TRUE) {
			$_SESSION['logged_user'] = $username;
			header("Location: index.php");
		}
		else {
			$_SESSION['logged_user'] = "";
		}
	}
?>