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

	$message = "";

	if ($edit != ""){
		$ceklimitrab = mysqli_query("select SUM(nilaikontrak) as totalkontrak from kontrak where no_rab = '$norab' group by no_rab");
		$query=mysql_fetch_assoc($ceklimitrab);
		$totalkontrak=$query['totalkontrak'];

		if($nilai < $totalkontrak){
			$message = " Nilai RAB tidak boleh lebih kecil dari total nilai kontrak yang sudah menggunakan RAB ini. " . $totalkontrak;
		}
	}

	if($message == ""){
		
		$qry = ($edit==""? "insert into rab(skk, no_rab,nilai_rp,tgl_rab,uraian_kegiatan,nip) values('$skk','$norab','$nilai','$tanggalrab','$uraian','$nip')" : "update rab set 
									skk = '$skk',
									nilai_rp = '$nilai', 
									tgl_rab = '$tanggalrab',
									uraian_kegiatan = '$uraian', 
									nip = '$nip'
								where id= $edit"
				);
		// echo $qry;
		// return;
		
		$sukses = mysqli_query($qry);

		if($sukses==1) {
			echo '<script>alert("RAB '.$norab.' berhasil disimpan.");</script>';
		}else{
			$message = mysql_error();
			echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
		}
	}else{
		echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
	}
	
	
	//$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
	// echo "$sukses<br>";
	
	//	
	//if ($sql) {					
	echo "<script>window.open('index.php', '_self')</script>";
	//}
?>