<?php
	include_once 'config/connect.php';
	// session_start();

	$pdo = connect();
	$username = $_SESSION['logged_user'];
	$id = $_SESSION['user_id'];

	$limit = 6;
	if (isset($_GET['gallery_page'])) {
		$page = $_GET['gallery_page'];
	}
	else {
		$page = 1;
	}
	$start = ($page - 1) * $limit;

	try {
		$fetch_images = "SELECT * FROM images ORDER BY created DESC LIMIT 6 OFFSET :startId";
		$stmt = $pdo->prepare($fetch_images);
		$stmt->bindValue('startId', $start, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchALL(PDO::FETCH_ASSOC);
	}
	catch (PDOException $e) {
		echo "Error: " . getMessage($e);
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset='utf-8' name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="styles/gallery.css" type="text/css" media="all">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="galleryfeatures.js"></script>
	<script src="like.js"></script>
</head>
<body>
	<div class="d-inline-flex p-2 flex-wrap justify-content-center align-content-around" id="gallery">
		<?php

			foreach ($res as $img) {
				if (file_exists($img['path'])) { ?>
				<div class="card" style="margin: 5px; max-width: 340px;">
					<div class="card-header" style="padding: 10px;">
					Img-title
					</div>
					<img class="image" style="padding: 10px;" name="<?=$img['img_id']?>" src="<?=$img['path']?>">
					<div class="card-footer" style="padding: 10px;">
						<img src="icons/like.png" id="<?=$img['img_id']?>" style="height: 20px; width: 20px; float: left;" onClick='like(this)'>
						<div class="likes" style="height: 20px; width: 20px; float: left;" id="likes_<?=$img['img_id']?>"></div>
						<img src="icons/comment.png" id="<?=$img['img_id']?>" style="height: 20px; width: 20px; float: left;" onClick='like(this)'>
						<div class="comments" style="height: 20px; width: 20px; float: left;" id="comments_<?=$img['img_id']?>"></div>
						<div id="error_<?=$img['img_id']?>" style="display: none;"></div>
					</div>
				</div>
				<?php }
			}
		?>
		<div class="d-inline-flex p2 justify-content-center" style="width: 100%;">
		<?php
		$get_total = "SELECT count(img_id) AS total FROM images;";
		$stmt = $pdo->prepare($get_total);
		$stmt->execute();
		$total_images = $stmt->fetchColumn();
		$total_pages = ceil($total_images / $limit);

		$pagLink = "<ul class='pagination'>";
			for ($i = 1; $i <= $total_pages; $i++) {
				$pagLink .= "<li class='page-item'><a class='page-link' href='index.php?page=gallery&gallery_page=".$i."'>".$i."</a></li>";
			}
			echo $pagLink . "</ul>";
	?>
		</div>
	</div>
	<div id="popup" class="modal">
		<div id="innerPopup">
		</div>
	</div>
</body>
</html>