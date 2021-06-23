<?php
	session_start(); 
	$nip=$_SESSION['cnip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';

	$id = $_REQUEST["id"];
	$tgl = $_REQUEST["tanggal"];
	$nilai = $_REQUEST["nilai"];

	$sukses = 0;
	$gagal = 0;
	$gagalmsg = '';

	$sql = ($id==""? "INSERT INTO kurs_dollar (tanggal, nilaitengah) VALUES ('$tgl', '$nilai')" : 						"update kurs_dollar set 
								tanggal = '$tgl',
								nilaitengah = '$nilai'
							 where id= $id"
			);
		
	$sukses = mysql_query($sql);// or die(mysql_error());

	if($sukses==1) {
		echo '<script>alert("Nilai Kurs berhasil disimpan.");</script>';
	}else{
		$message = mysql_error();
		echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
	}

	mysql_close($kon);
	echo '<script>window.open("kurs.php", "_self")</script>';
?>