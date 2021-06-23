<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=inboxbayar.xls");

	session_start();
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
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysql_query($sql);
	
	$b = "";
	$p = "";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysql_free_result($result);
	
	$sql = "
		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
		ORDER BY pos";
	$result = mysql_query($sql);
	
	$kdpos = "";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$kdpos = ($row["pos"]==$kdpos0? "$row[pos] - $row[namapos]": $kdpos);
	}
	mysql_free_result($result);

	$parm = "";
	// $parm .= ($p1==""? "": " and SUBSTR(tglawal, 1, 7) >= '$p1'");
	// $parm .= ($p2==""? "": " and SUBSTR(tglakhir, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4) . " AND MONTH(inputdt) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(inputdt) = " . substr($p2,0,4) . " AND MONTH(inputdt) <= " . substr($p2,-2));
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and skk = '$k0'");
	$parm .= ($o==""? "": " and skkoi = '$o'");
	$parm .= ($c==""? "": " and TRIM(nomorkontrak) = '$c'");
	/*
		SignLevel #0 -> Laporan penyerapan AI
		SignLevel #1 -> Inbox Bayar Pelaksana UP3 / Bidang
		SignLevel #2 -> Inbox Bayar Manager Bagian
		SignLevel #3 -> Inbox Bayar User Anggaran
		SignLevel #4 -> Inbox Bayar User Keuangan
	*/
	$parm .= ($_SESSION["org"] == '' || $_SESSION["org"] == 3 ? ($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03 ? ' AND (signlevel = 2 AND actiontype = 1)' : ' AND (signlevel = 3 AND actiontype = 1)') : ($_SESSION["roleid"] == 13 ? ' AND (signlevel = 1 AND actiontype = 1)' : ' AND ((signlevel = 0 AND actiontype = 1) OR (signlevel > 1 AND actiontype = 0))'));
	//echo "parm : $parm<br>";

	if($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05){
			
		$parm .= ($p0==""? " and pelaksana in (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."')": " and pelaksana = '$p0'");

	}else{

		if ($_SESSION["roleid"] > 5){
			//$parm .= " and pelaksana = '$_SESSION[org]'";

			if($_SESSION["cnip"] == "8610292Z" || $_SESSION["cnip"] == "94171330ZY"){
				$parm .= " and pelaksana IN ('$_SESSION[org]','1')";	
			}else{

				$parm .= " and pelaksana = '$_SESSION[org]'";	
			}
		}
	}

	if($_SESSION["org"] == '' && ($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03)){
		$parm .= " AND d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')";
	}

	if($v!="") {
			$sql = "
				SELECT	nomorskkoi, pos1, nomorkontrak, b.namaunit, vendor, k.uraian uraian, 
						nodokumen, nilaikontrak, bayar, ka.signed, ka.signdt, ka.signlevel, ka.actiontype, 
						ka.nilaitagihan, ka.catatan, ka.catatanreject, namapos, ap.nama namauser
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
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 LEFT JOIN 
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
						) p ON d.pos1 = p.pos LEFT JOIN
						(
							SELECT 	akses, nama 
							FROM 	akses_pos ap LEFT JOIN
									user u On ap.nip = u.nip
							where 	roleid IN (2,3)
						) ap ON d.pos1 = ap.akses
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
					<th>No SKK</th>
					<th>No Kontrak</th>
					<th>Pelaksana</th>
					<th>Vendor</th>
					<th>Uraian</th>
					<th>No Dokumen</th>
					<th>Nilai Kontrak</th>
					<th>Nilai Bayar</th>
					<th>Sisa</th>
					<th>Nilai Tagihan</th>
					<th>Catatan</th>
				</tr>
			";
			$no = 0;
			$parm = "";
			//$dummy = 0;
			$result = mysql_query($sql);

			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$no++;

				$nilaitagihan = $row["nilaitagihan"];

				if (empty($nilaitagihan)){

					$nilaitagihan = $row["nilaikontrak"] - $row["bayar"];
				}

				echo "
					<tr>
						<td>$no</td>
						<td>$row[skk]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[namaunit]</td>
						<td>$row[vendor]</td>
						<td>$row[uraian]</td>
						<td>$row[nodokumen]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td align='right'>".number_format($row["nilaikontrak"] - $row["bayar"])."</td>
						<td align='right'>".number_format($nilaitagihan)."</td>
						<td>$row[catatan]</td>
					</tr>";
			}
			echo "</table>";
			mysql_free_result($result);
	}
	mysql_close($kon);
	
	//echo $hasil;
?>