<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	
	// Select Jenis SKK
	$skki=mysql_query("select notadinas.skkoi from notadinas_detail left join notadinas on  notadinas_detail.nomornota=notadinas.nomornota where notadinas_detail.noskk='$skk'");
	$jskk=mysql_fetch_assoc($skki);
	
	mysql_free_result($skki);
	mysql_close($kon);	  			
	echo $jskk['skkoi'];
?>