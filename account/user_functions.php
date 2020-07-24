<!DOCTYPE html>
<html>
<head>
</head>
    <body>
		<h2>Change username</h2>
        <form action="" method="post">
            New username:<br />
			<input type="text" name="new_un" minlength="4" maxlength="25" value="" required /><br />
            <input class="button" type="submit" name="submit" value="Change username" />
        </form>
		<h2>Change email-address</h2>
        <form action="" method="post">
            New email:<br />
			<input type="email" name="new_email" value="" required /><br />
			Validate email:<br />
			<input type="email" name="validate_email" value="" required /><br />
            <input class="button" type="submit" name="submit" value="Change email" />
        </form>
        <h2>Change password</h2>
        <form action="" method="post">
            Old password:<br />
			<input type="password" name="old_pw" minlength="8" maxlength="20" value="" required />
			<br />
			New password:<br />
			<input type="password" name="new_pw" minlength="8" maxlength="20" value="" required />
			<br />
            Validate password:<br />
			<input type="password" name="validate_pw" minlength="8" maxlength="20" value="" required /><br />
            <input class="button" type="submit" name="submit" value="Change password" />
        </form>
        <br />
    </body>
</html>

<?php
	include '../config/connect.php';
	include 'validation.php';
	session_start();

	$submit = $_POST['submit'];
	$user = $_SESSION['logged_user'];

	function		change_password($info, $user, $pdo) {
		try {
			$get_pass = "SELECT password FROM users
						WHERE username = :username;";
			$stmt = $pdo->prepare($get_pass);
			$stmt->execute(array(':username' => $user));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$pass = $res['password'];
			$check = password_hash($info['old_pw'], PASSWORD_DEFAULT);
		
			if (password_verify($info['old_pw'], $pass)) {
				$hashed = password_hash($info['new_pw'], PASSWORD_DEFAULT);
				$change_pass = "UPDATE users
								SET password = :password
								WHERE username = :username;";
				$stmt = $pdo->prepare($change_pass);
				$stmt->execute(array(':password' => $hashed, ':username' => $user));
				echo "Password successfully changed. Redirecting to log in page . . .";
			}
			else {
				echo "Wrong password";
				exit;
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
	}

	function	update_username($new_un, $user, $pdo) {
		try {
			$get_email = "SELECT email FROM users WHERE username = :user;";
			$stmt = $pdo->prepare($get_email);
			$stmt->execute(array(':user' => $user));
			$res = $stmt->fetch(PDO::FETCH_ASSOC);
			$email = $res['email'];

			$change_user = "UPDATE users SET username = :new_un WHERE username = :user;";
			$stmt = $pdo->prepare($change_user);
			$stmt->execute(array(':new_un' => $new_un, ':user' => $user));

			$content = 'Your username has been changed to ' . $new_un . '. If this was not you, please contact support.';
			$subject = "Username changed";
			$headers = 'From: admin@camigru.com' . "\r\n";
			$_SESSION['logged_user'] = $new_un;
			if (mail($email, $subject, $content, $headers)) {
				echo "Username successfully changed. Notification sent to $email.";

			}
			else {
				echo "Username successfully changed. Couldn't send notification to $email. Is the address correct?";
			}
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
	}

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
                $subject = "Verify your email";
                $content = "Click this link to verify your email: " . $url;
				$headers = 'From: admin@camigru.com' . "\r\n";
                if (mail($email, $subject, $content, $headers)) {
                    return TRUE;
                }
                else {
                    echo "Unable to send verification email.";
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

	function	update_email($new_email, $user, $pdo) {
		try {
			$token = bin2hex(openssl_random_pseudo_bytes(16));
			$change_email = "UPDATE users SET email = :email, verified = :zero, token = :token WHERE username = :user;";
			$stmt = $pdo->prepare($change_email);
			$stmt->execute(array(':email' => $new_email, ':zero' => 0, ':token' => $token, ':user' => $user));
		}
		catch (PDOException $e) {
			echo "ERROR: " . getMessage($e);
		}
		if (send_mail($new_email, $token, $pdo)) {
			echo "Verification email sent to $new_email. You need to verify your email before logging back in.";
        }
	}

	if (isset($user)) {

		if ($submit === 'Change password') {

			$info['old_pw'] = filter_var($_POST['old_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$info['new_pw'] = filter_var($_POST['new_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$info['validate_pw'] = filter_var($_POST['validate_pw'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

			if (input_is_valid(NULL, NULL, $info['new_pw'], $info['validate_pw'])) {
				$pdo = connect();
				change_password($info, $user, $pdo);
				unset($_POST);
				unset($info);
				$_SESSION['logged_user'] = "";
				header("refresh:5;url=index.php?page=account/login");
			}
		}

		else if ($submit === "Change username") {

			$pdo = connect();
			$new_un = filter_var($_POST['new_un'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			
			if ($new_un === $_SESSION['logged_user']) {
				echo "This already is your username . . .";
			}
			else if (!$mess = user_exists(NULL, $new_un, $pdo)) {
				if (input_is_valid(NULL, $new_un, NULL, NULL)) {
					update_username($new_un, $user, $pdo);
					unset($_POST);
					$_SESSION['logged_user'] = "";
					header("refresh:5;url=index.php?page=account/login");
				}
			}
			else {
				echo $mess;
			}
		}

		else if ($submit === "Change email") {

			$pdo = connect();
			$new_email = filter_var($_POST['new_email'], FILTER_SANITIZE_EMAIL);
			$validate_email = filter_var($_POST['validate_email'], FILTER_SANITIZE_EMAIL);

			if (strcmp($new_email, $validate_email) === 0) {
				if (!$mess = user_exists($new_email, NULL, $pdo)) {
					if (input_is_valid($new_email, NULL, NULL, NULL)) {
						update_email($new_email, $user, $pdo);
						unset($_POST);
						$_SESSION['logged_user'] = "";
						header("refresh:5;url=index.php?page=account/login");
					}
				}
				else {
					echo $mess;
				}
			}
			else {
				echo "Check spelling.";
			}
		}
	}

	else {
		echo "You are not authorized to access this page.";
		header("Location: index.php");
	}
?>