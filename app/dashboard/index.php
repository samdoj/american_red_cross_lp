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