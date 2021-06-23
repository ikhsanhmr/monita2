<?php
	session_start(); 
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

	$sqlbayarid=mysql_query("select bayarid from realisasibayar order by bayarid desc");
	$query=mysql_fetch_assoc($sqlbayarid);
	$numid=$query['bayarid']+1;

	$gagal = 0;
	$gagalmsg = '';

	if (count($no) < 1){

		echo '<script>alert("Penyimpanan gagal. Tidak ada inbox bayar yang dipilih.");</script>';

	}else{

		foreach($no as $id) {

			$doc = $_REQUEST["doc".$id];
			$tgh = $_REQUEST["t".$id];
			$ctt = $_REQUEST["ctt".$id];
			$k = $_REQUEST["k".$id];
			$s = $_REQUEST["s".$id];

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
			$sukses = mysql_query($sql);// or die(mysql_error());
			//$message = "";

			if ($sukses != 1){
				$gagal++;
				$gagalmsg .= " - Kontrak $k : ".mysql_error()."\\n";
				continue;
				// echo '<script>alert("Penyimpanan Gagal untuk kontrak '.$k.'. '.$message.'");</script>';
				// break;
			}else{

				if($lvl == 1){
					if(!empty($doc)){
						mysql_query("update kontrak set nodokumen='$doc' where nomorkontrak='$k'");
					}
				}

				if ($lvl == 3 && $t == 1){

					$selectkontrak=mysql_query("select * from kontrak where nomorkontrak='$k'");
					$exekontrak=mysql_fetch_assoc($selectkontrak);

					mysql_query("update kontrak set signed='$nip',signeddt=sysdate() where nomorkontrak='$exekontrak[nomorkontrak]'");
				}

				if ($lvl == 4 && $t == 1){
					$selectkontrak=mysql_query("select * from kontrak where nomorkontrak='$k'");
					$exekontrak=mysql_fetch_assoc($selectkontrak);

					$pmn = "NON PMN";

					if ($exekontrak[pos] == '62.7'){
						$pmn = "PMN";
					}

					mysql_query("insert into realisasibayar(nokontrak, nodokrep, nilaibayar, tglbayar, bayarid, pmn, keterangan) values('$k', '$exekontrak[nodokumen]', '$tgh', sysdate(), '$numid', '$pmn', '$ctt')");
				}
			}

			$numid++;
		}

		if ($gagal > 0){
			
			echo '<script>alert("Penyimpanan gagal. Terdapat '.$gagal.' kontrak tidak tersimpan. \n '.$gagalmsg.'");</script>';
		}else{
			echo '<script>alert("Penyimpanan berhasil.");</script>';
		}
	}

	mysql_close($kon);
	echo '<script>window.open("index.php", "_self")</script>';
?>