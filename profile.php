<?php
    include_once 'account/validation.php';
    include_once 'config/connect.php';

    $username = $_SESSION['logged_user'];
    if (!$username || $username == "") {
        header('Location: index.php?page=account/login');
    }

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

    if ($_POST && $_POST['submit'] === "Upload Image") {

        $name = $_FILES['image']['name'];
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);

        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        if (!file_exists($target_dir))
            mkdir($target_dir);

        $extensions_arr = array("jpg","jpeg","png","gif");

        if (in_array($imageFileType, $extensions_arr)) {

            $pdo = connect();

            $insert_pic = "UPDATE users SET profile_pic = :file_src WHERE user_id = :id;";
            $stmt = $pdo->prepare($insert_pic);
            $stmt->execute(array(':file_src' => $target_file, ':id' => $id));

            move_uploaded_file($_FILES['image']['tmp_name'], $target_dir.$name);
        }
    }

    $profile_pic = fetch_picture_path($username);
    $id = fetch_uId($username);
?>

<!DOCTYPE html>
<html>
<head>
    <script src="profile.js"></script>
    <link rel="stylesheet" href="styles/gallery.css" type="text/css" media="all">
</head>
    <body>
        <div class="d-flex p-2 justify-content-center align-content-around">

    <table>
        <tr rowspan="2">
            <td><img class="img-circle" style="width: 100px;" src="<?php if ($profile_pic)
                        echo $profile_pic ?>"></td>
            <th>@<?=$username ?></th>
        </tr>
        <form action="account/user_functions.php" method="post">
            <tr>
                <th>Notifications</th>
                <td><label class="switch">
                <input type="checkbox" id="notifications" name="<?=$id ?>" onchange="setNotifications(this)">
                <span class="slider round"></span>
                </label></td>
            </tr>
        </form>
            <form action="account/user_functions.php" method="post">
            <tr>
                <th style="height: 50px;">Username</th>
                <td><input type="text" name="new_un" minlength="4" maxlength="25" value="" placeholder="<?php echo $username ?>" required /></td>
            </tr>
            <tr>
				<td></td>
				<td><input class="button" type="submit" name="submit" value="Change username" /></td>
            </tr>
            </form>
            <form action="account/user_functions.php" method="post">
            <tr>
                <th>Email</th>
                <td><input type="email" name="new_email" value="" placeholder="<?php echo $email ?>" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="email" name="validate_email" value="" placeholder="Validate email" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input class="button" type="submit" name="submit" value="Change email" /></td>
            </tr>
            </form>
            <form style="max-width: 400px; margin: 10px 0 10px;" action="account/user_functions.php" method="post">
            <tr>
                <th>New password</th>
                <td><input type="password" name="new_pw" minlength="8" maxlength="20" value="" placeholder="********" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="password" name="validate_pw" minlength="8" maxlength="20" value="" placeholder="Validate password" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="password" name="old_pw" minlength="8" maxlength="20" value="" placeholder="Password" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input class="button" type="submit" name="submit" value="Change password" /></td>
            </tr>
            </form>
        </table>
</div>
    </body>
</html>

