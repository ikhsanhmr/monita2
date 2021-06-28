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
		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
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

			$sql = "
				SELECT 
					skkoi, n.nomornota, nipuser, g.id userid, 
					pelaksana, b.namaunit, pos1, nilai1, namapos,
				--	nomorwbs, nomorcostcenter, nomorskko noskk, s.uraian uraians, tanggalskko, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs, 
				--	nomorskko skk, tanggalskko tglskk,
					skk, tglskk, nilaidisburse, nomorscore,
					inputdt, nomorkontrak, vendor, k.uraian uraiank, tglawal, tglakhir, nilaikontrak, bayar, nomorprk 
				FROM notadinas n
				LEFT JOIN bidang g ON n.nipuser = g.nick 
				LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
				LEFT JOIN bidang b ON d.pelaksana = b.id  
				
				LEFT JOIN (
					SELECT nomorskko skk, tanggalskko tglskk, '' as nomorprk, nilaidisburse, '' as nomorscore FROM skkoterbit UNION
					SELECT nomorskki skk, tanggalskki tglskk, nomorprk, nilaidisburse, nomorscore FROM skkiterbit 
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
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				$kontrak += $row["nilaikontrak"];
				$bayar += $row["bayar"];
				
				// if($o == 'SKKI'){
					$parm .= "
						<tr>
							<td>$no</td>
							<td>$row[skkoi]</td>
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
							<td>$row[skk]</td>
							<td>$row[tglskk]</td>
							<td>$row[nilaidisburse]</td>
							<td>$row[namaunit]</td>
							<td>$row[nomorprk]</td>
							<td>$row[nomorscore]</td>
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
						<th rowspan='2'>No</th>
						<th rowspan='2'>Jenis</th>
						<th colspan='10'>Kontrak</th>
						<th colspan='6'>SKK</th>
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
						<th>Tgl Input</th>
						<th>No SKK</th>
						<th>Tgl</th>
						<th>Nilai</th>
						<th>Pelaksana</th>
						<th>No PRK</th>
						<th>Basket / Fungsi</th>
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
						<td colspan='7'>Total</td>
						<td align='right'>".number_format($kontrak)."</td>
						<td align='right'>".number_format($bayar)."</td>
						<td align='right'>".number_format($kontrak-$bayar)."</td>
						<td align='right'>".number_format(@($bayar/$kontrak)*100,2)."</td>
						<td colspan='7'></td>
					</tr>
				</table>";
		}
		$mysqli->close();($kon);
	?>
</html>
