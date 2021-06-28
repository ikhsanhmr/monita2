<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$tahun = $_REQUEST["tahun"];
	$bidang = $_REQUEST["kdbidang"];

	$sql = "
		SELECT * FROM saldoakibidang WHERE tahun = $tahun AND kdbidang = $bidang
		";
	
	$nilai = 0;
	$kontrak = 0;
	$sisa = 0;
	$nama = "";
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));	
	$row = mysql_fetch_assoc($result);
	mysqli_free_result($result);
	$mysqli->close();($kon);
	
	echo json_encode($row);
?>