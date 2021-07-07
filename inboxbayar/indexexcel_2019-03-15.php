<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=inboxbayar.xls");

	error_reporting(0);  session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	
	$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
	$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
	$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
	$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
	$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
	$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	require_once "../config/koneksi.php";
	
	$userlvl = 0;

	if ($_SESSION["roleid"] <= 03){
		
		$userlvl = 3;

	} elseif($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05){

		$userlvl = 4;
		
	} elseif($_SESSION["roleid"] == 13){

		$userlvl = 2;
		
	} else {

		$userlvl = 1;
	}
	
	$parm = "";
	// $parm .= ($p1==""? "": " and SUBSTR(tglawal, 1, 7) >= '$p1'");
	// $parm .= ($p2==""? "": " and SUBSTR(tglakhir, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4) . " AND MONTH(inputdt) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(inputdt) = " . substr($p2,0,4) . " AND MONTH(inputdt) <= " . substr($p2,-2));
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	
	if(($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05) && $_SESSION["cnip"] != '7602006A'){
		
		$parm .= " and pelaksana in (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."')";

	}else{

		//if ($_SESSION["roleid"] > 3){
		//	$parm .= " and pelaksana = '$_SESSION[org]'";
		//}

		if ($_SESSION["roleid"] > 5){

			if($_SESSION["cnip"] == "8610292Z" || $_SESSION["cnip"] == "94171330ZY"){
				
				$parm .= " and pelaksana IN ('$_SESSION[org]','1')";	

			}elseif($_SESSION["roleid"] == 13 && $_SESSION["org"] < 6){

				//$parm .= " and pelaksana < 6";

			}else{

				$parm .= " and pelaksana = '$_SESSION[org]'";
			}
		}
	}
	
	$parm .= ($k0==""? "": " and skk = '$k0'");
	$parm .= ($o==""? "": " and skkoi = '$o'");
	$parm .= ($c==""? "": " and TRIM(nomorkontrak) = '$c'");

	$otherjoin = "";

	switch ($userlvl) {
	    case 1:
	        $parm .= " AND ((signlevel = 0 AND actiontype = 1) OR (signlevel > 1 AND actiontype = 0))";
	        break;
	    case 2:
	        $parm .= " AND (signlevel = 1 AND actiontype = 1)";
	        break;
	    case 3:
	        $parm .= " AND (signlevel = 2 AND actiontype = 1)";
	        break;
	    case 4:
	        $parm .= " AND (signlevel = 3 AND actiontype = 1)";
	        break;
	}

	if($_SESSION["cnip"] == '7602006A'){
			$otherjoin .= " LEFT JOIN
						(
							SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
							FROM 	akses_bidang ap INNER JOIN 
									user u On ap.nip = u.nip 
							where 	roleid IN (4,5)
							GROUP BY akses
						) ap ON pelaksana = ap.akses";

			$otherjoin2 .= " LEFT JOIN
						(
							SELECT 	akses, nama 
							FROM 	akses_bidang ap INNER JOIN 
									user u On ap.nip = u.nip 
							where 	roleid IN (4,5)
						) ap ON pelaksana = ap.akses";
		}

		if($_SESSION["cnip"] == '6793235Z'){
			$otherjoin .= " LEFT JOIN
						(
							SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
							FROM 	akses_pos ap LEFT JOIN
									user u On ap.nip = u.nip
							where 	roleid IN (2,3)
							GROUP BY akses
						) ap ON d.pos1 = ap.akses";

			$otherjoin2 .= " LEFT JOIN
						(
							SELECT 	akses, nama 
							FROM 	akses_pos ap LEFT JOIN
									user u On ap.nip = u.nip
							where 	roleid IN (2,3)
						) ap ON d.pos1 = ap.akses";
		}

	if(($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03)){
		$parm .= " AND d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')";
	}

	if($_SESSION["roleid"] == 13 && $_SESSION["org"] < 6){
		$parm .= " AND ( (n.skkoi = 'SKKI' and pelaksana = '$_SESSION[org]') OR (n.skkoi = 'SKKO' and pelaksana < 6 and d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')))";
	}

	if($v!="") {
			$sql = "
				SELECT	nomorskkoi, pos1, nomorkontrak, b.namaunit, vendor, k.uraian uraian, 
						nodokumen, nilaikontrak, bayar, ka.signed, ka.signdt, ka.signlevel, ka.actiontype, 
						ka.nilaitagihan, ka.catatan, ka.catatanreject, namapos ".( ($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A') ? ', ap.nama namauser' : '' )."
				FROM 	(
							SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
									nilaitagihan, catatan, catatanreject
							FROM	kontrak_approval t1
							WHERE	t1.id = (	SELECT	t2.id
												FROM	kontrak_approval t2
												WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
												ORDER BY t2.signdt DESC
												LIMIT 1
											)
						) ka INNER JOIN 
						kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN
						notadinas n ON d.nomornota = n.nomornota LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						(
							SELECT 	nokontrak, SUM(nilaibayar) bayar 
							FROM 	realisasibayar 
							GROUP BY nokontrak
						) r ON ka.nmrkontrak = r.nokontrak LEFT JOIN (
							SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos
						$otherjoin
				WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
				$parm
				ORDER BY k.inputdt DESC";
			//echo $sql;
			
			//$hasil = "
			echo "
			<strong>LAPORAN INBOX BAYAR</strong><br>
			<strong>Periode	: $p1  -  $p2</strong><br>
			<strong>Bidang : $b</strong><br>
			<strong>Pelaksana : $p</strong><br>
			<strong>No SKK : $k0</strong><br>
			<strong>POS : $kdpos</strong><br>
			Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB
			
			<table border='1'>
				<tr>
					<th>No</th>
					<th>SKK</th>
					<th>Kontrak</th>
					<th>Pelaksana</th>
					<th>Vendor</th>
					<th>Uraian</th>
					<th>POS</th>
					<th>No Dokumen</th>
					<th>Nilai Kontrak</th>
					<th>Nilai Bayar</th>
					<th>Sisa</th>
					<th>Nilai Tagihan</th>
					".($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A' ? "<th>PIC</th>" : "" )."
					<th>Catatan</th>
				</tr>
			";
			$no = 0;
			//$parm = "";
			//$dummy = 0;
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

			while ($row = mysqli_fetch_array($result)) {
				$no++;

				$nilaitagihan = $row["nilaitagihan"];

				if (empty($nilaitagihan)){

					$nilaitagihan = $row["nilaikontrak"] - $row["bayar"];
				}

				echo "
					<tr>
						<td>$no</td>
						<td>$row[nomorskkoi]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[namaunit]</td>
						<td>$row[vendor]</td>
						<td>$row[uraian]</td>
						<td>$row[namapos]</td>
						<td>$row[nodokumen]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td align='right'>".number_format($row["nilaikontrak"] - $row["bayar"])."</td>
						<td align='right'>".number_format($nilaitagihan)."</td>
						".($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A' ? "<td>$row[namauser]</td>" : "" )."
						<td>$row[catatan]</td>
					</tr>";
			}
			echo "</table>";
			mysqli_free_result($result);

			if($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A'){

				$sql = "
					SELECT	ap.nama, COUNT(ap.nama) as jml
					FROM 	(
								SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
										nilaitagihan, catatan, catatanreject
								FROM	kontrak_approval t1
								WHERE	t1.id = (	SELECT	t2.id
													FROM	kontrak_approval t2
													WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
													ORDER BY t2.signdt DESC
													LIMIT 1
												)
							) ka INNER JOIN 
							kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
							notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN
							notadinas n ON d.nomornota = n.nomornota LEFT JOIN 
							bidang b ON d.pelaksana = b.id LEFT JOIN 
							(
								SELECT 	nokontrak, SUM(nilaibayar) bayar 
								FROM 	realisasibayar 
								GROUP BY nokontrak
							) r ON ka.nmrkontrak = r.nokontrak LEFT JOIN (
								SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
							) p ON d.pos1 = p.pos
							$otherjoin2
					WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
					$parm
					GROUP BY ap.nama
					ORDER BY ap.nama";
				//echo $sql;

				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

				echo "<br/><br/><table><tr><th colspan='3'>Inbox Bayar Pending</th></tr>";

				while ($row = mysqli_fetch_array($result)) {
					
					if (!empty($row[nama])){
						echo "
							<tr>
								<td>$row[nama]</td>
								<td>:</td>
								<td>$row[jml] Inbox Bayar</td>
							</tr>";
					}
				}

				echo "</table>";

				mysqli_free_result($result);
			}
	}
	$mysqli->close();($kon);
	
	//echo $hasil;
?>