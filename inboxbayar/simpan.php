<?php
	session_start(); 
	$nip=$_SESSION['cnip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';

	

	$sukses = 0;

	// echo json_encode($_REQUEST);
	$inputreq =  json_decode(file_get_contents('php://input'), true);

	$datanya = $inputreq["datanya"];
	$t = $inputreq["actiontype"];
	$lvl = $inputreq["level"];
	$rejectreason = $inputreq["reason"];

	$sqlbayarid=mysqli_query("select bayarid from realisasibayar order by bayarid desc");
	$query=mysqli_fetch_assoc($sqlbayarid);
	$numid=$query['bayarid']+1;

	$gagal = 0;
	$gagalmsg = '';

	$response=null;

	if (count($datanya) < 1){

		echo '<script>alert("Penyimpanan gagal. Tidak ada inbox bayar yang dipilih.");</script>';

	}else{

		foreach($datanya as $val) {

			$k = preg_replace("/&amp;/", "&", $val[0]);
			$doc = $val[1];
			$s = str_replace(",", "", $val[2]);
			$tgh = $val[3];
			$ctt = $val[4];
			$tglbyr = (substr($val[5],2,1)=="/"? substr($val[5],-4)."/".substr($val[5],0,2)."/".substr($val[5],3,2): $val[5]);

			$error = "";

			if ($t == 1){
				if(empty($ctt) && $lvl == 1){
					$error .= " - Wajib mengisi catatan untuk kontrak $k. \n";
				}

				if(empty($doc) && $lvl == 1){
					
					$error .= " - Wajib mengisi nomor dokumen untuk kontrak $k. \n";
				}

				if(empty($tgh) && ($lvl == 1 || $lvl >= 3)){
					
					$error .= " - Wajib mengisi nilai tagihan untuk kontrak $k. \n";

				}elseif (!is_numeric($tgh)){

					$error .= " - Nilai Tagihan yang anda input bukan angka. \n";

				}elseif ($tgh > $s){

					$error .= " - Nilai tagihan tidak boleh lebih besar dari nilai sisa untuk kontrak $k. \n";
				}

				if (empty($tglbyr) && ($lvl == 4)){

					$error .= " - Tanggal Bayar tidak boleh kosong. \n";
				}
			}

			if(!empty($error)){
				$gagal++;
				$gagalmsg .= $error;
				continue;
			}

			$sql = "INSERT INTO kontrak_approval (nomorkontrak, actiontype, signdt, signed, signlevel, nilaitagihan, catatan, catatanreject) VALUES ('$k', '$t', sysdate(), '$nip', '$lvl', $tgh, '$ctt', '$rejectreason')";	
			// echo $sql;
			// return;
			$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
			//$message = "";

			if ($sukses != 1){
				$gagal++;
				$gagalmsg .= " - Kontrak $k : ".mysqli_error().". \n";
				continue;
				// echo '<script>alert("Penyimpanan Gagal untuk kontrak '.$k.'. '.$message.'");</script>';
				// break;
			}else{

				if($lvl == 1){
					if(!empty($doc)){
						mysqli_query("update kontrak set nodokumen='$doc' where nomorkontrak='$k'");
					}
				}

				if ($lvl == 3 && $t == 1){

					$selectkontrak=mysqli_query("select * from kontrak where nomorkontrak='$k'");
					$exekontrak=mysqli_fetch_assoc($selectkontrak);

					mysqli_query("update kontrak set signed='$nip',signeddt=sysdate() where nomorkontrak='$exekontrak[nomorkontrak]'");
				}

				if ($lvl == 4 && $t == 1){
					$selectkontrak=mysqli_query("select * from kontrak where nomorkontrak='$k'");
					$exekontrak=mysqli_fetch_assoc($selectkontrak);

					$pmn = "NON PMN";

					if ($exekontrak[pos] == '62.7'){
						$pmn = "PMN";
					}

					mysqli_query("insert into realisasibayar(nokontrak, nodokrep, nilaibayar, tglbayar, pmn, keterangan, inputdt, inputby) values('$k', '$exekontrak[nodokumen]', '$tgh', '$tglbyr', '$pmn', '$ctt', sysdate(), '$nip')");
				}
			}

			$numid++;
		}

		if ($gagal > 0){
			
			// echo '<script>alert("Penyimpanan gagal. Terdapat '.$gagal.' kontrak tidak tersimpan. \n '.$gagalmsg.'");</script>';
			$response = array(
				"error" => true,
				"message" => "Penyimpanan gagal. Terdapat $gagal kontrak tidak tersimpan. \n $gagalmsg"
			);
		}else{
			// echo '<script>alert("Penyimpanan berhasil.");</script>';
			$response = array(
				"error" => false,
				"message" => "Penyimpanan berhasil."
			);
		}
	}

	$mysqli->close();($kon);
	// echo '<script>window.open("index.php", "_self")</script>';
	echo json_encode($response);
?>