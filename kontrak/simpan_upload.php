<?php
	session_start(); 
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$user=$_SESSION['cnip'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';

	require_once '../lib/PHPExcel/PHPExcel.php';

	$file_uploaded=$_FILES['upload'];
	$name_uploaded=$_FILES['upload']['name'];
	$type_uploaded=$_FILES['upload']['type'];
	$tmp_uploaded=$_FILES['upload']['tmp_name'];

	move_uploaded_file($tmp_uploaded, '../files/excel/'.$name_uploaded.'');
	$excelreader = new PHPExcel_Reader_Excel2007();
	$loadexcel = $excelreader->load('../files/excel/'.$name_uploaded);
	$sheet = $loadexcel->getActiveSheet()->toArray(null, true, true ,true);
	$message = "";
	
	$sql=mysqli_query("select bayarid from realisasibayar order by bayarid desc");
	$query=mysqli_fetch_assoc($sql);
	$numid=$query['bayarid']+1;
	
	$numrow=0;
	foreach($sheet as $row) {
		if ($numrow>0) {
			
			$nokontrak = $row['A']."-h";
			$noskkio = $row['B'];
			$subpos = $row['C'];
			$uraian = $row['D'];
			$vendor = $row['E'];
			$nodokumen = $row['F'];
			$awal = $row['G'];
			$akhir = $row['H'];
			$tgltagih = $row['I'];
			$nilaikontrak = $row['J'];
			$nrab = $row['K'];
			$rutin = $row['L'];
			$tglbayar = $row['M'];

			$datetime=date("Y-m-d H:i:s");

			$sqlkon3 = mysqli_query("SELECT * FROM kontrak where nomorkontrak='$nokontrak'");

			$countkontrak = mysqli_num_rows($sqlkon3);

			if($countkontrak > 0){
				$message .= " - Error Baris ke #$numrow: No Kontrak $nokontrak sudah pernah diinput. <br />";
				$numrow++;
				continue;
			}

			$sqlkon1 = mysqli_query("
				SELECT 	nd.noskk, skktype
				FROM 	notadinas_detail nd inner join 
						(
							select nomorskki as noskk, 'SKKI' as skktype from skkiterbit
							UNION
							select nomorskko as noskk, 'SKKO' as skktype from skkoterbit
						) AS allskk ON nd.noskk = allskk.noskk
				where 	nd.noskk='$noskkio' AND nd.pos1='$subpos'
			");

			$countskk = mysqli_num_rows($sqlkon1);

			if($countskk < 1){
				$message .= " - Error Baris ke #$numrow: No SKK $noskkio Tidak Ditemukan. <br />";
				$numrow++;
				continue;
			}

			$cekkon1 = mysqli_fetch_assoc($sqlkon1);

			if($cekkon1['skktype'] == 'SKKI'){

				if(!empty($nrab)){

					$sqlrab = mysqli_query("
						SELECT 	(a.nilai_rp - coalesce(jumlah, 0)) as sisa
						FROM	rab a inner Join 
								( 
									SELECT 	no_rab, SUM(nilaikontrak) as jumlah 
									FROM 	kontrak
									GROUP BY no_rab 
								) b ON a.no_rab = b.no_rab 
						where 	a.no_rab = '$nrab'
					");

					$cekrab = mysqli_fetch_assoc($sqlrab);

					if($nilaikontrak > $cekrab['sisa']){
						
						$message .= " - Error Baris ke #$numrow: Nilai sisa RAB $nrab lebih kecil dari nilai kontrak yang di input atau nomor RAB $nrab tidak ada. <br />";
						$numrow++;
						continue;
					}
				}
			}

			$sqlkon2 = mysqli_query("
				SELECT 	(nilai1 - COALESCE(kontrak, 0)) as sisa
				FROM 	notadinas_detail d LEFT JOIN 
						(
							SELECT 	nomorskkoi, pos, SUM(nilaikontrak) kontrak 
							FROM 	kontrak 
							GROUP BY nomorskkoi, pos
						) k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
				where noskk='$noskkio' AND pos1='$subpos'
			");
			
			$cekkon2 = mysqli_fetch_assoc($sqlkon2);

			if($nilaikontrak > $cekkon2["sisa"]){

				$message .= " - Error Baris ke #$numrow: Nilai sisa SKK $noskkio dengan Pos $subpos lebih kecil dari nilai kontrak yang di input. <br />";
				$numrow++;
				continue;
			}

			$sql = "INSERT INTO kontrak (nomorskkoi, pos, nomorkontrak, uraian, vendor, tglawal, tglakhir, 
			nilaikontrak, inputdt, pettycash, nodokumen, tgltagih, no_rab, inputby, isrutin)
			VALUES ('$noskkio', '$subpos', '$nokontrak', '$uraian', '$vendor', '$awal', '$akhir', 
			'$nilaikontrak', sysdate(), '0','$nodokumen','$tgltagih','$nrab','$user','$rutin')";	
			// echo $sql;
			// return;
			$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
			//$message = "";

			if ($sukses != 1){

				$errmsg = mysqli_error();
				$message .= " - Error Baris ke #$numrow: Gagal menyimpan kontrak, $errmsg <br />";
				
			}else{

				if(!empty($tglbayar)){

					$suksesbayar = mysqli_query("insert into realisasibayar(nokontrak,nodokrep,nilaibayar,tglbayar,bayarid,pmn) values('$nokontrak','$nodokumen','$nilaikontrak','$tglbayar','$numid','$pmn')");

					if($suksesbayar != 1 ){

						$errmsg = mysqli_error();
						$message .= " - Error Baris ke #$numrow: kontrak berhasil disimpan tapi gagal masuk ke Realisasi Bayar, $errmsg <br />";

					}else{

						mysqli_query("update kontrak set signed = '$user',signeddt = sysdate() where nomorkontrak = '$nokontrak'");

						$numid++;
						$message .= " - Baris ke #$numrow: kontrak berhasil disimpan dan berhasil masuk ke Realisasi Bayar <br />";	
					}
				}else{
					$message .= " - Baris ke #$numrow: kontrak berhasil disimpan <br />";
				}
			}
		}

		$numrow++;
	}

	// echo '<script>alert("'.$message.'");</script>';
	// $mysqli->close();($kon);
	// echo '<script>window.open("index.php?msg='.$message.'", "_self")</script>';

	echo "<a href='index.php'>Back</a><hr>";
	echo $message;
	echo "<a href='index.php'>Back</a>";
?>