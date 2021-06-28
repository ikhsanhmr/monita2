<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$user=$_SESSION['cnip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	$edit = trim($_REQUEST["edit"]);
//	echo ($edit!=""? "isi" : "kosong");
	$skk = $_REQUEST["skk"];
	$pos = $_REQUEST["pos"];
	//$nilai = $_REQUEST["nilai"];

	$total_nilai_kontrak = 0;

	$pc = 0;
	$uploadkontrak = $_FILES['uploadkontrak']; 
		if (is_null($uploadkontrak)==false) {
				$file_name = $_FILES['uploadkontrak']['name'];
				$tmp_name   = $_FILES['uploadkontrak']['tmp_name'];
				$size     = $_FILES['uploadkontrak']['size'];
				$type     = $_FILES['uploadkontrak']['type'];
				$error    = $_FILES['uploadkontrak']['error'];
		
				$uploadedfilename = str_replace("/","_",$_POST['nkontrak0']);

			    $target_dir="files/";
			    $target_file = "".$target_dir.$uploadedfilename.".pdf";
		
			    $target_upload_dir="../files/";
			    $target_upload_file = "".$target_upload_dir.$uploadedfilename.".pdf";
			    if(move_uploaded_file($_FILES['uploadkontrak']['tmp_name'],$target_upload_file)) {
			        $images_uploaded = $target_file;
			    }
			    // if(move_uploaded_file($file_name,$target_upload_file)) {
			    //     $images_uploaded = $target_file;
			    // }
			}

	$message = "";
	$nrab=$_POST['nrab'];
	 foreach($_REQUEST as $param_name => $param_val) {
		//echo "parameter : $param_name - $param_val <br>";
		switch(substr($param_name,0,4)) {
			case "peti" : //echo "$param_name - $param_val<br>"; 
				$pc = 1; 
				break;
			case "nkon" : $kontrak = $param_val; break;
			case "urai" : $uraian = $param_val; break;
			case "vend" : $vendor = $param_val; break;
			case "nodo" : $nodokumen = $param_val; break;
			case "awal" : 
				$awal = $param_val; 
				$awal = (substr($awal,2,1)=="/"? substr($awal,-4)."/".substr($awal,0,2)."/".substr($awal,3,2): $awal);
				//$awal = substr($awal,-4) . "-" . substr($awal, 0, 2) . "-" . substr($awal, 3,2);
				break;
			case "akhi" : 
				$akhir = $param_val; 
				$akhir = (substr($akhir,2,1)=="/"? substr($akhir,-4)."/".substr($akhir,0,2)."/".substr($akhir,3,2): $akhir);
				//$akhir = substr($akhir,-4) . "-" . substr($akhir, 0, 2) . "-" . substr($akhir, 3,2);
				break;
			case "tglt" : 
				$tgltagih = $param_val; 
				$tgltagih = (substr($tgltagih,2,1)=="/"? substr($tgltagih,-4)."/".substr($tgltagih,0,2)."/".substr($tgltagih,3,2): $tgltagih);
				//$akhir = substr($akhir,-4) . "-" . substr($akhir, 0, 2) . "-" . substr($akhir, 3,2);
				break;
			case "ruti" : $rutin = $param_val; break;
			case "nila" : 
				if(substr($param_name,0,5)=="nilai" && strlen($param_name)>5) {
					$nilai = $param_val; 
					$total_nilai_kontrak += $nilai;
/*
					$ceksql = "SELECT COUNT(*) jumlah FROM kontrak WHERE nomorkontrak = '$kontrak'";
					//echo "$ceksql<br>";
					$result = mysqli_query($ceksql);
					while ($row = mysqli_fetch_array($result)) { $jumlah = $row["jumlah"]; }
					mysqli_free_result($result);
						
					if(($edit=="") && ($jumlah>0)) {
						//if($jumlah > 0) {
						$mysqli->close();($link);	 
						//echo $jumlah . "<br>";
						echo "<script>alert('Gagal membuat kontrak. Nomor Kontrak $kontrak sudah ada!');</script>";
						echo "<script>window.open('index.php', '_self')</script>";
						//}
					}
*/
					$cekNilaiPagu = mysqli_query("
						SELECT 	d.*, kontrak, namapos, n.skkoi 
						FROM 	notadinas_detail d LEFT JOIN 
								(
									SELECT 	nomorskkoi, pos, SUM(nilaikontrak) kontrak 
									FROM 	kontrak 
									WHERE	nomorkontrak != '$kontrak'
									GROUP BY nomorskkoi, pos
								) k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
								(
									SELECT kdindukpos kdpos, namaindukpos namapos FROM posinduk UNION ALL 
									SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk2 UNION ALL 
									SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk3 UNION ALL 
									SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk4
								) p ON d.pos1 = p.kdpos LEFT JOIN
								notadinas n ON d.nomornota = n.nomornota
						WHERE 	noskk = '$skk' AND pos1 = '$pos'
					");

					$exeNilaiPagu = mysql_fetch_assoc($cekNilaiPagu);

					$nilai_pagu = $exeNilaiPagu['nilai1'];
					$total_kontrak = $exeNilaiPagu['kontrak'];
					$sisa_pagu = $nilai_pagu - $total_kontrak;

					if($exeNilaiPagu['skkoi'] === 'SKKI'){
						$rutin = 0;
					}
					
					if($total_nilai_kontrak > $sisa_pagu){
						
						echo '<script>alert("Penyimpanan gagal. \n Nilai kontrak #'.$kontrak.' lebih besar dari sisa pagu.");</script>';
						break;
					}

					$sql = ($edit==""? 
					"INSERT INTO kontrak(nomorskkoi, pos, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak, inputdt, pettycash,nodokumen,tgltagih,file_path,no_rab,inputby,isrutin)
						VALUES ('$skk', '$pos', '$kontrak', '$uraian', '$vendor', '$awal', '$akhir', '$nilai', sysdate(), '$pc','$nodokumen','$tgltagih','$target_file','$nrab','$user',$rutin)" :
						
					/*	($jumlah==0? 
							"INSERT INTO kontrak(nomorskkoi, pos, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak)
							VALUES ('$skk', '$pos', '$kontrak', '$uraian', '$vendor', '$awal', '$akhir', '$nilai')" : 
					*/		
							"update kontrak set 
								uraian = '$uraian',
								vendor = '$vendor',
								tglawal = '$awal', 
								tglakhir = '$akhir',
								nilaikontrak = '$nilai', 
								pettycash = '$pc', 
								nodokumen = '$nodokumen', 
								tgltagih = '$tgltagih' , 
								editby = '$user' , 
								isrutin = $rutin , 
								editdt = sysdate() 
								".(empty($images_uploaded) ? "" : ", file_path = '$target_file'" )."
							 where kid= '$edit'"
	
					/*	) */
					);
					
					// echo "$sql<br>";
					// return;
					$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
					$message = "";
					
					if($sukses==1) {
						// input bayar - kontrak petty cash
						if($pc==1) {
							$sql = "INSERT INTO realisasibayar(nokontrak, nilaibayar, tglbayar) VALUES('$kontrak', '$nilai', date(sysdate()))";	
							//echo $sql;
							mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
							$update=mysqli_query("update rab set status='1' where no_rab='$nrab'");
						}

						$sql = "UPDATE notadinas_detail SET progress = 9 WHERE noskk = '$skk' and pos1 = '$pos'";
						$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
						$update=mysqli_query("update rab set status='1' where no_rab='$nrab'");
						// echo '<script>alert("Penyimpanan berhasil");</script>';

						$message .= " - Kontrak $kontrak berhasil disimpan. \\n";
					}else{
						$alasan = mysql_error();
						// echo '<script>alert("Penyimpanan Gagal. '.$message.'");</script>';
						$message .= " - Kontrak $kontrak gagal disimpan. $alasan \\n";
					}
					$pc = 0;
					//echo "$sql<br>";
				}
		}
	}
	
	//$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
	// echo "$sukses<br>";
	
	echo '<script>alert("'.$message.'");</script>';
	$mysqli->close();($kon);
	echo '<script>window.open("index.php?msg='.$message.'", "_self")</script>';
?>