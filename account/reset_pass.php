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
				<td>or Username</td>
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
	include_once 'config/connect.php';
	include_once 'get_user.php';
	include_once 'validation.php';

	function    send_reset_link($email, $id) {
		$pdo = connect();
		
		try {
			$reset = bin2hex(openssl_random_pseudo_bytes(16));
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
			}
			else {
				echo "Unable to send activation link. Please contact support.";
			}
        }
        catch (PDOException $e) {
            echo "ERROR: " . $e->getMessage();
        }
    }

	if ($_POST && $_POST['send'] === 'Send' && (isset($_POST['email']) || isset($_POST['login']))) {

		$email = $_POST['email'];
		$username = $_POST['login'];

		if (user_exists($email, $username)) {		
			if (!$email) {
				$user = get_user($username);
				$email = $user['email'];
				$id = $user['user_id'];
			}
			else
				$id = fetch_uId($email);
			
			send_reset_link($email, $id);
		}
		else
			echo "Couldn't find user. Please check spelling or contact customer service.";
	}
?>