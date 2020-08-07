<?php
	include_once 'config/connect.php';

	$pdo = connect();
	$username = $_SESSION['logged_user'];

	function	get_user_id($username, $pdo) {
			$get_id = "SELECT `user_id` FROM users WHERE username = :username;";
			$stmt = $pdo->prepare($get_id);
			$stmt->execute(array(':username' => $username));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$id = $res['user_id'];
			return $id;

	}

	$user_id = get_user_id($username, $pdo);

	try {
		$fetch_images = "SELECT * FROM images ORDER BY created DESC;";
		$stmt = $pdo->prepare($fetch_images);
		$stmt->execute();
		$res = $stmt->fetchALL(PDO::FETCH_ASSOC);
	}
	catch (PDOException $e) {
		echo "Error: " . getMessage($e);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
	<link rel="stylesheet" href="main.css" type="text/css" media="all">
	<script>
	function	like(element) {
		let img_id = element.id;
		let user_id = <?php echo $user_id ?>;
		console.log(img_id);
		console.log(user_id);
		if (user_id) {
			let xhttp = new XMLHttpRequest();
			xhttp.open('GET', 'like.php', true);
			xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
			xhttp.send('img_id=' + img_id + "&user_id=" + user_id);
		}
		else {
			alert("You must be logged in to like.");
		}
	}
	</script>
</head>
<body>
	<div id="gallery">
		<?php
			foreach ($res as $img) {
				if (file_exists($img['path'])) { ?>
					<img style="width: 45%; margin: 10px;" id="<?$img['img_id']?>" src="<?=$img['path']?>" ondblclick="like(this)">
				<?php }
			}
		?>
	</div>
</body>
</html>