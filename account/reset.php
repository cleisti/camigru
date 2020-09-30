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
                <td>New password</td>
                <td><input type="password" name="new_pw" minlength="8" maxlength="20" value="" required /></td>
			</tr>
			<tr>
                <td>Validate password</td>
                <td><input type="password" name="validate_pw" minlength="8" maxlength="20" value="" required /></td>
			</tr>
			<tr>
                <td></td>
                <td><input class="button" type="submit" name="submit" value="Reset" /></td>
			</tr>
		</form>
		</table>
	</div>
	</body>
</html>

<?php
	include_once 'config/connect.php';
	include_once 'validation.php';

	if (!$_GET)
		header("Location: index.php?page=account/login");

	function	reset_password($reset_token, $id, $new_pw) {
		$pdo = connect();

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

		if (input_is_valid(NULL, NULL, $new_pw, $validate_pw)) {
			reset_password($reset_token, $id, $new_pw);
		}
	}
?>