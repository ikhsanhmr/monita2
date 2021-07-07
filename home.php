<?php
error_reporting(0);  session_start();
if(!isset($_SESSION['nip'])) {
	echo "unauthorized user";
	echo "<script>window.open('index.php', '_parent')</script>";
	exit;
}
?>

<html>
 <head><title>Monita (Monitoring Anggaran)</title>
 <link rel="stylesheet" type="text/css" href="css/main.css"/></head>
 <frameset cols="22%,*" frameborder="no" border="0" >
 <frame name="menu" src="menu.php"scrolling="no" >
 <frame name="content" src="dashboard.php" scrolling="auto">
 </frameset><noframes></noframes>
 </html> 