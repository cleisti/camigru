<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
	<div class="d-flex p-2 justify-content-center align-content-around">
	<table>
        <form action="" method="post" enctype="multipart/form-data">
            <tr>
				<th colspan="2">Reset your password</th>
            </tr>
            <tr>
				<td>E-mail</td>
                <td><input style="margin: 10px;" type="email" name="email" value="" /></td>
            </tr>
            <tr>
				<td>Username</td>
				<td><input style="margin: 10px;" type="text" name="login" minlength="4" maxlength="25" value="" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input style="margin: 10px;" type="submit" name="send" value="Send" /></td>
			</tr>
		</form>
	</table>
</div>
	</body>
</html>

<?php
	include 'config/connect.php';
	include 'validation.php';
	// session_start();

	function	get_email($username, $pdo) {
		try {
			$get_email = "SELECT email FROM users
						WHERE username = :username";
			$stmt = $pdo->prepare($get_email);
			$stmt->execute(array(':username' => $username));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			return ($res['email']);
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage();
		}
	}

	function    send_reset_link($email, $pdo) {
        try {
            $get_id = "SELECT user_id FROM users
                    WHERE email = :email LIMIT 1;";
            $stmt = $pdo->prepare($get_id);
            $stmt->execute(array(':email' => $email));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$id = $res['user_id'];
			$reset = bin2hex(openssl_random_pseudo_bytes(16));

            if ($id && $reset) {
                $url = 'http://localhost:8080/camigru/index.php?page=account/reset&reset=' . $reset .'&id=' . $id;
                $subject = "Reset link";
                $content = "Click this link to reset your password: " . $url;
                $headers = 'From: admin@camigru.com' . "\r\n";
                if (mail($email, $subject, $content, $headers))
                {
					$set_reset_token = "UPDATE users SET reset = :reset WHERE user_id = :id;";
					$stmt = $pdo->prepare($set_reset_token);
					$stmt->execute(array(':reset' => $reset, ':id' => $id));
					echo "Activation link sent to $email.";
					header("refresh:5;url=index.php?page=account/login");
                }
                else {
                    echo "Unable to send activation link.";
                }
			}
            else {
                echo "Unable to find user.";
            }
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }

	if ($_POST && $_POST['send'] === 'Send' && (isset($_POST['email']) || isset($_POST['login']))) {
		
		$pdo = connect();

		$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
		$username = filter_var($_POST['login'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

		if (user_exists($email, $username, $pdo)) {
			if ($email === "") {
				$email = get_email($username, $pdo);
			}
			send_reset_link($email, $pdo);
		}
		else {
			echo "Couldn't find user. Please check spelling or contact customer service.";
		}
	}
?>