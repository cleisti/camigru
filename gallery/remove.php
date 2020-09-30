<?php
	include_once '../config/connect.php';
	session_start();

	$username = $_SESSION['logged_user'];
	$user_id = $_SESSION['user_id'];

	if (isset($_POST['img_id'])) {
		$pdo = connect();
		try {
			$img_id = $_POST['img_id'];
			$query = "SELECT `path` FROM `images` WHERE `img_id` = :img_id AND `img_user_id` = :userId;";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array(':img_id' => $img_id, ':userId' => $user_id));
			$res = $stmt->fetchColumn();
			unlink('../' . $res);
	
			$remove_pic = "DELETE FROM images WHERE img_id = :img_id AND img_user_id = :userId;";
			$stmt = $pdo->prepare($remove_pic);
			$stmt->execute(array(':img_id' => $img_id, ':userId' => $user_id));
		}
		catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
	}
?>