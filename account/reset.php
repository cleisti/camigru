<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<form action="" method="post">
			New password: <input type="password" name="new_pw" minlength="8" maxlength="20" value="" required />
			<br />
            Validate password: <input type="password" name="validate_pw" minlength="8" maxlength="20" value="" required />
            <input class="button" type="submit" name="submit" value="Reset" />
        </form>
	</body>
</html>

<?php
	include '../config/connect.php';

	$submit = $_POST['submit'];

	function	reset_password($reset_token, $id, $new_pw, $pdo) {
		try {
			$validate_token = "SELECT reset FROM users WHERE user_id = :id;";
			$stmt = $pdo->prepare($validate_token);
			$stmt->execute(array(':id' => $id));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($res['reset'] === $reset_token) {
				$hashed = password_hash($new_pw, PASSWORD_DEFAULT);
				$reset = "UPDATE users
						SET password = :password, reset = :zero
						WHERE user_id = :id;";
				$stmt = $pdo->prepare($reset);
				$stmt->execute(array(':id' => $id, 'password' => $hashed, ':zero' => 0));
				echo "Password reset. Redirecting to login page . . .";
			}
			else {
				echo "This link is not valid anymore.";
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
	}

	if ($submit === 'Reset' && isset($_GET['id']) && isset($_POST['new_pw']) && isset($_POST['validate_pw'])) {

		$reset_token = filter_var($_GET['reset'], FILTER_SANITIZE_STRING);
		$id = filter_var($_GET['id'], FILTER_SANITIZE_STRING);
		$new_pw = filter_var($_POST['new_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$validate_pw = filter_var($_POST['validate_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

		if ($new_pw !== $validate_pw) {
			echo "Password doesn't match validation.";
		}
		else {
			$pdo = connect();
			reset_password($reset_token, $id, $new_pw, $pdo);
		}
	}
?>