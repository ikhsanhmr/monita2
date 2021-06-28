<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=ao.xls");

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
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$p = "";
	$b = "";
	while ($row = mysqli_fetch_array($result)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysqli_free_result($result);
	
	$sql = "
		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
		ORDER BY pos";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$kdpos = "";
	while ($row = mysqli_fetch_array($result)) {
		$kdpos = ($row["pos"]==$kdpos0? "$row[pos] - $row[namapos]": $kdpos);
	}
	mysqli_free_result($result);

	$parm = "";
//	$parm .= ($p1==""? "": " and SUBSTR(tanggalskko, 1, 7) >= '$p1'");
//	$parm .= ($p2==""? "": " and SUBSTR(tanggalskko, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(tanggalskko) = " . substr($p1,0,4) . " AND MONTH(tanggalskko) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(tanggalskko) = " . substr($p2,0,4) . " AND MONTH(tanggalskko) <= " . substr($p2,-2));
	// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskko = '$k0'");
	$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
	//echo "parm : $parm<br>";

	if($v!="") {
		$sql = "
			SELECT 
				n.nomornota, nipuser, 
				pelaksana, b.namaunit, pos1, nilai1, namapos,
				nomorwbs, nomorcostcenter, nomorskko noskk, s.uraian uraians, tanggalskko, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, date(k.tgltagih) as tgltagih, isrutin, 
				inputdt, nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak kontrak, bayar, 
				kapel.signdt as app_pel, kaang.signdt as app_ang, kakeu.signdt as app_keu 
			FROM notadinas n
			LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
			LEFT JOIN bidang b ON d.pelaksana = b.id  
			LEFT JOIN skkoterbit s ON d.noskk = s.nomorskko
			LEFT JOIN (
				SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			) p ON d.pos1 = p.pos
			LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
			LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak LEFT JOIN
			(
				SELECT	t.nomorkontrak as nmrkontrak, signdt
				FROM		kontrak_approval t INNER JOIN 
								(
									SELECT nomorkontrak, MAX( id ) AS lastid
									FROM kontrak_approval
									Where signlevel = 2 and actiontype = 1
									GROUP BY nomorkontrak
								)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
			) kapel  ON TRIM(k.nomorkontrak) = TRIM(kapel.nmrkontrak) LEFT JOIN
			(
				SELECT	t.nomorkontrak as nmrkontrak, signdt
				FROM		kontrak_approval t INNER JOIN 
								(
									SELECT nomorkontrak, MAX( id ) AS lastid
									FROM kontrak_approval
									Where signlevel = 3 and actiontype = 1
									GROUP BY nomorkontrak
								)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
			) kaang  ON TRIM(k.nomorkontrak) = TRIM(kaang.nmrkontrak) LEFT JOIN
			(
				SELECT	t.nomorkontrak as nmrkontrak, signdt
				FROM		kontrak_approval t INNER JOIN 
								(
									SELECT nomorkontrak, MAX( id ) AS lastid
									FROM kontrak_approval
									Where signlevel = 4 and actiontype = 1
									GROUP BY nomorkontrak
								)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
			) kakeu  ON TRIM(k.nomorkontrak) = TRIM(kakeu.nmrkontrak)
			WHERE d.progress >= 7 AND NOT nomorskko IS NULL 
			$parm
			ORDER BY nomorskko, LPAD(pelaksana, 2, '0'), k.pos, k.inputdt DESC, nomorkontrak ";
		//echo $sql;
		
		//$hasil = "
		echo "
		<strong>LAPORAN MONITORING PENYERAPAN ANGGARAN OPERASI</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		<strong>POS : $kdpos</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB
		
		<table border='1'>
			<tr>
				<th rowspan='3' scope='col'>No Urut</th>
				<th colspan='12' scope='col'>SKKO Terbit</th>
				<th colspan='8' scope='col'>Kontrak</th>
				<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
				<th colspan='2' scope='col'>Sisa</th>
				<th rowspan='3' scope='col'>Tgl Entry</th>
				<th rowspan='3' scope='col'>Tgl Approve Bidang/UP3</th>
				<th rowspan='3' scope='col'>Tgl Approve Anggaran</th>
				<th rowspan='3' scope='col'>Tgl Approve Keuangan</th>
			</tr>
			<tr>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>No WBS / Cost Center</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor Nota</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
				<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
				<td align='center' colspan='3' style='background-color:rgb(127,255,127)'>POS</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Vendor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Jenis Kontrak</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Bulan Tagih</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nilai</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKO (Disburse - Kontrak)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
			</tr>
			<tr>
				<td style='background-color:rgb(127,255,127)'>Anggaran (Rp.)</td>
				<td style='background-color:rgb(127,255,127)'>Disburse (Rp.)</td>
				<td style='background-color:rgb(127,255,127)'>Kode</td>
				<td style='background-color:rgb(127,255,127)'>Ket</td>
				<td style='background-color:rgb(127,255,127)'>Nilai</td>
			</tr>
		";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$no = 0;
		$dummy = "";
		$dummypos = "";
		$hasil0 = "";
		$pnk = 0;
		$pnb = 0;
		$snk = 0;
		$snb = 0;
		$npost = 0;

		$angt = 0;
		$disbt = 0;
		$wbst = 0;
		$post = 0;
		$kont = 0;
		$bayt = 0;

		$hasilk = "";
		
		
		while ($row = mysqli_fetch_array($result)) {
			$cskk = ($dummy == $row["noskk"]? true: false);
			
			if($dummy != $row["noskk"] || $dummypos != $row["pos1"]) {
				if($no>0) {
					$hasilp .= "
							<td></td>
							<td></td>
							<td align='right'>".($pnk)."</td>
							<td align='right'>".($pnb)."</td>
							<td align='right'>".($npos-$pnk)."</td>
							<td align='right'>".($pnk-$pnb)."</td>
							<td></td>
						</tr>
					";
					
					$hasil0 .= $hasilp . $hasilk;
					$pnk = 0;
					$pnb = 0;
					$hasilk = "";
				}

				$hasilp = "
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align='right'></td>
						<td align='right'></td>
						<td align='right'></td>
						<td>$row[pos1]</td>
						<td>$row[namapos]</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>";
						
						$npos = $row["nilai1"];
						//if($dummy!=$row["noskk"])
						$npost = ($dummy==$row["noskk"]? $npost+$row["nilai1"]: $npost);
						$post += $row["nilai1"];
/*							
						<td align='right'>".($row["nilai1"])."</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td></td>
					</tr>
				";
*/
			}
			
			if($dummy != $row["noskk"]) {
				if($no>0) {
					$hasils .= "
							<td align='right'>".($npost)."</td>
							<td>$nu</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align='right'>".($snk)."</td>
							<td align='right'>".($snb)."</td>
							<td align='right'>".($disb-$snk)."</td>
							<td align='right'>".($snk-$snb)."</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					";
					
					//$hasil .= $hasils . $hasil0;
					echo $hasils . $hasil0;
					
					//$hasil = "";
					$hasil0 = "";
					$snk = 0;
					$snb = 0;
				}

				$no++;
				$npost = $row["nilai1"];
				$nu = $row["namaunit"];
				$disb = $row["disburse"];
				
				$angt += $row["anggaran"];
				$disbt += $row["disburse"];
				$wbst += $row["wbs"];
				
				$hasils = "
					<tr>
						<td>$no</td>
						<td>" . $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorcostcenter"]!=""? " / ": "") . $row["nomorcostcenter"] . "</td>
						<td>$row[nomornota]</td>
						<td>$row[noskk]</td>
						<td>$row[uraians]</td>
						<td>$row[tanggalskko]</td>
						<td align='right'>".($row["anggaran"])."</td>
						<td align='right'>".($row["disburse"])."</td>
						<td align='right'>".($row["wbs"])."</td>
						<td></td>
						<td></td>";
/*							
						<td align='right'>".($npost)."</td>
						<td>$row[namaunit]</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align='right'>".($npos$row["nilai1"])."</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td align='right'>".($row["nilai1"])."</td>
						<td>$row[inputdt]</td>
					</tr>
				";
*/
			}
			
			
			$dummypos = $row["pos1"];
			$dummy = $row["noskk"];
			
			$snk += $row["kontrak"];
			$snb += $row["bayar"];
			
			$pnk += $row["kontrak"];
			$pnb += $row["bayar"];
			
			$kont += $row["kontrak"];
			$bayt += $row["bayar"];
			
			$hasilk .= "
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'></td>
					<td align='right'></td>
					<td align='right'></td>
					<td></td>
					<td></td>
					<td align='right'></td>
					<td></td>
					<td>$row[nomorkontrak]</td>
					<td>$row[vendor]</td>
					<td>$row[uraiank]</td>
					<td>".(empty($row["nomorkontrak"]) ? "" : ($row["isrutin"] == 0 ? "NON RUTIN" : 'RUTIN'))."</td>
					<td>".(empty($row["nomorkontrak"]) ? "" : ($row["tgltagih"] != '0000-00-00' ? date_format(date_create($row["tgltagih"]), "M-Y") : '-'))."</td>
					<td>$row[tglawal]</td>
					<td>$row[tglakhir]</td>
					<td align='right'>".($row["kontrak"])."</td>
					<td align='right'>".($row["bayar"])."</td>
					<td></td>
					<td align='right'>".number_format($row["kontrak"]-$row["bayar"])."</td>
					<td>$row[inputdt]</td>
					<td>$row[app_pel]</td>
					<td>$row[app_ang]</td>
					<td>$row[app_keu]</td>
				</tr>
			";
		}
		mysqli_free_result($result);
		
		$hasilp .= "
				<td></td>
				<td></td>
				<td align='right'>".($pnk)."</td>
				<td align='right'>".($pnb)."</td>
				<td align='right'>".($npos-$pnk)."</td>
				<td align='right'>".($pnk-$pnb)."</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		";
		
		$hasil0 .= $hasilp . $hasilk;
		$pnk = 0;
		$pnb = 0;
		$hasilk = "";

		$hasils .= "
				<td align='right'>".($npost)."</td>
				<td>$nu</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'>".($snk)."</td>
				<td align='right'>".($snb)."</td>
				<td align='right'>".($disb-$snk)."</td>
				<td align='right'>".($snk-$snb)."</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		";
		
		//$hasil .= $hasils . $hasil0;
		echo $hasils . $hasil0;
		
		//$hasil = "";
		$hasil0 = "";
		$snk = 0;
		$snb = 0;
		
		//$hasil .= "</table>";
		echo "
			<tr>
				<td colspan='6'>Total</td>
				<td align='right'>" . ($angt) . "</td>
				<td align='right'>" . ($disbt) . "</td>
				<td align='right'>" . ($wbst) . "</td>
				<td></td>
				<td></td>
				<td align='right'>" . ($post) . "</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'>" . ($kont) . "</td>
				<td align='right'>" . ($bayt) . "</td>
				<td align='right'>" . ($disbt-$kont) . "</td>
				<td align='right'>" . ($kont-$bayt) . "</td>
				<td colspan='4'></td>
			</tr>";
		echo "</table>";

	}
	$mysqli->close();($kon);
	
	//echo $hasil;
?>