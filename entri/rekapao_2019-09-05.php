<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(x) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var kpos = document.getElementById("kdpos").value;
			//alert(kpos);
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI((x==undefined? "rekapao.php": "rekapaoexcel.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&kpos="+kpos+"&v=1");
			//alert(url);
			window.open(url, "_self");
		}
	</script>
	
	<?php
//		header("Content-type: application/vnd.ms-excell");
//		header("Content-Disposition: attachment; Filename=ao.xls");

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
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysql_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
		
		$sql = "
			SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			ORDER BY pos";
		$result = mysql_query($sql);
		
		$kdpos = "<select name='kdpos' id='kdpos'><option value=''></option>";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$kdpos .= "<option value='$row[pos]' " . ($row["pos"]==$kdpos0? "selected": "") . ">$row[pos] - $row[namapos]</option>";
		}
		$kdpos .= "</select>";
		mysql_free_result($result);
	?>
</head>


<body>
	<?php
		$parm = "";
//		$parm .= ($p1==""? "": " and SUBSTR(tanggalskko, 1, 7) >= '$p1'");
//		$parm .= ($p2==""? "": " and SUBSTR(tanggalskko, 1, 7) <= '$p2'");
		$parm .= ($p1==""? "": " and YEAR(tanggalskko) = " . substr($p1,0,4) . " AND MONTH(tanggalskko) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(tanggalskko) = " . substr($p2,0,4) . " AND MONTH(tanggalskko) <= " . substr($p2,-2));
		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and nomorskko = '$k0'");
		$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
		//echo "parm : $parm<br>";
		echo "
			<h2>Rekap Monitoring Penyerapan Anggaran Operasi</h2>
			<table>
				<tr>
					<th>Periode (yyyy-mm)</th>
					<td>:</td>
					<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
				</tr>

				<tr>
					<th>Bidang</th>
					<td>:</td>
					<td>$b</td>
				</tr>
				<tr>
					<th>Pelaksana</th>
					<td>:</td>
					<td>$p</td>
				</tr>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='49' value='$k0'></td>
				</tr>
				<tr>
					<th>POS</th>
					<td>:</td>
					<td>$kdpos</td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='View' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>";
		
		if($v!="") {
			$sql = "
				SELECT 
					n.nomornota, nipuser, g.id userid, 
					pelaksana, b.namaunit, pos1, nilai1, namapos,
					nomorwbs, nomorcostcenter, nomorskko noskk, s.uraian uraians, tanggalskko, datediff(sysdate(), tanggalskko) umur, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
					COALESCE(kontrak,0) kontrak, COALESCE(bayar,0) bayar
				FROM notadinas n
				LEFT JOIN bidang g ON n.nipuser = g.nick 
				LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
				LEFT JOIN bidang b ON d.pelaksana = b.id  
				LEFT JOIN skkoterbit s ON d.noskk = s.nomorskko
				LEFT JOIN (
					SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
					SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
				) p ON d.pos1 = p.pos
				LEFT JOIN (
					SELECT nomorskkoi noskk, pos, SUM(nilaikontrak) kontrak FROM kontrak GROUP BY nomorskkoi, pos
				) k ON d.noskk = k.noskk AND d.pos1 = k.pos
				LEFT JOIN (
					SELECT nomorskkoi noskk, pos, SUM(bayar) bayar FROM kontrak k1 LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k1.nomorkontrak = r.nokontrak GROUP BY nomorskkoi, pos
				) rr ON d.noskk = rr.noskk AND d.pos1 = rr.pos
				WHERE d.progress >= 7 AND NOT nomorskko IS NULL 
				$parm
				ORDER BY nomorskko, LPAD(pelaksana, 2, '0'), pos1";
			//echo $sql;
			
			//$hasil = "
			echo "
			<table border='1'>
				<tr>
					<th rowspan='3' scope='col'>No Urut</th>
					<th colspan='12' scope='col'>SKKO Terbit</th>
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
					<td align='center' colspan='3' style='background-color:rgb(127,255,127)'>POS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
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

			$hasilk = "";
			
			
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$cskk = ($dummy == $row["noskk"]? true: false);
				
				if($dummy != $row["noskk"] || $dummypos != $row["pos1"]) {
					if($no>0) {
						$hasilp .= "
								<td align='right'>".number_format($pnk)."</td>
								<td align='right'>".number_format($pnk/$npos*100,2)."</td>
								<td align='right'>".number_format($pnb)."</td>
								<td align='right'>".number_format($pnb/$pnk*100,2)."</td>
								<td align='right'>".number_format($npos-$pnk)."</td>
								<td align='right'>".number_format($pnk-$pnb)."</td>
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
							<td bgcolor='" . ($row["kontrak"]>0? "green": ($row["umur"]>90? "red": "yellow")) . "'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td>$row[pos1]</td>
							<td>$row[namapos]</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
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
								<td align='right'>".number_format($snk)."</td>
								<td align='right'>".number_format($snk/$npost*100,2)."</td>
								<td align='right'>".number_format($snb)."</td>
								<td align='right'>".number_format($snb/$snk*100,2)."</td>
								<td align='right'>".number_format($disb-$snk)."</td>
								<td align='right'>".number_format($snk-$snb)."</td>
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
							<td>$row[noskk]</td>
							<td>$row[uraians]</td>
							<td>$row[tanggalskko]</td>
							<td bgcolor='" . ($row["kontrak"]>0? "green": ($row["umur"]>90? "red": "yellow")) . "'>$row[umur]</td>
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
				/*
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
					</tr>
				";
				*/
			}
			mysql_free_result($result);
			
			$hasilp .= "
					<td align='right'>".number_format($pnk)."</td>
					<td align='right'>".number_format($pnk/$npos*100,2)."</td>
					<td align='right'>".number_format($pnb)."</td>
					<td align='right'>".number_format($pnb/$pnk*100,2)."</td>
					<td align='right'>".number_format($npos-$pnk)."</td>
					<td align='right'>".number_format($pnk-$pnb)."</td>
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
					<td align='right'>".number_format($snk)."</td>
					<td align='right'>".number_format($snk/$npost*100,2)."</td>
					<td align='right'>".number_format($snb)."</td>
					<td align='right'>".number_format($snb/$snk*100,2)."</td>
					<td align='right'>".number_format($disb-$snk)."</td>
					<td align='right'>".number_format($snk-$snb)."</td>
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
					<td align='right'>" . number_format($angt) . "</td>
					<td align='right'>" . number_format($disbt) . "</td>
					<td align='right'>" . number_format($wbst) . "</td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($post) . "</td>
					<td></td>
					<td align='right'>" . number_format($kont) . "</td>
					<td align='right'>" . number_format($kont/$post*100,2) . "</td>
					<td align='right'>" . number_format($bayt) . "</td>
					<td align='right'>" . number_format($bayt/$kont*100,2) . "</td>
					<td align='right'>" . number_format($disbt-$kont) . "</td>
					<td align='right'>" . number_format($kont-$bayt) . "</td>
					<td></td>
				</tr>";
			echo "</table>";

		}
		mysql_close($kon);
		
		//echo $hasil;
	?>
</html>
