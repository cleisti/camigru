<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<div class="d-flex p-2 justify-content-center align-content-around">
		<table>
		<form action="" method="post">
			<tr>
				<th colspan="2" style="margin: 30px;">Log in</th>
			</tr>
			<tr>
                <td>Username</td>
                <td><input style="margin: 10px;" type="text" name="login" value="" /></td>
			</tr>
			<tr>
                <td>Password</td>
                <td><input style="margin: 10px;" type="password" name="passwd" value="" /></td>
			</tr>
			<tr>
                <td></td>
                <td><input style="margin: 10px;" type="submit" name="connect" value="Log in" /></td>
			</tr>
			<tr>
				<td></td>
				<td><a href="index.php?page=account/reset_pass">Forgot your password?</a></td>
			</tr>
		</form>
		</table>
	</div>
	</body>
</html>

<?php
	include_once 'config/connect.php';
	include_once 'validation.php';
	// session_start();

	function    auth($username, $passwd, $pdo) {
		try {
			$get_hash = "SELECT `password`, `verified` FROM users
						WHERE username = :username;";

			$stmt = $pdo->prepare($get_hash);
			$stmt->execute(array(':username' => $username));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$v = $res['verified'];
			$hash = $res['password'];

			if ($v === '0') {
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

	function	get_user_id($username, $pdo) {
		$get_id = "SELECT `user_id` FROM users WHERE username = :username;";
		$stmt = $pdo->prepare($get_id);
		$stmt->execute(array(':username' => $username));
		$res = $stmt->fetch(PDO::FETCH_ASSOC);
		$id = $res['user_id'];
		return $id;
	}

	if ($_POST && $_POST['connect'] === 'Log in' && isset($_POST['login']) && isset($_POST['passwd'])) {
		
		$pdo = connect();

		$username = $_POST['login'];
		$passwd = $_POST['passwd'];

		if (user_exists(NULL, $username, $pdo)) {
			if (auth($username, $passwd, $pdo) === TRUE) {
				$_SESSION['logged_user'] = $username;
				$_SESSION['user_id'] = get_user_id($username, $pdo);
				header("Location: index.php");
			}
		}
		else {
			echo "<br><br>User " . $username . " doesn't exist.";
		}
	}
?>