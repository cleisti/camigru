<?php
include_once 'config/connect.php';

$username = $_SESSION['logged_user'];
$id = $_SESSION['user_id'];
// $submit = $_POST['submit'];

// if ($submit === "Upload Image") {

//   $folderPath = "images/";
//   $target_file = $folderPath . basename($_FILES["img"]["name"]); // $folderPath needed?
//   $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
//   $fileName = uniqid() . $imageFileType;
//   $file = $folderPath . $fileName;

//   if (!file_exists($folderPath))
//       mkdir($folderPath);

//   $extensions_arr = array("jpg","jpeg","png","gif");

//   if (in_array($imageFileType, $extensions_arr)) {
//       if (move_uploaded_file($_FILES['img']['tmp_name'], $folderPath . $fileName)) {
//         $pdo = connect();

//         $get_id = "SELECT user_id FROM users WHERE username = :username;";
//         $stmt = $pdo->prepare($get_id);
//         $stmt->execute(array(':username' => $username));
//         $res = $stmt->fetch(PDO::FETCH_ASSOC);
//         $id = $res['user_id'];

//         $insert_pic = "INSERT INTO images(`img_user_id`, `path`, `created`) VALUES (:id, :path, :date);";
//         $stmt = $pdo->prepare($insert_pic);
//         $stmt->execute(array('id' => $id, ':path' => $file, ':date' => date('Y-m-d H:i:s')));

//         echo "Image uploaded to gallery.";
//       }
//       else {
//         echo "Unable to upload image.";
//       }
//   }
//   else
//     echo "Wrong format";
// }

try {
  $fetch_images = "SELECT * FROM images WHERE img_user_id = :user_id ORDER BY created DESC;";
  $stmt = $pdo->prepare($fetch_images);
  $stmt->execute(array(':user_id' => $id));
  $images = $stmt->fetchALL(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="webcam.js">
	</script>
</head>
<body>
<h2 style="text-align: center;">Create</h2>
<div class="d-inline-flex flex-wrap align-content-center justify-content-center">
    <div id="filters" style="margin: 20px;">
      <img class="filter" src="filters/bwnoise.png" id="bwnoise" data-clickcount="0">
      <img class="filter" src="filters/flowers.png" id="flowers" data-clickcount="0">
      <img class="filter" src="filters/frame.png" id="frame" data-clickcount="0">
      <img class="filter" src="filters/hearts.png" id="hearts" data-clickcount="0">
      <img class="filter" src="filters/roses.png" id="roses" data-clickcount="0">
    </div>
    <div class="camera" id="camera" style="margin: 20px;">
      <div id="photo" data-uploaded="0"></div>
      <div id="selectedFilters"></div>
      <video id="video">Video stream not available.</video>
      <!-- <div style="background-color: #AAA; width: 320px; height: 240px; position: absolute; top: 0; left: 0; opacity: 0.3;"></div> -->
      <div class="d-inline-flex align-content-center justify-content-center" id="buttons">
        <button id="save">Save</button>
        <button id="new">New</button>
        <button id="startbutton">Capture</button>
        <input type="hidden" name="image" id="image-tag" value="">
        <input class="form-control-file" type="file" accept="image/*" name="img" onchange="uploadImageToCanvas(this)">
      </div>
    </div>
  <canvas id="canvas">
  </canvas>
</div>
<h2 style="text-align: center;">Your images</h2>
<div class="d-inline-flex flex-wrap justify-content-center" style="padding: 20px;" id="gallery">
		<?php
			foreach ($images as $img) {
        if (file_exists($img['path'])) { ?>
        <div class="card" style="margin: 5px; max-width: 150px;">
					  <img class="card-img-top" name="image" id="<?=$img['img_id']?>" src="<?=$img['path']?>">
        </div>
				<?php }
			}
		?>
</div>
</body>
</html>