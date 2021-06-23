<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<script type="text/javascript">
		function onlyme() {
			var o = document.getElementById("o").value;
			var parm = "ai.php?" + "o=" +  o;
			//alert(parm);
			window.open(parm, "_self");
		}
		
		function signed(c, s) {
			var url = encodeURI("signed.php?" + "c=" + c + "&s=" + s);
			window.open(url, "_self");
//			var parm = encodeURI("c=" + c + "&s=" + s);
//			alert(url + parm);
/*
			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			} else {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					alert(xmlhttp.responseText);
					//document.getElementById("showhere").innerHTML=xmlhttp.responseText;
					//window.open("ai.php", "_self");
				}
			}
			
			xmlhttp.open("POST", url, true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send(parm);		
*/
		}
	</script>
</head>

<body>
<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];
	
	$o = (isset($_REQUEST["o"])? $_REQUEST["o"]: "");

	$vo = "";
//	if($adm>=1 && $adm<=3) {
		$vo = "<select name='o' id='o' onchange='onlyme()'><option value=''></option>";

		$sql = "SELECT * FROM bidang WHERE NOT namaunit LIKE '%wilayah%'";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$vo .= "<option value='$row[id]'" . ($row["id"]==$o? " selected": "") .">$row[namaunit]</option>";
		}
		mysql_free_result($result);

		$vo .= "</select>";
//	}
	
	$sql = "
SELECT * FROM (
-- SELECT '' pos, nomorskki, s.uraian, tanggalskki, nilaianggaran, nilaidisburse, '' nilaiwbs, '' nomorkontrak, '' vendor, '' urai, '' tglawal, '' tglakhir, '' nilaikontrak, '' totalbayar, '' nipuser, '' pelaksana, '' namaunit
-- FROM skkiterbit s LEFT JOIN kontrak k ON s.nomorskki = k.nomorskkoi WHERE NOT nomorkontrak IS NULL
-- UNION
SELECT nndb.pos, nomorprk, nomorskki, s.uraian, tanggalskki, nilaianggaran, nilaidisburse, nilaiwbs, nomorkontrak, k.signed, vendor, k.uraian urai, tglawal, tglakhir, nilaikontrak, totalbayar, nipuser, pelaksana, namaunit
FROM skkiterbit s
LEFT JOIN (
	SELECT n.nomornota, pos1 pos, noskk, nipuser, pelaksana, namaunit FROM notadinas n 
	LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
	LEFT JOIN bidang b ON d.pelaksana = b.id
) nndb ON s.nomorskki = nndb.noskk AND s.posinduk = nndb.pos
LEFT JOIN kontrak k ON s.nomorskki = k.nomorskkoi -- AND s.posinduk = k.pos
LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) totalbayar FROM realisasibayar GROUP BY nokontrak) b ON k.nomorkontrak = b.nokontrak
WHERE YEAR(tanggalskki) = " . date("Y") . " AND NOT /*nomorkontrak*/ nomorskki IS NULL) xy"; 
//	echo "sql : $sql<br>";
	

	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or pelaksana='$org')");
	$sql .= $sql1;
	
	if($o!="") {
		$sql = "select * from (" . $sql . ") vo where pelaksana = $o ";
	}
		
	$sql .= " ORDER BY nomorskki, pos";

//	echo "sql : $sql<br>";
	echo "
		<h2>Laporan Monitoring Penyerapaan Anggaran Investasi Tahun " . date("Y") . "<br>Posisi Tanggal : " . date("d-m-Y") . "</h2>
		
		<table border='1'>
			<tr>
				<th rowspan='3' scope='col'>No Urut</th>
				<th colspan='8' scope='col'>SKKI Terbit</th>
				<th colspan='6' scope='col'>Kontrak</th>
				<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
				<th colspan='2' scope='col'>Sisa</th>
				<th rowspan='3' scope='col'>Keterangan</th>
			</tr>
			<tr>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Score</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
				<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana" . /*($adm>=1 && $adm<=3? $vo: "")*/ $vo . "</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Vendor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Mulai</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Selesai</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nilai</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKI (Disburse - Kontrak)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
			</tr>
			<tr>
				<td style='background-color:rgb(127,255,127)'>Anggaran (Rp.)</td>
				<td style='background-color:rgb(127,255,127)'>Disburse (Rp.)</td>
			</tr>";

	$no = 0;
	$ang = 0;
	$dis = 0;
	$wbs = 0;
	$kon = 0;
	$bay = 0;
	$diskon = 0;
	$konbay = 0;
	$dummy = "";
	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		if($dummy!=$row["nomorskki"]) {
			$no++;
			$diskon = $row["nilaidisburse"]; 

			echo "
				<tr>
					<td>" . ($dummy==$row["nomorskki"]? "": $no) . "</td>
					<td>" . ($dummy==$row["nomorskki"]? "": $row["nomorprk"]) . "</td>
					<td>" . ($dummy==$row["nomorskki"]? "": $row["nomorskki"]) . "</td>
					<td>" . ($dummy==$row["nomorskki"]? "": $row["uraian"]) . "</td>
					<td>" . ($dummy==$row["nomorskki"]? "": $row["tanggalskki"]) . "</td>
					<td align='right'>" . ($dummy==$row["nomorskki"]? "": number_format($row["nilaianggaran"],2)) . "</td>
					<td align='right'>" . ($dummy==$row["nomorskki"]? "": number_format($row["nilaidisburse"],2)) . "</td>
					<td align='right'>" . ($dummy==$row["nomorskki"]? "": number_format($row["nilaiwbs"],2)) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'></td>
					<td align='right'><div id='kon$no'></div></td>
					<td align='right'></td>
					<td align='right'></td>
					<td align='right'></td>
					<td></td>
				</tr>";
				
			$dummy = $row["nomorskki"];
			$ang += $row["nilaianggaran"];
			$dis += $row["nilaidisburse"];
			$wbs += $row["nilaiwbs"];
			
			//$bay += $row["totalbayar"];				
			if($no>1) {
				$num = $no - 1;
				echo "<script>document.getElementById('kon$num').innerHTML = '". number_format($nkon,2) . "'</script>";
			}

			$nkon = 0;
			$nbay = 0;
		}
		
		$kon += $row["nilaikontrak"];
		$nkon += $row["nilaikontrak"];
		$bay += $row["totalbayar"];
		$nbay += $row["totalbayar"];
		echo "
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'></td>
				<td align='right'></td>
				<td align='right'></td>
				<td>$row[namaunit]</td>
				<td align='right'>". $row["nomorkontrak"] ."</td>
				<td align='right'>". $row["vendor"] ."</td>
				<td align='right'>". $row["urai"] ."</td>
				<td align='right'>". $row["tglawal"] ."</td>
				<td align='right'>". $row["tglakhir"] ."</td>
				<td align='right'>".number_format($row["nilaikontrak"],2)."</td>
				<td align='right'>".number_format($row["totalbayar"],2)."</td>
				<td align='right'>".number_format($diskon-$row["nilaikontrak"],2)."</td>
				<td align='right'>".number_format($row["nilaikontrak"]-$row["totalbayar"],2)."</td>
				<td>". ($row["nomorkontrak"]==""? "": ($org==""? ($row["signed"]==""? 
							"<a href='#' onclick='signed(\"$row[nomorkontrak]\", 1)'><img src='no.png' width='24' height='24' alt='Signed' title='Signed'></img></a>": 
							"<a href='#' onclick='signed(\"$row[nomorkontrak]\", 0)'><img src='ok.png' width='24' height='24' alt='Unsigned' title='Unsigned'></img></a>"): "")
						) .
				"</td>
			</tr>";
		$diskon -= $row["nilaikontrak"];
	}

		$num = $no;
//		echo "<script>document.getElementById('kon$num').innerHTML = '". number_format($nkon,2) . "'</script>";
//		echo "<script>document.getElementById('bay$num').innerHTML = '". number_format($nbay,2) . "'</script>";
//		echo "<script>document.getElementById('diskon$num').innerHTML = '". number_format($ndis-$nbay,2) . "'</script>"; 
//		echo "<script>document.getElementById('konbay$num').innerHTML = '". number_format($ndis-$nbay,2) . "'</script>"; 	
	
		echo "
			<tr>
				<td colspan='3'>Jumlah</td>
				<td></td>
				<td>$row[tanggalskki]</td>
				<td align='right'>".number_format($ang,2)."</td>
				<td align='right'>".number_format($dis,2)."</td>
				<td align='right'>".number_format($wbs,2)."</td>
				<td>$row[namaunit]</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'>".number_format($kon,2)."</td>
				<td align='right'>".number_format($bay,2)."</td>
				<td align='right'>".number_format($dis-$kon,2)."</td>
				<td align='right'>".number_format($kon-$bay,2)."</td>
				<td></td>
			</tr>";
	
	echo "</table>";
	mysql_free_result($result);
	mysql_close($link);	  
?>
</body>
</html>