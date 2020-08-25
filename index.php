<?php
	include 'config/setup.php';
	session_start();

	$user = $_SESSION['logged_user'] ? $_SESSION['logged_user'] : "";
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Camigru</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<!-- <link rel="stylesheet" type="text/css" href="styles/style.css"> -->
		<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body>
		<header>
			<nav class="navbar fixed-top navbar-light bg-light" style="display: flex; align-items: center;">
				<a class="navbar-brand" href="index.php?page=gallery">Gallery</a>
		<?php
			if (!$user || $user == "") {
				?>
				<a class="navbar-brand" href="index.php?page=account/create">Create account</a>
				<a class="navbar-brand" href="index.php?page=account/login">Log in</a>
				<?php
			}
			else {
				?>
				<a class="navbar-brand" href="index.php?page=profile">Profile</a>
				<a class="navbar-brand" href="index.php?page=upload">New image</a>
				<a class="navbar-brand" href="index.php?page=account/logout">Log out</a>
				<?php
			}
		?>
			</nav>
		</header>
		<div class="d-flex flex-column" style="position: relative; padding-top: 50px;">
				<?php
					if (isset($_GET['page']) && $_GET['page'] != '')
						$page = $_GET['page'];
					else
						$page = 'gallery';
					include($page.'.php');
				?>
		</div>
		<footer class="page-footer">
			<nav class="navbar fixed-bottom navbar-light bg-light"></nav>
		</footer>
	</body>
</html>