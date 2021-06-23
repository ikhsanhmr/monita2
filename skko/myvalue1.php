<?php
	session_start();
	$nip = $_SESSION['nip'];
	if($nip=="") {
		exit;
	}

	$t = trim($_REQUEST["t"]);
	$i = trim($_REQUEST["i"]);
	require_once "../config/control.inc.php";	
	
	$query = "SELECT * FROM notadinas_detail WHERE nid = '$i'";
//	echo "$query<br>";
	
	if ($result = mysql_query($query)) {
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			echo "$row[pos1]<data>$row[nilai1]";
		}
		mysql_free_result($result);
	}
	mysql_close($kon);
?>