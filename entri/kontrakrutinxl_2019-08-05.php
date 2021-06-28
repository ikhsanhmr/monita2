<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=kontrak.xls");

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
	$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
	$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	require_once "../config/koneksi.php";
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
	$parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4) . " AND MONTH(inputdt) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(inputdt) = " . substr($p2,0,4) . " AND MONTH(inputdt) <= " . substr($p2,-2));
	// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskko = '$k0'");
	$parm .= ($o==""? "": " and skkoi = '$o'");
	$parm .= ($c==""? "": " and nomorkontrak = '$c'");
	
	echo "
		<strong>Data Kontrak Rutin Wilayah Sumatera Utara</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
	
	if($v!="") {

		$sql = "
			SELECT 	n.nomornota, nipuser, pelaksana, b.namaunit, pos1, nilai1, namapos, nomorwbs, nomorcostcenter, 
					nomorskko noskk, s.uraian uraians, tanggalskko, nilaianggaran anggaran, nilaidisburse disburse, 
					nilaiwbs wbs, inputdt, k.nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, k.file_path, 
					nilaikontrak kontrak, bayar, k.kid, k.signed, date(k.tgltagih) as tgltagih, kapel.signdt as app_pel, 
					kaang.signdt as app_ang, kakeu.signdt as app_keu, isrutin
			FROM 	notadinas n LEFT JOIN 
					notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN 
					bidang b ON d.pelaksana = b.id LEFT JOIN 
					skkoterbit s ON d.noskk = s.nomorskko LEFT JOIN 
					(
						SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
						SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
						SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
						SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
					) p ON d.pos1 = p.pos LEFT JOIN 
					kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
					(
						SELECT 	nokontrak, SUM(nilaibayar) bayar 
						FROM 	realisasibayar 
						GROUP BY nokontrak
					) r ON TRIM(k.nomorkontrak) = TRIM(r.nokontrak) LEFT JOIN
					(
						SELECT	t.nomorkontrak as nmrkontrak, signlevel, actiontype
						FROM	kontrak_approval t INNER JOIN 
								(
									SELECT nomorkontrak, MAX( id ) AS lastid
									FROM kontrak_approval
									GROUP BY nomorkontrak
								)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
					) ka  ON TRIM(k.nomorkontrak) = TRIM(ka.nmrkontrak) LEFT JOIN
					(
						SELECT	nomorkontrak, max(signdt) as signdt
						FROM	kontrak_approval
						Where 	signlevel <= 2 and actiontype = 1
						GROUP BY nomorkontrak
					) kapel  ON TRIM(k.nomorkontrak) = TRIM(kapel.nomorkontrak) LEFT JOIN
					(
						SELECT	nomorkontrak, max(signdt) as signdt
						FROM	kontrak_approval
						Where	signlevel = 3 and actiontype = 1
						GROUP BY nomorkontrak
					) kaang  ON TRIM(k.nomorkontrak) = TRIM(kaang.nomorkontrak) LEFT JOIN
					(
						SELECT	nomorkontrak, max(signdt) as signdt
						FROM	kontrak_approval
						Where	signlevel = 4 and actiontype = 1
						GROUP BY nomorkontrak
					) kakeu  ON TRIM(k.nomorkontrak) = TRIM(kakeu.nomorkontrak)
					WHERE 	d.progress >= 7 AND NOT nomorskko IS NULL AND NOT k.nomorkontrak IS NULL and isrutin = 1
					$parm
					ORDER BY LPAD(pelaksana, 2, '0'), nomorskko, k.pos, k.tgltagih
		";

		//echo $sql;
		//echo $parm;
		
		$kontrak = 0;
		$bayar = 0;
		$no = 0;
		$parm = "";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {
			$no++;
			$kontrak += $row["nilaikontrak"];
			$bayar += $row["bayar"];
			
			$parm .= "
				<tr>
					<td>$no</td>
					<td>$row[noskk]</td>
					<td>$row[namaunit]</td>
					<td>$row[pos1]</td>
					<td>$row[namapos]</td>
					<td>$row[nomorkontrak]</td>
					<td>$row[vendor]</td>
					<td>$row[uraiank]</td>
					<td>".($row["tgltagih"] != '0000-00-00' ? date_format(date_create($row["tgltagih"]), "M-Y") : '-')."</td>
					<td>$row[tglawal]</td>
					<td>$row[tglakhir]</td>
					<td align='right'>".number_format($row["kontrak"])."</td>
					<td align='right'>".number_format($row["bayar"])."</td>
					<td>$row[inputdt]</td>
					<td>$row[app_pel]</td>
					<td>$row[app_ang]</td>
					<td>$row[app_keu]</td>
				</tr>";
				//min='0' max='$dummy' 
		}
		mysqli_free_result($result);
		
		echo "
			<table border='1'>
				<tr>
					<th rowspan='2'>No</th>
					<th colspan='4'>SKKO</th>
					<th colspan='8'>Kontrak</th>
					<th colspan='4'>Tanggal</th>
				</tr>
				<tr>
					<th>No SKK</th>
					<th>Pelaksana</th>
					<th>Kode POS</th>
					<th>Ket POS</th>
					<th>Nomor</th>
					<th>Vendor</th>
					<th>Uraian</th>
					<th>Bulan Tagih</th>
					<th>Tgl Awal</th>
					<th>Tgl Akhir</th>
					<th>Nilai</th>
					<th>Total Bayar</th>
					<th>Entry</th>
					<th>Approve Bidang/UP3</th>
					<th>Approve Anggaran</th>
					<th>Approve Keuangan</th>
				</tr>
				$parm
			</table>";
	}
	$mysqli->close();($kon);
?>