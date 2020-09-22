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
    session_start();

    $submit = $_POST['submit'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var(htmlentities($_POST['login']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $validate_pw = filter_var($_POST['validate_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

    function    send_mail($email, $token, $pdo) {
        try {
            $get_id = "SELECT user_id FROM users
                    WHERE email = :email LIMIT 1;";
            $stmt = $pdo->prepare($get_id);
            $stmt->execute(array(':email' => $email));
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $res['user_id'];

            if ($id) {
                $url = 'http://localhost:8080/camigru/index.php?page=account/verify&token=' . $token .'&id=' . $id;
                $subject = "Activate your account at Camigru";
                $content = "Click this link to activate your account: " . $url;
                $headers = 'From: admin@camigru.com' . "\r\n";
                if (mail($email, $subject, $content, $headers)) {
                    return TRUE;
                }
                else {
                    echo "Unable to send activation email.";
                    return FALSE;
                }
            }
            else {
                echo "Unable to fetch user_id";
            }
        }
        catch (PDOException $e) {
            echo "ERROR: " . getMessage($e);
        }
    }

    function    create_user($email, $username, $passwd, $pdo) {

        try {
        $hashed = password_hash($passwd, PASSWORD_DEFAULT);
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        $insert_user = "INSERT INTO users(`email`, `username`, `password`, `token`)
                    VALUES (:email, :username, :password, :token);";
        $stmt = $pdo->prepare($insert_user);
        $stmt->execute(array(':email' => $email, ':username' => $username, ':password' => $hashed, ':token' => $token));
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
        if (send_mail($email, $token, $pdo)) {
            echo "Activation email sent to $email. Follow the link to activate your account.";
            $stmt->close();
        }
    }

    if ($submit === 'Create' && $email && $username && $passwd && $validate_pw) {
        
        $pdo = connect();

        if (!$mess = user_exists($email, $username, $pdo)) {
            if (input_is_valid($email, $username, $passwd, $validate_pw)) {
                create_user($email, $username, $passwd, $pdo);
                unset($_POST);
            }
        }
        else {
            echo $mess;
        }
    }

?>