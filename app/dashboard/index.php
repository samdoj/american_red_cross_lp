<?php
// if user clicked the "logout" button, destroy the session
if ($_GET['status'] == 'logout') {
	session_start();
	// destroy the session
	if(session_destroy()) {
		// redirect user to the login page
		header("location: index.php");
	}
// otherwise, display the login form
} else {
	// include the login script
	include('_login.php');

	// redirect the user to the admin panel
	if(isset($_SESSION['login_user'])){
		header("location: admin.php");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="inc/bootstrap.min.css">
	<link rel="stylesheet" href="inc/dashboard.css">
	
	<!-- Favicon -->
	<link rel="apple-touch-icon" sizes="57x57" href="media/favicons/apple-touch-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="media/favicons/apple-touch-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="media/favicons/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="media/favicons/apple-touch-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="media/favicons/apple-touch-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="media/favicons/apple-touch-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="media/favicons/apple-touch-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="media/favicons/apple-touch-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="media/favicons/apple-touch-icon-180x180.png">
	<link rel="icon" type="image/png" href="media/favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="media/favicons/android-chrome-192x192.png" sizes="192x192">
	<link rel="icon" type="image/png" href="media/favicons/favicon-96x96.png" sizes="96x96">
	<link rel="icon" type="image/png" href="media/favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="media/favicons/manifest.json">
	<link rel="mask-icon" href="media/favicons/safari-pinned-tab.svg" color="#ed1b2e">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="msapplication-TileImage" content="/mstile-144x144.png">
	<meta name="theme-color" content="#ed1b2e">

</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-6 col-sm-push-3 col-md-4 col-md-push-4">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
					<h1>Please Log In</h1>
					<?php echo $error; ?>
					<p>
						<label for="username">User Name :</label>
						<input type="text" class="form-control" id="username" name="username">
					</p>
					<p>
						<label for="password">Password :</label>
						<input type="password" class="form-control" id="password" name="password">
					</p>
					<p>
						<input type="submit" class="btn btn-lg btn-primary btn-block" name="submit" value=" Login ">
					</p>
				</form>
			</div><!-- col-sm-* -->
		</div><!-- row -->
	</div><!-- /container -->

</body>
</html>