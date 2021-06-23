<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "<script>window.open('.', '_self')</script>";
		exit;
	}
	
    require_once 'config/koneksi.php';
	//$nip = $_SESSION["nip"];
	$nip = $_REQUEST["nip"];
	$opwd = $_REQUEST["opwd"];
	$pwd = $_REQUEST["pwd"];

    $sql = "select * from user where nip = '$nip' and pasword = '".md5($opwd)."'";
	// echo $sql;
 //    return;
    $hasil = mysql_query($sql);
    $chasil = mysql_num_rows($hasil);
    
    if($chasil==0) { 
		echo "User atau Password Tidak Sesuai";
	} else {
        $sql = "update user set pasword = '".md5($pwd)."' where nip ='$nip' and pasword='".md5($opwd)."'";
   		$hasil=mysql_query($sql); 
	}	
?>