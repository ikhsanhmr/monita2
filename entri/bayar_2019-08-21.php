<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$k = (isset($_REQUEST["k"])? $_REQUEST["k"]: "");
	$lvl = (isset($_REQUEST["lvl"])? $_REQUEST["lvl"]: "");
	$id = (isset($_REQUEST["id"])? $_REQUEST["id"]: "");
	$t = (isset($_REQUEST["t"])? $_REQUEST["t"]: "");
	$nip=$_SESSION['cnip'];

	if($nip==""||$k==""||$id==""||$lvl=="") {die('error api');}
	
	
	$sql = "INSERT INTO kontrak_approval (nomorkontrak, actiontype, signdt, signed, signlevel) VALUES ('$k', '$t', sysdate(), '$nip', '$lvl')";

	//echo "$sql<br>";
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	echo json_encode(array($k, $lvl, $id, $sukses));
	
	// echo "<script>window.open('ai.php', '_self')</script>";
?>