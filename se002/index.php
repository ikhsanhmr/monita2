<?php error_reporting(0); ?>
<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(x) {
			var p = document.getElementById("th").value;
			var url = encodeURI(x==undefined? "airth.php": "airthexcel.php") + "?p="+p+"&v=1";
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
		
		require_once "../config/koneksi.php";
		$sql = "SELECT DISTINCT YEAR(tanggalskki) tahun FROM skkiterbit";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		$p = isset($_REQUEST["p"])? $_REQUEST["p"]: "";

		$th = "<select name='th' id='th'><option value=''></option>";
		while ($row = mysqli_fetch_array($result)) {
			$th .= "<option value='$row[tahun]'" . ($row["tahun"]==$p? " selected": "") . ">$row[tahun]</option>";
		}
		$th .= "</select>";
		mysqli_free_result($result);
	?>
</head>


<body>
	<?php
		echo "
			<h2>Rekap Laporan SE002</h2>
			";
		
				$sql = "
				SELECT 
					pelaksana, namaunit, SUM(nilaianggaran) anggaran, SUM(nilaidisburse) disburse, SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar , 
					SUM(IF( fungsi = 'Efisiensi' AND nip='$_SESSION[nip]', fungsi_val, 0)) AS efisiensi
				FROM (
					SELECT noskk, pelaksana FROM notadinas_detail WHERE progress >=7 AND NOT noskk IS NULL GROUP BY noskk, pelaksana 
				) d INNER JOIN skkiterbit s ON d.noskk = s.nomorskki 
				LEFT JOIN (
					SELECT nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
					FROM kontrak k LEFT JOIN (
						SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak
					) r ON k.nomorkontrak = r.nokontrak WHERE COALESCE(pos, '') != '' GROUP BY nomorskkoi
				) kr ON s.nomorskki = kr.noskk 
				LEFT JOIN bidang b ON d.pelaksana = b.id
				WHERE b.id>=6
				GROUP BY pelaksana, namaunit
				ORDER BY LPAD(pelaksana, 2, '0')";

			//echo $sql;
			
			//$hasil = "
			echo "
			<table border='1'>
				<tr>
					<th rowspan='2' scope='col'>No</th>
					<th rowspan='2' scope='col'>Pelaksana</th>
					<th rowspan='2' scope='col'>Uraian Nilai</th>
					<th colspan='6' scope='col'>Fungsi</th>
				</tr>
				<tr>
					<td align='center' style='background-color:rgb(127,255,127)'>Peningkatan Keandalan Jaringan</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Efisiensi</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Pemasaran</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Sarana Penunjang Fungsi</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Sarana</td>
					<td align='center' style='background-color:rgb(127,255,127)'>PLTD</td>
				</tr>
			";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			$no = 0;
			$a = 0;
			$d = 0;
			$k = 0;
			$b = 0;
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				$a += $row["anggaran"];
				$d += $row["disburse"];
				$k += $row["kontrak"];
				$b += $row["bayar"];

				$fungsi=array("Anggaran","Pek. Dalam Pelaksanaan","Pek. Selesai","Presentase","JTM (Kms)","JTR (Kms)","Gardu(Unit)");
				
				echo "
					<tr>
						<td>$no</td>
						<td>$row[namaunit]</td>";
				$query=mysqli_query("SELECT 
					pelaksana, namaunit, s.fungsi fungsi, s.fungsi_val fungsi_val, s.nilaidisburse nilaidisburse, 
					SUM(IF( fungsi = 'Peningkatan Keandalan Jaringan', nilaidisburse, 0)) AS keandalanjaringan_a,
					SUM(IF( fungsi = 'Efisiensi', nilaidisburse, 0)) AS efisiensi_a,
					SUM(IF( fungsi = 'Pemasaran', nilaidisburse, 0)) AS pemasaran_a,
					SUM(IF( fungsi = 'Sarana Penunjang Fungsi', nilaidisburse, 0)) AS penunjangfungsi_a,
					SUM(IF( fungsi = 'Sarana', nilaidisburse, 0)) AS sarana_a,
					SUM(IF( fungsi = 'PLTD', nilaidisburse, 0)) AS pltd_a,

					SUM(IF( fungsi = 'Peningkatan Keandalan Jaringan', kontrak, 0)) AS keandalanjaringan_pdp,
					SUM(IF( fungsi = 'Efisiensi', kontrak, 0)) AS efisiensi_pdp,
					SUM(IF( fungsi = 'Pemasaran', kontrak, 0)) AS pemasaran_pdp,
					SUM(IF( fungsi = 'Sarana Penunjang Fungsi', kontrak, 0)) AS penunjangfungsi_pdp,
					SUM(IF( fungsi = 'Sarana', kontrak, 0)) AS sarana_pdp,
					SUM(IF( fungsi = 'PLTD', kontrak, 0)) AS pltd_pdp,

					SUM(IF( fungsi = 'Peningkatan Keandalan Jaringan', bayar, 0)) AS keandalanjaringan_ps,
					SUM(IF( fungsi = 'Efisiensi', bayar, 0)) AS efisiensi_ps,
					SUM(IF( fungsi = 'Pemasaran', bayar, 0)) AS pemasaran_ps,
					SUM(IF( fungsi = 'Sarana Penunjang Fungsi', bayar, 0)) AS penunjangfungsi_ps,
					SUM(IF( fungsi = 'Sarana', bayar, 0)) AS sarana_ps,
					SUM(IF( fungsi = 'PLTD', bayar, 0)) AS pltd_ps,

					SUM(IF( fungsi = 'Peningkatan Keandalan Jaringan', jtm, 0)) AS keandalanjaringan_jtm,
					SUM(IF( fungsi = 'Efisiensi', jtm, 0)) AS efisiensi_jtm,
					SUM(IF( fungsi = 'Pemasaran', jtm, 0)) AS pemasaran_jtm,
					SUM(IF( fungsi = 'Sarana Penunjang Fungsi', jtm, 0)) AS penunjangfungsi_jtm,
					SUM(IF( fungsi = 'Sarana', jtm, 0)) AS sarana_jtm,
					SUM(IF( fungsi = 'PLTD', jtm, 0)) AS pltd_jtm,

					SUM(IF( fungsi = 'Peningkatan Keandalan Jaringan', jtr, 0)) AS keandalanjaringan_jtr,
					SUM(IF( fungsi = 'Efisiensi', jtr, 0)) AS efisiensi_jtr,
					SUM(IF( fungsi = 'Pemasaran', jtr, 0)) AS pemasaran_jtr,
					SUM(IF( fungsi = 'Sarana Penunjang Fungsi', jtr, 0)) AS penunjangfungsi_jtr,
					SUM(IF( fungsi = 'Sarana', jtr, 0)) AS sarana_jtr,
					SUM(IF( fungsi = 'PLTD', jtr, 0)) AS pltd_jtr,

					SUM(IF( fungsi = 'Peningkatan Keandalan Jaringan', gd, 0)) AS keandalanjaringan_gd,
					SUM(IF( fungsi = 'Efisiensi', gd, 0)) AS efisiensi_gd,
					SUM(IF( fungsi = 'Pemasaran', gd, 0)) AS pemasaran_gd,
					SUM(IF( fungsi = 'Sarana Penunjang Fungsi', gd, 0)) AS penunjangfungsi_gd,
					SUM(IF( fungsi = 'Sarana', gd, 0)) AS sarana_gd,
					SUM(IF( fungsi = 'PLTD', gd, 0)) AS pltd_gd
				FROM (
					SELECT noskk, pelaksana FROM notadinas_detail WHERE progress >=7 AND NOT noskk IS NULL GROUP BY noskk, pelaksana 
				) d INNER JOIN skkiterbit s ON d.noskk = s.nomorskki 
				LEFT JOIN (
					SELECT nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
					FROM kontrak k LEFT JOIN (
						SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak
					) r ON k.nomorkontrak = r.nokontrak WHERE COALESCE(pos, '') != '' GROUP BY nomorskkoi
				) kr ON s.nomorskki = kr.noskk 
				LEFT JOIN bidang b ON d.pelaksana = b.id 
				WHERE pelaksana='$row[pelaksana]' 
				GROUP BY pelaksana, namaunit
				ORDER BY LPAD(pelaksana, 2, '0')
				");
				$exe=mysql_fetch_assoc($query);
							if ($key!=0) {
								echo "<tr><td colspan='2'></td>";
							}
							echo "
								<td align='right'>Anggaran</td>
								<td align='right'>" . number_format($exe["keandalanjaringan_a"]) . "</td>
								<td align='right'>" . number_format($exe["efisiensi_a"]) . "</td>
								<td align='right'>" . number_format($exe["pemasaran_a"]) . "</td>
								<td align='right'>" . number_format($exe["penunjangfungsi_a"]) . "</td>
								<td align='right'>" . number_format($exe["sarana_a"]) . "</td>
								<td align='right'>" . number_format($exe["pltd_a"]) . "</td>
							</tr>";

							echo "<tr><td colspan='2'></td>
								<td align='right'>Pek. Dalam Pelaksanaan</td>
								<td align='right'>" . number_format($exe["keandalanjaringan_pdp"]) . "</td>
								<td align='right'>" . number_format($exe["efisiensi_pdp"]) . "</td>
								<td align='right'>" . number_format($exe["pemasaran_pdp"]) . "</td>
								<td align='right'>" . number_format($exe["penunjangfungsi_pdp"]) . "</td>
								<td align='right'>" . number_format($exe["sarana_pdp"]) . "</td>
								<td align='right'>" . number_format($exe["pltd_pdp"]) . "</td>
							</tr>";

							echo "<tr><td colspan='2'></td>
								<td align='right'>Pek. Selesai</td>
								<td align='right'>" . number_format($exe["keandalanjaringan_ps"]) . "</td>
								<td align='right'>" . number_format($exe["efisiensi_ps"]) . "</td>
								<td align='right'>" . number_format($exe["pemasaran_ps"]) . "</td>
								<td align='right'>" . number_format($exe["penunjangfungsi_ps"]) . "</td>
								<td align='right'>" . number_format($exe["sarana_ps"]) . "</td>
								<td align='right'>" . number_format($exe["pltd_ps"]) . "</td>
							</tr>";

							echo "<tr><td colspan='2'></td>
								<td align='right'>JTM (Kms)</td>
								<td align='right'>" . number_format($exe["keandalanjaringan_jtm"]) . "</td>
								<td align='right'>" . number_format($exe["efisiensi_jtm"]) . "</td>
								<td align='right'>" . number_format($exe["pemasaran_jtm"]) . "</td>
								<td align='right'>" . number_format($exe["penunjangfungsi_jtm"]) . "</td>
								<td align='right'>" . number_format($exe["sarana_jtm"]) . "</td>
								<td align='right'>" . number_format($exe["pltd_jtm"]) . "</td>
							</tr>";

							echo "<tr><td colspan='2'></td>
								<td align='right'>JTR (Kms)</td>
								<td align='right'>" . number_format($exe["keandalanjaringan_jtr"]) . "</td>
								<td align='right'>" . number_format($exe["efisiensi_jtr"]) . "</td>
								<td align='right'>" . number_format($exe["pemasaran_jtr"]) . "</td>
								<td align='right'>" . number_format($exe["penunjangfungsi_jtr"]) . "</td>
								<td align='right'>" . number_format($exe["sarana_jtr"]) . "</td>
								<td align='right'>" . number_format($exe["pltd_jtr"]) . "</td>
							</tr>";

							echo "<tr><td colspan='2'></td>
								<td align='right'>Gardu(Unit)</td>
								<td align='right'>" . number_format($exe["keandalanjaringan_gd"]) . "</td>
								<td align='right'>" . number_format($exe["efisiensi_gd"]) . "</td>
								<td align='right'>" . number_format($exe["pemasaran_gd"]) . "</td>
								<td align='right'>" . number_format($exe["penunjangfungsi_gd"]) . "</td>
								<td align='right'>" . number_format($exe["sarana_gd"]) . "</td>
								<td align='right'>" . number_format($exe["pltd_gd"]) . "</td>
							</tr>";


						
			}
			mysqli_free_result($result);
			
			// echo "
			// 	<tr>
			// 		<td colspan='2'>Total</td>
			// 		<td align='right'>" . number_format($a) . "</td>
			// 		<td align='right'>" . number_format($d) . "</td>
			// 		<td align='right'>" . number_format($k) . "</td>
			// 		<td align='right'>" . number_format($k/$d*100,2) . "</td>
			// 		<td align='right'>" . number_format($b) . "</td>
			// 		<td align='right'>" . number_format($b/$k*100,2) . "</td>
			// 		<td align='right'>" . number_format($d-$k) . "</td>
			// 		<td align='right'>" . number_format($k-$b) . "</td>
			// 	</tr>";
			
		$mysqli->close();($kon);
		
		//echo $hasil;
	?>
</html>
