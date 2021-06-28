<?php
	session_start(); 
	$nip=$_SESSION['cnip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';

	$nomornota = $_REQUEST["nomornota"];
	$progress = $_REQUEST["progress"];

	$sukses = 0;
	$gagal = 0;
	$gagalmsg = '';

	if(!empty($nomornota) && !empty($progress)){
		$sql = "update notadinas set progress = '$progress' where nomornota= '$nomornota'";
		
		$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());

		if($sukses==1) {
			echo '<script>alert("Progress Nota Dinas berhasil dirubah.");</script>';
		}else{
			$message = mysql_error();
			echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
		}
	}
	
	$mysqli->close();($kon);
	echo '<script>window.open("editprogressnotadinas.php", "_self")</script>';
?>