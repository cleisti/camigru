<!DOCTYPE html>
<html>
	<head>
	</head>
	<body>
		<h2>Reset your password</h2>
		<form action="" method="post">
			E-mail: <input type="email" name="email" value="" />
			<br />or
			<br />
			Username: <input type="text" name="login" minlength="4" maxlength="25" value="" />
			<input type="submit" name="send" value="Send" />
		</form>
	</body>
</html>

<?php
	include '../config/connect.php';
	session_start();

	$submit = $_POST['send'];

	function    user_exists($email, $username, $pdo) {
	
		if ($email) {
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
				return TRUE;
			}
		}
		
		if ($username) {
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
				return TRUE;
			}
		}
		return FALSE;
	}

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
			$token = bin2hex(openssl_random_pseudo_bytes(16));

            if ($id && $token) {
                $url = 'http://localhost:8080/camigru/index.php?page=account/reset&token=' . $token .'&id=' . $id;
                $subject = "Reset link";
                $content = "Click this link to reset your password: " . $url;
                $headers = 'From: admin@camigru.com' . "\r\n";
                if (mail($email, $subject, $content, $headers))
                {
					$set_token = "UPDATE users SET token = :token WHERE user_id = :id;";
					$stmt = $pdo->prepare($set_token);
					$stmt->execute(array(':token' => $token, ':id' => $id));
					$stmt->close;
					echo "Activation link sent to $email.";
					return (1);
                }
                else {
                    echo "Unable to send activation link.";
                    return (0);
                }
            }
            else {
                echo "Unable to find user.";
            }
        }
        catch (PDOException $e) {
            echo "ERROR: " . getMessage($e);
        }
    }

	if ($submit === 'Send' && (isset($_POST['email']) || isset($_POST['login']))) {
		
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
	else if ($submit) {
		echo "Please enter your email or username.";
	}
?>