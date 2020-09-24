<?php
	include_once '../config/connect.php';
	include_once 'validation.php';
	session_start();
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
				return true;
			}
			else {
				return false;
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
			mail($email, $subject, $content, $headers);
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
		send_mail($new_email, $token, $pdo);
	}

	if (isset($user)) {

		if ($_POST['submit'] === 'Change password') {

			$info['old_pw'] = $_POST['old_pw'];
			$info['new_pw'] = $_POST['new_pw'];
			$info['validate_pw'] = $_POST['validate_pw'];

			if (input_is_valid(NULL, NULL, $info['new_pw'], $info['validate_pw'])) {
				$pdo = connect();
				if (change_password($info, $user, $pdo)) {
					unset($_POST);
					unset($info);
					header("refresh:3;url=../index.php?page=account/logout");
				}
				else {
					echo "Wrong password";
					header("refresh:3;url=../index.php?page=profile");
				}
			}
		}

		else if ($_POST['submit'] === "Change username") {

			$pdo = connect();
			$new_un = $_POST['new_un'];
			
			if ($new_un === $_SESSION['logged_user']) {
				echo "This already is your username . . .";
			}
			else if (!$mess = user_exists(NULL, $new_un, $pdo)) {
				if (input_is_valid(NULL, $new_un, NULL, NULL)) {
					
					update_username($new_un, $user, $pdo);
					unset($_POST);
					header("refresh:1;url=../index.php?page=profile");
				}
			}
			else {
				echo $mess;
				header("refresh:3;url=../index.php?page=profile");
			}
		}

		else if ($_POST['submit'] === "Change email") {

			$pdo = connect();
			$new_email = filter_var($_POST['new_email'], FILTER_SANITIZE_EMAIL);
			$validate_email = filter_var($_POST['validate_email'], FILTER_SANITIZE_EMAIL);

			if (strcmp($new_email, $validate_email) === 0) {
				if (!$mess = user_exists($new_email, NULL, $pdo)) {
					if (input_is_valid($new_email, NULL, NULL, NULL)) {
						update_email($new_email, $user, $pdo);
						unset($_POST);
						header("refresh:1;url=../index.php?page=profile");
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

		else if ($_POST['notifications']) {
			$pdo = connect();
			$id = $_POST['notifications'];
			$query = "SELECT verified FROM users WHERE user_id = :userId;";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array(':userId' => $id));
			$res = $stmt->fetchColumn();

			if ($res == 1) {
				$query = "UPDATE users SET verified = :two WHERE user_id = :userId;";
				$stmt = $pdo->prepare($query);
				$stmt->execute(array(':two' => 2, ':userId' => $id));
			}
			else {
				$query = "UPDATE users SET verified = :one WHERE user_id = :userId;";
				$stmt = $pdo->prepare($query);
				$stmt->execute(array(':one' => 1, ':userId' => $id));
			}
			echo $res;
		}

		else if ($_POST['checkboxInfo']) {
			$pdo = connect();
			$id = $_POST['checkboxInfo'];
			$query = "SELECT verified FROM users WHERE user_id = :userId;";
			$stmt = $pdo->prepare($query);
			$stmt->execute(array(':userId' => $id));
			$res = $stmt->fetchColumn();

			echo $res;
		}
	}

	else {
		echo "You are not authorized to access this page.";
		header("refresh:3;url=../index.php?page=account/logout");
	}
?>