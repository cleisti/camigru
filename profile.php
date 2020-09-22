<?php
    include_once 'config/connect.php';
    // include 'account/user_functions.php';
    include_once 'account/validation.php';
    session_start();

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
    <style>
        table th {
            height: 50px;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            }
            .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 15px;
  width: 15px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(15px);
  -ms-transform: translateX(15px);
  transform: translateX(15px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 20px;
}

.slider.round:before {
  border-radius: 50%;
}
    </style>

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
            <tr>
                <th>Full name</th>
                <td>test</td>
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
</div>
    </body>
</html>

