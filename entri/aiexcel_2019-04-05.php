<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=ai.xls");

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
//	$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//	$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));
	
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskki = '$k0'");
	$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
	//echo "parm : $parm<br>";

	if($v!="") {
			$sql = "
				SELECT 
					n.nomornota, nipuser, g.id userid, 
					pelaksana, b.namaunit, pos1, nilai1, namapos,
					nomorwbs, nomorprk, nomorscore, nomorskki noskk, s.uraian uraians, tanggalskki, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
					inputdt, nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak kontrak, k.signed, bayar 
				FROM notadinas n
				LEFT JOIN bidang g ON n.nipuser = g.nick 
				LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
				LEFT JOIN bidang b ON d.pelaksana = b.id  
				LEFT JOIN skkiterbit s ON d.noskk = s.nomorskki
				LEFT JOIN (
					SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
				) p ON d.pos1 = p.pos
				LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
				LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak
				WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";
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
					<th colspan='11' scope='col'>SKKI Terbit</th>
					<th colspan='6' scope='col'>Kontrak</th>
					<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
					<th colspan='2' scope='col'>Sisa</th>
					<th rowspan='3' scope='col'>Tgl Entry</th>
					<th rowspan='3' scope='col'>Status</th>
				</tr>
				<tr>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Score</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>KMS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>RP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>KMS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>RP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>UNIT</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>RP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>PLGN</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>RP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>PLGN</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>RP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Unit</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>RP</td>
					<td align='center' colspan='3' style='background-color:rgb(127,255,127)'>POS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Vendor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKI (Disburse - Kontrak)</td>
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
			$result = mysql_query($sql);
			
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
			$hasilk = 0;

			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$cskk = ($dummy == $row["noskk"]? true: false);
				
				if($dummy != $row["noskk"] || $dummypos != $row["pos1"]) {
					if($no>0) {
						$hasilp .= "
								<td align='right'>".number_format($pnk)."</td>
								<td align='right'>".number_format($pnb)."</td>
								<td align='right'>".number_format($npos-$pnk)."</td>
								<td align='right'>".number_format($pnk-$pnb)."</td>
								<td></td>
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
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td>$row[pos1]</td>
							<td>$row[namapos]</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
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
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td></td>
						</tr>
					";
*/
				}
				
				if($dummy != $row["noskk"]) {
					if($no>0) {
						$hasils .= "
								<td align='right'>".number_format($npost)."</td>
								<td>$nu</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align='right'>".number_format($snk)."</td>
								<td align='right'>".number_format($snb)."</td>
								<td align='right'>".number_format($disb-$snk)."</td>
								<td align='right'>".number_format($snk-$snb)."</td>
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
							<td>" . $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorprk"]!=""? " / ": "") . $row["nomorprk"] . ($row["nomorscore"]==""? "": "/$row[nomorscore]") . "</td>
							<td>$row[noskk]</td>
							<td>$row[uraians]</td>
							<td>$row[tanggalskki]</td>
							<td align='right'>".number_format($row["anggaran"])."</td>
							<td align='right'>".number_format($row["disburse"])."</td>
							<td align='right'>".number_format($row["wbs"])."</td>
							<td></td>
							<td></td>";
/*							
							<td align='right'>".number_format($npost)."</td>
							<td>$row[namaunit]</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align='right'>".number_format($npos$row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
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
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["kontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td></td>
						<td></td>
						<td>$row[inputdt]</td>
						<td>". 
							($row["nomorkontrak"]==""? "": 
								($_SESSION["org"]==""? ($row["signed"]==""? "": "signed"): ($row["signed"]==""? "": "Signed"))
							) .
						"</td>						
					</tr>
				";
			}
			mysql_free_result($result);
			
			$hasilp .= "
					<td align='right'>".number_format($pnk)."</td>
					<td align='right'>".number_format($pnb)."</td>
					<td align='right'>".number_format($npos-$pnk)."</td>
					<td align='right'>".number_format($pnk-$pnb)."</td>
					<td></td>
					<td></td>
				</tr>
			";
			
			$hasil0 .= $hasilp . $hasilk;
			$pnk = 0;
			$pnb = 0;
			$hasilk = "";

			$hasils .= "
					<td align='right'>".number_format($npost)."</td>
					<td>$nu</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>".number_format($snk)."</td>
					<td align='right'>".number_format($snb)."</td>
					<td align='right'>".number_format($disb-$snk)."</td>
					<td align='right'>".number_format($snk-$snb)."</td>
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
					<td colspan='5'>Total</td>
					<td align='right'>" . number_format($angt) . "</td>
					<td align='right'>" . number_format($disbt) . "</td>
					<td align='right'>" . number_format($wbst) . "</td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($post) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($kont) . "</td>
					<td align='right'>" . number_format($bayt) . "</td>
					<td align='right'>" . number_format($disbt-$kont) . "</td>
					<td align='right'>" . number_format($kont-$bayt) . "</td>
					<td></td>
					<td></td>
				</tr>";
			echo "</table>";
	}
	mysql_close($kon);
	
	//echo $hasil;
?>