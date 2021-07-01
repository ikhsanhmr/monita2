<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	
	// Select Jenis SKK
	$skki=mysqli_query("select notadinas.skkoi from notadinas_detail left join notadinas on  notadinas_detail.nomornota=notadinas.nomornota where notadinas_detail.noskk='$skk'");
	$jskk=mysqli_fetch_assoc($skki);
	
	mysqli_free_result($skki);
	$mysqli->close();($kon);	  			
	echo $jskk['skkoi'];
?>