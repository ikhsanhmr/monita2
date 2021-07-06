<?php
	error_reporting(0);  session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}
	
	//$edit = trim($_REQUEST["edit"]);
	$isedit = $_POST["edit"];
	$nk = trim($_POST["nk"]);
	$jtm = ($_POST["jtm"]==""? "null": "'$_POST[jtm]'");
	$jtmr = ($_POST["jtmr"]==""? "null": "'$_POST[jtmr]'");
	$gd = ($_POST["gd"]==""? "null": "'$_POST[gd]'");
	$gdr = ($_POST["gdr"]==""? "null": "'$_POST[gdr]'");
	$jtr = ($_POST["jtr"]==""? "null": "'$_POST[jtr]'");
	$jtrr = ($_POST["jtrr"]==""? "null": "'$_POST[jtrr]'");
	$sl1 = ($_POST["sl1"]==""? "null": "'$_POST[sl1]'");
	$sl1r = ($_POST["sl1r"]==""? "null": "'$_POST[sl1r]'");
	$sl3 = ($_POST["sl3"]==""? "null": "'$_POST[sl3]'");
	$sl3r = ($_POST["sl3r"]==""? "null": "'$_POST[sl3r]'");
	$kp = ($_POST["kp"]==""? "null": "'$_POST[kp]'");
	$kpr = ($_POST["kpr"]==""? "null": "'$_POST[kpr]'");

	if($isedit==0) {
		$sql = "INSERT INTO asetpdp(nomorkontrak, jtmaset, jtmrp, gdaset, gdrp, jtraset, jtrrp, sl1aset, sl1rp, sl3aset, sl3rp, keypointaset, keypointrp) VALUES('$nk', $jtm, $jtmr, $gd, $gdr, $jtr, $jtrr, $sl1, $sl1r, $sl3, $sl3r, $kp, $kpr)";
	} else {
		$sql = "
			update asetpdp set 
				jtmaset = $jtm, 
				jtmrp = $jtmr,
				gdaset = $gd,
				gdrp = $gdr,
				jtraset = $jtr,
				jtrrp = $jtrr,
				sl1aset = $sl1,
				sl1rp = $sl1r,
				sl3aset = $sl3,
				sl3rp = $sl3r
				keypointaset = $kp,
				keypointrp = $kpr
			where nomorkontrak = '$nk'";
	}
	//echo $sql;
	//foreach($_REQUEST as $param_name => $param_val) { echo "parameter : $param_name - $param_val <br>"; }
	
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	// echo "$sukses<br>";
	
	//
	$mysqli->close();($link);	  							
	echo "<script>window.open('.', '_self')</script>";
?>