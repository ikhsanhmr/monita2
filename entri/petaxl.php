<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=pagu.xls");

	error_reporting(0);  session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	
	require_once "../config/koneksi.php";
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
	$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	$p = "";
	while ($row = mysqli_fetch_array($result)) {
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysqli_free_result($result);

	$parm = "";
	// $parm .= ($p1==""? "": " and year(tglskk) = $p1");
	// $parm .= ($p0==""? "": " and pelaksana = '$p0'");

	$user = $_SESSION['cnip'];

	if ($user == "93162829ZY"){

		$parm .= ($p0==""? " AND d.pos1 IN (Select akses From akses_pos Where nip = '$user')": " and (pelaksana = '$p0' or d.pos1 IN (Select akses From akses_pos Where nip = '$user'))");

	}else{

		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	}
	
	echo "
		<h2>Peta Pagu</h2>
		<table>
			<tr>
				<th>Periode</th>
				<td>:</td>
				<td>$p1</td>
			</tr>
			<tr>
				<th>Pelaksana</th>
				<td>:</td>
				<td>$p</td>
			</tr>
			<tr>
				<td colspan='3' align='right'>".
				date("d-m-Y H:i:s")
				."</td>
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
		// 	SELECT 
		// 		pelaksana, namaunit, rppos, pos1 pos, namapos, 
		// 		SUM(COALESCE(nilai1,0)) nilai, SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar 
		// 	FROM notadinas_detail d 
		// 	LEFT JOIN bidang b ON d.pelaksana = b.id
		// 	LEFT JOIN (
		// 		SELECT nomorskkoi noskk, pos, SUM(nilaikontrak) kontrak, SUM(bayar) bayar FROM kontrak k
		// 		LEFT JOIN (
		// 			SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak
		// 		) r ON k.nomorkontrak = r.nokontrak GROUP BY nomorskkoi, pos
		// 	) kr ON d.noskk = kr.noskk AND d.pos1 = kr.pos
		// 	INNER JOIN (
		// 		SELECT nomorskko noskk, tanggalskko tglskk FROM skkoterbit UNION 
		// 		SELECT nomorskki noskk, tanggalskki tglskk FROM skkiterbit
		// 	)s ON d.noskk = s.noskk
		// 	RIGHT JOIN (
		// 		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
		// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
		// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
		// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
		// 	) p ON d.pos1 = p.pos
		// 	LEFT JOIN (
		// 		SELECT kdsubpos, rppos FROM saldopos WHERE tahun = $p1 UNION 
		// 		SELECT kdsubpos, rppos FROM saldopos2 WHERE tahun = $p1 UNION 
		// 		SELECT kdsubpos, rppos FROM saldopos3 WHERE tahun = $p1 UNION 
		// 		SELECT kdsubpos, rppos FROM saldopos4 WHERE tahun = $p1 
		// 	) np ON d.pos1 = np.kdsubpos
		// 	WHERE d.progress >= 7 AND NOT d.noskk IS NULL 
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
			
		//echo $sql;
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
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {
			if($dummy!=$row["akses"]) {
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
						</tr>
					";
					
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
		mysqli_free_result($result);
		
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
			</table>
		";
	}
	$mysqli->close();($kon);
?>