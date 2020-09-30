<?php
include_once 'user_functions.php';
include_once '../get_user.php';

$user = get_user($_SESSION['logged_user']);

if (isset($_POST['getCheckboxInfo'])) {

	$info['user_id'] = $user['user_id'];

	$res = get_checkboxinfo($info);

	echo $res;
}

else if (isset($_POST['setNotifications'])) {

	$info['user_id'] = $user['user_id'];

	$res = change_notifications($info);

	echo $res;
}

else if ($_POST['submit'] === "ChangeUsername") {

	$info = array();
	$info['user'] = $user['username'];
	$info['new_un'] = $_POST['new_un'];
	$info['email'] = $user['email'];
	
	$mess = update_username($info);

	unset($_POST);
	unset($info);
	echo "<p style='text-align: center'>" . $mess . "</p>";
}

else if ($_POST['submit'] === "ChangeEmail") {

	$info = array();
	$info['user_id'] = $user['user_id'];
	$info['new_email'] = $_POST['new_email'];
	$info['validate_email'] = $_POST['validate_email'];

	$mess = update_email($info);

	unset($_POST);
	unset ($info);
	echo "<p style='text-align: center'>" . $mess . "</p>";
}

else if ($_POST['submit'] === 'ChangePassword') {

	$info = array();
	$info['user'] = $user['username'];
	$info['old_pw'] = $_POST['old_pw'];
	$info['new_pw'] = $_POST['new_pw'];
	$info['validate_pw'] = $_POST['validate_pw'];

	$mess = update_password($info);

	unset($_POST);
	unset($info);
	echo "<p style='text-align: center'>" . $mess . "</p>";
}
?>