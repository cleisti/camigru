<?php
	include_once 'config/connect.php';
	session_start();
	if (isset($_SESSION['logged_user']))
		$username = $_SESSION['logged_user'];
	else
		$username = "";

	$json = array();

	function is_ajax_request() {
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
	}

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

	function	get_likes($img_id, $pdo) {
		$likes = "SELECT count(like_id) AS nb_likes FROM likes WHERE like_img_id = :img_id;";
		$stmt = $pdo->prepare($likes);
		$stmt->bindValue('img_id', $img_id, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchColumn();
		return ($res);
	}

	if (isset($_POST['img_id'])) {

		if (!$username || $username == "") {
			$json['success'] = false;
			$json['err_mess'] = "You have to be logged in to like.";
			echo json_encode($json);
			exit;
		} 
		$pdo = connect();
		$img_id = $_POST['img_id'];
		$user_id = get_id($username, $pdo);

		$liked = "SELECT like_id FROM likes WHERE like_user_id = :username AND like_img_id = :img_id;";
		$stmt = $pdo->prepare($liked);
		$stmt->execute(array(':username' => $user_id, 'img_id' => $img_id));
		$res = $stmt->fetchColumn();

		if (!$res) {
			$add_like = "INSERT INTO likes(like_user_id, like_img_id) VALUES(:username, :img_id);";
			$stmt = $pdo->prepare($add_like);
			$stmt->execute(array(':username' => $user_id, ':img_id' => $img_id));
			
			$json['success'] = true;
		}
		else {
			$remove_like = "DELETE FROM likes WHERE like_user_id = :username AND like_img_id = :img_id;";
			$stmt = $pdo->prepare($remove_like);
			$stmt->execute(array(':username' => $user_id, 'img_id' => $img_id));
			$json['success'] = true;
		}

		$likes = "SELECT count(like_id) AS nb_likes FROM likes WHERE like_img_id = :img_id;";
		$stmt = $pdo->prepare($likes);
		$stmt->bindValue('img_id', $img_id, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchColumn();
		$json['likes_total'] = get_likes($img_id, $pdo);

		echo json_encode($json);

	}

	if (isset($_POST['nb_likes'])) {
		$pdo = connect();
		$nb_likes = $_POST['nb_likes'];
		$json = array();

		$json['likes_total'] = get_likes($nb_likes, $pdo);

		echo json_encode($json);
	}
?>