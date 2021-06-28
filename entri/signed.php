<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$c = (isset($_REQUEST["c"])? $_REQUEST["c"]: "");
	$s = (isset($_REQUEST["s"])? $_REQUEST["s"]: "");
	$i = (isset($_REQUEST["i"])? $_REQUEST["i"]: "");
	$nip=$_SESSION['nip'];

	if($nip==""||$c=="") {die('error api');}
	
	
	$sql = ($s==0?
		"update kontrak set signed = null, unsigneddt = sysdate() where nomorkontrak = '$c'":
		"update kontrak set signed = '$nip', signeddt = sysdate() where nomorkontrak = '$c'"
	);
	//echo "$sql<br>";
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
	echo json_encode(array($c, $s, $i));
	
	// echo "<script>window.open('ai.php', '_self')</script>";
?>