<!DOCTYPE html>
<html>
<head>
</head>
    <body>
        <div class="d-flex p-2 justify-content-center align-content-around">
        <table>
        <form action="" method="post">
            <tr style="margin: 30px;">
                <th colspan="2">Create account</th>
            </tr>
            <tr>
                <td>Email</td>
                <td><input style="margin: 10px;" type="email" name="email" value="" required /></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><input style="margin: 10px;" type="text" name="login" minlength="4" maxlength="25" value="" required /></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input style="margin: 10px;" type="password" name="passwd" minlength="8" maxlength="20" value="" required /></td>
            </tr>
            <tr>
                <td>Password</td>
                <td><input style="margin: 10px;" type="password" name="validate_pw" minlength="8" maxlength="20" value="" required /></td>
            </tr>
            <tr>
                <td></td>
                <td><input style="margin: 10px;" type="submit" name="submit" value="Create" /></td>
            </tr>
        </form>
        </table>
        <br />
</div>
    </body>
</html>

<?php
    include_once 'config/connect.php';
    include_once 'account/validation.php';

    function    create_user($email, $username, $passwd, $pdo) {

        try {
            $hashed = password_hash($passwd, PASSWORD_DEFAULT);
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $insert_user = "INSERT INTO users(`email`, `username`, `password`, `token`)
                            VALUES (:email, :username, :password, :token);";
            $stmt = $pdo->prepare($insert_user);
            $stmt->execute(array(':email' => $email, ':username' => $username, ':password' => $hashed, ':token' => $token));

            $id = fetch_uId($username);
            $url = 'http://localhost:8080/camigru/index.php?page=account/verify&token=' . $token .'&id=' . $id;
            $message = "Click this link to activate your account at Camigru: " . $url;
            $subject = "Activate your account at Camigru";
            $headers = 'From: admin@camigru.com' . "\r\n";
            if (mail($email, $subject, $message, $headers))
                return TRUE;
            else
                return FALSE;
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }

    if ($_POST && $_POST['submit'] === 'Create') {
        
        $pdo = connect();
        $submit = $_POST['submit'];
        $email = $_POST['email'];
        $username = $_POST['login'];
        $passwd = $_POST['passwd'];
        $validate_pw = $_POST['validate_pw'];

        if (!$mess = user_exists($email, $username, $pdo)) {
            if (input_is_valid($email, $username, $passwd, $validate_pw)) {
                if (create_user($email, $username, $passwd, $pdo))
                    echo "Your account has been created. Follow the activation link that has been sent to your email.";
                else
                    echo "Something went wrong with sending email. Please contact support at admin@camigru.";
                unset($_POST);
            }
        }
        else {
            echo $mess;
        }
    }

?>