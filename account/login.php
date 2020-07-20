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
			<input class="button" type="submit" name="connect" value="Log in" />
		</form>
		<a href="index.php?page=reset_pass">Forgot your password?</a>
	</body>
</html>

<?php
	include '../config/connect.php';
	session_start();

	$submit = $_POST['connect'];
	$username = filter_var($_POST['login'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
	$passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

	function    auth($username, $passwd, $pdo) {
		try {
			$get_hash = "SELECT `password` FROM users
						WHERE username = :username;";

			$stmt = $pdo->prepare($get_hash);
			$stmt->execute(array(':username' => $username));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$hash = $res['password'];

			if (password_verify($passwd, $hash)) {
				return TRUE;
			}
			else {
				return FALSE;
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
	}

	if ($submit === 'Log in') {
		
		$pdo = connect();

		if (auth($username, $passwd, $pdo) === TRUE) {
			$_SESSION['logged_user'] = $username;
			header("Location: index.php");
		}
		else {
			$_SESSION['logged_user'] = "";
			?>
			<p>ERROR: Wrong username or password.</p>
			<?php
		}
	}
?>