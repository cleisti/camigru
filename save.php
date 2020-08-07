<?php
    include_once 'config/connect.php';
    session_start();

    $username = $_SESSION['logged_user'];

    $img = $_POST['image'];
	$folderPath = "images/";
  
    $image_parts = explode(";base64,", $img);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
  
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = uniqid() . '.png';
  
    $file = $folderPath . $fileName;

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
            console_log("Error: " . getMessage($e));
        }
    }

    if (file_put_contents($file, $image_base64)) {
        try {
            $pdo = connect();
            $id = get_id($username, $pdo);
        
            $insert_pic = "INSERT INTO images(`img_user_id`, `path`, `created`) VALUES (:id, :path, :date)";
            $stmt = $pdo->prepare($insert_pic);
            $stmt->execute(array(':id' => $id, ':path' => $file, ':date' => date('Y-m-d H:i:s')));
            echo "Image saved to gallery.";
        }
        catch (PDOException $e) {
            echo "Error: " . getMessage($e);
        }
    }
    else {
        echo "Unable to save image.";
    }
?>