<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(x) {
			var p = document.getElementById("th").value;
			var url = encodeURI(x==undefined? "aorth.php": "aorthexcel.php") + "?p="+p+"&v=1";
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
		
		require_once "../config/koneksi.php";
		$sql = "SELECT DISTINCT YEAR(tanggalskko) tahun FROM skkoterbit";
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
			<h2>Rekap Realisasi SKKO - Tahun</h2>
			<table>
				<tr>
					<th>Periode</th>
					<td>:</td>
					<td>$th</td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='View' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>";
		
		$parm = ($p==""? "": "WHERE YEAR(tanggalskko) = $p");
		if($v!="") {
			$sql = "
				SELECT 
					pelaksana, namaunit, SUM(nilaianggaran) anggaran, SUM(nilaidisburse) disburse, SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar 
				FROM (
					SELECT noskk, pelaksana FROM notadinas_detail WHERE progress >=7 and NOT noskk IS NULL GROUP BY noskk, pelaksana 
				) d INNER JOIN skkoterbit s ON d.noskk = s.nomorskko 
				LEFT JOIN (
					SELECT nomorskkoi noskk, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
					FROM kontrak k LEFT JOIN (
						SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak
					) r ON k.nomorkontrak = r.nokontrak WHERE COALESCE(pos, '') != '' GROUP BY nomorskkoi
				) kr ON s.nomorskko = kr.noskk 
				LEFT JOIN bidang b ON d.pelaksana = b.id
				$parm
				GROUP BY pelaksana, namaunit
				ORDER BY LPAD(pelaksana, 2, '0')";
			//echo $sql;
			
			//$hasil = "
			echo "
			<table border='1'>
				<tr>
					<th rowspan='2' scope='col'>No</th>
					<th rowspan='2' scope='col'>Pelaksana</th>
					<th colspan='2' scope='col'>SKKO Terbit</th>
					<th colspan='2' scope='col'>Terkontrak</th>
					<th colspan='2' scope='col'>Terbayar</th>
					<th colspan='2' scope='col'>Sisa</th>
				</tr>
				<tr>
					<td align='center' style='background-color:rgb(127,255,127)'>Anggaran</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Disburse</td>
					<td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
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
					<td align='center'>e=d/c</td>
					<td align='center'>f</td>
					<td align='center'>g=f/d</td>
					<td align='center'>h=c-d</td>
					<td align='center'>i=d-f</td>
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
				
				echo "
					<tr>
						<td>$no</td>
						<td>$row[namaunit]</td>
						<td align='right'>" . number_format($row["anggaran"]) . "</td>
						<td align='right'>" . number_format($row["disburse"]) . "</td>
						<td align='right'>" . number_format($row["kontrak"]) . "</td>
						<td align='right'>" . number_format(@($row["kontrak"]/$row["disburse"])*100,2) . "</td>
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
					<td align='right'>" . number_format($k) . "</td>
					<td align='right'>" . number_format($k/$d*100,2) . "</td>
					<td align='right'>" . number_format($b) . "</td>
					<td align='right'>" . number_format($b/$k*100,2) . "</td>
					<td align='right'>" . number_format($d-$k) . "</td>
					<td align='right'>" . number_format($k-$b) . "</td>
				</tr>";
			
		}
		$mysqli->close();($kon);
		
		//echo $hasil;
	?>
</html>
