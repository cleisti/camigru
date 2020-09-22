<?php
// require_once "Mail.php";

$from = "admin@camigru";
$to = 'camilla.leisti@gmail.com';

$host = "ssl://smtp.gmail.com";
$port = "465";
$username = 'camilla.leisti@gmail.com';
$password = 'L1lL4n4NuscH4';

function	send_mail($email, $subject, $content, $pdo) {

	$get_email = "SELECT users.email AS mailAddr FROM users INNER JOIN images ON users.user_id = images.img_user_id WHERE img_id = :imageId;";
	$stmt = $pdo->prepare($get_email);
	$stmt->execute(array(':imageId' => $imageId));
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$email = $res['mailAddr'];
	$subject = "A new comment on you picture";
	$content = $username . "commented on your image:<br><br>" . $comment;
	$headers = 'From: admin@camigru.com' . "\r\n";
	if (mail($email, $subject, $content, $headers))
		return true;
	else
		return false;
}

// $subject = "test";
// $body = "test";

// $headers = array ('From' => $from, 'To' => $to,'Subject' => $subject);
// $smtp = Mail::factory('smtp',
//   array ('host' => $host,
//     'port' => $port,
//     'auth' => true,
//     'username' => $username,
//     'password' => $password));

// $mail = $smtp->send($to, $headers, $body);

// if (PEAR::isError($mail)) {
//   echo($mail->getMessage());
// } else {
//   echo("Message successfully sent!\n");
// }
?>