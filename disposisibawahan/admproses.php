<?php
	require_once "config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);
	$sql = "UPDATE notadinas SET progress = COALESCE(progress,0) + 1 WHERE nomornota = '$_GET[n]'";
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
	$mysqli->close();($link);	
	echo "<script>window.open('content.php','_self');</script>";
?>
