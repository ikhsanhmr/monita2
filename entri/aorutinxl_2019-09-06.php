<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=aorutin.xls");

	session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	
	require_once "../config/koneksi.php";
	
	$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
	$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
	$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
	$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
	// $o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
	$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$p = "";
	$b = "";
	while ($row = mysqli_fetch_array($result)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysqli_free_result($result);

	$parm = "";
	// $parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4));
	// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($b0==""? "": " and (nipuser = '$b0' or f.nick = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and skk = '$k0'");
	$parm .= ($o==""? "": " and skkoi = '$o'");
	// $parm .= ($c==""? "": " and nomorkontrak = '$c'");
	
	
	
	echo "
		<strong>Data Kontrak Wilayah Sumatera Utara</strong><br>
		<strong>Periode	: $p1</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
	
	if($v!="") {

		$sql = "
			SELECT	id, nama, SUM(nilai_jan) AS nilai_jan, SUM(nilai_feb) AS nilai_feb, 
					SUM(nilai_mar) AS nilai_mar, SUM(nilai_apr) AS nilai_apr, SUM(nilai_mei) AS nilai_mei, 
					SUM(nilai_jun) AS nilai_jun, SUM(nilai_jul) AS nilai_jul, SUM(nilai_ags) AS nilai_ags, 
					SUM(nilai_sep) AS nilai_sep, SUM(nilai_okt) AS nilai_okt, SUM(nilai_nov) AS nilai_nov, 
					SUM(nilai_des) AS nilai_des, SUM(nilai_utang) AS nilai_utang, SUM(bayar_jan) AS bayar_jan, 
					SUM(bayar_feb) AS bayar_feb, SUM(bayar_mar) AS bayar_mar, SUM(bayar_apr) AS bayar_apr, 
					SUM(bayar_mei) AS bayar_mei, SUM(bayar_jun) AS bayar_jun, SUM(bayar_jul) AS bayar_jul, 
					SUM(bayar_ags) AS bayar_ags, SUM(bayar_sep) AS bayar_sep, SUM(bayar_okt) AS bayar_okt, 
					SUM(bayar_nov) AS bayar_nov, SUM(bayar_des) AS bayar_des, SUM(bayar_utang) AS bayar_utang
			FROM 	(
						SELECT	a.*, MONTH(tgltagih), YEAR(tgltagih), 
								(CASE WHEN MONTH(tgltagih) = 1 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jan,
								(CASE WHEN MONTH(tgltagih) = 2 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_feb, 
								(CASE WHEN MONTH(tgltagih) = 3 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_mar,
								(CASE WHEN MONTH(tgltagih) = 4 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_apr, 
								(CASE WHEN MONTH(tgltagih) = 5 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_mei,
								(CASE WHEN MONTH(tgltagih) = 6 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jun, 
								(CASE WHEN MONTH(tgltagih) = 7 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jul,
								(CASE WHEN MONTH(tgltagih) = 8 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_ags, 
								(CASE WHEN MONTH(tgltagih) = 9 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_sep,
								(CASE WHEN MONTH(tgltagih) = 10 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_okt, 
								(CASE WHEN MONTH(tgltagih) = 11 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_nov,
								(CASE WHEN MONTH(tgltagih) = 12 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_des,
								(CASE WHEN YEAR(tgltagih) = ".($p1 - 1)." THEN nilaikontrak ELSE 0 END) AS nilai_utang,
								(CASE WHEN MONTH(tgltagih) = 1 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jan,
								(CASE WHEN MONTH(tgltagih) = 2 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_feb, 
								(CASE WHEN MONTH(tgltagih) = 3 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_mar,
								(CASE WHEN MONTH(tgltagih) = 4 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_apr, 
								(CASE WHEN MONTH(tgltagih) = 5 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_mei,
								(CASE WHEN MONTH(tgltagih) = 6 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jun, 
								(CASE WHEN MONTH(tgltagih) = 7 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jul,
								(CASE WHEN MONTH(tgltagih) = 8 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_ags, 
								(CASE WHEN MONTH(tgltagih) = 9 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_sep,
								(CASE WHEN MONTH(tgltagih) = 10 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_okt, 
								(CASE WHEN MONTH(tgltagih) = 11 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_nov,
								(CASE WHEN MONTH(tgltagih) = 12 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_des,
								(CASE WHEN YEAR(tgltagih) = ".($p1 - 1)." THEN nilaibayar ELSE 0 END) AS bayar_utang
						FROM	kontrak_type a INNER JOIN 
								kontrak b ON a.id = b.isrutin LEFT JOIN 
								realisasibayar c ON b.nomorkontrak = c.nokontrak LEFT JOIN
								notadinas_detail d ON b.nomorskkoi = d.noskk LEFT JOIN
								notadinas e ON d.nomornota = e.nomornota LEFT JOIN
								bidang f ON d.pelaksana = f.id
						WHERE	YEAR(b.inputdt) = $p1 and YEAR(tgltagih) IN (".($p1 - 1).", $p1) $parm
						GROUP BY a.id, a.nama, MONTH(tgltagih), YEAR(tgltagih)
						ORDER BY a.id, a.nama, YEAR(tgltagih), MONTH(tgltagih)
					) AS md
					GROUP BY id, nama";
		// echo $sql;
		// return;
		//echo $parm;
		
		$kontrak = 0;
		$bayar = 0;
		$ttl_nilai_utang = 0;
		$ttl_bayar_utang = 0;
		$ttl_nilai_jan = 0;
		$ttl_bayar_jan = 0;
		$ttl_nilai_feb = 0;
		$ttl_bayar_feb = 0;
		$ttl_nilai_mar = 0;
		$ttl_bayar_mar = 0;
		$ttl_nilai_apr = 0;
		$ttl_bayar_apr = 0;
		$ttl_nilai_mei = 0;
		$ttl_bayar_mei = 0;
		$ttl_nilai_jun = 0;
		$ttl_bayar_jun = 0;
		$ttl_nilai_jul = 0;
		$ttl_bayar_jul = 0;
		$ttl_nilai_ags = 0;
		$ttl_bayar_ags = 0;
		$ttl_nilai_sep = 0;
		$ttl_bayar_sep = 0;
		$ttl_nilai_okt = 0;
		$ttl_bayar_okt = 0;
		$ttl_nilai_nov = 0;
		$ttl_bayar_nov = 0;
		$ttl_nilai_des = 0;
		$ttl_bayar_des = 0;
		$ttl_nilai = 0;
		$ttl_bayar = 0;

		$no = 0;
		$parm = "";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {
			$no++;
			$kontrak += $row["kontrak"];
			$bayar += $row["bayar"];

			$ttl_nilai_utang += $row["nilai_utang"];
			$ttl_bayar_utang += $row["bayar_utang"];
			$ttl_nilai_jan += $row["nilai_jan"];
			$ttl_bayar_jan += $row["bayar_jan"];
			$ttl_nilai_feb += $row["nilai_feb"];
			$ttl_bayar_feb += $row["bayar_feb"];
			$ttl_nilai_mar += $row["nilai_mar"];
			$ttl_bayar_mar += $row["bayar_mar"];
			$ttl_nilai_apr += $row["nilai_apr"];
			$ttl_bayar_apr += $row["bayar_apr"];
			$ttl_nilai_mei += $row["nilai_mei"];
			$ttl_bayar_mei += $row["bayar_mei"];
			$ttl_nilai_jun += $row["nilai_jun"];
			$ttl_bayar_jun += $row["bayar_jun"];
			$ttl_nilai_jul += $row["nilai_jul"];
			$ttl_bayar_jul += $row["bayar_jul"];
			$ttl_nilai_ags += $row["nilai_ags"];
			$ttl_bayar_ags += $row["bayar_ags"];
			$ttl_nilai_sep += $row["nilai_sep"];
			$ttl_bayar_sep += $row["bayar_sep"];
			$ttl_nilai_okt += $row["nilai_okt"];
			$ttl_bayar_okt += $row["bayar_okt"];
			$ttl_nilai_nov += $row["nilai_nov"];
			$ttl_bayar_nov += $row["bayar_nov"];
			$ttl_nilai_des += $row["nilai_des"];
			$ttl_bayar_des += $row["bayar_des"];

			$total_nilai = 	$row["nilai_utang"] + $row["nilai_jan"] + $row["nilai_feb"] + $row["nilai_mar"] + 
							$row["nilai_apr"] + $row["nilai_mei"] + $row["nilai_jun"] + $row["nilai_jul"] + 
							$row["nilai_ags"] + $row["nilai_sep"] + $row["nilai_okt"] + $row["nilai_nov"] + 
							$row["nilai_des"];

			$total_bayar = 	$row["bayar_utang"] + $row["bayar_jan"] + $row["bayar_feb"] + $row["bayar_mar"] + 
							$row["bayar_apr"] + $row["bayar_mei"] + $row["bayar_jun"] + $row["bayar_jul"] + 
							$row["bayar_ags"] + $row["bayar_sep"] + $row["bayar_okt"] + $row["bayar_nov"] + 
							$row["bayar_des"];

			$ttl_nilai += $total_nilai;

			$ttl_bayar += $total_bayar;
			
			$parm .= "
				<tr>
					<td>$no</td>
					<td>$row[nama]</td>
					<td align='right'>".number_format($row["nilai_utang"])."</td>
					<td align='right'>".number_format($row["bayar_utang"])."</td>
					<td align='right'>".number_format($row["nilai_jan"])."</td>
					<td align='right'>".number_format($row["bayar_jan"])."</td>
					<td align='right'>".number_format($row["nilai_feb"])."</td>
					<td align='right'>".number_format($row["bayar_feb"])."</td>
					<td align='right'>".number_format($row["nilai_mar"])."</td>
					<td align='right'>".number_format($row["bayar_mar"])."</td>
					<td align='right'>".number_format($row["nilai_apr"])."</td>
					<td align='right'>".number_format($row["bayar_apr"])."</td>
					<td align='right'>".number_format($row["nilai_mei"])."</td>
					<td align='right'>".number_format($row["bayar_mei"])."</td>
					<td align='right'>".number_format($row["nilai_jun"])."</td>
					<td align='right'>".number_format($row["bayar_jun"])."</td>
					<td align='right'>".number_format($row["nilai_jul"])."</td>
					<td align='right'>".number_format($row["bayar_jul"])."</td>
					<td align='right'>".number_format($row["nilai_ags"])."</td>
					<td align='right'>".number_format($row["bayar_ags"])."</td>
					<td align='right'>".number_format($row["nilai_sep"])."</td>
					<td align='right'>".number_format($row["bayar_sep"])."</td>
					<td align='right'>".number_format($row["nilai_okt"])."</td>
					<td align='right'>".number_format($row["bayar_okt"])."</td>
					<td align='right'>".number_format($row["nilai_nov"])."</td>
					<td align='right'>".number_format($row["bayar_nov"])."</td>
					<td align='right'>".number_format($row["nilai_des"])."</td>
					<td align='right'>".number_format($row["bayar_des"])."</td>
					<td align='right'>".number_format($total_nilai)."</td>
					<td align='right'>".number_format($total_bayar)."</td>
				</tr>";
		}
		mysqli_free_result($result);
		
		echo "
			<table border='1'>
				<thead>
					<tr>
						<th rowspan='3'>No</th>
						<th rowspan='3'>Jenis Tagihan</th>
						<th colspan='26'>Bulan Tagih</th>
						<th colspan='2'>Total</th>
					</tr>
					<tr>
						<th colspan='2'>Utang</th>
						<th colspan='2'>Januari</th>
						<th colspan='2'>Februari</th>
						<th colspan='2'>Maret</th>
						<th colspan='2'>April</th>
						<th colspan='2'>Mei</th>
						<th colspan='2'>Juni</th>
						<th colspan='2'>Juli</th>
						<th colspan='2'>Agustus</th>
						<th colspan='2'>September</th>
						<th colspan='2'>Oktober</th>
						<th colspan='2'>November</th>
						<th colspan='2'>Desember</th>
						<th rowspan='2'>Kontrak</th>
						<th rowspan='2'>Bayar</th>
					</tr>
					<tr>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
						<th>Kontrak</th>
						<th>Bayar</th>
					</tr>
				</thead>
				$parm
				<tfoot>
					<tr>
						<td colspan='2'>Total</td>
						<td align='right'>".number_format($ttl_nilai_utang)."</td>
						<td align='right'>".number_format($ttl_bayar_utang)."</td>
						<td align='right'>".number_format($ttl_nilai_jan)."</td>
						<td align='right'>".number_format($ttl_bayar_jan)."</td>
						<td align='right'>".number_format($ttl_nilai_feb)."</td>
						<td align='right'>".number_format($ttl_bayar_feb)."</td>
						<td align='right'>".number_format($ttl_nilai_mar)."</td>
						<td align='right'>".number_format($ttl_bayar_mar)."</td>
						<td align='right'>".number_format($ttl_nilai_apr)."</td>
						<td align='right'>".number_format($ttl_bayar_apr)."</td>
						<td align='right'>".number_format($ttl_nilai_mei)."</td>
						<td align='right'>".number_format($ttl_bayar_mei)."</td>
						<td align='right'>".number_format($ttl_nilai_jun)."</td>
						<td align='right'>".number_format($ttl_bayar_jun)."</td>
						<td align='right'>".number_format($ttl_nilai_jul)."</td>
						<td align='right'>".number_format($ttl_bayar_jul)."</td>
						<td align='right'>".number_format($ttl_nilai_ags)."</td>
						<td align='right'>".number_format($ttl_bayar_ags)."</td>
						<td align='right'>".number_format($ttl_nilai_sep)."</td>
						<td align='right'>".number_format($ttl_bayar_sep)."</td>
						<td align='right'>".number_format($ttl_nilai_okt)."</td>
						<td align='right'>".number_format($ttl_bayar_okt)."</td>
						<td align='right'>".number_format($ttl_nilai_nov)."</td>
						<td align='right'>".number_format($ttl_bayar_nov)."</td>
						<td align='right'>".number_format($ttl_nilai_des)."</td>
						<td align='right'>".number_format($ttl_bayar_des)."</td>
						<td align='right'>".number_format($ttl_nilai)."</td>
						<td align='right'>".number_format($ttl_bayar)."</td>
					</tr>
				</tfoot>
			</table>
		";
	}
	$mysqli->close();($kon);
?>
