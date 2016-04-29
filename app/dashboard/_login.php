<?php

// begin session
session_start();

// variable to store error message
$error = '';

$submitDateTime = date('Y-m-d H:i:s');
$submittedIP    = $_SERVER['REMOTE_ADDR'];

if (isset($_POST['submit'])) {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		$error = "<p>* Username or Password is invalid</p>";
	} else {
		// define $username and $password
		$username = $_POST['username'];
		$password = $_POST['password'];

		// include database login credentials
		include_once('login_info.php');

		// find login credentials matching form input
		$query = "SELECT salt, password FROM cstm_users WHERE username='$username'";
		// $rows = mysql_num_rows($query);
		$result = mysql_query($query) or die( mysql_error() );
		$rows = mysql_fetch_assoc($result);

		// tell crypt to use blowfish for 10 rounds.
		$Blowfish_Pre = '$2a$10$';
		$Blowfish_End = '$';

		$hashed_pass = crypt($password, $Blowfish_Pre . $rows['salt'] . $Blowfish_End);

		if ($hashed_pass == $rows['password']) {
			// save successful login attempt to history log
			$sql = sprintf("INSERT INTO cstm_users_login_history (username, successful, submitted, submitted_ip) VALUES ('%s', '1', '$submitDateTime', '$submittedIP')",
			    mysql_real_escape_string($username)
			  );
			// execute query
			$result = mysql_query($sql);

			// initialize session
			$_SESSION['login_user'] = $username;

			// set time of last activity
			$_SESSION['LAST_ACTIVITY'] = time();

			// redirect to admin page
			header("location: index.php");
		} else {
			// save unsuccessful login attempt to history log
			$sql = sprintf("INSERT INTO cstm_users_login_history (username, successful, submitted, submitted_ip) VALUES ('%s', '0', '$submitDateTime', '$submittedIP')",
			    mysql_real_escape_string($username)
			  );
			// execute query
			$result = mysql_query($sql);

			$error = "<p>* Username or Password is invalid</p>";
		}

		// close database connection
	    mysql_close($cn);
	}
}

?>