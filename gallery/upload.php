<?php
include_once 'config/connect.php';
// session_start();
$username = $_SESSION['logged_user'];
if (!$username || $username == "") {
    header('Location: index.php?page=account/login');
}
$id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset='utf-8'>
  <script src="scripts/webcam.js">
	</script>
</head>
<body>
<div style="display: block;">
  <h2 style="text-align: center;">Create</h2>
    <div id="filters">
      <img class="filter" src="filters/bwnoise.png" id="bwnoise" data-clickcount="0">
      <img class="filter" src="filters/flowers.png" id="flowers" data-clickcount="0">
      <img class="filter" src="filters/frame.png" id="frame" data-clickcount="0">
      <img class="filter" src="filters/hearts.png" id="hearts" data-clickcount="0">
      <img class="filter" src="filters/roses.png" id="roses" data-clickcount="0">
    </div>
    <div class="camera" id="camera">
      <div id="photo" data-uploaded="0"></div>
      <div id="selectedFilters"></div>
      <video id="video">Video stream not available.</video>
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
<div class="d-inline-flex flex-wrap justify-content-center" style="padding: 20px;" id="gallery">
<h2 style="text-align: center;">Your images</h2>
</div>
</body>
</html>
