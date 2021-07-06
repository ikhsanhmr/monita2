<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function excel() {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var s = document.getElementById("skk").value;
			var k = document.getElementById("kon").value;
			var o = document.getElementById("o").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}

			var url = "excel.php?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&s="+s+"&k="+k+"&o="+o+"&v=1";
			url = encodeURI(url);
			window.open(url, "_self");
		}
		

		function viewk(e, n, num) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var s = document.getElementById("skk").value;
			var k = document.getElementById("kon").value;
			var o = document.getElementById("o").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}

			var url = "report.php?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&s="+s+"&k="+k+"&o="+o+"&v=1";
			if(e!=undefined) {
				if(e==1) {
					var d = document.getElementById("d"+num).value;
					var r = document.getElementById("v"+num).value;
					var b = document.getElementById("b"+num).value;
					var f = document.getElementById("f"+num).value;
					url += "&e="+e+"&n="+n+"&d="+d+"&r="+r+"&b="+b+"&f="+f;
				} else {
					url += "&e="+e+"&n="+n;
				}
			}
			
			url = encodeURI(url);
			
			//alert(url);
			window.open(url, "_self");
		}
		
		function edit(n, num) {
//		alert(n + " " + num);
//		n = n.toString();
//		num = num.toString();
/*
			var dummy;
			
			dummy = "d" + num;
			document.getElementById("t"+num).innerHTML = "<br><input type='date' name='" + dummy + "' id='" + dummy + "' value='" +  + "'>";
			dummy = "v" + num;
			document.getElementById("n"+num).innerHTML = "<br><input type='number' name='" + dummy + "' id='" + dummy + "'>";
*/
			document.getElementById("t"+num).style.display = "block";
			document.getElementById("n"+num).style.display = "block";
			document.getElementById("a"+num).style.display = "block";
			document.getElementById("c"+num).style.display = "block";
			document.getElementById("e"+num).innerHTML = "<a href='#' onClick='goedit(0, \"c" + n + "\", " + num + ")'><img src='../images/cross.png' title='Batal'></img>";
			document.getElementById("h"+num).innerHTML = "<a href='#' onClick='goedit(1, \"c" + n + "\", " + num + ")'><img src='../images/check.png' title='Simpan'></img>";
		}
		
		function goedit(s, n, num) {
//			alert(s + " " + n + " " + num);
			if(s==0) {
/*
				var dummy = "d" + num;				
				document.getElementById("t"+num).innerHTML = "";
				dummy = "v" + num;
				document.getElementById("n"+num).innerHTML = "";
*/
//			alert("2:" + " " + n + " " + num);
			document.getElementById("t"+num).style.display = "none";
			document.getElementById("n"+num).style.display = "none";
			document.getElementById("a"+num).style.display = "none";
			document.getElementById("c"+num).style.display = "none";

				document.getElementById("e"+num).innerHTML = "<a href='#' onClick='edit(" +  n + ", " + num + ")'><img src='../images/pencil.gif' title='Edit'></img>";
				document.getElementById("h"+num).innerHTML = "<a href='#' onClick='hapus(" +  n + ", " + num + ")'><img src='../images/cross.png' title='Hapus'></img>";
			} else {
//				alert("3:" + " " + n + " " + num);
				viewk(1, n, num);
			}
		}
		
		function hapus(n) {
			//alert(n);
			if(confirm("Hapus data bayar?")) {
				viewk(2, n);
			}
		}
	</script>
	
	<?php
		error_reporting(0);  session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		$editor = (($_SESSION["roleid"]=="01" || $_SESSION["roleid"]=="04" || $_SESSION["roleid"]=="05" || $_SESSION["roleid"]=="10")? true: false);
		
		require_once "../config/koneksi.php";
		$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$p2 = ($p2==""? $p1: $p2);
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$s0 = isset($_REQUEST["s"])? $_REQUEST["s"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
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
//		$parm .= ($p1==""? "": " and SUBSTR(tglbayar, 1, 7) >= '$p1'"); //
//		$parm .= ($p2==""? "": " and SUBSTR(tglbayar, 1, 7) <= '$p2'"); //
		$parm .= ($p1==""? "": " and YEAR(tglbayar) = " . substr($p1,0,4) . " AND MONTH(tglbayar) >= " . substr($p1,-2));		
		$parm .= ($p1==""? "": " and YEAR(tglbayar) = " . substr($p2,0,4) . " AND MONTH(tglbayar) <= " . substr($p2,-2));
		
		$parm .= ($p1==""? "": " and YEAR(tglskk) = " . substr($p1,0,4) . " AND MONTH(tglskk) >= " . substr($p1,-2));		
		$parm .= ($p1==""? "": " and YEAR(tglskk) = " . substr($p2,0,4) . " AND MONTH(tglskk) <= " . substr($p2,-2));				
		$parm .= ($b0==""? "": " and g.id = '$b0'");  //
		$parm .= ($p0==""? "": " and pelaksana = '$p0'"); //
		$parm .= ($s0==""? "": " and skk = '$s0'"); // 
		$parm .= ($k0==""? "": " and k.nomorkontrak = '$k0'"); //
		$parm .= ($o==""? "": " and skkoi = '$o'"); //
		//echo $parm;
		
		echo "
			<h2>Laporan Realisasi Bayar</h2>
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
					<td><input type='text' name='skk' id='skk' size='49' value='$s0'></td>
				</tr>
				<tr>
					<th>No Kontrak</th>
					<td>:</td>
					<td><input type='text' name='kon' id='kon' size='49' value='$k0'></td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='Ok' onclick='viewk()'>
						<input type='button' value='Excel' onclick='excel()'>
					</td>
				</tr>
			</table>		
		";
		
		if($v!="") {
			//echo "E: $_REQUEST[e]<br>";
			
			if(isset($_REQUEST["e"])) {
				//echo "ada<br>";
				$sql = (
					$_REQUEST["e"]=="1"? 
					"update realisasibayar set nilaibayar = '$_REQUEST[r]', tglbayar = '$_REQUEST[d]', nodokrep = '$_REQUEST[b]', pmn = '$_REQUEST[f]' where bayarid = '" . substr($_REQUEST["n"],1,strlen($_REQUEST["n"])-1) . "'": 
					"delete from realisasibayar where bayarid = '$_REQUEST[n]'"
				);
				//echo "$sql<br>";
				mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			}
			
			// $sql = "
			// 	SELECT 
			// 		n.skkoi skkoi, nipuser, g.id iduser, pelaksana, b.namaunit, skk, pos1, namapos, k.nomorkontrak, k.uraian uraiank, k.tglawal tglmulai, k.tglakhir tglakhir, k.nilaikontrak nilaikontrak, k.vendor vendor, k.nodokumen nodokumen, nilaibayar, tglbayar, bayarid, aset.jtmaset jtma, aset.jtraset jtra, aset.gdaset gda, o.nomorprk prk, o.nomorscore score, o.nilaitunai nilaiskk, o.tglskk tanggalterbit, o.posinduk posinduk, unit.namaunit nmunit, r.pmn pmn, r.nodokrep, r.keterangan
			// 	FROM (
			// 		SELECT nomorskko skk, '' AS nomorscore, nilaitunai, tanggalskko as tglskk, posinduk, '' AS nomorprk, nip FROM skkoterbit 
			// 		UNION 
			// 		SELECT nomorskki skk, nomorscore, nilaitunai, tanggalskki as tglskk, posinduk, nomorprk, nip FROM skkiterbit
			// 	) o
				 
			// 	LEFT JOIN notadinas_detail d ON o.skk = d.noskk 
			// 	LEFT JOIN user u ON o.nip = u.nip
			// 	LEFT JOIN unit unit ON u.kdunit = unit.kdunit
			// 	LEFT JOIN notadinas n ON d.nomornota = n.nomornota 
			// 	LEFT JOIN bidang g ON n.nipuser = g.nick 
			// 	LEFT JOIN bidang b ON d.pelaksana = b.id 
			// 	LEFT JOIN (
			// 		SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
			// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
			// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
			// 		SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			// 	) p ON d.pos1 = p.pos
			// 	LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi and d.pos1 = k.pos
			// 	LEFT JOIN realisasibayar r ON k.nomorkontrak = r.nokontrak
			// 	LEFT JOIN asetpdp aset ON r.nokontrak = aset.nomorkontrak
			// 	WHERE NOT tglbayar IS NULL
			// 	$parm
			// 	ORDER BY skkoi, skk, pos1, pelaksana, k.nomorkontrak, tglbayar";

			$sql = "
				SELECT 	n.skkoi skkoi, nipuser, g.id iduser, pelaksana, b.namaunit, skk, pos1, namapos, k.nomorkontrak, 
						k.uraian uraiank, k.tglawal tglmulai, k.tglakhir tglakhir, k.nilaikontrak nilaikontrak, 
						k.vendor vendor, k.nodokumen nodokumen, nilaibayar, tglbayar, bayarid, aset.jtmaset jtma, 
						aset.jtraset jtra, aset.gdaset gda, o.nomorprk prk, o.nomorscore score, o.nilaitunai nilaiskk, 
						o.tglskk tanggalterbit, o.posinduk posinduk, unit.namaunit nmunit, r.pmn pmn, r.nodokrep, 
						r.keterangan
				FROM 	(
							SELECT 	nomorskko skk, '' AS nomorscore, nilaitunai, tanggalskko as tglskk, posinduk, 
									'' AS nomorprk, nip 
							FROM skkoterbit 
							UNION 
							SELECT 	nomorskki skk, nomorscore, nilaitunai, tanggalskki as tglskk, posinduk, nomorprk, 
									nip 
							FROM skkiterbit
						) o LEFT JOIN 
						notadinas_detail d ON o.skk = d.noskk LEFT JOIN 
						user u ON o.nip = u.nip LEFT JOIN 
						unit unit ON u.kdunit = unit.kdunit LEFT JOIN 
						notadinas n ON d.nomornota = n.nomornota  LEFT JOIN 
						bidang g ON n.nipuser = g.nick LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						(
							SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos LEFT JOIN 
						kontrak k ON d.noskk = k.nomorskkoi and d.pos1 = k.pos LEFT JOIN 
						realisasibayar r ON k.nomorkontrak = r.nokontrak LEFT JOIN 
						asetpdp aset ON r.nokontrak = aset.nomorkontrak 
				WHERE NOT tglbayar IS NULL
				$parm
				ORDER BY skkoi, skk, pos1, pelaksana, k.nomorkontrak, tglbayar";
			//echo $sql;
			
			$no = 0;
			$parm = "";
			$dummy = "";
			$total = 0;
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				$parm .= "
					<tr>
						<td>$no</td>
						<td>" . ($dummy==$row["skk"]? "": $row["prk"]) . "</td>
						<td>" . (empty($row["score"])? $row["namapos"]: $row["score"]) . "</td>
						<td>" . ($dummy==$row["skk"]? "": $row["skk"]) . "</td>
						<td>" . ($dummy==$row["skk"]? "": number_format($row["nilaiskk"])) . "</td>
						<td>" . ($dummy==$row["skk"]? "": $row['tanggalterbit']) . "</td>
						<td>" . ($dummy==$row["skk"]? "": $row['namaunit']) . "</td>
						<td>" . ($dummy==$row["skk"]? "": ($row['posinduk']=="62.1"?"Lanjutan":"Murni")) . "</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[uraiank]</td>
						<td>$row[tglmulai]</td>
						<td>$row[tglakhir]</td>
						<td>".number_format($row['nilaikontrak'])."</td>
						<td>$row[vendor]</td>
						<td>$row[nodokrep]
							<div id='a$no' style='display:none'><input type='text' name='b$no' id='b$no' value='$row[nodokrep]'></div>
						</td>
						<td>$row[pmn]
							<div id='c$no' style='display:none'>
								<input type='text' name='f$no' id='f$no' value='$row[pmn]'>
							</div>
						</td>
						<td>$row[tglbayar]
							<div id='t$no' style='display:none'>
								<input type='date' name='d$no' id='d$no' value='$row[tglbayar]'>
							</div>
						</td>
						<td align='right'>" . number_format($row["nilaibayar"],0) . "
							<div id='n$no' style='display:none'>
								<input type='number' name='v$no' id='v$no' value='$row[nilaibayar]'>
							</div>
						</td>
						<td>$row[keterangan]</td>
						". (
							$editor? "
							<td><div id='e$no'><a href='#' onClick='edit(\"$row[bayarid]\", $no)'><img src='../images/pencil.gif' title='Edit'></img></a></div></td>
							<td><div id='h$no'><a href='#' onClick='hapus(\"$row[bayarid]\")'><img src='../images/cross.png' title='Hapus'></img></a></div></td>
							": ""
						) ."
					</tr>";
					
				$dummy = $row["skk"];
				$total += $row["nilaibayar"];
			}
			mysqli_free_result($result);
			
			echo "
				<table>
					<tr>
						<th rowspan='2'>No</th>
						<th colspan='2'>Program Rencana Kerja</th>
						<th rowspan='2'>No SKK</th>
						<th rowspan='2'>Nilai SKK</th>
						<th rowspan='2'>Tanggal Terbit</th>
						<th rowspan='2'>Unit/Pelaksana</th>
						<th rowspan='2'>Jenis</th>
						<th colspan='8'>Kontrak</th>
						<th colspan='3'>Realisasi Bayar</th>". ($editor? "<th colspan='2' rowspan='2'>Action</th>": "") ."
					</tr>
					<tr>
						<th>No. PRK</th>
						<th>Sasaran/Basket</th>
						<th>Nomor</th>
						<th>Uraian Kegiatan</th>
						<th>Tgl. Mulai</th>
						<th>Tgl. Akhir</th>
						<th>Nilai Kontrak</th>
						<th>Vendor</th>
						<th>No. SAP</th>
						<th>PMN</th>
						<th>Tanggal</th>
						<th>Nilai</th>
						<th>Keterangan</th>
					</tr>
					$parm
					<tr>
						<td colspan='17'>Total</td>
						<td align='right'>" . number_format($total) ."</td>". ($editor? "<td colspan='2'></td>": "") ."
						<td></td>
					</tr>
			</table>";
		}
		$mysqli->close();($kon);
	?>
</html>
