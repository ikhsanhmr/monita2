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
	$result = mysql_query($sql);
	
	$p = "";
	$b = "";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysql_free_result($result);

	$parm = "";
	$parm .= ($p1==""? "": " and SUBSTR(tglskk, 1, 7) >= '$p1'");
	$parm .= ($p2==""? "": " and SUBSTR(tglskk, 1, 7) <= '$p2'");
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and skk = '$k0'");
	$parm .= ($o==""? "": " and skkoi = '$o'");
	$parm .= ($c==""? "": " and nomorkontrak = '$c'");
	
	echo "
		<strong>Data Kontrak Wilayah Sumatera Utara</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Jenis : $o</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
	
	if($v!="") {
/*
		$sql = "
			SELECT 
				skkoi, n.nomornota nd, nip, nipuser, pelaksana, g.id nick, pos1, namapos, b.namaunit, skk, oi.uraian oiuraian, 
				nomorkontrak, k.pos kpos, k.uraian kuraian, vendor, tglawal, tglakhir, coalesce(nilaikontrak,0) nilaikontrak, coalesce(bayar,0) bayar, coalesce(ke,0) ke, SIGNED sgd, inputdt
			FROM notadinas n
			LEFT JOIN bidang g ON n.nipuser = g.nick 
			LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
			LEFT JOIN bidang b ON d.pelaksana = b.id 
			LEFT JOIN (
				SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			) p ON d.pos1 = p.pos
			LEFT JOIN (
				SELECT nomorskko skk, uraian FROM skkoterbit UNION
				SELECT nomorskki skk, uraian FROM skkiterbit
			) oi ON d.noskk = oi.skk
			LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
			LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar, count(*) ke FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak
			WHERE d.progress >= 7 
				-- AND COALESCE(oi.skk, '') != '' AND NOT nomorkontrak IS NULL 
				$parm
			ORDER BY nd, skk, nomorkontrak, pos1";
*/

		$sql = "
			SELECT 
				skkoi, n.nomornota, nipuser, g.id userid, 
				pelaksana, b.namaunit, pos1, nilai1, namapos,
			--	nomorwbs, nomorcostcenter, nomorskko noskk, s.uraian uraians, tanggalskko, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
			--	nomorskko skk, tanggalskko tglskk,
				skk, tglskk,
				inputdt, nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak, bayar, nomorprk
			FROM notadinas n
			LEFT JOIN bidang g ON n.nipuser = g.nick 
			LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
			LEFT JOIN bidang b ON d.pelaksana = b.id  
			
			LEFT JOIN (
				SELECT nomorskko skk, tanggalskko tglskk, '' as nomorprk FROM skkoterbit UNION
				SELECT nomorskki skk, tanggalskki tglskk, nomorprk FROM skkiterbit 
			)s ON d.noskk = s.skk
			
			-- left join skkoterbit s on d.noskk = s.nomorskko 
			LEFT JOIN (
				SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			) p ON d.pos1 = p.pos
			LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
			LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak
			WHERE d.progress >= 7 AND NOT skk IS NULL AND NOT nomorkontrak IS NULL 
			$parm
			-- WHERE d.progress >= 7 AND NOT nomorskko IS NULL AND NOT nomorkontrak IS NULL 
			-- AND SUBSTR(tanggalskko,1,7) >= '2015-01' AND SUBSTR(tanggalskko,1,7) <= '2015-12' -- and skk like '%SKK%O%'
			ORDER BY skk, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";
		//echo $sql;
		//echo $parm;
		
		$kontrak = 0;
		$bayar = 0;
		$no = 0;
		$parm = "";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$no++;
			$kontrak += $row["nilaikontrak"];
			$bayar += $row["bayar"];
			
			$parm .= "
				<tr>
					<td>$no</td>
					<td>$row[skkoi]</td>
					<td>$row[namaunit]</td>
					<td>$row[nomorprk]</td>
					<td>$row[skk]</td>
					<td>$row[nomorkontrak]</td>
					<td>$row[vendor]</td>
					<td>$row[uraiank]</td>
					<td>$row[tglawal]</td>
					<td>$row[tglakhir]</td>
					<td align='right'>".number_format($row["nilaikontrak"])."</td>
					<td align='right'>".number_format($row["bayar"])."</td>
					<td align='right'>".number_format($row["nilaikontrak"]-$row["bayar"])."</td>
					<td align='right'>".number_format((empty($row["nilaikontrak"]) ? 0 : $row["bayar"]/$row["nilaikontrak"]*100),2)."</td>
					<td>$row[inputdt]</td>
				</tr>";
				//min='0' max='$dummy' 
		}
		mysql_free_result($result);
		
		echo "
			<table border='1'>
				<tr>
					<th rowspan='2'>No</th>
					<th rowspan='2'>Jenis</th>
					<th rowspan='2'>Pelaksana</th>
					<th rowspan='2'>No PRK</th>
					<th rowspan='2'>No SKK</th>
					<th colspan='10'>Kontrak</th>
				</tr>
				<tr>
					<th>Nomor</th>
					<th>Vendor</th>
					<th>Uraian</th>
					<th>Tgl Awal</th>
					<th>Tgl Akhir</th>
					<th>Nilai</th>
					<th>Total Bayar</th>
					<th>Sisa</th>
					<th>Prosentase (%)</th>
					<th>Keterangan</th>
				</tr>
				$parm
				<tr>
					<td colspan='9'>Total</td>
					<td align='right'>".number_format($kontrak)."</td>
					<td align='right'>".number_format($bayar)."</td>
					<td align='right'>".number_format($kontrak-$bayar)."</td>
					<td align='right'>".number_format($bayar/$kontrak*100,2)."</td>
					<td></td>
				</tr>
			</table>";
	}
	mysql_close($kon);
?>