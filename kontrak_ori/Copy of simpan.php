<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>

<body>
	<?php
		session_start(); 
		require_once '../config/koneksi.php';
		$nip=$_SESSION['nip'];
		$bidang=$_SESSION['bidang'];
		$kdunit=$_SESSION['kdunit'];
		$nama=$_SESSION['nama'];
		$adm=$_SESSION['adm'];
		if($nip=="") {exit;}
		
		$edit = $_REQUEST["edit"];
		$skk = $_REQUEST["skk"];
		$pos = $_REQUEST["pos"];
		$kontrak = $_REQUEST["kontrak"];
		$uraian = $_REQUEST["uraian"];
		$vendor = $_REQUEST["vendor"];
		$awal = $_REQUEST["awal"];
		$akhir = $_REQUEST["akhir"];
		$nilai = $_REQUEST["nilai"];
		
		$sql = ($edit==""? 
			"INSERT INTO kontrak(nomorskkoi, pos, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak)
			VALUES ('$skk', '$pos', '$kontrak', '$uraian', '$vendor', '$awal', '$akhir', '$nilai')" :
			
			"update kontrak set 
				uraian = '$uraian',
				vendor = '$vendor',
				tglawal = '$awal', 
				tglakhir = '$akhir',
				nilaikontrak = '$nilai'"
			);
		
		// echo "$sql<br>";
		$sukses = mysql_query($sql);// or die(mysql_error());
		// echo "$sukses<br>";
		
		echo "<script>window.open('kontrak.php', '_self')</script>";
	?>
</body>
</html
