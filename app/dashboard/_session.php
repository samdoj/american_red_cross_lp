<?php
// start or resume existing session
session_start();

// if the last request was more than 10 minutes ago (600 = 60 seconds * 10),
// automatically log out the user
if ((time() - $_SESSION['LAST_ACTIVITY']) > 600) {
	session_unset();     // unset $_SESSION variable for the run-time
	session_destroy();   // destroy session data in storage

	// redirect user to the login page
	header("location: index.php");
}

// include database login credentials
include_once('login_info.php');

// store session
$user_check = $_SESSION['login_user'];

// find login credentials matching form input
$ses_sql = mysql_query("SELECT username FROM cstm_users WHERE username='$user_check'");
$row = mysql_fetch_assoc($ses_sql);
$login_session = $row['username'];

if(!isset($login_session)){
	// close database connection
	mysql_close($cn);

	// redirect user to the login page
	header('location: index.php');
}

$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
?>