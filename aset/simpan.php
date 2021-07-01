<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}
	
	//$edit = trim($_REQUEST["edit"]);
	$isedit = $_POST["isedit"];
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

	$uploadpdp = $_FILES['uploadpdp']; 
	if (is_null($uploadpdp)==false) {
		$file_name = $_FILES['uploadpdp']['name'];
		$tmp_name   = $_FILES['uploadpdp']['tmp_name'];
		$size     = $_FILES['uploadpdp']['size'];
		$type     = $_FILES['uploadpdp']['type'];
		$error    = $_FILES['uploadpdp']['error'];

		$uploadedfilename = "asetpdp_".str_replace("/","_",$nk);

		$target_dir="files/";
		$target_file = "".$target_dir.$uploadedfilename.".pdf";

		$target_upload_dir="../files/";
		$target_upload_file = "".$target_upload_dir.$uploadedfilename.".pdf";
		if(move_uploaded_file($_FILES['uploadpdp']['tmp_name'],$target_upload_file)) {
			$images_uploaded = $target_file;
		}
		// if(move_uploaded_file($file_name,$target_upload_file)) {
		//     $images_uploaded = $target_file;
		// }
	}

	// echo json_encode($_FILES['uploadpdp']);
	// return;

	if($isedit==0) {
		$sql = "INSERT INTO asetpdp(nomorkontrak, jtmaset, jtmrp, gdaset, gdrp, jtraset, jtrrp, sl1aset, sl1rp, sl3aset, sl3rp, keypointaset, keypointrp, file_path) VALUES('$nk', $jtm, $jtmr, $gd, $gdr, $jtr, $jtrr, $sl1, $sl1r, $sl3, $sl3r, $kp, $kpr, '$target_file')";
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
				sl3rp = $sl3r,
				keypointaset = $kp,
				keypointrp = $kpr
				".(empty($images_uploaded) ? "" : ", file_path = '$target_file'" )."
			where trim(nomorkontrak) = '$nk'";
	}

	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	
	if($sukses==1) {
		echo '<script>alert("Penyimpanan berhasil");</script>';
	}else{
		$message = mysqli_error();
		echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
	}

	$mysqli->close();($kon);	  							
	echo "<script>window.open('.', '_self')</script>";
?>