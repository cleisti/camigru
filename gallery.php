<?php
	include_once 'config/connect.php';

	$pdo = connect();
	if (isset($_SESSION['logged_user']))
		$username = $_SESSION['logged_user'];
	else
		$username = "";

	$limit = 6;
	if (isset($_GET['gallery_page'])) {
		$page = $_GET['gallery_page'];
	}
	else {
		$page = 1;
	}
	$start = ($page - 1) * $limit;

	try {
		$fetch_images = "SELECT images.*, users.username AS uname FROM images INNER JOIN users ON images.img_user_id = users.user_id ORDER BY created DESC LIMIT 6 OFFSET :startId";
		$stmt = $pdo->prepare($fetch_images);
		$stmt->bindValue('startId', $start, PDO::PARAM_INT);
		$stmt->execute();
		$res = $stmt->fetchALL(PDO::FETCH_ASSOC);
	}
	catch (PDOException $e) {
		echo "Error: " . $e->getMessage();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset='utf-8' name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="styles/gallery.css" type="text/css" media="all">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<script src="galleryfeatures.js"></script>
</head>
<body>
	<div class="d-inline-flex p-2 flex-wrap justify-content-center align-content-around" id="gallery">
		<?php

			foreach ($res as $img) {
				if (file_exists($img['path'])) { ?>
				<div class="card" style="margin: 5px auto; width: 100%; max-width: 600px;">
					<div class="card-header"><p style="margin: 0;" id="creator_<?=$img['img_id']?>"><b>@<?=$img['uname']?></b></p></div>
					<img class="image" style="padding: 10px; width: 100%;" name="<?=$img['img_id']?>" id="<?=$img['img_id']?>" src="<?=$img['path']?>">
					<div class="card-footer" style="padding: 10px;">
						<img class="like" src="icons/like.png" id="likeImg_<?=$img['img_id']?>" name="<?=$img['img_id']?>" onClick='like(this)'>
						<div class="likes" id="likes_<?=$img['img_id']?>"></div>
						<img class="comment" src="icons/comment.png" name="<?=$img['img_id']?>" onClick="openImagePopup(this)">
						<div class="comments" id="comments_<?=$img['img_id']?>"></div>
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
		<div id="popup" class="modal">
		<div id="innerPopup" class="container">
		<span class="close" onClick=close()>&times;</span>
			<div class="row align-items-center justify-content-center" style="padding: 0; ">
				<div id="imageBox" class="col-sm-8"></div>
				<div id="commentBox" class="col-sm-4">
					<h5 id="creator"></h5>
					<div id="likeBox"></div>
					<h6 id="commentHeader"></h6>
					<div id="allComments"></div>
					<input type="text" id="newComment" placeholder="Add a comment..." required />
					<input type="submit" id="commentSubmit" class="commentButton">
				</div>
				<div id="errorBox" style="display: none;"></div>
			</div>
		</div>
	</div>
	</div>
</body>
</html>