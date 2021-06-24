<?php
require_once "parser-php-version.php";
// koneksi ke mysql
$srv = "localhost";
$usr = "root";
$pwd = "root";
// $usr = "monita";
// $pwd = "monita74736";
$db = "newmonita";

$kon = mysql_connect($srv, $usr, $pwd);
if (!$kon)
	die('ERROR KARENA' . mysql_error());

$dbkon  = mysql_select_db($db, $kon);
if (!$dbkon)
	die('ERROR KARENA' . mysql_error());
