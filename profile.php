<?php
    include_once 'config/connect.php';
    include_once 'account/validation.php';
    $username = $_SESSION['logged_user'];

    function    fetch_picture_path($username) {
        $pdo = connect();
        $fetch_path = "SELECT profile_pic FROM users WHERE username = :username;";
        $stmt = $pdo->prepare($fetch_path);
        $stmt->execute(array(':username' => $username));
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $path = $res['profile_pic'];
        return ($path);
    }

    $email = fetch_email($username);
    $submit = $_POST['submit'];

    if ($submit === "Upload Image") {

        $name = $_FILES['image']['name'];
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

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

            $insert_pic = "UPDATE users SET profile_pic = :file_src WHERE user_id = :id;";
            $stmt = $pdo->prepare($insert_pic);
            $stmt->execute(array(':file_src' => $target_file, ':id' => $id));

            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir.$name);
        }
    }

    $profile_pic = fetch_picture_path($username);
?>

<!DOCTYPE html>
<html>
<head>

</head>
    <body>
    <img style="width: 100px; height: auto; border-radius: 50%;" src="<?php if ($profile_pic)
                        echo $profile_pic ?>">
    <form action="" method="post" enctype="multipart/form-data">
        Upload a new picture:<br />
        <input type="file" name="image" id="fileToUpload"><br />
        <input type="submit" value="Upload Image" name="submit">
    </form><br />
        <table>
            <tr>
                <th>Username</th>
                <td><?php echo $username ?></td>
            </tr>
            <tr>
                <th>Full name</th>
                <td>test</td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $email ?></td>
            </tr>
            <tr>
                <th>Phone number</th>
                <td>000-000-000-00</td>
            </tr>
            <tr>
                <th>Address</th>
                <td>test</td>
            </tr>
        </table>

        <?php
            include 'account/user_functions.php';
        ?>
        <br />
    </body>
</html>

