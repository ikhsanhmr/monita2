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
	$parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
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

		// $sql = "
		// 	SELECT 
		// 		skkoi, n.nomornota, nipuser, g.id userid, 
		// 		pelaksana, b.namaunit, pos1, nilai1, namapos,
		// 	--	nomorwbs, nomorcostcenter, nomorskko noskk, s.uraian uraians, tanggalskko, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
		// 	--	nomorskko skk, tanggalskko tglskk,
		// 		skk, tglskk, nilaidisburse, nomorscore,
		// 		inputdt, nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak, bayar, nomorprk 
		// 	FROM notadinas n
		// 	LEFT JOIN bidang g ON n.nipuser = g.nick 
		// 	LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
		// 	LEFT JOIN bidang b ON d.pelaksana = b.id  
			
		// 	LEFT JOIN (
		// 		SELECT nomorskko skk, tanggalskko tglskk, '' as nomorprk, nilaidisburse, '' as nomorscore FROM skkoterbit UNION
		// 		SELECT nomorskki skk, tanggalskki tglskk, nomorprk, nilaidisburse, nomorscore FROM skkiterbit 
		// 	)s ON d.noskk = s.skk
			
		// 	-- left join skkoterbit s on d.noskk = s.nomorskko 
		// 	LEFT JOIN (
		// 		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
		// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
		// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
		// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
		// 	) p ON d.pos1 = p.pos
		// 	LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
		// 	LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak
		// 	WHERE d.progress >= 7 AND NOT skk IS NULL AND NOT nomorkontrak IS NULL 
		// 	$parm
		// 	-- WHERE d.progress >= 7 AND NOT nomorskko IS NULL AND NOT nomorkontrak IS NULL 
		// 	-- AND SUBSTR(tanggalskko,1,7) >= '2015-01' AND SUBSTR(tanggalskko,1,7) <= '2015-12' -- and skk like '%SKK%O%'
		// 	ORDER BY skk, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";

		$sql = "
			SELECT	skkoi, n.nomornota, nipuser, pelaksana, b.namaunit, pos1, nilai1, namapos,	skk, tglskk, 
					nilaidisburse, nomorscore, inputdt, k.nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, 
					nilaikontrak, bayar, nomorprk, pdp.jtmaset, pdp.jtmrp, pdp.gdaset, pdp.gdrp, pdp.jtraset, pdp.jtrrp, 
					pdp.sl1aset, pdp.sl1rp, pdp.sl3aset, pdp.sl3rp, pdp.keypointaset, pdp.keypointrp, pdp.file_path as 
					pdp_file_path, date(k.tgltagih) as tgltagih, kt.nama as name_type
			FROM 	notadinas n	LEFT JOIN 
					notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN 
					bidang b ON d.pelaksana = b.id LEFT JOIN 
					(
						SELECT 	nomorskko skk, tanggalskko tglskk, '' as nomorprk, nilaidisburse, '' as nomorscore 
						FROM 	skkoterbit 
						UNION
						SELECT 	nomorskki skk, tanggalskki tglskk, nomorprk, nilaidisburse, nomorscore 
						FROM 	skkiterbit 
					)s ON d.noskk = s.skk LEFT JOIN 
					(
						SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
						SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
						SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
						SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
					) p ON d.pos1 = p.pos LEFT JOIN 
					kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
					(
						SELECT nokontrak, SUM(nilaibayar) bayar 
						FROM realisasibayar 
						GROUP BY nokontrak
					) r ON k.nomorkontrak = r.nokontrak LEFT JOIN 
					asetpdp pdp ON k.nomorkontrak = pdp.nomorkontrak LEFT JOIN
					kontrak_type kt ON k.isrutin = kt.id
			WHERE 	d.progress >= 7 AND NOT skk IS NULL AND NOT k.nomorkontrak IS NULL 
			$parm
			ORDER BY skk, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak
		";

		//echo $sql;
		//echo $parm;
		
		$kontrak = 0;
		$bayar = 0;
		$t_jtm_unt = 0;
		$t_gd_unt = 0;
		$t_jtr_unt = 0;
		$t_sl1_unt = 0;
		$t_sl3_unt = 0;
		$t_kp_unt = 0;
		$t_jtm_rp = 0;
		$t_gd_rp = 0;
		$t_jtr_rp = 0;
		$t_sl1_rp = 0;
		$t_sl3_rp = 0;
		$t_kp_rp = 0;
		$no = 0;
		$parm = "";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$no++;
			$kontrak += $row["nilaikontrak"];
			$bayar += $row["bayar"];
			$t_jtm_unt += $row["jtmaset"];
			$t_gd_unt += $row["gdaset"];
			$t_jtr_unt += $row["jtraset"];
			$t_sl1_unt += $row["sl1aset"];
			$t_sl3_unt += $row["sl3aset"];
			$t_kp_unt += $row["keypointaset"];
			$t_jtm_rp += $row["jtmrp"];
			$t_gd_rp += $row["gdrp"];
			$t_jtr_rp += $row["jtrrp"];
			$t_sl1_rp += $row["sl1rp"];
			$t_sl3_rp += $row["sl3rp"];
			$t_kp_rp += $row["keypointrp"];
			
			$parm .= "
				<tr>
					<td>$no</td>
					<td>$row[skkoi]</td>
					<td>$row[nomorkontrak]</td>
					<td>$row[vendor]</td>
					<td>$row[uraiank]</td>
					<td>$row[name_type]</td>
					<td>".($row["tgltagih"] != '0000-00-00' ? date_format(date_create($row["tgltagih"]), "M-Y") : '-')."</td>
					<td>$row[tglawal]</td>
					<td>$row[tglakhir]</td>
					<td align='right'>".number_format($row["nilaikontrak"])."</td>
					<td align='right'>".number_format($row["bayar"])."</td>
					<td align='right'>".number_format($row["nilaikontrak"]-$row["bayar"])."</td>
					<td align='right'>".number_format((empty($row["nilaikontrak"]) ? 0 : $row["bayar"]/$row["nilaikontrak"]*100),2)."</td>
					<td>$row[inputdt]</td>
					<td>$row[skk]</td>
					<td>$row[tglskk]</td>
					<td>$row[nilaidisburse]</td>
					<td>$row[namaunit]</td>
					<td>$row[nomorprk]</td>
					<td>$row[nomorscore]</td>
					<td align='right'>".number_format($row["jtmaset"])."</td>
					<td align='right'>".number_format($row["jtmrp"])."</td>
					<td align='right'>".number_format($row["gdaset"])."</td>
					<td align='right'>".number_format($row["gdrp"])."</td>
					<td align='right'>".number_format($row["jtraset"])."</td>
					<td align='right'>".number_format($row["jtrrp"])."</td>
					<td align='right'>".number_format($row["sl1aset"])."</td>
					<td align='right'>".number_format($row["sl1rp"])."</td>
					<td align='right'>".number_format($row["sl3aset"])."</td>
					<td align='right'>".number_format($row["sl3rp"])."</td>
					<td align='right'>".number_format($row["keypointaset"])."</td>
					<td align='right'>".number_format($row["keypointrp"])."</td>
					<td>". 
						($row["pdp_file_path"]==""? "": "<br><a href='../$row[pdp_file_path]' target='_blank'>Download Realisasi Fisik</a>")
					."</td>
				</tr>";
				//min='0' max='$dummy' 
		}
		mysql_free_result($result);
		
		echo "
			<table border='1'>
				<tr>
					<th rowspan='2'>No</th>
					<th rowspan='2'>Jenis</th>
					<th colspan='12'>Kontrak</th>
					<th colspan='6'>SKK</th>
					<th colspan='13'>Realisasi Fisik</th>
				</tr>
				<tr>
					<th rowspan='2'>Nomor</th>
					<th rowspan='2'>Vendor</th>
					<th rowspan='2'>Uraian</th>
					<th rowspan='2'>Jenis Kontrak</th>
					<th rowspan='2'>Bulan Tagih</th>
					<th rowspan='2'>Tgl Awal</th>
					<th rowspan='2'>Tgl Akhir</th>
					<th rowspan='2'>Nilai</th>
					<th rowspan='2'>Total Bayar</th>
					<th rowspan='2'>Sisa</th>
					<th rowspan='2'>Prosentase (%)</th>
					<th rowspan='2'>Tgl Input</th>
					<th rowspan='2'>No SKK</th>
					<th rowspan='2'>Tgl</th>
					<th rowspan='2'>Nilai</th>
					<th rowspan='2'>Pelaksana</th>
					<th rowspan='2'>No PRK</th>
					<th rowspan='2'>Basket / Fungsi</th>
					<th colspan='2'>JTM</th>
					<th colspan='2'>Gardu</th>
					<th colspan='2'>JTR</th>
					<th colspan='2'>SL1 Phasa</th>
					<th colspan='2'>SL3 Phasa</th>
					<th colspan='2'>Key Point</th>
					<th rowspan='2'>Attachment</th>
				</tr>
				<tr>
					<th>KMS</th>
					<th>RP</th>
					<th>KMS</th>
					<th>RP</th>
					<th>UNIT</th>
					<th>RP</th>
					<th>PLGN</th>
					<th>RP</th>
					<th>PLGN</th>
					<th>RP</th>
					<th>PLGN</th>
					<th>RP</th>
				</tr>
				$parm
				<tr>
					<td colspan='9'>Total</td>
					<td align='right'>".number_format($kontrak)."</td>
					<td align='right'>".number_format($bayar)."</td>
					<td align='right'>".number_format($kontrak-$bayar)."</td>
					<td align='right'>".number_format(@($bayar/$kontrak)*100,2)."</td>
					<td colspan='7'></td>
					<td align='right'>".number_format($t_jtm_unt)."</td>
					<td align='right'>".number_format($t_jtm_rp)."</td>
					<td align='right'>".number_format($t_gd_unt)."</td>
					<td align='right'>".number_format($t_gd_rp)."</td>
					<td align='right'>".number_format($t_jtr_unt)."</td>
					<td align='right'>".number_format($t_jtr_rp)."</td>
					<td align='right'>".number_format($t_sl1_unt)."</td>
					<td align='right'>".number_format($t_sl1_rp)."</td>
					<td align='right'>".number_format($t_sl3_unt)."</td>
					<td align='right'>".number_format($t_sl3_rp)."</td>
					<td align='right'>".number_format($t_kp_unt)."</td>
					<td align='right'>".number_format($t_kp_rp)."</td>
					<td></td>
				</tr>
			</table>";
	}
	mysql_close($kon);
?>