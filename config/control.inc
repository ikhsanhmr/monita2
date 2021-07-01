<?php
	$srv = "localhost";
	$usr = "root";
	$pwd = "root";
	$db = "newmonita";

$kon =new mysqli($srv, $usr, $pwd,$db);
if(!$kon)
die('ERROR KARENA'.mysqli_error());

$dbkon  =mysqli_select_db($db,$kon);
if(!$dbkon)
die('ERROR KARENA'.mysqli_error());
?>
