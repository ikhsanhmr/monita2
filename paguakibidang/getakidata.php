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
	
	$result = mysql_query($sql);	
	$row = mysql_fetch_assoc($result);
	mysql_free_result($result);
	mysql_close($kon);
	
	echo json_encode($row);
?>