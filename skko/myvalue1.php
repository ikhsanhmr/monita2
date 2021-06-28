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
	
	if ($result = mysqli_query($query)) {
		while ($row = mysqli_fetch_array($result)) {
			echo "$row[pos1]<data>$row[nilai1]";
		}
		mysqli_free_result($result);
	}
	$mysqli->close();($kon);
?>