<?php
include_once 'config/connect.php';

$username = $_SESSION['logged_user'];
$id = $_SESSION['user_id'];
$submit = $_POST['submit'];

if ($submit === "Upload Image") {

  $folderPath = "images/";
  $target_file = $folderPath . basename($_FILES["img"]["name"]); // $folderPath needed?
  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
  $fileName = uniqid() . $imageFileType;
  $file = $folderPath . $fileName;

  if (!file_exists($folderPath))
      mkdir($folderPath);

  $extensions_arr = array("jpg","jpeg","png","gif");

  if (in_array($imageFileType, $extensions_arr)) {
      if (move_uploaded_file($_FILES['img']['tmp_name'], $folderPath . $fileName)) {
        $pdo = connect();

        $get_id = "SELECT user_id FROM users WHERE username = :username;";
        $stmt = $pdo->prepare($get_id);
        $stmt->execute(array(':username' => $username));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $res['user_id'];

        $insert_pic = "INSERT INTO images(`img_user_id`, `path`, `created`) VALUES (:id, :path, :date);";
        $stmt = $pdo->prepare($insert_pic);
        $stmt->execute(array('id' => $id, ':path' => $file, ':date' => date('Y-m-d H:i:s')));

        echo "Image uploaded to gallery.";
      }
      else {
        echo "Unable to upload image.";
      }
  }
  else
    echo "Wrong format";
}

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
	<link rel="stylesheet" href="main.css" type="text/css" media="all">
	<script src="webcam.js">
	</script>
</head>
<body>
<h2 style="text-align: center;">Create a new image</h2>
<div class="contentArea" style="display: flex; justify-content: space-between;">
    <div class="stickers" style="height: 300px;">
      <img draggable="true" src="stickers/bwnoise.png" id="bwnoise" style="width: 100px; cursor: move;" onclick=addFilter(this)>
    </div>
    <div class="camera">
      <video id="video">Video stream not available.</video>
      <button id="startbutton">Take photo</button>
      <input type="hidden" name="image" id="image-tag" value="">
    </div>
  <canvas id="canvas">
  </canvas>
  <!-- <form action="" method="post" enctype="multipart/form-data">
        Or upload a picture:<br />
        <input type="file" accept="image/*" name="img"><br />
        <input type="submit" value="Upload Image" name="submit">
  </form> -->
  <div id="output" style="max-height: 300px; width: 180px; overflow: scroll;">
  </div>
</div>
<h2 style="text-align: center;">Your images</h2>
<div id="gallery">
		<?php
			foreach ($images as $img) {
				if (file_exists($img['path'])) { ?>
					<img style="width: 25%; margin: 10px;" id="<?$img['img_id']?>" src="<?=$img['path']?>" ondblclick="remove(this)">
				<?php }
			}
		?>
	</div>
</body>
</html>