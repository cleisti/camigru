<?php
	include_once 'config/connect.php';
	session_start();

	$username = $_SESSION['logged_user'];
	$img_id = $_POST['img_id'];

	function    get_id($username, $pdo) {
        try {
            $get_id = "SELECT user_id FROM users WHERE username = :username;";
            $stmt = $pdo->prepare($get_id);
            $stmt->execute(array(':username' => $username));
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $res['user_id'];

            return $id;
        }
        catch (PDOException $e) {
            console_log("Error: " . getMessage($e));
        }
	}

	if (isset($img_id)) {
		$pdo = connect();
		$id = get_id($username, $pdo);

		$path = "SELECT path FROM images WHERE img_id = :img_id AND img_user_id = :userId;";
		$stmt = $pdo->prepare($path);
		$stmt->execute(array(':img_id' => $img_id, ':userId' => $id));
		$res = $stmt->fetchColumn();
		unlink($res);

		$remove_pic = "DELETE FROM images WHERE img_id = :img_id AND img_user_id = :userId;";
		$stmt = $pdo->prepare($remove_pic);
		$stmt->execute(array(':img_id' => $img_id, ':userId' => $id));
	}
?>