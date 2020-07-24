<?php
	include 'config/setup.php';
	session_start();

	$user = $_SESSION['logged_user'] ? $_SESSION['logged_user'] : "";
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Camigru</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<header>
		<?php
			if (!$user || $user == "") {
				?>
				<a href="index.php?page=account/create">Create account</a><br />
				<a href="index.php?page=account/login">Log in</a>
				<?php
			}
			else {
				?>
				<a href="index.php?page=profile">Profile</a>
				<a href="index.php?page=account/logout">Log out</a>
				<?php
			}
		?>
		</header>
		<div id="container">
			<div class="side">
			</div>
			<div class="middle">
				<?php
					if (isset($_GET['page']) && $_GET['page'] != '')
						$page = $_GET['page'];
					else
						$page = 'home';
					include($page.'.php');
				?>
			</div>
			<div class="side">
			</div>
		</div>
		<footer>
			<p>footer</p>
		</footer>
	</body>
</html>