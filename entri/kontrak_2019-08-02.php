<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk(me) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var o = document.getElementById("o").value;
			var c = document.getElementById("kon").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI((me==undefined? "kontrak.php": "kontrakxl.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&o="+o+"&c="+c+"&v=1");
			window.open(url, "_self"); 
		}		
	</script>
	
	<?php
		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
		$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysqli_fetch_array($result)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysqli_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
		$parm .= ($p1==""? "": " and SUBSTR(tglskk, 1, 7) >= '$p1'");
		$parm .= ($p2==""? "": " and SUBSTR(tglskk, 1, 7) <= '$p2'");
		// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($b0==""? "": " and (nipuser = '$b0' or b.nick = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and skk = '$k0'");
		$parm .= ($o==""? "": " and skkoi = '$o'");
		$parm .= ($c==""? "": " and nomorkontrak = '$c'");
		
		echo "
			<h2>Data Kontrak Wilayah Sumatera Utara</h2>
			<table>
				<tr>
					<th>Periode (yyyy-mm)</th>
					<td>:</td>
					<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
				</tr>
				<tr>
					<th>Jenis</th>
					<td>:</td>
					<td>
						<select name='o' id='o'>
							<option value=''></option>
							<option value='SKKO'" . ($o=="SKKO"? "selected": "") . ">SKKO</option>
							<option value='SKKI'" . ($o=="SKKI"? "selected": "") . ">SKKI</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Bidang</th>
					<td>:</td>
					<td>$b</td>
				</tr>
				<tr>
					<th>Pelaksana</th>
					<td>:</td>
					<td>$p</td>
				</tr>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='49' value='$k0'></td>
				</tr>
				<tr>
					<th>No Kontrak</th>
					<td>:</td>
					<td><input type='text' name='kon' id='kon' size='49' value='$c'></td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='Ok' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>		
		";
		
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
						pdp_file_path, date(k.tgltagih) as tgltagih, isrutin
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
						asetpdp pdp ON k.nomorkontrak = pdp.nomorkontrak
				WHERE 	d.progress >= 7 AND NOT skk IS NULL AND NOT k.nomorkontrak IS NULL 
				$parm
				ORDER BY skk, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";
			// echo $sql;
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
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
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
				
				// if($o == 'SKKI'){
					$parm .= "
						<tr>
							<td>$no</td>
							<td>$row[skkoi]</td>
							<td>$row[nomorkontrak]</td>
							<td>$row[vendor]</td>
							<td>$row[uraiank]</td>
							<td>".($row["skkoi"] == 'SKKI' ? "" : ($row["isrutin"] == "" ? "-" : ($row["isrutin"] == 0 ? "NON RUTIN" : 'RUTIN')))."</td>
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
				// }else{
					
				// 	$parm .= "
				// 		<tr>
				// 			<td>$no</td>
				// 			<td>$row[skkoi]</td>
				// 			<td>$row[namaunit]</td>
				// 			<td>$row[nomorprk]</td>
				// 			<td>$row[skk]</td>
				// 			<td>$row[nomorkontrak]</td>
				// 			<td>$row[vendor]</td>
				// 			<td>$row[uraiank]</td>
				// 			<td>$row[tglawal]</td>
				// 			<td>$row[tglakhir]</td>
				// 			<td align='right'>".number_format($row["nilaikontrak"])."</td>
				// 			<td align='right'>".number_format($row["bayar"])."</td>
				// 			<td align='right'>".number_format($row["nilaikontrak"]-$row["bayar"])."</td>
				// 			<td align='right'>".number_format((empty($row["nilaikontrak"]) ? 0 : $row["bayar"]/$row["nilaikontrak"]*100),2)."</td>
				// 			<td>$row[inputdt]</td>
				// 		</tr>";
				// 		//min='0' max='$dummy' 
				// }
			}
			mysqli_free_result($result);
			
			echo "
				<table>
			";
			
			// if($o == 'SKKI'){

				echo "
					<tr>
						<th rowspan='3'>No</th>
						<th rowspan='3'>Jenis</th>
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
				";

			// }else{
				
			// 	echo "
			// 		<tr>
			// 			<th rowspan='2'>No</th>
			// 			<th rowspan='2'>Jenis</th>
			// 			<th rowspan='2'>Pelaksana</th>
			// 			<th rowspan='2'>No PRK</th>
			// 			<th rowspan='2'>No SKK</th>
			// 			<th colspan='10'>Kontrak</th>
			// 		</tr>
			// 		<tr>
			// 			<th>Nomor</th>
			// 			<th>Vendor</th>
			// 			<th>Uraian</th>
			// 			<th>Tgl Awal</th>
			// 			<th>Tgl Akhir</th>
			// 			<th>Nilai</th>
			// 			<th>Total Bayar</th>
			// 			<th>Sisa</th>
			// 			<th>Prosentase (%)</th>
			// 			<th>Tgl Input</th>
			// 		</tr>
			// 	";
			// }
			
			echo "
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
		$mysqli->close();($kon);
	?>
</html>
