<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=reimbursement.xls");

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
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$b = "";
	$p = "";
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
//	$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//	$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(tglbayar) = " . substr($p1,0,4) . " AND MONTH(tglbayar) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(tglbayar) = " . substr($p2,0,4) . " AND MONTH(tglbayar) <= " . substr($p2,-2));
	
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskki = '$k0'");
	$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
	//echo "parm : $parm<br>";

	if($v!="") {
			$sql = "
				SELECT	nomorprk, nomorscore, nomorskki noskk, nilaianggaran anggaran, nilaidisburse disburse, 
						tanggalskki, b.namaunit, namapos, nomorkontrak, k.vendor, k.uraian uraiank, tglawal, tglakhir, 
						nilaikontrak, nilaibayar, tglbayar, pmn, COALESCE(nodokumen, nodokrep) as nodokumen,
						(Select nilaitengah From kurs_dollar kd where kd.tanggal <= r.tglbayar Order by tanggal DESC Limit 1) as nilaikurs
				FROM 	notadinas n LEFT JOIN 
						bidang g ON n.nipuser = g.nick LEFT JOIN 
						notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
						(
							SELECT 	kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos LEFT JOIN 
						kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
						realisasibayar r ON k.nomorkontrak = r.nokontrak
				WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";
			//echo $sql;
			
			
			$dateform = date("d M Y", mktime(0, 0, 0, substr($p1,-2), 1, substr($p1,0,4)));
			$dateuntil = date("t M Y", mktime(0, 0, 0, substr($p2,-2), 1, substr($p2,0,4)));

			echo "
			<p>PT PLN(Persero)Wilayah Sumatera Utara<br>JL. K. L. Yos Sudarso No 284 Medan</p>
			
			<strong><h2>REALISASI PEMBAYARAN ANGGARAN INVESTASI</h2></strong>
			<strong><h4>Pengajuan  Reimbursement RBL-Loan ADB dan PMN $dateform sd $dateuntil</strong></h4>

			<table border='1'>
				<tr>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>No</th>
					<th colspan='2' scope='col' style='background-color:rgb(127,255,127)'>Program Rencana Kerja</th>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>Nomor SKKI</th>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>Nilai SKKI</th>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>Tanggal Terbit</th>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>PLN Unit/Pelaksana</th>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>Jenis</th>
					<th colspan='6' scope='col' style='background-color:rgb(127,255,127)'>Kontrak Pekerjaan</th>
					<th colspan='2' scope='col' style='background-color:rgb(127,255,127)'>Pembayaran</th>
					<th colspan='3' scope='col' style='background-color:rgb(127,255,127)'>Equivalent Currency</th>
					<th rowspan='2' scope='col' style='background-color:rgb(127,255,127)'>PMN/NON PMN</th>
				</tr>
				<tr>
					<td align='center' style='background-color:rgb(127,255,127)'>Nomor PRK</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Fungsi/Basket</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nomor Kontrak</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nama Rekanan</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Uraian Pekerjaan</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai Kontrak</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Tanggal</td>
					<td align='center' style='background-color:rgb(127,255,127)'>No. SAP</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Kusr SAP (Pada tanggal pembayaran)</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Equivalent Kurs (USD)</td>
				</tr>
				<tr>
					<td align='center'>1</td>
					<td align='center'>2</td>
					<td align='center'>3</td>
					<td align='center'>4</td>
					<td align='center'>5</td>
					<td align='center'>6</td>
					<td align='center'>7</td>
					<td align='center'>8</td>
					<td align='center'>9</td>
					<td align='center'>10</td>
					<td align='center'>11</td>
					<td align='center'>12</td>
					<td align='center'>13</td>
					<td align='center'>14</td>
					<td align='center'>15</td>
					<td align='center'>16</td>
					<td align='center'>17</td>
					<td align='center'>18</td>
					<td align='center'>19</td>
					<td align='center'>20</td>
				</tr>
			";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			$no = 0;
			$totalkontrak = 0;
			$totalbayar = 0;
			$totalconvertion = 0;
			$totalpmn = 0;
			$totalnonpmn = 0;
			
			while ($row = mysqli_fetch_array($result)) {
				$no++;

				if (!empty($row["nilaikurs"])){
					$nilaisetelahkurs = round(($row["nilaibayar"]/$row["nilaikurs"]));
				}
				
				$totalkontrak += $row["nilaikontrak"];
				$totalbayar += $row["nilaibayar"];
				$totalconvertion += $nilaisetelahkurs;

				if(strtolower($row[pmn])  == "pmn"){

					$totalpmn += $row["nilaibayar"];
				}

				if(strtolower($row[pmn]) == "non pmn"){
					
					$totalnonpmn += $row["nilaibayar"];
				}

				echo "
					<tr>
						<td>$no</td>
						<td>$row[nomorprk]</td>
						<td>$row[nomorscore]</td>
						<td>$row[noskk]</td>
						<td align='right'>".number_format($row["anggaran"])."</td>
						<td>$row[tanggalskki]</td>
						<td>$row[namaunit]</td>
						<td>$row[namapos]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[vendor]</td>
						<td>$row[uraiank]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["nilaibayar"])."</td>
						<td>$row[tglbayar]</td>
						<td>$row[nodokumen]</td>
						<td align='right'>".number_format($row["nilaikurs"])."</td>
						<td align='right'>".number_format($nilaisetelahkurs)."</td>
						<td>$row[pmn]</td>
					</tr>
				";

				
			}
			mysqli_free_result($result);
			
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
			
			echo "
				<tr>
					<td colspan='13' align='center'><b>Total</b></td>
					<td align='right'>" . number_format($totalkontrak) . "</td>
					<td align='right'>" . number_format($totalbayar) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($totalconvertion) . "</td>
					<td></td>
				</tr>";
		  echo "</tbody>";	
		echo "</table>";
		echo "
				<table>
					<tr>
						<td colspan='12' rowspan='4'></td>
						<td colspan='3'>
							<table border='1'>
								<tr>
									<td colspan='2' align='right'><b>Uraian</b></td>
									<td align='center'><b>Nilai</b></td>
								</tr>
								<tr>
									<td colspan='2' align='right'>NON PMN</td>
									<td align='right'>" . number_format($totalnonpmn) . "</td>
								</tr>
								<tr>
									<td colspan='2' align='right'>LISDES/PMN</td>
									<td align='right'>" . number_format($totalpmn) . "</td>
								</tr>
								<tr>
									<td colspan='2' align='right'>Total</td>
									<td align='right'>" . number_format($totalnonpmn + $totalpmn) . "</td>
								</tr>
							</table>
						<td>
						<td colspan='5' rowspan='4'></td>
					</tr>
				</table>";
	}
	$mysqli->close();($kon);
	
	//echo $hasil;
?>