<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "<script>window.open('.', '_self')</script>";
		exit;
	}
	
    require_once 'config/koneksi.php';
	$nip = $_SESSION["nip"];
	$opwd = $_REQUEST["opwd"];
	$pwd = $_REQUEST["pwd"];

    $sql = "select * from user where nip = '$nip' and password = '$opwd'";
    $hasil = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    $chasil = mysqli_num_rows($hasil);
    
    if($chasil==0) { 
		echo "User atau Password Tidak Sesuai";
	} else {
        $sql = "update user set password = '$pwd' where nip ='$nip' and password='$opwd'";
   		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 
	}	
?>