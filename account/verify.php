<!DOCTYPE html>
<html>
<head>
</head>
    <body>
    </body>
</html>

<?php
	include '../config/connect.php';

	if (isset($_GET['token']) && isset($_GET['id'])) {

		$pdo = connect();

		$token = filter_var($_GET['token'], FILTER_SANITIZE_STRING);
		$id = filter_var($_GET['id'], FILTER_SANITIZE_STRING);
	
		try {
			$get_user = "SELECT user_id FROM users WHERE user_id = :id AND token = :token;";
			$stmt = $pdo->prepare($get_user);
			$stmt->execute(array(':id' => $id, ':token' => $token));

			$result = $stmt->fetch(PDO::FETCH_ASSOC);

			if ($result['user_id'] === $id) {
				$validate = "UPDATE users
							SET verified = :v, token = :zero
							WHERE user_id = :id;";
				$stmt = $pdo->prepare($validate);
				$stmt->execute(array(':v' => 1, ':zero' => 0, ':id' => $id));
				$stmt->close();
				echo "Validation successfull. Log in.";
			}
			else {
				echo "Unable to validate email.";
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
		
	}
?>