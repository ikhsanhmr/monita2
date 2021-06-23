<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=aoskk.xls");

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
	//$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	require_once "../config/koneksi.php";
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysql_query($sql);
	
	$p = "";
	$b = "";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysql_free_result($result);

	$parm = "";
	$parmkk = "";
//	$parm .= ($p1==""? "": " and SUBSTR(tanggalskko, 1, 7) >= '$p1'");
//	$parm .= ($p2==""? "": " and SUBSTR(tanggalskko, 1, 7) <= '$p2'");
	// $parm .= ($p1==""? "": " and YEAR(tanggalskko) = " . substr($p1,0,4) . " AND MONTH(tanggalskko) >= " . substr($p1,-2));
	// $parm .= ($p2==""? "": " and YEAR(tanggalskko) = " . substr($p2,0,4) . " AND MONTH(tanggalskko) <= " . substr($p2,-2));
	$parm .= ($p1==""? "": " and tanggalskko >= '" . substr($p1,0,4) . "-" . substr($p1,-2) . "-01'");
	$parm .= ($p2==""? "": " and tanggalskko <= '" . substr($p2,0,4) . "-" . substr($p2,-2) . "-31'");

	$parmkk .= ($p1==""? "": " and YEAR(inputdt) >= " . substr($p1,0,4));
	$parmkk .= ($p2==""? "": " and YEAR(inputdt) <= " . substr($p2,0,4));

	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskko = '$k0'");
//		$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
	//echo "parm : $parm<br>";
	echo "
		<strong>LAPORAN MONITORING PENYERAPAN ANGGARAN OPERASI PER NOMOR SKK</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
			
	if($v!="") {
		$sql = "
			SELECT 	b.namaunit, nomorwbs, nomorcostcenter, nomorskko noskk, s.uraian uraians, nilaiwbs wbs, 
					DATE_FORMAT(tanggalskko, '%d-%m-%Y') as tanggalskko, DATEDIFF(SYSDATE(), tanggalskko) umur,
					nilaianggaran anggaran, nilaidisburse disburse, COALESCE(kontrak,0) kontrak, 
					COALESCE(bayar,0) bayar
			FROM 	notadinas n LEFT JOIN 
					bidang g ON n.nipuser = g.nick LEFT JOIN 
					(
						SELECT 	nomornota, noskk, pelaksana, progress 
						FROM 	notadinas_detail  
						GROUP BY nomornota, noskk, pelaksana
					) d ON n.nomornota = d.nomornota LEFT JOIN 
					bidang b ON d.pelaksana = b.id LEFT JOIN 
					skkoterbit s ON d.noskk = s.nomorskko LEFT JOIN 
					(
						SELECT 	nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
						FROM 	kontrak k1 LEFT JOIN 
								(
									SELECT 	nokontrak, SUM(nilaibayar) bayar 
									FROM 	realisasibayar 
									GROUP BY nokontrak
								) r ON k1.nomorkontrak = r.nokontrak 
						WHERE 	1=1 $parmkk
						GROUP BY nomorskkoi
					) rr ON d.noskk = rr.noskk
			WHERE 	d.progress >= 7 AND NOT nomorskko IS NULL 
			$parm
			ORDER BY nomorskko, LPAD(pelaksana, 2, '0')
		";
						//echo $sql;
		
		//$hasil = "
		echo "
		<table border='1'>
			<tr>
				<th rowspan='3' scope='col'>No Urut</th>
				<th colspan='9' scope='col'>SKKO Terbit</th>
				<th rowspan='3' scope='col'>Nilai Kontrak (Rp.)</th>
				<th rowspan='3' scope='col'>% Kontrak</th>
				<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
				<th rowspan='3' scope='col'>% Bayar</th>
				<th colspan='2' scope='col'>Sisa</th>
				<th rowspan='3' scope='col'>Keterangan</th>
			</tr>
			<tr>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>No WBS / Cost Center</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Umur SKKO</td>
				<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKO (Disburse - Kontrak)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
			</tr>
			<tr>
				<td style='background-color:rgb(127,255,127)'>Anggaran (Rp.)</td>
				<td style='background-color:rgb(127,255,127)'>Disburse (Rp.)</td>
			</tr>
		";
		$result = mysql_query($sql);
		
		$no = 0;
		$dummy = "";
		$a = 0;
		$d = 0;
		$w = 0;
		$k = 0;
		$b = 0;
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$no++;
			
			$a += $row["anggaran"];
			$d += $row["disburse"];
			$w += $row["wbs"];
			$k += $row["kontrak"];
			$b += $row["bayar"];
			
			echo "
				<tr>
					<td>$no</td>
					<td>" . $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorcostcenter"]!=""? " / ": "") . $row["nomorcostcenter"] . "</td>
					<td>$row[noskk]</td>
					<td>$row[uraians]</td>
					<td>$row[tanggalskko]</td>
					<td bgcolor='" . ($row["kontrak"]>0? "green": ($row["umur"]>90? "red": "yellow")) . "'>$row[umur]</td>
					<td align='right'>".number_format($row["anggaran"])."</td>
					<td align='right'>".number_format($row["disburse"])."</td>
					<td align='right'>".number_format($row["wbs"])."</td>
					<td>$row[namaunit]</td>
					<td align='right'>".number_format($row["kontrak"])."</td>
					<td align='right'>".number_format(@($row["kontrak"]/$row["disburse"])*100,2)."</td>
					<td align='right'>".number_format($row["bayar"])."</td>
					<td align='right'>".number_format(@($row["bayar"]/$row["kontrak"])*100,2)."</td>
					<td align='right'>".number_format($row["disburse"]-$row["kontrak"])."</td>
					<td align='right'>".number_format($row["kontrak"]-$row["bayar"])."</td>
					<td></td>
				</tr>
			";
		}
		
		mysql_free_result($result);		
		echo "
			<tr>
				<td colspan='6'>Total</td>
				<td align='right'>" . number_format($a) . "</td>
				<td align='right'>" . number_format($d) . "</td>
				<td align='right'>" . number_format($w) . "</td>
				<td></td>
				<td align='right'>" . number_format($k) . "</td>
				<td align='right'>" . number_format(@($k/$d)*100,2) . "</td>
				<td align='right'>" . number_format($b) . "</td>
				<td align='right'>" . number_format(@($b/$k)*100,2) . "</td>
				<td align='right'>" . number_format($d-$k) . "</td>
				<td align='right'>" . number_format($k-$b) . "</td>
				<td></td>
			</tr>";
		echo "</table>";
	}
	mysql_close($kon);
	
	//echo $hasil;
?>