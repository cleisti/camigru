<!DOCTYPE html>
<html>
<head>
</head>
    <body>
        <h2>Create account</h2>
        <form action="" method="post">
            Email: <input type="email" name="email" value="" required />
			Username: <input type="text" name="login" minlength="4" maxlength="25" value="" required />
            <br />
            Password: <input type="password" name="passwd" minlength="8" maxlength="20" value="" required />
            <br />
            Validate password: <input type="password" name="validate_pw" minlength="8" maxlength="20" value="" required />
            <input class="button" type="submit" name="submit" value="Create" />
        </form>
        <br />
    </body>
</html>

<?php
    include '../config/connect.php';
    session_start();

    $submit = $_POST['submit'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST['login'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $passwd = filter_var($_POST['passwd'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
    $validate_pw = filter_var($_POST['validate_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

    function    user_exists($email, $username, $pdo) {
        try {
            $check_user = "SELECT username FROM users
                    WHERE username = :username;";
        
            $stmt = $pdo->prepare($check_user);
            $stmt->execute(array(':username' => $username));
            $res = $stmt->fetch();
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage;
        }
        if ($res) {
            echo "User already exists.";
            return TRUE;
        }
        try {
            $check_email = "SELECT email FROM users
                    WHERE email = :email;";
            
            $stmt = $pdo->prepare($check_email);
            $stmt->execute(array(':email' => $email));
            $res = $stmt->fetch();
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage;
        }
        if ($res) {
            echo "This email is already in use on another account.";
            return TRUE;
        }
        return FALSE;
    }

    function    input_is_valid($email, $username, $passwd, $validate_pw) {

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {        
            if (preg_match('/[^a-zA-Z0-9_]/', $username)) {
                echo "Username contains illegal characters.<br>Allowed characters are alphabetical characters, numerical characters and _.";
                return (0);
            }
            if (!preg_match('/^(?=.*\d)(?=.*[a-zA-Z]).*[a-z0-9!?@#$%]$/', $passwd)) {
                echo "Password must contain at least one alphabetical character and one number.<br>Special characters ?, !, @, #, $ and % are allowed.";
                return (0);
            }
            if ($passwd !== $validate_pw) {
                echo "Password doesn't match validation. Try again.";
                return (0);
            }
            return (1);
        }
        else {
            echo "Invalid email.";
            return (0);
        }
    }

    function    send_mail($email, $token, $pdo) {
        try {
            $get_id = "SELECT user_id FROM users
                    WHERE email = :email LIMIT 1;";
            $stmt = $pdo->prepare($get_id);
            $stmt->execute(array(':email' => $email));
            $id = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($id['user_id']) {
                $url = 'http://localhost:8080/camigru/account/verify.php?token=' . $token .'&id=' . $id['user_id'];
                $subject = "Activate your account at Camigru";
                $content = "Click this link to activate your account: " . $url;
                $headers = 'From: admin@camigru.com' . "\r\n";
                if (mail($email, $subject, $content, $headers))
                {
                    return (1);
                }
                else {
                    echo "Unable to send activation email.";
                    return (0);
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
        }
    }

    if ($submit === 'Create' && $email && $username && $passwd && $validate_pw) {
        
        $pdo = connect();

        if (!user_exists($email, $username, $pdo)) {
            if (input_is_valid($email, $username, $passwd, $validate_pw)) {
                create_user($email, $username, $passwd, $pdo);
                unset($_POST);
            }
        }
    }
?>