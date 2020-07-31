<?php
include_once 'config/connect.php';

$username = $_SESSION['logged_user'];
$submit = $_POST['submit'];

if ($submit === "Upload Image") {

  $name = $_FILES['img']['name'];
  $target_dir = "images/";
  $target_file = $target_dir . basename($_FILES["img"]["name"]);

  $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

  if (!file_exists($target_dir))
      mkdir($target_dir);

  $extensions_arr = array("jpg","jpeg","png","gif");
  if (in_array($imageFileType, $extensions_arr)) {

      $pdo = connect();

      $get_id = "SELECT user_id FROM users WHERE username = :username;";
      $stmt = $pdo->prepare($get_id);
      $stmt->execute(array(':username' => $username));
      $res = $stmt->fetch(PDO::FETCH_ASSOC);
      $id = $res['user_id'];

      $insert_pic = "INSERT INTO images(`img_user_id`, `path`) VALUES (:id, :path)";
      $stmt = $pdo->prepare($insert_pic);
      $stmt->execute(array('id' => $id, ':path' => $target_file));

      move_uploaded_file($_FILES['img']['tmp_name'], $target_dir.$name);
  }
  else
    echo "Wrong format";
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
<div class="contentarea">
  <h2>Create a new image</h2>
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
  <div id="output">
  </div>
</div>
</body>
</html>