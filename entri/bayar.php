<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$k = (isset($_REQUEST["k"])? $_REQUEST["k"]: "");
	$lvl = (isset($_REQUEST["lvl"])? $_REQUEST["lvl"]: "");
	$id = (isset($_REQUEST["id"])? $_REQUEST["id"]: "");
	$t = (isset($_REQUEST["t"])? $_REQUEST["t"]: "");
	$nip=$_SESSION['cnip'];

	if($nip==""||$k==""||$id==""||$lvl=="") {die('error api');}
	
	
	$sql = "INSERT INTO kontrak_approval (nomorkontrak, actiontype, signdt, signed, signlevel, id_kontrak) VALUES ('$k', '$t', sysdate(), '$nip', '$lvl', '$id')";

	//echo "$sql<br>";
	$sukses = mysql_query($sql);// or die(mysql_error());
	echo json_encode(array($k, $lvl, $id, $sukses));
	
	// echo "<script>window.open('ai.php', '_self')</script>";
?>