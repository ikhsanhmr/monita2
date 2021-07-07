<?php
	session_start(); 
	require_once '../../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}
	
//	echo ($edit!=""? "isi" : "kosong");
	$skk = $_POST["skk"];
	$norab = $_POST["norab"];
	$nilai = $_POST["nilai"];
	$tanggalrab = $_POST["tanggalrab"];
	$uraian = $_POST["uraian"];
	//$nilai = $_REQUEST["nilai"];

	$sql=mysqli_query("insert into rab(skk, no_rab,nilai_rp,tgl_rab,uraian_kegiatan,nip) values('$skk','$norab','$nilai','$tanggalrab','$uraian','$nip')");



	
	
	//$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	// echo "$sukses<br>";
	
	//	
	if ($sql) {					
	echo "<script>window.open('index.php', '_self')</script>";
	}
?>