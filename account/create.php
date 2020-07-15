<!DOCTYPE html>
<html>
<head>
</head>
    <body>
        <h2>Create account</h2>
        <form action="" method="post">
			<span style="color: red">*</span> Username: <input type="text" name="login" value="" />
            <br />
            <span style="color: red">*</span> Password: <input type="password" name="passwd" value="" />
            <input class="button" type="submit" name="submit" value="Create" />
        </form>
        <br />
    </body>
</html>

<?php
    include '../config/connect.php';
    include_once '../config/setup.php';
    session_start();
    $pdo = connect();

    function    user_exists($username) {
        $check_user = "SELECT username FROM users
                    WHERE username = ':name;'";
        
        $stmt = $pdo->prepare($check_user);
        $stmt->bindParam(':name', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "User already exists";
            return TRUE;
        }
        else {
            echo "ok";
            return FALSE;
        }
    }

    function    create_user($username, $passwd) {
        $hashed = hash(whirlpool, $passwd);
        $sql_insert = "INSERT INTO users(`username`, `password`)
                    VALUES ('$username', '$hashed');";
        
        try {
            $stmt = $pdo->prepare($sql_insert);
            $stmt->exec($sql_insert);
            echo "User created";
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }

    // create validation functions for username and password && validate them first with filter_var

    if ($_POST['submit'] === 'Create') {
        if ($_POST['login'] && $_POST['passwd']) {
            if (!user_exists($_POST['login'])) {
                if (create_user($_POST['login'], $_POST['passwd'])) {
                    echo "User created successfully.<br>You can log in now.";
                }
            }
        }
    }
?>
