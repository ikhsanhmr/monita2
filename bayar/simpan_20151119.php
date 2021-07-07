<?php
	error_reporting(0);  session_start(); 
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';
	$n = $_REQUEST["n"];
	$t = $_REQUEST["t"];
	$t = (substr($t,2,1)=="/"? substr($t,-4)."/".substr($t,0,2)."/".substr($t,3,2): $t);
	$k = $_REQUEST["k"];
	
	$sql = "INSERT INTO realisasibayar(nokontrak, nilaibayar, tglbayar) VALUES('$k', '$n', '$t')";	
	//echo $sql;
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	$mysqli->close();($link);	  							
	echo $sukses;
?>