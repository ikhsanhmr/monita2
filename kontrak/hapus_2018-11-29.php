<?php
	error_reporting(0);  session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	
	if($nip=="") {exit;}
	
	$k = $_REQUEST["k"];
//	$p = $_REQUEST["p"];

	$sql = "SELECT * FROM kontrak WHERE (nomorskkoi, pos) = (SELECT nomorskkoi, pos FROM kontrak WHERE nomorkontrak = '$k')";
	$num_rows = mysqli_num_rows(mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)));
	
	if($num_rows==1) {
		$sql = "update notadinas_detail set progress = 7 where (noskk,pos1) = 
			(SELECT nomorskkoi, pos FROM kontrak  WHERE nomorkontrak = '$k')";
	//	echo "$sql<br>";
		mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));		
	}

	$sql = "delete from kontrak where nomorkontrak='$k'";
//	echo "$sql<br>";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	
	echo "$result";

	$mysqli->close();($kon);	  							
?>