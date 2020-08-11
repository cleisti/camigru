<?php
	include_once 'config/connect.php';

	$pdo = connect();
	$username = $_SESSION['logged_user'];
	$id = $_SESSION['user_id'];

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
	<link rel="stylesheet" href="styles/gallery.css" type="text/css" media="all">
	<script src="galleryfeatures.js">
	// function	like(element) {
	// 	let img_id = element.id;
		// let user_id = ;
	// 	console.log(img_id);
	// 	console.log(user_id);
	// 	if (user_id) {
	// 		let xhttp = new XMLHttpRequest();
	// 		xhttp.open('GET', 'like.php', true);
	// 		xhttp.setRequestHeader('Content-type', 'Application/x-www-form-urlencoded');
	// 		xhttp.send('img_id=' + img_id + "&user_id=" + user_id);
	// 	}
	// 	else {
	// 		alert("You must be logged in to likea picture.");
	// 	}
	// }
	</script>
</head>
<body>
	<div class="gallery">
		<?php
			foreach ($res as $img) {
				if (file_exists($img['path'])) { ?>
				<a href="#">
					<img style="width: 45%; margin: 10px;" id="<?=$img['img_id']?>" src="<?=$img['path']?>">
				</a>
				<?php }
			}
		?>
	</div>
	<div id="popup" style="display: none; position: fixed; z-index: 1; top: 0; background-color: green;"></div>
</body>
</html>