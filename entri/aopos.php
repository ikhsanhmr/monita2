<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(x) {
			var p = document.getElementById("th").value;
			var url = encodeURI(x==undefined? "aopos.php": "aoposexcel.php") + "?p="+p+"&v=1";
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
			<h2>Rekap Realisasi SKKO SUB POS- Tahun</h2>
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
		
		$parm = ($p==""? "": "and YEAR(tanggalskko) = $p");
		$parmkontrak = ($p==""? "": "WHERE YEAR(inputdt) = $p");

		if($v!="") {
			$sql = "
				SELECT 	pelaksana, namaunit, pos1 pos, namapos, SUM(COALESCE(nilai1,0)) nilai, 
						SUM(COALESCE(kontrak,0)) kontrak, SUM(COALESCE(bayar,0)) bayar 
				FROM 	notadinas_detail d LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						(
							SELECT 	nomorskkoi noskk, pos, SUM(nilaikontrak) kontrak, SUM(bayar) bayar 
							FROM 	kontrak k LEFT JOIN 
									(
										SELECT 	nokontrak, SUM(nilaibayar) bayar 
										FROM 	realisasibayar
										GROUP BY nokontrak
									) r ON k.nomorkontrak = r.nokontrak 
							$parmkontrak
							GROUP BY nomorskkoi, pos
						) kr ON d.noskk = kr.noskk AND d.pos1 = kr.pos INNER JOIN 
						skkoterbit s ON d.noskk = s.nomorskko LEFT JOIN 
						(
							SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos
				WHERE d.progress >= 7 AND NOT d.noskk IS NULL 
				$parm
				GROUP BY pos1, pelaksana
				ORDER BY LPAD(pelaksana, 2, '0'), pos1";
			// echo $sql;
			
			//$hasil = "
			echo "
			<table border='1'>
				<tr>
					<th rowspan='2' scope='col'>No</th>
					<th rowspan='2' scope='col'>Pelaksana</th>
					<th rowspan='2' scope='col'>Kode Sub Pos</th>
					<th rowspan='2' scope='col'>Uraian SUb Pos</th>
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
					<td align='center'>e</td>
					<td align='center'>f</td>
					<td align='center'>g=f/e</td>
					<td align='center'>h</td>
					<td align='center'>i=h/f</td>
					<td align='center'>j=e-f</td>
					<td align='center'>k=f-h</td>
				</tr>
			";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			$no = 0;
			$d = 0;
			$d1 = 0;
			$k = 0;
			$k1 = 0;
			$b = 0;
			$b1 = 0;
			
			$dummy = "";
			while ($row = mysqli_fetch_array($result)) {
				$show = false;
				if($dummy != $row["pelaksana"]) {
					if($no>0) {
						echo "
							<tr>
								<td bgcolor='lightgreen'></td>
								<td bgcolor='lightgreen'>Sub Total</td>
								<td bgcolor='lightgreen'></td>
								<td bgcolor='lightgreen'></td>
								<td bgcolor='lightgreen'></td>
								<td align='right' bgcolor='lightgreen'>" . number_format($d1) . "</td>
								<td align='right' bgcolor='lightgreen'>" . number_format($k1) . "</td>
								<td align='right' bgcolor='lightgreen'>" . number_format(@($k1/$d1)*100,2) . "</td>
								<td align='right' bgcolor='lightgreen'>" . number_format($b1) . "</td>
								<td align='right' bgcolor='lightgreen'>" . number_format(@($b1/$k1)*100,2) . "</td>
								<td align='right' bgcolor='lightgreen'>" . number_format($d1-$k1) . "</td>
								<td align='right' bgcolor='lightgreen'>" . number_format($k1-$b1) . "</td>
							</tr>";
						$d1 = 0;
						$k1 = 0;
						$b1 = 0;
					}
					
					$show = true;
					$dummy = $row["pelaksana"];
					$no++;
				}

				$d += $row["nilai"];
				$d1 += $row["nilai"];
				$k += $row["kontrak"];
				$k1 += $row["kontrak"];
				$b += $row["bayar"];
				$b1 += $row["bayar"];
				
				echo "
					<tr>
						<td>" . ($show? $no: "") . "</td>
						<td>" . ($show? $row["namaunit"]: "") . "</td>
						<td>$row[pos]</td>
						<td>$row[namapos]</td>
						<td></td>
						<td align='right'>" . number_format($row["nilai"]) . "</td>
						<td align='right'>" . number_format($row["kontrak"]) . "</td>
						<td align='right'>" . number_format(@($row["kontrak"]/$row["nilai"])*100,2) . "</td>
						<td align='right'>" . number_format($row["bayar"]) . "</td>
						<td align='right'>" . number_format(@($row["bayar"]/$row["kontrak"])*100,2) . "</td>
						<td align='right'>" . number_format($row["nilai"]-$row["kontrak"]) . "</td>
						<td align='right'>" . number_format($row["kontrak"]-$row["bayar"]) . "</td>
					</tr>";
			}
			mysqli_free_result($result);
			
			echo "
				<tr>
					<td bgcolor='lightgreen'></td>
					<td bgcolor='lightgreen'>Sub Total</td>
					<td bgcolor='lightgreen'></td>
					<td bgcolor='lightgreen'></td>
					<td bgcolor='lightgreen'></td>
					<td align='right' bgcolor='lightgreen'>" . number_format($d1) . "</td>
					<td align='right' bgcolor='lightgreen'>" . number_format($k1) . "</td>
					<td align='right' bgcolor='lightgreen'>" . number_format(@($k1/$d1)*100,2) . "</td>
					<td align='right' bgcolor='lightgreen'>" . number_format($b1) . "</td>
					<td align='right' bgcolor='lightgreen'>" . number_format(@($b1/$k1)*100,2) . "</td>
					<td align='right' bgcolor='lightgreen'>" . number_format($d1-$k1) . "</td>
					<td align='right' bgcolor='lightgreen'>" . number_format($k1-$b1) . "</td>
				</tr>
				<tr>
					<td></td>
					<td align='center'><strong>TOTAL</strong></td>
					<td align='center'></td>
					<td align='center'></td>
					<td></td>
					<td align='right'><strong>" . number_format($d) . "</strong></td>
					<td align='right'><strong>" . number_format($k) . "</strong></td>
					<td align='right'><strong>" . number_format(@($k/$d)*100,2) . "</strong></td>
					<td align='right'><strong>" . number_format($b) . "</strong></td>
					<td align='right'><strong>" . number_format(@($b/$k)*100,2) . "</strong></td>
					<td align='right'><strong>" . number_format($d-$k) . "</strong></td>
					<td align='right'><strong>" . number_format($k-$b) . "</strong></td>
				</tr>
				";
		}
		$mysqli->close();($kon);
		
		//echo $hasil;
	?>
</html>