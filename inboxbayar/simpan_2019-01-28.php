<?php
	error_reporting(0);  session_start(); 
	$nip=$_SESSION['cnip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';

	$no = $_REQUEST["id"];
	$t = $_REQUEST["actiontype"];
	$lvl = $_REQUEST["level"];
	$rejectreason = $_REQUEST["reason"];

	$sukses = 0;

	$sqlbayarid=mysqli_query("select bayarid from realisasibayar order by bayarid desc");
	$query=mysqli_fetch_assoc($sqlbayarid);
	$numid=$query['bayarid']+1;

	$gagal = 0;
	$gagalmsg = '';

	foreach($no as $id) {

		$doc = $_REQUEST["doc".trim($id)];
		$tgh = $_REQUEST["t".trim($id)];
		$ctt = $_REQUEST["ctt".trim($id)];
		$k = $_REQUEST["k".trim($id)];
		$s = $_REQUEST["s".trim($id)];

		$error = "";

		if ($t == 1){
			if(empty($ctt) && $lvl == 1){
				$error .= " - Wajib mengisi catatan untuk kontrak $k \\n";
			}

			if(empty($doc) && $lvl == 1){
				
				$error .= " - Wajib mengisi nomor dokumen untuk kontrak $k \\n";
			}

			if(empty($tgh) && $lvl == 1){
				
				$error .= " - Wajib mengisi nilai tagihan untuk kontrak $k \\n";
			}elseif ($tgh > $s){

				$error .= " - Nilai tagihan tidak boleh lebih besar dari nilai sisa untuk kontrak $k \\n";
			}
		}

		if(!empty($error)){
			$gagal++;
			$gagalmsg .= $error;
			continue;
		}

		$sql = "INSERT INTO kontrak_approval (nomorkontrak, actiontype, signdt, signed, signlevel, nilaitagihan, catatan, catatanreject) VALUES ('$k', '$t', sysdate(), '$nip', '$lvl', $tgh, '$ctt', '$rejectreason')";	
		/*echo $sql;
		return;*/
		$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
		//$message = "";

		if ($sukses != 1){
			$gagal++;
			$gagalmsg .= " - Kontrak $k : ".mysqli_error()."\\n";
			continue;
			// echo '<script>alert("Penyimpanan Gagal untuk kontrak '.$k.'. '.$message.'");</script>';
			// break;
		}else{

			if($lvl == 1){
				if(!empty($doc)){
					mysqli_query("update kontrak set nodokumen='$doc' where nomorkontrak='$k'");
				}
			}

			if ($lvl == 4){
				$selectkontrak=mysqli_query("select * from kontrak where nomorkontrak='$k'");
				$exekontrak=mysqli_fetch_assoc($selectkontrak);

				$pmn = "NON PMN";

				if ($exekontrak[pos] == '62.7'){
					$pmn = "PMN";
				}

				mysqli_query("insert into realisasibayar(nokontrak, nodokrep, nilaibayar, tglbayar, bayarid, pmn, keterangan) values('$k', '$exekontrak[nodokumen]', '$tgh', sysdate(), '$numid', '$pmn', '$ctt')");

				mysqli_query("update kontrak set signed='$nip',signeddt='$datetime' where nomorkontrak='$exekontrak[nomorkontrak]'");
			}
		}

		$numid++;
	}

	if ($gagal > 0){
		
		echo '<script>alert("Penyimpanan gagal. Terdapat '.$gagal.' kontrak tidak tersimpan. \n '.$gagalmsg.'");</script>';
	}else{
		echo '<script>alert("Penyimpanan berhasil.");</script>';
	}

	$mysqli->close();($kon);
	echo '<script>window.open("index.php", "_self")</script>';
?>