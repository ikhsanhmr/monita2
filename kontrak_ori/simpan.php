<?php
	session_start(); 
	require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
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

	
	 foreach($_REQUEST as $param_name => $param_val) {
		//echo "parameter : $param_name - $param_val <br>";
		
		switch(substr($param_name,0,4)) {
			case "nkon" : $kontrak = $param_val; break;
			case "urai" : $uraian = $param_val; break;
			case "vend" : $vendor = $param_val; break;
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
			case "nila" : 
				if(substr($param_name,0,5)=="nilai" && strlen($param_name)>5) {
					$nilai = $param_val; 

					$ceksql = "SELECT COUNT(*) jumlah FROM kontrak WHERE nomorkontrak = '$kontrak'";
					//echo "$ceksql<br>";
					$result = mysql_query($ceksql);
					while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { $jumlah = $row["jumlah"]; }
					mysql_free_result($result);
						
					if(($edit=="") && ($jumlah>0)) {
						//if($jumlah > 0) {
						mysql_close($link);	 
						//echo $jumlah . "<br>";
						echo "<script>alert('Gagal membuat kontrak. Nomor Kontrak $kontrak sudah ada!');</script>";
						echo "<script>window.open('index.php', '_self')</script>";
						//}
					}

					$sql = ($edit==""? 
						"INSERT INTO kontrak(nomorskkoi, pos, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak)
						VALUES ('$skk', '$pos', '$kontrak', '$uraian', '$vendor', '$awal', '$akhir', '$nilai')" :
						
						($jumlah==0? 
							"INSERT INTO kontrak(nomorskkoi, pos, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak)
							VALUES ('$skk', '$pos', '$kontrak', '$uraian', '$vendor', '$awal', '$akhir', '$nilai')" : 
							
							"update kontrak set 
								uraian = '$uraian',
								vendor = '$vendor',
								tglawal = '$awal', 
								tglakhir = '$akhir',
								nilaikontrak = '$nilai'
							 where nomorkontrak = '$kontrak'"
	
						)
					);
					
					//echo "$sql<br>";
					$sukses = mysql_query($sql);// or die(mysql_error());
					
					if($sukses==1) {
						$sql = "UPDATE notadinas_detail SET progress = 9 WHERE noskk = '$skk' and pos1 = '$pos'";
						$sukses = mysql_query($sql);// or die(mysql_error());
					}
					//echo "$sql<br>";
				}
		}
	}
	
	//$sukses = mysql_query($sql);// or die(mysql_error());
	// echo "$sukses<br>";
	
	//
	mysql_close($link);	  							
	echo "<script>window.open('index.php', '_self')</script>";
?>