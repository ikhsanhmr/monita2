<?php
	session_start();
	$nip = $_SESSION['nip'];
	if($nip=="") {
		exit;
	}

	$n = trim($_REQUEST["n"]);
	$p = trim($_REQUEST["p"]);
	$nid = trim($_REQUEST["nid"]);
	require_once "../config/control.inc.php";	
	
	// $query = "UPDATE notadinas_detail SET progress = NULL WHERE nomornota = '$n' AND pelaksana = '$p'";
	$query = "UPDATE notadinas_detail SET progress = NULL WHERE nid = '$nid'";
	mysqli_query($query);
	$mysqli->close();($kon);
	echo "<script>window.close()</script>";		
?>