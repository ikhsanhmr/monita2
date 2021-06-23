<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	
	if($nip=="") {exit;}
	
	$k = $_REQUEST["k"];
	$p = $_REQUEST["p"];
	
	$sql = "DELETE FROM asetpdp WHERE nomorkontrak = '$k' AND pdpid = '$p'";
	//echo $sql;
	//foreach($_REQUEST as $param_name => $param_val) { echo "parameter : $param_name - $param_val <br>"; }
	
	$sukses = mysql_query($sql);// or die(mysql_error());
	echo "$sukses";
	
	//
	mysql_close($link);	  							
?>