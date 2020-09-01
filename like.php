<?php
	include_once 'config/connect.php';
	session_start();
	$username = $_SESSION['logged_user'];

	if (!$username || $username == "") {
		die(header("HTTP/1.0 404 Not Found"));
		// echo "You have to be logged in to like.";
	}

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

	function	add_like($img_id, $pdo) {
		$user_id = get_id($username, $pdo);

		$add_like = "INSERT INTO likes(like_user_id, like_img_id) VALUES(:username, :img_id);";
		$stmt = $pdo->prepare($add_like);
		$stmt->execute(array(':username' => $user_id, ':img_id' => $img_id));
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
		$pdo = connect();
		$img_id = $_POST['img_id'];
		$user_id = get_id($username, $pdo);

		$liked = "SELECT like_id FROM likes WHERE like_user_id = :username AND like_img_id = :img_id;";
		$stmt = $pdo->prepare($liked);
		$stmt->execute(array(':username' => $user_id, 'img_id' => $img_id));
		$res = $stmt->fetchColumn();

		// echo $res;

		if (!$res) {
			$add_like = "INSERT INTO likes(like_user_id, like_img_id) VALUES(:username, :img_id);";
			$stmt = $pdo->prepare($add_like);
			$stmt->execute(array(':username' => $user_id, ':img_id' => $img_id));
		}
		else {
			$remove_like = "DELETE FROM likes WHERE like_user_id = :username AND like_img_id = :img_id;";
			$stmt = $pdo->prepare($remove_like);
			$stmt->execute(array(':username' => $user_id, 'img_id' => $img_id));
		}

		$likes = "SELECT count(like_id) AS nb_likes FROM likes WHERE like_img_id = :img_id;";
		$stmt = $pdo->prepare($likes);
		$stmt->bindValue('img_id', $img_id, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchColumn();

		echo get_likes($img_id, $pdo);
		
		// add_like($img_id, $pdo);

	}

	if (isset($_POST['nb_likes'])) {
		$pdo = connect();
		$nb_likes = $_POST['nb_likes'];

		echo get_likes($nb_likes, $pdo);
	}
?>