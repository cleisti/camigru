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

		$token = trim($_GET['token']);
		$id = trim($_GET['user']);
		
		try {
			$sql = "SELECT COUNT(*) AS num FROM users WHERE user_id = :id AND token = :token;";
			$stmt = $pdo->prepare($sql);
			$stmt->execute(array(':id' => $id, ':token' => $token));
		
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$stmt->close();
			if ($result['num'] == 1){
				try {
					$validate = "UPDATE users WHERE user_id = :id SET verified = '1;";
					$stmt = $pdo->prepare($validate);
					$stmt->execute(array(':id' => $id));
					$stmt->close;
					echo "Validation successfull. Log in.";
				}
				catch (PDOException $e) {
					echo "ERROR: " . getMessage();
				}
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