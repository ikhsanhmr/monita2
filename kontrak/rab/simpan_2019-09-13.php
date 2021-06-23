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
	$edit = $_POST["edit"];
	$skk = $_POST["skk"];
	$norab = $_POST["norab"];
	$nilai = $_POST["nilai"];
	$tanggalrab = $_POST["tanggalrab"];
	$uraian = $_POST["uraian"];
	//$nilai = $_REQUEST["nilai"];

	$qry = ($edit==""? "insert into rab(skk, no_rab,nilai_rp,tgl_rab,uraian_kegiatan,nip) values('$skk','$norab','$nilai','$tanggalrab','$uraian','$nip')" : 						"update rab set 
								skk = '$skk',
								nilai_rp = '$nilai', 
								tgl_rab = '$tanggalrab',
								uraian_kegiatan = '$uraian', 
								nip = '$nip'
							 where id= $edit"
			);
	// echo $qry;
	// return;
	
	$sukses = mysql_query($qry);
	$message = "";

	if($sukses==1) {
		echo '<script>alert("RAB '.$norab.' berhasil disimpan.");</script>';
	}else{
		$message = mysql_error();
		echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
	}
	
	
	//$sukses = mysql_query($sql);// or die(mysql_error());
	// echo "$sukses<br>";
	
	//	
	//if ($sql) {					
	echo "<script>window.open('index.php', '_self')</script>";
	//}
?>