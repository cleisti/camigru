<?php
	include_once 'config/connect.php';
	session_start();
	$id = $_SESSION['user_id'];

	if ($_POST['get_images']) {
		try {
		  $pdo = connect();
		  $fetch_images = "SELECT * FROM images WHERE img_user_id = :user_id ORDER BY created DESC;";
		  $stmt = $pdo->prepare($fetch_images);
		  $stmt->execute(array(':user_id' => $id));
		  $images = $stmt->fetchALL(PDO::FETCH_ASSOC);
		  echo json_encode($images);
		}
		catch (PDOException $e) {
		  echo "Error: " . getMessage($e);
		} 
	}
?>