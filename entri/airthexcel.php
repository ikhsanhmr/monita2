<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=aitahun.xls");

	session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	
	require_once "../config/koneksi.php";
	$sql = "SELECT DISTINCT YEAR(tanggalskki) tahun FROM skkiterbit";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	$p = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
	$s = isset($_REQUEST["s"])? $_REQUEST["s"]: "";

	$parm = ($p==""? "": "WHERE YEAR(tanggalskki) = $p");
	$parm .= ((empty($s)) ? "": ( ($s==1) ? " AND posinduk IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')" : " AND posinduk IN ('62.1','62.01')" ));
	
	$otherparm = ((empty($s)) ? " WHERE pos LIKE '62%' ": ( ($s==1) ? " WHERE pos IN ('62.2','62.3','62.4','62.5','62.6','62.7','62.8','62.9','62.10','62.02','62.03','62.04','62.05','62.06','62.07','62.08','62.09')" : " WHERE pos IN ('62.1','62.01')" ));
	
	if($v!="") {
			// $sql = "
			// SELECT 
				// pelaksana, namaunit, SUM(nilaianggaran) anggaran, SUM(nilaidisburse) disburse, SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar 
			// FROM (
				// SELECT noskk, pelaksana FROM notadinas_detail WHERE progress >=7 AND NOT noskk IS NULL GROUP BY noskk, pelaksana 
			// ) d INNER JOIN skkiterbit s ON d.noskk = s.nomorskki 
			// LEFT JOIN (
				// SELECT nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
				// FROM kontrak k LEFT JOIN (
					// SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak
				// ) r ON k.nomorkontrak = r.nokontrak WHERE COALESCE(pos, '') != '' GROUP BY nomorskkoi
			// ) kr ON s.nomorskki = kr.noskk 
			// LEFT JOIN bidang b ON d.pelaksana = b.id
			// $parm
			// GROUP BY pelaksana, namaunit
			// ORDER BY LPAD(pelaksana, 2, '0')";
			
			$sql = "
				SELECT 	pelaksana, namaunit, SUM(nilaianggaran) anggaran, SUM(nilaidisburse) disburse, 
						SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar, SUM(jml_rab) jmlrab, 
						SUM(nilai_rab) nrab, aki.rpaki, SUM(jml_kontrak) jmlkontrak
				FROM 	(
							SELECT 	noskk, pelaksana 
							FROM 	notadinas_detail 
							WHERE 	progress >=7 AND NOT noskk IS NULL 
							GROUP BY noskk, pelaksana 
						) d INNER JOIN 
						skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
						(
							SELECT 	nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar, 
									COUNT(nomorkontrak) jml_kontrak 
							FROM 	kontrak k LEFT JOIN 
									(
										SELECT 	nokontrak, SUM(nilaibayar) bayar 
										FROM 	realisasibayar 
										GROUP BY nokontrak
									) r ON k.nomorkontrak = r.nokontrak $otherparm 
							GROUP BY nomorskkoi
						) kr ON s.nomorskki = kr.noskk LEFT JOIN 
						(
							SELECT 	skk, SUM(nilai_rp) as nilai_rab, count(no_rab) as jml_rab
							FROM 	rab
							GROUP BY skk
						) rb ON d.noskk = rb.skk LEFT JOIN
						bidang b ON d.pelaksana = b.id LEFT JOIN
						saldoakibidang aki ON b.id = aki.kdbidang ".($p==""? "": " and aki.tahun = $p")."
				$parm
				GROUP BY pelaksana, namaunit, aki.rpaki
				ORDER BY LPAD(pelaksana, 2, '0')";

		//echo $sql;
		
		//$hasil = "
		echo "
		<table border='1'>
			<tr>
				<th rowspan='2' scope='col'>No</th>
				<th rowspan='2' scope='col'>Pelaksana</th>
				<th colspan='3' scope='col'>SKKI Terbit</th>
				<th colspan='3' scope='col'>RAB</th>
				<th colspan='4' scope='col'>Terkontrak</th>
				<th colspan='2' scope='col'>Terbayar</th>
				<th colspan='2' scope='col'>Sisa</th>
			</tr>
			<tr>
				<td align='center' style='background-color:rgb(127,255,127)'>Anggaran</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Disburse</td>
				<td align='center' style='background-color:rgb(127,255,127)'>AKI</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Jml</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
				<td align='center' style='background-color:rgb(127,255,127)'>%</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Jml</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
				<td align='center' style='background-color:rgb(127,255,127)'>%</td>
				<td align='center' style='background-color:rgb(127,255,127)'>%</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
				<td align='center' style='background-color:rgb(127,255,127)'>%</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Kontrak</td>
				<td align='center' style='background-color:rgb(127,255,127)'>Bayar</td>
			</tr>
			<tr>
				<td></td>
				<td align='center'>a</td>
				<td align='center'>b</td>
				<td align='center'>c</td>
				<td align='center'>d</td>
				<td align='center'>e</td>
				<td align='center'>f</td>
				<td align='center'>g=f/c</td>
				<td align='center'>h</td>
				<td align='center'>i</td>
				<td align='center'>j=i/c</td>
				<td align='center'>k=h/e</td>
				<td align='center'>l</td>
				<td align='center'>m=l/i</td>
				<td align='center'>n=c-i</td>
				<td align='center'>o=i-l</td>
			</tr>
		";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$no = 0;
		$a = 0;
		$d = 0;
		$k = 0;
		$b = 0;
		$aki = 0;
		$jmlrab = 0;
		$nrab = 0;
		$jmlkontrak = 0;
		while ($row = mysqli_fetch_array($result)) {
			$no++;
			$a += $row["anggaran"];
			$d += $row["disburse"];
			$k += $row["kontrak"];
			$b += $row["bayar"];
			$aki += $row["rpaki"];
			$jmlrab += $row["jmlrab"];
			$nrab += $row["nrab"];
			$jmlkontrak += $row["jmlkontrak"];
			
			echo "
				<tr>
					<td>$no</td>
					<td>$row[namaunit]</td>
					<td align='right'>" . number_format($row["anggaran"]) . "</td>
					<td align='right'>" . number_format($row["disburse"]) . "</td>
					<td align='right'>" . number_format($row["rpaki"]) . "</td>
					<td align='right'>" . number_format($row["jmlrab"]) . "</td>
					<td align='right'>" . number_format($row["nrab"]) . "</td>
					<td align='right'>" . number_format(@($row["nrab"]/$row["disburse"])*100,2) . "</td>
					<td align='right'>" . number_format($row["jmlkontrak"]) . "</td>
					<td align='right'>" . number_format($row["kontrak"]) . "</td>
					<td align='right'>" . number_format(@($row["kontrak"]/$row["disburse"])*100,2) . "</td>
					<td align='right'>" . number_format(@($row["jmlkontrak"]/$row["jmlrab"])*100,2) . "</td>
					<td align='right'>" . number_format($row["bayar"]) . "</td>
					<td align='right'>" . number_format(@($row["bayar"]/$row["kontrak"])*100,2) . "</td>
					<td align='right'>" . number_format($row["disburse"]-$row["kontrak"]) . "</td>
					<td align='right'>" . number_format($row["kontrak"]-$row["bayar"]) . "</td>
				</tr>";
		}
		mysqli_free_result($result);
		
		echo "
			<tr>
				<td colspan='2'>Total</td>
				<td align='right'>" . number_format($a) . "</td>
				<td align='right'>" . number_format($d) . "</td>
				<td align='right'>" . number_format($aki) . "</td>
				<td align='right'>" . number_format($jmlrab) . "</td>
				<td align='right'>" . number_format($nrab) . "</td>
				<td align='right'>" . number_format(($d == 0 ? 0 : ($nrab/$d*100)),2) . "</td>
				<td align='right'>" . number_format($jmlkontrak) . "</td>
				<td align='right'>" . number_format($k) . "</td>
				<td align='right'>" . number_format(($d == 0 ? 0 : ($k/$d*100)),2) . "</td>
				<td align='right'>" . number_format(($jmlrab == 0 ? 0 : ($jmlkontrak/$jmlrab*100)),2) . "</td>
				<td align='right'>" . number_format($b) . "</td>
				<td align='right'>" . number_format(($k == 0 ? 0 : ($b/$k*100)),2) . "</td>
				<td align='right'>" . number_format($d-$k) . "</td>
				<td align='right'>" . number_format($k-$b) . "</td>
			</tr>";
		
	}
	$mysqli->close();($kon);
	
	//echo $hasil;
?>
