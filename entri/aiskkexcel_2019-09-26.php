<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=aiskk.xls");

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
//	$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//	$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskki = '$k0'");
//		$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
	//echo "parm : $parm<br>";
	echo "
		<strong>LAPORAN MONITORING PENYERAPAN ANGGARAN INVESTASI PER NOMOR SKK</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
			
	if($v!="") {
		$sql = "
			SELECT 
				n.nomornota, nipuser, g.id userid, 
				pelaksana, b.namaunit, /* pos1, nilai1, namapos, */
				nomorscore, nomorwbs, nomorprk, nomorskki noskk, s.uraian uraians, tanggalskki, DATEDIFF(SYSDATE(), tanggalskki) umur, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
				COALESCE(kontrak,0) kontrak, COALESCE(bayar,0) bayar
			FROM notadinas n
			LEFT JOIN bidang g ON n.nipuser = g.nick 
			LEFT JOIN (SELECT nomornota, noskk, pelaksana, progress FROM notadinas_detail  GROUP BY nomornota, noskk, pelaksana) d ON n.nomornota = d.nomornota
			LEFT JOIN bidang b ON d.pelaksana = b.id  
			LEFT JOIN skkiterbit s ON d.noskk = s.nomorskki
			/*
			LEFT JOIN (
				SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			) p ON d.pos1 = p.pos
			LEFT JOIN (
				SELECT nomorskkoi noskk, pos, SUM(nilaikontrak) kontrak FROM kontrak GROUP BY nomorskkoi, pos
			) k ON d.noskk = k.noskk AND d.pos1 = k.pos
			*/
			LEFT JOIN (
				SELECT nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar FROM kontrak k1 LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k1.nomorkontrak = r.nokontrak GROUP BY nomorskkoi
			) rr ON d.noskk = rr.noskk /* AND d.pos1 = rr.pos */
			WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
			$parm
			ORDER BY nomorskki, LPAD(pelaksana, 2, '0')";
						//echo $sql;
		
		//$hasil = "
		echo "
		<table border='1'>
			<tr>
				<th rowspan='3' scope='col'>No Urut</th>
				<th colspan='10' scope='col'>SKKI Terbit</th>
				<th rowspan='3' scope='col'>Nilai Kontrak (Rp.)</th>
				<th rowspan='3' scope='col'>% Kontrak</th>
				<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
				<th rowspan='3' scope='col'>% Bayar</th>
				<th colspan='2' scope='col'>Sisa</th>
				<th rowspan='3' scope='col'>Keterangan</th>
			</tr>
			<tr>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Score</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>NomorSKKI</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>NomorNotadinas</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Umur SKKI</td>
				<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKI (Disburse - Kontrak)</td>
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
					<td>" . $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorprk"]!=""? " / ": "") . $row["nomorprk"] . ($row["nomorscore"]==""? "": "/$row[nomorscore]") . "</td>
					<td>$row[noskk]</td>
					<td>$row[nomornota]</td>
					<td>$row[uraians]</td>
					<td>$row[tanggalskki]</td>
					<td bgcolor='" . ($row["kontrak"]>0? "green": ($row["umur"]>90? "red": "yellow")) . "'>$row[umur]</td>
					<td align='right'>".number_format($row["anggaran"])."</td>
					<td align='right'>".number_format($row["disburse"])."</td>
					<td align='right'>".number_format($row["wbs"])."</td>
					<td>$row[namaunit]</td>
					<td align='right'>".number_format($row["kontrak"])."</td>
					<td align='right'>".number_format($row["kontrak"]/$row["disburse"]*100,2)."</td>
					<td align='right'>".number_format($row["bayar"])."</td>
					<td align='right'>".number_format($row["bayar"]/$row["kontrak"]*100,2)."</td>
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
				<td align='right'>" . number_format($k/$d*100,2) . "</td>
				<td align='right'>" . number_format($b) . "</td>
				<td align='right'>" . number_format($b/$k*100,2) . "</td>
				<td align='right'>" . number_format($d-$k) . "</td>
				<td align='right'>" . number_format($k-$b) . "</td>
				<td></td>
			</tr>";
		echo "</table>";
	}
	mysql_close($kon);
	
	//echo $hasil;
?>