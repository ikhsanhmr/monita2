<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(me) {
			var p1 = document.getElementById("p1").value;
			var p = document.getElementById("pelaksana").value;
			var url = encodeURI((me==undefined? "peta.php": "petaxl.php") + "?p1="+p1+"&p="+p+"&v=1");
			window.open(url, "_self"); 
		}		
	</script>
	
	<?php
		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysql_query($sql);
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysql_free_result($result);
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
		// $parm .= ($p1==""? "": " and year(tglskk) = $p1");

		$user = $_SESSION['cnip'];

		if ($user == "93162829ZY"){

			$parm .= ($p0==""? " AND d.pos1 IN (Select akses From akses_pos Where nip = '$user')": " and (pelaksana = '$p0' or d.pos1 IN (Select akses From akses_pos Where nip = '$user'))");

		}else{

			$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		}
		//$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		
		$t = "<select name='p1' id='p1'>";
		for($i=2015; $i<=date("Y"); $i++) {
			$t .= "<option value='$i'". ($i==$p1? " selected": "") .">$i</option>";
		}
		$t .= "</select>";
		
		echo "
			<h2>Peta Pagu</h2>
			<table>
				<tr>
					<th>Periode</th>
					<td>:</td>
					<td>$t</td>
				</tr>
				<tr>
					<th>Pelaksana</th>
					<td>:</td>
					<td>$p</td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='Ok' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>		
		";
		
		if($v!="") {
			echo "
			<table border='1'>
				<tr>
					<th rowspan='2' scope='col'>No</th>" . "" /*($p0==""? "": "<th rowspan='2' scope='col'>Pelaksana</th>")*/  . "
					<th rowspan='2' scope='col'>Kode Sub Pos</th>
					<th rowspan='2' scope='col'>Uraian SUb Pos</th>
					<th colspan='2' scope='col'>SKK</th>
					<th colspan='2' scope='col'>Terkontrak</th>
					<th colspan='2' scope='col'>Terbayar</th>
					<th colspan='2' scope='col'>Sisa</th>
				</tr>
				<tr>
					<td align='center' style='background-color:rgb(127,255,127)'></td>
					<td align='center' style='background-color:rgb(127,255,127)'>Disburse</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>%</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' style='background-color:rgb(127,255,127)'>%</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Kontrak</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Bayar</td>
				</tr>
				<tr>" . "" /*($p0==""? "": "<td></td>")*/  . "
					<td align='center'>a</td>
					<td align='center'>b</td>
					<td align='center'>c</td>
					<td align='center'>d</td>
					<td align='center'>e</td>
					<td align='center'>f</td>
					<td align='center'>g=f/e</td>
					<td align='center'>h</td>
					<td align='center'>i=h/f</td>
					<td align='center'>j=e-f</td>
					<td align='center'>k=f-h</td>
				</tr>";

			// $sql = "
			// 	SELECT 	pelaksana, namaunit, rppos, pos1 pos, namapos, SUM(COALESCE(nilai1,0)) nilai, 
			// 			SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar 
			// 	FROM 	notadinas_detail d LEFT JOIN 
			// 			bidang b ON d.pelaksana = b.id LEFT JOIN 
			// 			(
			// 				SELECT 	nomorskkoi noskk, pos, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
			// 				FROM 	kontrak k LEFT JOIN 
			// 						(
			// 							SELECT 	nokontrak, SUM(nilaibayar) bayar 
			// 							FROM 	realisasibayar 
			// 							GROUP BY nokontrak
			// 						) r ON k.nomorkontrak = r.nokontrak 
			// 				GROUP BY nomorskkoi, pos
			// 			) kr ON d.noskk = kr.noskk AND d.pos1 = kr.pos INNER JOIN 
			// 			(
			// 				SELECT 	nomorskko noskk, tanggalskko tglskk 
			// 				FROM 	skkoterbit 
			// 				WHERE	year(tanggalskko) = ".date("Y")."
			// 				UNION 
			// 				SELECT 	nomorskki noskk, tanggalskki tglskk 
			// 				FROM 	skkiterbit
			// 				WHERE	year(tanggalskki) = ".date("Y")."
			// 			)s ON d.noskk = s.noskk RIGHT JOIN (
			// 				SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			// 				SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			// 				SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			// 				SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			// 			) p ON d.pos1 = p.pos LEFT JOIN 
			// 			(
			// 				SELECT kdsubpos, rppos FROM saldopos WHERE tahun = $p1 UNION 
			// 				SELECT kdsubpos, rppos FROM saldopos2 WHERE tahun = $p1 UNION 
			// 				SELECT kdsubpos, rppos FROM saldopos3 WHERE tahun = $p1 UNION 
			// 				SELECT kdsubpos, rppos FROM saldopos4 WHERE tahun = $p1 
			// 			) np ON d.pos1 = np.kdsubpos
			// 	WHERE 	d.progress >= 7 AND NOT d.noskk IS NULL 
			// 	$parm
			// 	GROUP BY pos1, pelaksana
			// 	ORDER BY " . ($p0==""? "pos1, LPAD(pelaksana, 2, '0')": "LPAD(pelaksana, 2, '0'), pos1");

			$sql = "
				SELECT v.*, rppos, nilai, kontrak, bayar 
				FROM 	(
							SELECT 	pos1, SUM(nilai1) nilai 
							FROM 	notadinas_detail d LEFT JOIN 
									notadinas n ON d.nomornota = n.nomornota 
							WHERE YEAR(tanggal) = $p1 AND d.progress >= 7 $parm
							GROUP BY pos1
						) d	LEFT JOIN 
						(
							SELECT DISTINCT	akses, nama 
							FROM 	v_pos 
							UNION
							SELECT DISTINCT	akses, namasubpos as nama 
							FROM 	akses_pos ap inner join
									(
										SELECT kdindukpos as kdsubpos, namaindukpos as namasubpos FROM posinduk
										UNION 
										SELECT kdsubpos, namasubpos FROM posinduk2
										UNION 
										SELECT kdsubpos, namasubpos FROM posinduk3
										UNION 
										SELECT kdsubpos, namasubpos FROM posinduk4
									) subpos ON ap.akses = subpos.kdsubpos 
							ORDER BY akses
						) v ON d.pos1 = v.akses  LEFT JOIN 
						(
							SELECT kdsubpos, rppos FROM saldopos WHERE tahun = $p1
							UNION 
							SELECT kdsubpos, rppos FROM saldopos2 WHERE tahun = $p1
							UNION 
							SELECT kdsubpos, rppos FROM saldopos3 WHERE tahun = $p1
							UNION 
							SELECT kdsubpos, rppos FROM saldopos4 WHERE tahun = $p1
						) p ON v.akses = p.kdsubpos LEFT JOIN 
						(
							SELECT 	pos, SUM(nilaikontrak) kontrak
							FROM 	(
										SELECT DISTINCT noskk 
										FROM 	notadinas_detail d LEFT JOIN 
												notadinas n ON d.nomornota = n.nomornota 
										WHERE YEAR(tanggal) = $p1 AND d.progress >= 7 
										$parm
									) nd LEFT JOIN 
									kontrak k ON nd.noskk = k.nomorskkoi
							WHERE NOT nomorkontrak IS NULL
							GROUP BY pos 
						) k ON v.akses = k.pos LEFT JOIN 
						(
							SELECT 	pos, SUM(nilaibayar) bayar
							FROM 	(
										SELECT DISTINCT noskk 
										FROM 	notadinas_detail d LEFT JOIN 
												notadinas n ON d.nomornota = n.nomornota 
										WHERE YEAR(tanggal) = $p1 AND d.progress >= 7 
										$parm
									) nd LEFT JOIN 
									kontrak k ON nd.noskk = k.nomorskkoi LEFT JOIN 
									realisasibayar b ON k.nomorkontrak = b.nokontrak 
							WHERE NOT nomorkontrak IS NULL
							GROUP BY pos 
						) b ON v.akses = b.pos
				order by akses
			";
				
			// echo $sql;
			// return;
			//echo $parm;
			
			$no = 0;
			$a = 0;
			$a1 = 0;
			$d = 0;
			$d1 = 0;
			$k = 0;
			$k1 = 0;
			$b = 0;
			$b1 = 0;
			$dummy = "";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if($dummy!==$row["akses"]) {
					$no++;
					$dummy = $row["akses"];
					if($no>1) {
						echo "
							<td align='right'>" . number_format($d1) . "</td>
							<td align='right'>" . number_format($k1) . "</td>
							<td align='right'>" . number_format(@($k1/$d1)*100,2) . "</td>
							<td align='right'>" . number_format($b1) . "</td>
							<td align='right'>" . number_format(@($b1/$k1)*100,2) ."</td>
							<td align='right'>" . number_format($d1-$k1) . "</td>
							<td align='right'>" . number_format($k1-$b1) . "</td>
						</tr>";
						
						$d1 = 0;
						$k1 = 0;
						$b1 = 0;
						$a += $a1;
					}
					
					echo "
						<tr>
							<td>$no</td>
							<td>$row[akses]</td>
							<td>$row[nama]</td>
							<td align='right'>" . "" /*number_format($row["rppos"])*/ . "</td>
					";
/*							
							<td align='right'>" . number_format($row["nilai"]) . "</td>
							<td align='right'>" . number_format($row["kontrak"]) . "</td>
							<td align='right'>" . number_format($row["kontrak"]/$row["nilai"]*100,2) . "</td>
							<td align='right'>" . number_format($row["bayar"]) . "</td>
							<td align='right'>" . number_format($row["bayar"]/$row["kontrak"]*100,2) . "</td>
							<td align='right'>" . number_format($row["nilai"]-$row["kontrak"]) . "</td>
							<td align='right'>" . number_format($row["kontrak"]-$row["bayar"]) . "</td>
						</tr>";
*/					
				}
				
				$a1 = $row["rppos"];
				$d += $row["nilai"];
				$d1 += $row["nilai"];
				$k += $row["kontrak"];
				$k1 += $row["kontrak"];
				$b += $row["bayar"];
				$b1 += $row["bayar"];
/*
				echo "
					<tr>
						<td>$no</td>
						<td>$row[pos]</td>
						<td>$row[namapos]</td>
						<td></td>
						<td align='right'>" . number_format($row["nilai"]) . "</td>
						<td align='right'>" . number_format($row["kontrak"]) . "</td>
						<td align='right'>" . number_format($row["kontrak"]/$row["nilai"]*100,2) . "</td>
						<td align='right'>" . number_format($row["bayar"]) . "</td>
						<td align='right'>" . number_format($row["bayar"]/$row["kontrak"]*100,2) . "</td>
						<td align='right'>" . number_format($row["nilai"]-$row["kontrak"]) . "</td>
						<td align='right'>" . number_format($row["kontrak"]-$row["bayar"]) . "</td>
					</tr>";
*/
			}
			mysql_free_result($result);
			
			$a += $a1;
			echo "
					<td align='right'>" . number_format($d1) . "</td>
					<td align='right'>" . number_format($k1) . "</td>
					<td align='right'>" . number_format(($d1 == 0 ? 0 : $k1/$d1*100),2) . "</td>
					<td align='right'>" . number_format($b1) . "</td>
					<td align='right'>" . number_format(($k1 == 0 ? 0 : $b1/$k1*100),2) . "</td>
					<td align='right'>" . number_format($d1-$k1) . "</td>
					<td align='right'>" . number_format($k1-$b1) . "</td>
				</tr>
				<tr>
					<td colspan='3'>Total</td>
					<td align='right'>" . "" /*number_format($a)*/ . "</td>
					<td align='right'>" . number_format($d) . "</td>
					<td align='right'>" . number_format($k) . "</td>
					<td align='right'>" . number_format($k/$d*100,2) . "</td>
					<td align='right'>" . number_format($b) . "</td>
					<td align='right'>" . number_format($b/$k*100,2) . "</td>
					<td align='right'>" . number_format($d-$k) . "</td>
					<td align='right'>" . number_format($k-$b) . "</td>
				</tr>
			</table>";
		}
		mysql_close($kon);
	?>
</html>