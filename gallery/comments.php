<?php
	// error_reporting(-1);
	// ini_set('display_errors', 'On');
	// set_error_handler("var_dump");

	include_once '../config/connect.php';
	session_start();
	$username = $_SESSION['logged_user'];

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
            console_log("Error: " . $e->getMessage());
        }
	}

	function	get_comments($imageId) {
		$pdo = connect();
		$comments = "SELECT count(comment_id) FROM comments WHERE comment_img_id = :imageId;";
		$stmt = $pdo->prepare($comments);
		$stmt->bindValue('imageId', $imageId, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchColumn();
		return ($res);
	}

	if (isset($_POST['nb_comments'])) {
		$imageId = $_POST['nb_comments'];
		$json = array();

		$json['comments_total'] = get_comments($imageId);

		echo json_encode($json);
	}

	function	fetchAllComments($imageId) {
		$pdo = connect();
		try {
			$query = "SELECT comments.*, users.username AS uname FROM comments INNER JOIN users ON comments.comment_user_id = users.user_id WHERE comment_img_id = :imageId ORDER BY comments.comment_id ASC;";
			$stmt = $pdo->prepare($query);
			$stmt->bindValue('imageId', $imageId, PDO::PARAM_INT);
			$stmt->execute();
			$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		catch (PDOException $e) {
			echo "Error: " . $e->getMessage();
		}
		if ($comments) {
			return $comments;
		}
		else {
			$json = array();
			$json['err_mess'] = "No comments yet";
			return $json;
		}
	}

	if (isset($_POST['allComments'])) {
		$imageId = $_POST['allComments'];
		$allComments = fetchAllComments($imageId);

		echo json_encode($allComments);
	}

	if (isset($_POST['newComment'])) {
		$imageId = $_POST['img_id'];
		$comment = $_POST['newComment'];
		if (strlen($comment) < 2 || strlen($comment) > 255) {
			$json['success'] = false;
			$json['err_mess'] = "Comment must be between 2 and 255 characters.";
		}
		else {
			$comment = htmlentities($_POST['newComment']);

			try {
				if ($username && $username != "") {
					$pdo = connect();
					$userId = get_id($username, $pdo);
					$insert_comment = "INSERT INTO comments(`comment_user_id`, `comment_img_id`, `comment`, `date`)
										VALUES (:userId, :imgId, :comment, :date);";
					$stmt = $pdo->prepare($insert_comment);
					$stmt->execute(array(':userId' => $userId, ':imgId' => $imageId, ':comment' => $comment, ':date' => date('Y-m-d H:i:s')));
					
					$get_email = "SELECT users.email AS mailAddr, users.verified AS verified FROM users INNER JOIN images ON users.user_id = images.img_user_id WHERE img_id = :imageId;";
					$stmt = $pdo->prepare($get_email);
					$stmt->execute(array(':imageId' => $imageId));
					$res = $stmt->fetch(PDO::FETCH_ASSOC);
	
					if ($res['verified'] == 2) {
						$email = $res['mailAddr'];
						$subject = "A new comment on you picture";
						$content = $username . " commented on your image: \n\n'" . $comment . "'";
						$headers = 'From: admin@camigru.com' . "\r\n";
						mail($email, $subject, $content, $headers);
					}
					$json['success'] = true;
					
				}
				else {
					$json['success'] = false;
					$json['err_mess'] = "You have to be logged in to comment";
				}
			}
			catch (PDOException $e) {
				echo "Error: " . $e->getMessage();
			}
		}
		echo json_encode($json);
	}
?>