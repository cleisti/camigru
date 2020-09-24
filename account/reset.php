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

	include_once 'validation.php';
	include_once 'config/connect.php';

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
				header("refresh:3;url=index.php?page=account/login");
			}
			else {
				echo "This link is not valid anymore.";
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . $e->getMessage();
		}
	}

	if ($_POST && $_POST['submit'] === 'Reset' && isset($_GET['id']) && isset($_POST['new_pw']) && isset($_POST['validate_pw'])) {

		$reset_token = $_GET['reset'];
		$id = $_GET['id'];
		$new_pw = $_POST['new_pw'];
		$validate_pw = $_POST['validate_pw'];

		if ($new_pw !== $validate_pw) {
			echo "Password doesn't match validation.";
		}
		else if (input_is_valid(NULL, NULL, $new_pw, $validate_pw)) {
			$pdo = connect();
			reset_password($reset_token, $id, $new_pw, $pdo);
		}
	}
?>