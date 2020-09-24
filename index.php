<?php
	session_start();

	if (isset($_SESSION['logged_user']))
		$user = $_SESSION['logged_user'];
	else
		$user = "";
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Camigru</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<link rel="stylesheet" href="styles/gallery.css" type="text/css" media="all">
		<meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/ico" href="favicon.ico"/>
	</head>
	<body style="margin-top: 20px;">
		<header>
			<nav class="navbar fixed-top navbar-expand-sm navbar-light bg-light" style="padding-left: 25vw;">
				<a class="navbar-brand" href="index.php?page=gallery">Camigru</a>
				<input type="checkbox" id="lua-navbar-toggler" class="d-none" />
				<label for="lua-navbar-toggler" class="navbar-toggler" data-toggle="collapse" data-target="#lua-navbar-content" aria-controls="lua-navbar-content" aria-expanded="false" aria-label="Toggle navigation">
      				<span class="navbar-toggler-icon"></span>
    			</label>
				<div class="collapse navbar-collapse" id="lua-navbar-content"> 
					<ul class="navbar-nav mr-auto"> 
			<?php if (!$user || $user == "") { ?>
						<li class="nav-item active">
							<a class="navbar-brand" href="index.php?page=account/create">Create account</a>
						</li>
						<li class="nav-item">
							<a class="navbar-brand" href="index.php?page=account/login">Log in</a>
						</li>
			<?php }	else { ?>
						<li class="nav-item">
							<a class="navbar-brand" href="index.php?page=profile">Profile</a>
						</li>
						<li class="nav-item">
							<a class="navbar-brand" href="index.php?page=upload">New image</a>
						</li>
						<li class="nav-item">
							<a class="navbar-brand" style="align-self: right;" href="index.php?page=account/logout">Log out</a>
						</li>
				<?php } ?>
					</ul>
			  	</div>
			</nav>
		</header>
		<div style="position: relative; padding-top: 50px; min-height: 89vh;">
				<?php
					if (isset($_GET['page']) && $_GET['page'] != '')
						$page = $_GET['page'];
					else
						$page = 'gallery';
					include($page.'.php');
				?>
		</div>
		<footer class="page-footer">
			<nav class="navbar navbar-light bg-light"><p>© <a href="http://www.github.com/cleisti/">cleisti</a> 2020</p></nav>
		</footer>
	</body>
</html>