<?php
$hostname = '127.0.0.1';
$loginname = 'root';
$loginpassword = 'root';

$cn = mysql_connect($hostname, $loginname , $loginpassword) or die( mysql_error() );
mysql_select_db("arc_biomed_database") or die( mysql_error() );
?>