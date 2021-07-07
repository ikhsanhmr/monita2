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
			//var kpos = document.getElementById("kdpos").value;
			//alert(kpos);
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI((x==undefined? "aiskk.php": "aiskkexcel.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&v=1");
			//alert(url);
			window.open(url, "_self");
		}
	</script>
	
	<?php
//		header("Content-type: application/vnd.ms-excell");
//		header("Content-Disposition: attachment; Filename=ao.xls");

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
		//$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		while ($row = mysqli_fetch_array($result)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysqli_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
/*		
		$sql = "
			SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			ORDER BY pos";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$kdpos = "<select name='kdpos' id='kdpos'><option value=''></option>";
		while ($row = mysqli_fetch_array($result)) {
			$kdpos .= "<option value='$row[pos]' " . ($row["pos"]==$kdpos0? "selected": "") . ">$row[pos] - $row[namapos]</option>";
		}
		$kdpos .= "</select>";
		mysqli_free_result($result);
*/
	?>
</head>


<body>
	<?php
		$parm = "";
//		$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//		$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
		$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));		
		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and nomorskki = '$k0'");
//		$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
		//echo "parm : $parm<br>";
		echo "
			<h2>Rekap Monitoring Penyerapan Anggaran Investasi Per Nomor SKK</h2>
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
				</tr>" . 
/*
				<tr>
					<th>POS</th>
					<td>:</td>
					<td>$kdpos</td>
				</tr>
*/
				"<tr>
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
					pelaksana, b.namaunit, /* pos1, nilai1, namapos, */
					nomorscore, nomorwbs, nomorprk, nomorskki noskk, s.uraian uraians, DATE_FORMAT(tanggalskki, '%d-%m-%Y') as tanggalskki, DATEDIFF(SYSDATE(), tanggalskki) umur, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
					COALESCE(kontrak,0) kontrak, COALESCE(bayar,0) bayar, s.gd gd, s.nilaianggarangd nilaianggarangd, s.jtm jtm, s.nilaianggaranjtm nilaianggaranjtm, s.jtr jtr, s.nilaianggaranjtr nilaianggaranjtr, s.sl1 sl1, s.nilaianggaransl1 nilaianggaransl1, s.sl3 sl3, s.nilaianggaransl3 nilaianggaransl3, s.keypoint keypoint, s.nilaianggarankp nilaianggarankp
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
					<th colspan='23' scope='col'>SKKI Terbit</th>
					<th rowspan='3' scope='col'>Nilai Kontrak (Rp.)</th>
					<th rowspan='3' scope='col'>% Kontrak</th>
					<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
					<th rowspan='3' scope='col'>% Bayar</th>
					<th colspan='2' scope='col'>Sisa</th>
					<th rowspan='3' scope='col'>Keterangan</th>
				</tr>
				<tr>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>PRK</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Basket / Fungsi</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Umur SKKI</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>GARDU</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>JTM</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>JTR</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>SL1</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>SL3</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>KeyPoint</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKI (Disburse - Kontrak)</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
				</tr>
				<tr>
					<td style='background-color:rgb(127,255,127)'>Unit</td>
					<td style='background-color:rgb(127,255,127)'>Rp.</td>
					<td style='background-color:rgb(127,255,127)'>Kms</td>
					<td style='background-color:rgb(127,255,127)'>Rp.</td>
					<td style='background-color:rgb(127,255,127)'>Kms</td>
					<td style='background-color:rgb(127,255,127)'>Rp.</td>
					<td style='background-color:rgb(127,255,127)'>Plgn</td>
					<td style='background-color:rgb(127,255,127)'>Rp.</td>
					<td style='background-color:rgb(127,255,127)'>Plgn</td>
					<td style='background-color:rgb(127,255,127)'>Rp.</td>
					<td style='background-color:rgb(127,255,127)'>Plgn</td>
					<td style='background-color:rgb(127,255,127)'>Rp.</td>	
					<td style='background-color:rgb(127,255,127)'>Anggaran (Rp.)</td>
					<td style='background-color:rgb(127,255,127)'>Disburse (Rp.)</td>
				</tr>
			";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			$no = 0;
			$dummy = "";
			$a = 0;
			$d = 0;
			$w = 0;
			$k = 0;
			$b = 0;

			$gd = 0;
			$jtm = 0;
			$jtr = 0;
			$sl1 = 0;
			$sl3 = 0;

			$anggd = 0;
			$angjtm = 0;
			$angjtr = 0;
			$angsl1 = 0;
			$angsl3 = 0;
			
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				
				$a += $row["anggaran"];
				$d += $row["disburse"];
				$w += $row["wbs"];
				$k += $row["kontrak"];
				$b += $row["bayar"];

				$gd += $row["gd"];
				$jtm += $row["jtm"];
				$jtr += $row["jtr"];
				$sl1 += $row["sl1"];
				$sl3 += $row["sl3"];
				$kp += $row["keypoint"];

				$anggd += $row["nilaianggarangd"];
				$angjtm += $row["nilaianggaranjtm"];
				$angjtr += $row["nilaianggaranjtr"];
				$angsl1 += $row["nilaianggaransl1"];
				$angsl3 += $row["nilaianggaransl3"];
				$angkp += $row["nilaianggarankp"];
				
				echo "
					<tr>
						<td>$no</td>
						<td>$row[nomorprk]</td>
						<td>$row[nomorscore]</td>
						<td>$row[nomorwbs]</td>
						<td>$row[noskk]</td>
						<td>$row[uraians]</td>
						<td>$row[tanggalskki]</td>
						<td bgcolor='" . ($row["kontrak"]>0? "green": ($row["umur"]>90? "red": "yellow")) . "'>$row[umur]</td>
						<td align='right'>".$row["gd"]."</td>
						<td align='right'>".$row["nilaianggarangd"]."</td>
						<td align='right'>".$row["jtm"]."</td>
						<td align='right'>".$row["nilaianggaranjtm"]."</td>
						<td align='right'>".$row["jtr"]."</td>
						<td align='right'>".$row["nilaianggaranjtr"]."</td>
						<td align='right'>".$row["sl1"]."</td>
						<td align='right'>".$row["nilaianggaransl1"]."</td>
						<td align='right'>".$row["sl3"]."</td>
						<td align='right'>".$row["nilaianggaransl3"]."</td>
						<td align='right'>".$row["keypoint"]."</td>
						<td align='right'>".$row["nilaianggarankp"]."</td>
						<td align='right'>".number_format($row["anggaran"])."</td>
						<td align='right'>".number_format($row["disburse"])."</td>
						<td>$row[namaunit]</td>
						<td>$row[wbs]</td>
						<td align='right'>".number_format($row["kontrak"])."</td>
						<td align='right'>".number_format(@($row["kontrak"]/$row["disburse"])*100,2)."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td align='right'>".number_format(@($row["bayar"]/$row["kontrak"])*100,2)."</td>
						<td align='right'>".number_format($row["disburse"]-$row["kontrak"])."</td>
						<td align='right'>".number_format($row["kontrak"]-$row["bayar"])."</td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				";
			}
			
			mysqli_free_result($result);		
			echo "
				<tr>
					<td colspan='8'>Total</td>
					<td align='right'>" . number_format($gd) . "</td>
					<td align='right'>" . number_format($anggd) . "</td>
					<td align='right'>" . number_format($jtm) . "</td>
					<td align='right'>" . number_format($angjtm) . "</td>
					<td align='right'>" . number_format($jtr) . "</td>
					<td align='right'>" . number_format($angjtr) . "</td>
					<td align='right'>" . number_format($sl1) . "</td>
					<td align='right'>" . number_format($angsl1) . "</td>
					<td align='right'>" . number_format($sl3) . "</td>
					<td align='right'>" . number_format($angsl3) . "</td>
					<td align='right'>" . number_format($kp) . "</td>
					<td align='right'>" . number_format($angkp) . "</td>
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
					<td colspan='3'></td>
				</tr>";
			echo "</table>";
		}
		$mysqli->close();($kon);
		
		//echo $hasil;
	?>
</html>
