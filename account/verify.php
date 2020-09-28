<!DOCTYPE html>
<html>
<head>
</head>
    <body style="text-align: center;">

	</body>
</html>
<?php
	include_once 'config/connect.php';

	if (isset($_GET['token']) && isset($_GET['id'])) {

		$pdo = connect();

		$token = filter_var($_GET['token'], FILTER_SANITIZE_STRING);
		$id = filter_var($_GET['id'], FILTER_SANITIZE_STRING);

		try {
			$get_user = "SELECT user_id, token FROM users WHERE user_id = :id AND token = :token;";
			$stmt = $pdo->prepare($get_user);
			$stmt->execute(array(':id' => $id, ':token' => $token));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($res) {
				$validate = "UPDATE users
							SET verified = :v, token = :zero
							WHERE user_id = :id;";
				$stmt = $pdo->prepare($validate);
				$stmt->execute(array(':v' => 2, ':zero' => 0, ':id' => $id));
				echo "Validation successfull. You can <a href='index.php?page=account/login'>log in</a> now.";
			}
			else {
				echo "Unable to validate email.";
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . $e->getMessage();
		}
	}
?>

