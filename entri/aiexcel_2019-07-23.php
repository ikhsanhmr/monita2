<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=ai.xls");

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
	$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	require_once "../config/koneksi.php";
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysql_query($sql);
	
	$b = "";
	$p = "";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysql_free_result($result);
	
	$sql = "
		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
		ORDER BY pos";
	$result = mysql_query($sql);
	
	$kdpos = "";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$kdpos = ($row["pos"]==$kdpos0? "$row[pos] - $row[namapos]": $kdpos);
	}
	mysql_free_result($result);

	$parm = "";
//	$parm .= ($p1==""? "": " and SUBSTR(tanggalskki, 1, 7) >= '$p1'");
//	$parm .= ($p2==""? "": " and SUBSTR(tanggalskki, 1, 7) <= '$p2'");
	$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));
	
	// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	// $parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
	
	if ($user == "93162829ZY"){

		// $parm .= ($b0==""? " AND d.pos1 IN (Select akses From akses_pos Where nip = '$user') ": " and (g.id = '$b0' or pelaksana = '$b0' or d.pos1 IN (Select akses From akses_pos Where nip = '$user'))");
		$parm .= ($b0==""? " AND c.pos1 IN (Select akses From akses_pos Where nip = '$user') ": " and (nipuser = '$b0' or b.nick = '$b0' or c.pos1 IN (Select akses From akses_pos Where nip = '$user'))");

	}else{

		// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
	}

	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and nomorskki = '$k0'");
	$parm .= ($kdpos0==""? "": " and pos1 = '$kdpos0'");
	//echo "parm : $parm<br>";

	if($v!="") {
			// $sql = "
			// 	SELECT 
			// 		n.nomornota, nipuser, g.id userid, 
			// 		pelaksana, b.namaunit, pos1, nilai1, namapos,
			// 		nomorwbs, nomorprk, nomorscore, nomorskki noskk, s.uraian uraians, tanggalskki, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
			// 		inputdt, k.nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak kontrak, k.signed, bayar, rb.no_rab, rb.nilai_rp, pdp.jtmaset, pdp.jtmrp, pdp.gdaset, pdp.gdrp, pdp.jtraset, pdp.jtrrp, pdp.sl1aset, 
			// 		pdp.sl1rp, pdp.sl3aset, pdp.sl3rp, pdp.keypointaset, pdp.keypointrp, s.jtm, s.gd, s.jtr, s.sl1, s.sl3, s.keypoint, 
			// 		s.nilaianggaranjtm, s.nilaianggarangd, s.nilaianggaranjtr, s.nilaianggaransl1, s.nilaianggaransl3, s.nilaianggarankp 
			// 	FROM notadinas n
			// 	LEFT JOIN bidang g ON n.nipuser = g.nick 
			// 	LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
			// 	LEFT JOIN bidang b ON d.pelaksana = b.id  
			// 	LEFT JOIN skkiterbit s ON d.noskk = s.nomorskki
			// 	LEFT JOIN (
			// 		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			// 	) p ON d.pos1 = p.pos
			// 	LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos 
			// 	LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak LEFT JOIN 
			// 	rab rb ON k.no_rab = rb.no_rab LEFT JOIN 
			// 	asetpdp pdp ON k.nomorkontrak = pdp.nomorkontrak
			// 	WHERE d.progress >= 7 AND NOT nomorskki IS NULL 
			// 	$parm
			// 	ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, k.nomorkontrak";
			$sql = "
				SELECT	n.nomornota, nipuser, pelaksana, b.namaunit, pos1, nilai1, namapos, nomorwbs, nomorprk, 
						nomorscore, nomorskki noskk, s.uraian uraians, tanggalskki, nilaianggaran anggaran, 
						nilaidisburse disburse, nilaiwbs wbs, inputdt, k.nomorkontrak, k.nodokumen nodokumen, vendor, 
						k.uraian uraiank, tglawal, tglakhir, nilaikontrak kontrak, k.signed, bayar, k.kid , k.file_path, 
						rb.no_rab, rb.nilai_rp, pdp.jtmaset, pdp.jtmrp, pdp.gdaset, pdp.gdrp, pdp.jtraset, pdp.jtrrp, 
						pdp.sl1aset, pdp.sl1rp, pdp.sl3aset, pdp.sl3rp, pdp.keypointaset, pdp.keypointrp, s.jtm, s.gd, s.jtr, 
						s.sl1, s.sl3, s.keypoint, s.nilaianggaranjtm, s.nilaianggarangd, s.nilaianggaranjtr, 
						s.nilaianggaransl1, s.nilaianggaransl3, s.nilaianggarankp, kapel.signdt as app_pel, 
						kaang.signdt as app_ang, kakeu.signdt as app_keu
				FROM 	notadinas n LEFT JOIN 
						notadinas_detail d ON n.nomornota = d.nomornota	LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
						(
							SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
						(
							SELECT 	nokontrak, SUM(nilaibayar) bayar 
							FROM 	realisasibayar 
							GROUP BY nokontrak
						) r ON k.nomorkontrak = r.nokontrak LEFT JOIN 
						rab rb ON k.no_rab = rb.no_rab LEFT JOIN asetpdp pdp ON k.nomorkontrak = pdp.nomorkontrak LEFT JOIN
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
				WHERE 	d.progress >= 7 AND NOT nomorskki IS NULL 
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, k.inputdt DESC, k.nomorkontrak";
			//echo $sql;
			
			//$hasil = "
			echo "
			<strong>LAPORAN MONITORING PENYERAPAN ANGGARAN OPERASI</strong><br>
			<strong>Periode	: $p1  -  $p2</strong><br>
			<strong>Bidang : $b</strong><br>
			<strong>Pelaksana : $p</strong><br>
			<strong>No SKK : $k0</strong><br>
			<strong>POS : $kdpos</strong><br>
			Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB
			
			<table border='1'>
				<tr>
					<th rowspan='3' scope='col'>No Urut</th>
					<th colspan='12' scope='col'>skki Terbit</th>
					<th colspan='12' scope='col'>Data Fisik</th>
					<th colspan='2' scope='col'>RAB</th>
					<th colspan='7' scope='col'>Kontrak</th>
					<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
					<th colspan='2' scope='col'>Sisa</th>
					<th colspan='12' scope='col'>Realisasi Fisik</th>
					<th rowspan='3' scope='col'>Tgl Entry</th>
					<th rowspan='3' scope='col'>Tgl Approve Bidang/UP3</th>
					<th rowspan='3' scope='col'>Tgl Approve Anggaran</th>
					<th rowspan='3' scope='col'>Tgl Approve Keuangan</th>
					<th rowspan='3' scope='col'>Status</th>
				</tr>
				<tr>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS <br /> PRK <br /> Basket / Fungsi</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor Nota</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
					<td align='center' colspan='3' style='background-color:rgb(127,255,127)'>POS</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>JTM</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Gardu</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>JTR</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>SL1 Phasa</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>SL3 Phasa</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Key Point</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>No. SAP</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Vendor</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nilai</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKI (Disburse - Kontrak)</td>
					<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>JTM</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Gardu</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>JTR</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>SL1 Phasa</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>SL3 Phasa</td>
					<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Key Point</td>
				</tr>
				<tr>
					<td style='background-color:rgb(127,255,127)'>Anggaran (Rp.)</td>
					<td style='background-color:rgb(127,255,127)'>Disburse (Rp.)</td>
					<td style='background-color:rgb(127,255,127)'>Kode</td>
					<td style='background-color:rgb(127,255,127)'>Ket</td>
					<td style='background-color:rgb(127,255,127)'>Nilai</td>
					<td style='background-color:rgb(127,255,127)'>KMS</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>KMS</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>UNIT</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>PLGN</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>PLGN</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>PLGN</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>KMS</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>KMS</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>UNIT</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>PLGN</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>PLGN</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
					<td style='background-color:rgb(127,255,127)'>PLGN</td>
					<td style='background-color:rgb(127,255,127)'>RP</td>
				</tr>
			";
			$result = mysql_query($sql);
			
			$no = 0;
			$dummy = "";
			$dummypos = "";
			$hasil0 = "";

			$pnk = 0;
			$pnb = 0;
			$prab = 0;
			$pjtmaset = 0;
			$pjtmrp = 0;
			$pgdaset = 0;
			$pgdrp = 0;
			$pjtraset = 0;
			$pjtrrp = 0;
			$psl1aset = 0;
			$psl1rp = 0;
			$psl3aset = 0;
			$psl3rp = 0;
			$pkeypointaset = 0;
			$pkeypointrp = 0;

			$snk = 0;
			$snb = 0;
			$srab = 0;
			$sjtmaset = 0;
			$sjtmrp = 0;
			$sgdaset = 0;
			$sgdrp = 0;
			$sjtraset = 0;
			$sjtrrp = 0;
			$ssl1aset = 0;
			$ssl1rp = 0;
			$ssl3aset = 0;
			$ssl3rp = 0;
			$skeypointaset = 0;
			$skeypointrp = 0;

			$npost = 0;

			$angt = 0;
			$disbt = 0;
			$wbst = 0;
			$post = 0;
			$kont = 0;
			$bayt = 0;
			$hasilk = 0;

			$ang_jtm_assett = 0;
			$ang_gd_assett = 0;
			$ang_jtr_assett = 0;
			$ang_sl1_assett = 0;
			$ang_sl3_assett = 0;
			$ang_kp_assett = 0;

			$ang_jtm_rpt = 0;
			$ang_gd_rpt = 0;
			$ang_jtr_rpt = 0;
			$ang_sl1_rpt = 0;
			$ang_sl3_rpt = 0;
			$ang_kp_rpt = 0;

			$rea_jtm_assett = 0;
			$rea_gd_assett = 0;
			$rea_jtr_assett = 0;
			$rea_sl1_assett = 0;
			$rea_sl3_assett = 0;
			$rea_kp_assett = 0;

			$rea_jtm_rpt = 0;
			$rea_gd_rpt = 0;
			$rea_jtr_rpt = 0;
			$rea_sl1_rpt = 0;
			$rea_sl3_rpt = 0;
			$rea_kp_rpt = 0;

			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$cskk = ($dummy == $row["noskk"]? true: false);
				
				if($dummy != $row["noskk"] || $dummypos != $row["pos1"]) {
					if($no>0) {
						$hasilp .= "
								<td align='right'>".(empty($prab)? "" : number_format($prab))."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align='right'>".number_format($pnk)."</td>
								<td align='right'>".number_format($pnb)."</td>
								<td align='right'>".number_format($npos-$pnk)."</td>
								<td align='right'>".number_format($pnk-$pnb)."</td>
								<td align='right'>".number_format($pjtmaset)."</td>
								<td align='right'>".number_format($pjtmrp)."</td>
								<td align='right'>".number_format($pgdaset)."</td>
								<td align='right'>".number_format($pgdrp)."</td>
								<td align='right'>".number_format($pjtraset)."</td>
								<td align='right'>".number_format($pjtrrp)."</td>
								<td align='right'>".number_format($psl1aset)."</td>
								<td align='right'>".number_format($psl1rp)."</td>
								<td align='right'>".number_format($psl3aset)."</td>
								<td align='right'>".number_format($psl3rp)."</td>
								<td align='right'>".number_format($pkeypointaset)."</td>
								<td align='right'>".number_format($pkeypointrp)."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						";
						
						$hasil0 .= $hasilp . $hasilk;
						$pnk = 0;
						$pnb = 0;
						$prab = 0;
						$pjtmaset = 0;
						$pjtmrp = 0;
						$pgdaset = 0;
						$pgdrp = 0;
						$pjtraset = 0;
						$pjtrrp = 0;
						$psl1aset = 0;
						$psl1rp = 0;
						$psl3aset = 0;
						$psl3rp = 0;
						$pkeypointaset = 0;
						$pkeypointrp = 0;
						$hasilk = "";
					}

					$hasilp = "
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td>$row[pos1]</td>
							<td>$row[namapos]</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td align='right'></td>
							<td></td>";
							
							$npos = $row["nilai1"];
							//if($dummy!=$row["noskk"])
							$npost = ($dummy==$row["noskk"]? $npost+$row["nilai1"]: $npost);
							$post += $row["nilai1"];
/*							
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td></td>
						</tr>
					";
*/
				}
				
				if($dummy != $row["noskk"]) {
					if($no>0) {
						$hasils .= "
								<td align='right'>".number_format($npost)."</td>
								<td>$nu</td>
								<td align='right'>".number_format($row["jtm"])."</td>
								<td align='right'>".number_format($row["nilaianggaranjtm"])."</td>
								<td align='right'>".number_format($row["gd"])."</td>
								<td align='right'>".number_format($row["nilaianggarangd"])."</td>
								<td align='right'>".number_format($row["jtr"])."</td>
								<td align='right'>".number_format($row["nilaianggaranjtr"])."</td>
								<td align='right'>".number_format($row["sl1"])."</td>
								<td align='right'>".number_format($row["nilaianggaransl1"])."</td>
								<td align='right'>".number_format($row["sl3"])."</td>
								<td align='right'>".number_format($row["nilaianggaransl3"])."</td>
								<td align='right'>".number_format($row["keypoint"])."</td>
								<td align='right'>".number_format($row["nilaianggarankp"])."</td>
								<td></td>
								<td align='right'>".(empty($srab)? "" : number_format($srab))."</td>
								<td></td>
								<td align='right'>$row[nodokumen]</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td align='right'>".number_format($snk)."</td>
								<td align='right'>".number_format($snb)."</td>
								<td align='right'>".number_format($disb-$snk)."</td>
								<td align='right'>".number_format($snk-$snb)."</td>
								<td align='right'>".number_format($sjtmaset)."</td>
								<td align='right'>".number_format($sjtmrp)."</td>
								<td align='right'>".number_format($sgdaset)."</td>
								<td align='right'>".number_format($sgdrp)."</td>
								<td align='right'>".number_format($sjtraset)."</td>
								<td align='right'>".number_format($sjtrrp)."</td>
								<td align='right'>".number_format($ssl1aset)."</td>
								<td align='right'>".number_format($ssl1rp)."</td>
								<td align='right'>".number_format($ssl3aset)."</td>
								<td align='right'>".number_format($ssl3rp)."</td>
								<td align='right'>".number_format($skeypointaset)."</td>
								<td align='right'>".number_format($skeypointrp)."</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						";
						
						//$hasil .= $hasils . $hasil0;
						echo $hasils . $hasil0;
						
						//$hasil = "";
						$hasil0 = "";
						$snk = 0;
						$snb = 0;
						$srab = 0;
						$sjtmaset = 0;
						$sjtmrp = 0;
						$sgdaset = 0;
						$sgdrp = 0;
						$sjtraset = 0;
						$sjtrrp = 0;
						$ssl1aset = 0;
						$ssl1rp = 0;
						$ssl3aset = 0;
						$ssl3rp = 0;
						$skeypointaset = 0;
						$skeypointrp = 0;
					}

					$no++;
					$npost = $row["nilai1"];
					$nu = $row["namaunit"];
					$disb = $row["disburse"];
					
					$angt += $row["anggaran"];
					$disbt += $row["disburse"];
					$wbst += $row["wbs"];

					$ang_jtm_assett += $row["jtm"];
					$ang_gd_assett += $row["gd"];
					$ang_jtr_assett += $row["jtr"];
					$ang_sl1_assett += $row["sl1"];
					$ang_sl3_assett += $row["sl3"];
					$ang_kp_assett += $row["keypoint"];

					$ang_jtm_rpt += $row["nilaianggaranjtm"];
					$ang_gd_rpt += $row["nilaianggarangd"];
					$ang_jtr_rpt += $row["nilaianggaranjtr"];
					$ang_sl1_rpt += $row["nilaianggaransl1"];
					$ang_sl3_rpt += $row["nilaianggaransl3"];
					$ang_kp_rpt += $row["nilaianggarankp"];
					
					$hasils = "
						<tr>
							<td>$no</td>
							<td style='white-space: nowrap;'>" . $row["nomorwbs"] . ($row["nomorwbs"]!="" && $row["nomorprk"]!=""? " <br /> ": "") . $row["nomorprk"] . ($row["nomorscore"]==""? "": "<br /> $row[nomorscore]") . "</td>
							<td>$row[nomornota]</td>
							<td>$row[noskk]</td>
							<td>$row[uraians]</td>
							<td>$row[tanggalskki]</td>
							<td align='right'>".number_format($row["anggaran"])."</td>
							<td align='right'>".number_format($row["disburse"])."</td>
							<td align='right'>".$row["wbs"]."</td>
							<td></td>
							<td></td>";
/*							
							<td align='right'>".number_format($npost)."</td>
							<td>$row[namaunit]</td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td align='right'>".number_format($npos$row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td align='right'>".number_format($row["nilai1"])."</td>
							<td>$row[inputdt]</td>
						</tr>
					";
*/
				}
				
				
				$dummypos = $row["pos1"];
				$dummy = $row["noskk"];
				
				$snk += $row["kontrak"];
				$snb += $row["bayar"];
				$srab += $row["nilai_rp"];
				$sjtmaset += $row["jtmaset"];
				$sjtmrp += $row["jtmrp"];
				$sgdaset += $row["gdaset"];
				$sgdrp += $row["gdrp"];
				$sjtraset += $row["jtraset"];
				$sjtrrp += $row["jtrrp"];
				$ssl1aset += $row["sl1aset"];
				$ssl1rp += $row["sl1rp"];
				$ssl3aset += $row["sl3aset"];
				$ssl3rp += $row["sl3rp"];
				$skeypointaset += $row["keypointaset"];
				$skeypointrp += $row["keypointrp"];
				
				$pnk += $row["kontrak"];
				$pnb += $row["bayar"];
				$prab += $row["nilai_rp"];
				$pjtmaset += $row["jtmaset"];
				$pjtmrp += $row["jtmrp"];
				$pgdaset += $row["gdaset"];
				$pgdrp += $row["gdrp"];
				$pjtraset += $row["jtraset"];
				$pjtrrp += $row["jtrrp"];
				$psl1aset += $row["sl1aset"];
				$psl1rp += $row["sl1rp"];
				$psl3aset += $row["sl3aset"];
				$psl3rp += $row["sl3rp"];
				$pkeypointaset += $row["keypointaset"];
				$pkeypointrp += $row["keypointrp"];
				
				$kont += $row["kontrak"];
				$bayt += $row["bayar"];
				
				$hasilk .= "
					<tr>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td align='right'></td>
						<td align='right'></td>
						<td align='right'></td>
						<td></td>
						<td></td>
						<td align='right'></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>$row[no_rab]</td>
						<td align='right'>".(empty($row["no_rab"]) ? "" : number_format($row["nilai_rp"]))."</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[nodokumen]</td>
						<td>$row[vendor]</td>
						<td>$row[uraiank]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["kontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td></td>
						<td></td>
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
						<td>$row[inputdt]</td>
						<td>$row[app_pel]</td>
						<td>$row[app_ang]</td>
						<td>$row[app_keu]</td>
						<td>". 
							($row["nomorkontrak"]==""? "": 
								($_SESSION["org"]==""? ($row["signed"]==""? "": "signed"): ($row["signed"]==""? "": "Signed"))
							) .
						"</td>						
					</tr>
				";
			}
			mysql_free_result($result);
			
			$hasilp .= "
					<td align='right'>".(empty($prab)? "" : number_format($prab))."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>".number_format($pnk)."</td>
					<td align='right'>".number_format($pnb)."</td>
					<td align='right'>".number_format($npos-$pnk)."</td>
					<td align='right'>".number_format($pnk-$pnb)."</td>
					<td align='right'>".number_format($pjtmaset)."</td>
					<td align='right'>".number_format($pjtmrp)."</td>
					<td align='right'>".number_format($pgdaset)."</td>
					<td align='right'>".number_format($pgdrp)."</td>
					<td align='right'>".number_format($pjtraset)."</td>
					<td align='right'>".number_format($pjtrrp)."</td>
					<td align='right'>".number_format($psl1aset)."</td>
					<td align='right'>".number_format($psl1rp)."</td>
					<td align='right'>".number_format($psl3aset)."</td>
					<td align='right'>".number_format($psl3rp)."</td>
					<td align='right'>".number_format($pkeypointaset)."</td>
					<td align='right'>".number_format($pkeypointrp)."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			";
			
			$hasil0 .= $hasilp . $hasilk;
			$pnk = 0;
			$pnb = 0;
			$prab = 0;
			$pjtmaset = 0;
			$pjtmrp = 0;
			$pgdaset = 0;
			$pgdrp = 0;
			$pjtraset = 0;
			$pjtrrp = 0;
			$psl1aset = 0;
			$psl1rp = 0;
			$psl3aset = 0;
			$psl3rp = 0;
			$pkeypointaset = 0;
			$pkeypointrp = 0;
			$hasilk = "";

			$hasils .= "
					<td align='right'>".number_format($npost)."</td>
					<td>$nu</td>
					<td align='right'>".number_format($row["jtm"])."</td>
					<td align='right'>".number_format($row["nilaianggaranjtm"])."</td>
					<td align='right'>".number_format($row["gd"])."</td>
					<td align='right'>".number_format($row["nilaianggarangd"])."</td>
					<td align='right'>".number_format($row["jtr"])."</td>
					<td align='right'>".number_format($row["nilaianggaranjtr"])."</td>
					<td align='right'>".number_format($row["sl1"])."</td>
					<td align='right'>".number_format($row["nilaianggaransl1"])."</td>
					<td align='right'>".number_format($row["sl3"])."</td>
					<td align='right'>".number_format($row["nilaianggaransl3"])."</td>
					<td align='right'>".number_format($row["keypoint"])."</td>
					<td align='right'>".number_format($row["nilaianggarankp"])."</td>
					<td></td>
					<td align='right'>".(empty($srab)? "" : number_format($srab))."</td>
					<td></td>
					<td align='right'>$row[nodokumen]</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>".number_format($snk)."</td>
					<td align='right'>".number_format($snb)."</td>
					<td align='right'>".number_format($disb-$snk)."</td>
					<td align='right'>".number_format($snk-$snb)."</td>
					<td align='right'>".number_format($sjtmaset)."</td>
					<td align='right'>".number_format($sjtmrp)."</td>
					<td align='right'>".number_format($sgdaset)."</td>
					<td align='right'>".number_format($sgdrp)."</td>
					<td align='right'>".number_format($sjtraset)."</td>
					<td align='right'>".number_format($sjtrrp)."</td>
					<td align='right'>".number_format($ssl1aset)."</td>
					<td align='right'>".number_format($ssl1rp)."</td>
					<td align='right'>".number_format($ssl3aset)."</td>
					<td align='right'>".number_format($ssl3rp)."</td>
					<td align='right'>".number_format($skeypointaset)."</td>
					<td align='right'>".number_format($skeypointrp)."</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			";
			
			//$hasil .= $hasils . $hasil0;
			echo $hasils . $hasil0;
			
			//$hasil = "";
			$hasil0 = "";
			$snk = 0;
			$snb = 0;
			$srab = 0;
			$sjtmaset = 0;
			$sjtmrp = 0;
			$sgdaset = 0;
			$sgdrp = 0;
			$sjtraset = 0;
			$sjtrrp = 0;
			$ssl1aset = 0;
			$ssl1rp = 0;
			$ssl3aset = 0;
			$ssl3rp = 0;
			$skeypointaset = 0;
			$skeypointrp = 0;
			
			//$hasil .= "</table>";
			echo "
				<tr>
					<td colspan='6'>Total</td>
					<td align='right'>" . number_format($angt) . "</td>
					<td align='right'>" . number_format($disbt) . "</td>
					<td align='right'>" . number_format($wbst) . "</td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($post) . "</td>
					<td></td>
					<td align='right'>" . number_format($ang_jtm_assett) . "</td>
					<td align='right'>" . number_format($ang_jtm_rpt) . "</td>
					<td align='right'>" . number_format($ang_gd_assett) . "</td>
					<td align='right'>" . number_format($ang_gd_rpt) . "</td>
					<td align='right'>" . number_format($ang_jtr_assett) . "</td>
					<td align='right'>" . number_format($ang_jtr_rpt) . "</td>
					<td align='right'>" . number_format($ang_sl1_assett) . "</td>
					<td align='right'>" . number_format($ang_sl1_rpt) . "</td>
					<td align='right'>" . number_format($ang_sl3_assett) . "</td>
					<td align='right'>" . number_format($ang_sl3_rpt) . "</td>
					<td align='right'>" . number_format($ang_kp_assett) . "</td>
					<td align='right'>" . number_format($ang_kp_rpt) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'>" . number_format($kont) . "</td>
					<td align='right'>" . number_format($bayt) . "</td>
					<td align='right'>" . number_format($disbt-$kont) . "</td>
					<td align='right'>" . number_format($kont-$bayt) . "</td>
					<td align='right'>" . number_format($rea_jtm_assett) . "</td>
					<td align='right'>" . number_format($rea_jtm_rpt) . "</td>
					<td align='right'>" . number_format($rea_gd_assett) . "</td>
					<td align='right'>" . number_format($rea_gd_rpt) . "</td>
					<td align='right'>" . number_format($rea_jtr_assett) . "</td>
					<td align='right'>" . number_format($rea_jtr_rpt) . "</td>
					<td align='right'>" . number_format($rea_sl1_assett) . "</td>
					<td align='right'>" . number_format($rea_sl1_rpt) . "</td>
					<td align='right'>" . number_format($rea_sl3_assett) . "</td>
					<td align='right'>" . number_format($rea_sl3_rpt) . "</td>
					<td align='right'>" . number_format($rea_kp_assett) . "</td>
					<td align='right'>" . number_format($rea_kp_rpt) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>";
			echo "</table>";
	}
	mysql_close($kon);
	
	//echo $hasil;
?>