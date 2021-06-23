<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="..css/jquery.dataTables.min.css">
	
	<script type="text/javascript">
		function viewk() {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var o = document.getElementById("o").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI("index.php?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&o="+o+"&v=1");
			window.open(url, "_self");
		}
		
		function tambah(p) {
			url = (p==undefined? encodeURI("kontrak0.php"): encodeURI("kontrak0.php?kon=" + p));
			//alert(url);
			window.open(url, "_self");
		}
		
		function edit(p) {
			url = encodeURI("kontrak.php?kon=" + p);
			//alert(url);
			window.open(url, "_self");
		}
		
		function hapus(k, id) {
			if(confirm("Hapus Kontrak " + k + " ?")) {
				var url = "hapus.php";
				var parm= encodeURI("k=" + id.trim());
				//alert(url + "?" + parm);
				//window.open(url+"?"+parm, "_blank");
	
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				} else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						//alert(xmlhttp.responseText);
						if(xmlhttp.responseText=="1") {
							//window.open(".", "_self");
							viewk();
						}
						//document.getElementById("showhere").innerHTML=xmlhttp.responseText;
					}
				}
				
				xmlhttp.open("POST", url, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(parm);	
			}
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
		$result = mysql_query($sql);
		
		$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
		$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
		$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
		$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
		$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysql_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
/*		
		$parm .= ($p1==""? "": " and SUBSTR(tglawal, 1, 7) >= '$p1'");
		$parm .= ($p2==""? "": " and SUBSTR(tglakhir, 1, 7) <= '$p2'");

		$parm .= ($p1==""? "": " and YEAR(tglawal) = " . substr($p1,0,4) . " AND MONTH(tglawal) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(tglakhir) = " . substr($p2,0,4) . " AND MONTH(tglakhir) >= " . substr($p2,-2));

		$parm .= ($p1==""? "": " and CONCAT(YEAR(tglawal), '-', (CASE WHEN MONTH(tglawal)<10 THEN '0' ELSE '' END), MONTH(tglawal)) >= '$p1'");
		$parm .= ($p2==""? "": " and CONCAT(YEAR(tglakhir), '-', (CASE WHEN MONTH(tglakhir)<10 THEN '0' ELSE '' END), MONTH(tglakhir)) <= '$p2'");
*/		
		$parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4) . " AND MONTH(inputdt) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(inputdt) = " . substr($p2,0,4) . " AND MONTH(inputdt) <= " . substr($p2,-2));
		
		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and skk = '$k0'");
		$parm .= ($o==""? "": " and skkoi = '$o'");
		
		echo "
			<h2>Kontrak</h2>
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
					<td colspan='3' align='right'><input type='button' value='Ok' onclick='viewk()'></td>
				</tr>
			</table>		
			<a href='#' onClick='tambah()'>(+) Tambah Kontrak</a>
		";
		
		if($v!="") {
			$sql = "
				SELECT 
					skkoi, n.nomornota nd, nip, nipuser, pelaksana, g.id nick, pos1, namapos, b.namaunit, skk, oi.uraian oiuraian, 
					nomorkontrak, k.pos kpos, k.uraian kuraian, vendor, tglawal, tglakhir, nilaikontrak, bayar, k.file_path, SIGNED sgd, k.kid
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
				LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) bayar FROM realisasibayar GROUP BY nokontrak) r ON k.nomorkontrak = r.nokontrak
				WHERE n.progress = 7 
					AND COALESCE(oi.skk, '') != '' AND NOT nomorkontrak IS NULL 
					$parm
				ORDER BY nd, skk, pos1, nomorkontrak";
			//echo $parm;
			//echo $sql;
			
			$no = 0;
			$parm = "";
			$dummy = "";
			$dummynd = "";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$no++;
				$parm .= "
					<tr>
						<td>$no</td>
						<td>$row[skkoi]</td>
						<td>" . ($dummynd==$row["nd"]? "": $row["nd"]) . "</td>
						<td>" . ($dummy==$row["skk"]? "": $row["skk"]) . "</td>
						<td>" . ($dummy==$row["skk"]? "": $row["oiuraian"]) . "</td>
						<td>$row[pos1]</td>
						<td>$row[namapos]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[kuraian]</td>
						<td>$row[vendor]</td>
						<td>$row[tglawal]</td>
						<td>$row[tglakhir]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td>"
						. ($dummy==$row["skk"]? "": "<a href='#' onClick='tambah(\"$row[nomorkontrak]\")'>Tambah</a><br>") .
						//($row["skkoi"]=="SKKI"? 
							($row["sgd"]==""? "<a href='#' onClick='edit(\"$row[nomorkontrak]\")'>Edit</a><br><a href='#' onClick='hapus(\"$row[nomorkontrak]\", \"$row[kid]\")'>Hapus</a>": "<strong>SIGNED</strong>")//: 
							//($row["bayar"]==""? "<a href='#' onClick='edit(\"$row[nomorkontrak]\")'>Edit</a><br><a href='#' onClick='hapus(\"$row[nomorkontrak]\", \"$row[kid]\")'>Hapus</a>": "")

						//)
						.
						($row["file_path"]==""? "": "<br><a href='../$row[file_path]' target='_blank'>Download</a>") .
						"</td>
					</tr>";
					
				$dummy = $row["skk"];
				$dummynd = $row["nd"];
			}
			mysql_free_result($result);
			
			echo "
				<table id='dataTables' class='display' cellspacing='0' width='100%'>
				<thead>	
					<tr>
						<th rowspan='2'>No</th>
						<th rowspan='2'>Jenis</th>
						<th rowspan='2'>Nota Dinas</th>
						<th rowspan='2'>No SKK</th>
						<th rowspan='2'>Uraian</th>
						<th rowspan='2' colspan='2'>Pos</th>
						<th colspan='6'>Kontrak</th>
						<th rowspan='2'>Proses</th>
					</tr>
					<tr>
						<th>Nomor</th>
						<th>Uraian</th>
						<th>Vendor</th>
						<th>Tgl Awal</th>
						<th>Tgl Akhir</th>
						<th>Nilai</th>
					</tr>
				</thead>
					$parm
			</table>";
		}
		mysql_close($kon);
	?>
	<script src="../js/jquery-1.12.0.min.js"></script>
	<script src="../js/jquery.dataTables.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#dataTables').DataTable();
	} );
	</script>
	</body>
</html>
