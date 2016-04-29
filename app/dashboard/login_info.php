<?php
$hostname = 'localhost';
$loginname = 'altegram_admin';
$loginpassword = 'B@yard16!';

$cn = mysql_connect($hostname, $loginname , $loginpassword) or die( mysql_error() );
mysql_select_db("altegram_database") or die( mysql_error() );
?>
