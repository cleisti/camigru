<?php
    include_once 'config/connect.php';
    session_start();

    $username = $_SESSION['logged_user'];

    $img = $_POST['image'];
    $sticker =  $_POST['sticker'];
    // $folderPath = "images/";
    
    if (empty($sticker))
        echo "No filter selected.";
  
    // $image_parts = explode(";base64,", $img);
    // $image_type_aux = explode("image/", $image_parts[0]);
    // $image_type = $image_type_aux[1];

    $img_base64 = str_replace('data:image/png;base64,', '', $img);
    $img_base64 = str_replace(' ', '+', $img_base64);
    $img_data = base64_decode($img_base64);
    $src = imagecreatefrompng('stickers/' . $sticker . '.png');
    $dest = imagecreatefromstring($img_data);
    imagecopy($dest, $src, 0, 0, 0, 0, imagesx($src), imagesy($src));
    header('Content-Type: image/png');
    $filename = uniqid('', true) . '.png';
    $path = 'images/' . $filename;
    // Saving to path
    $status = imagepng($dest, $path);
    imagedestroy($dest);
    imagedestroy($src);
  
    // $image_base64 = base64_decode($image_parts[1]);
    // $fileName = uniqid() . '.png';

    // $filter = imagecreatefrompng('stickers/' . $sticker . '.png');
    // $image = imagecreatefromstring($image_base64);
    // imagecopy($image, $filter, 0, 0, 0, 0, imagesx($filter), imagesy($filter));

    // $file = $folderPath . $fileName;

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

    if ($status) {
        try {
            $pdo = connect();
            $id = get_id($username, $pdo);
        
            $insert_pic = "INSERT INTO images(`img_user_id`, `path`, `created`) VALUES (:id, :path, :date)";
            $stmt = $pdo->prepare($insert_pic);
            $stmt->execute(array(':id' => $id, ':path' => $path, ':date' => date('Y-m-d H:i:s')));
            echo "Image saved to gallery.";
        }
        catch (PDOException $e) {
            echo "Error: " . getMessage($e);
        }
    }
    else {
        echo "Unable to save image.";
    }

    // if (file_put_contents($file, $image)) {
    //     try {
    //         $pdo = connect();
    //         $id = get_id($username, $pdo);
        
    //         $insert_pic = "INSERT INTO images(`img_user_id`, `path`, `created`) VALUES (:id, :path, :date)";
    //         $stmt = $pdo->prepare($insert_pic);
    //         $stmt->execute(array(':id' => $id, ':path' => $file, ':date' => date('Y-m-d H:i:s')));
    //         echo "Image saved to gallery.";
    //     }
    //     catch (PDOException $e) {
    //         echo "Error: " . getMessage($e);
    //     }
    // }
    // else {
    //     echo "Unable to save image.";
    // }
?>