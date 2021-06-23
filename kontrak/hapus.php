<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	
	if($nip=="") {exit;}
	
	$k = $_REQUEST["k"];
//	$p = $_REQUEST["p"];

	$sql = "SELECT * FROM kontrak WHERE (nomorskkoi, pos) = (SELECT nomorskkoi, pos FROM kontrak WHERE kid = '$k')";
	$num_rows = mysql_num_rows(mysql_query($sql));
	
	if($num_rows==1) {
		$sql = "update notadinas_detail set progress = 7 where (noskk,pos1) = 
			(SELECT nomorskkoi, pos FROM kontrak  WHERE kid = '$k')";
	//	echo "$sql<br>";
		mysql_query($sql);		
	}

	$sql = "delete from kontrak where kid='$k'";
//	echo "$sql<br>";
	$result = mysql_query($sql);
	
	
	echo "$result";

	mysql_close($kon);	  							
?>