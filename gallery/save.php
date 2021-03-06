<?php
    include_once '../config/connect.php';
    session_start();

    $username = $_SESSION['logged_user'];
    $user_id = $_SESSION['user_id'];

    $img = $_POST['image'];
    $filters =  $_POST['filter'];
    $filters = explode(',', $filters);
    array_pop($filters);

    $filename = uniqid('', true) . '.png';
    $path = 'images/' . $filename;

    $img_base64 = str_replace('data:image/png;base64,', '', $img);
    $img_base64 = str_replace(' ', '+', $img_base64);
    $img_data = base64_decode($img_base64);

    if (file_put_contents('../' . $path, $img_data)) {
        foreach ($filters as $filter) {
            $dest = imagecreatefrompng('../' . $path);
            $f = imagecreatefrompng('../filters/' . $filter . '.png');
            imagecopyresampled($dest, $f, 0, 0, 0, 0, imagesx($dest), imagesy($dest), imagesx($f), imagesy($f));
            header('Content-Type: image/png');
            imagepng($dest, '../' . $path);
            imagedestroy($dest);
            imagedestroy($f);
        }

        try {
            $pdo = connect();
        
            $insert_pic = "INSERT INTO images(`img_user_id`, `path`, `created`) VALUES (:id, :path, :date)";
            $stmt = $pdo->prepare($insert_pic);
            $stmt->execute(array(':id' => $user_id, ':path' => $path, ':date' => date('Y-m-d H:i:s')));
            echo "Image saved to gallery.";
        }
        catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

    }
    unset($_POST);
?>