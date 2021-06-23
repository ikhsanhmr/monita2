<?php
	$srv = "localhost";
	$usr = "root";
	$pwd = "";
	$db = "newmonita";

$kon =mysql_connect($srv, $usr, $pwd);
if(!$kon)
die('ERROR KARENA'.mysql_error());

$dbkon  =mysql_select_db($db,$kon);
if(!$dbkon)
die('ERROR KARENA'.mysql_error());
?>