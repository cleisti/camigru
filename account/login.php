<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<h2>Login</h2>
		<form action="" method="post">
			Username: <input type="text" name="login" value="" />
			<br />
			Password: <input type="password" name="passwd" value="" />
			<input class="button" type="submit" name="connect" value="Log in" />
		</form>
	</body>
</html>

<?php
	include '../config/connect.php';
	session_start();

	$pdo = connect();

	function    auth($login, $passwd) {
		$hashed = hash(whirlpool, $passwd);
		$sql_validate = "SELECT `username`, `password` FROM users
					WHERE `username` = " . $pdo->quote($login) . "
					AND `password` = " . $pdo->quote($hashed) . "
					LIMIT 1;";

		$que = $pdo->query($sql_validate);

		$res = $que->fetch(PDO::FETCH_ASSOC);

		if ($res !== FALSE) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	if (auth($_POST['login'], $_POST['passwd']) === TRUE) {
		$_SESSION['logged_user'] = $_POST['login'];
		header("Location: index.php");
	}
	else {
		$_SESSION['logged_user'] = "";
		?>
		<p>ERROR: Wrong username or password.</p>
		<?php
	}
?>