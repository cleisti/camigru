<?php
	include_once 'config/connect.php';

	function	fetch_images() {
		$pdo = connect();

		$fetch_images = "SELECT * FROM images;";
		$stmt = prepare($fetch_images);
		$stmt->execute();
		$res = $stmt(PDO::FETCH_ASSOC);
		$paths = $res['path'];
		return $paths;
	}

	$images = fetch_images();
	print_r($images);
	$len = len($images);
	for ($i = 0; $i < $len; $i++) {
		echo "<img src=" . $images[$i] . ">";
	}
?>