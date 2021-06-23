<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>
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

	$sql = "
SELECT
	pos, nomorskko, uraian, tanggalskko, nomorkontrak, vendor, urai, tglawal, tglakhir, nilaianggaran, nilaidisburse, nilaiwbs, 
	coalesce(SUM(nilaikontrak),0) nilaikontrak, coalesce(SUM(totalbayar),0) totalbayar
FROM (
	SELECT pos, nomorskko, s.uraian, tanggalskko, nilaianggaran, nilaidisburse, nilaiwbs, nomorkontrak, vendor, k.uraian urai, tglawal, tglakhir, nilaikontrak, totalbayar
	FROM skkoterbit s
	LEFT JOIN kontrak k ON s.nomorskko = k.nomorskkoi
	LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) totalbayar FROM realisasibayar GROUP BY nokontrak) b ON k.nomorkontrak = b.nokontrak
	WHERE YEAR(tanggalskko) = " . date("Y") . " and not nomorkontrak is null 
) sk
GROUP BY pos, nomorskko, uraian, tanggalskko, nomorkontrak, vendor, urai, tglawal, tglakhir";

	$sql = "SELECT rpt.*, namauser, nipuser, unitpelaksana FROM (" . $sql . "
) rpt
LEFT JOIN (
	SELECT n.nomornota, nipuser, nama namauser, noskk, pelaksana, namaunit unitpelaksana, pos1
	FROM notadinas n 
	LEFT JOIN USER u ON n.nipuser = u.nip
	LEFT JOIN (
		SELECT * FROM notadinas_detail d LEFT JOIN bidang b ON d.pelaksana = b.id
	) db ON n.nomornota = db.nomornota
) ndub ON rpt.nomorskko = ndub.noskk AND rpt.pos = ndub.pos1";


	$sql1 = "";
	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama')");
	
	$sql1 .= ($sql1==""? ($parm==""? "": " where $parm"): ($parm==""? "": " and $parm"));
	$sql .= $sql1;
	$sql .= " ORDER BY nomorskko, nomorkontrak";
	
	echo $sql;

	echo "
		<h2>Monitoring Pelaksanaan Anggaran Operasi Tahun " . date("Y") . "<br>Posisi Tanggal : " . date("d-m-Y") . "</h2>
		
		<table border='1'>
		<tr>
			<th rowspan='2' scope='col'>No</th>
			<th rowspan='2' scope='col'>User</th>
			<th rowspan='2' scope='col'>Pelaksana</th>
			<th rowspan='2' scope='col'>No. SKK-O</th>
			<th rowspan='2' scope='col'>Uraian</th>
			<th rowspan='2' scope='col'>Tgl Terbit</th>
			<th colspan='2' scope='col'>Ketetapan</th>
			<th colspan='6' scope='col'>Komitmen Kontrak</th>
			<th rowspan='2' scope='col'>Realisasi Pembayaran (Rp.)</th>
			<th rowspan='2' scope='col'>Tanggal Terima di Anggaran</th>
			<th rowspan='2' scope='col'>Sisa SKKO</th>
		</tr>
		<tr>
			<td>Anggaran</td>
			<td>Disburse</td>
			<td>Nomor</td>
			<td>Vendor</td>
			<td>Uraian Kegiatan</td>
			<td>Tgl Mulai</td>
			<td>Tgl Selesai</td>
			<td>Nilai (Rp.)</td>
		</tr>";


	$dummy = "";
	$no = 0;
	$ang = 0;
	$dis = 0;
	$wbs = 0;
	$kon = 0;
	$bay = 0;

/*	
	$dummyskk = "";
	$dummykon = "";
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$no++;
		$kon += $row["nilaikontrak"];
		$bay += $row["totalbayar"];
		$ang += ($dummyskk==$row["nomorskko"]? 0: $row["nilaianggaran"]);
		$dis += ($dummyskk==$row["nomorskko"]? 0: $row["nilaidisburse"]);
		
		echo "
			<tr>
				<td>$no</td>
				<td>" . ($dummyskk==$row["nomorskko"]? "" : $row["namauser"]) . "</td>
				<td>" . ($dummyskk==$row["nomorskko"]? "" : $row["unitpelaksana"]) . "</td>
				<td>" . ($dummyskk==$row["nomorskko"]? "" : $row["nomorskko"]) . "</td>
				<td>" . ($dummyskk==$row["nomorskko"]? "": $row["uraian"]) . "</td>
				<td>" . ($dummyskk==$row["nomorskko"]? "": $row["tanggalskko"]) . "</td>
				<td align='right'>" . ($dummyskk==$row["nomorskko"]? "": number_format($row["nilaianggaran"],2)) ."</td>
				<td align='right'>" . ($dummyskk==$row["nomorskko"]? "": number_format($row["nilaidisburse"],2)) ."</td>
				<td>" . ($dummyskk==$row["nomorkontrak"]? "" : $row["nomorkontrak"]) . "</td>
				<td>$row[vendor]</td>
				<td>$row[urai]</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
				<td align='right'>".($row["pos"]==""? "<div id='n$no'></div>": number_format($row["nilaikontrak"],2))."</td>
				<td align='right'>".($row["pos"]==""? "<div id='b$no'></div>": number_format($row["totalbayar"],2))."</td>
				<td></td>
				<td align='right'>".($row["pos"]==""? "<div id='s$no'></div>": "")."</td>
			</tr>";
			
		$dummyskk = $row["nomorskko"];
		$dummykon = $row["dummykon"];
	}
		echo "
			<tr>
				<td colspan='5'>PAGU ANGGARAN " . date("Y") . "</td>
				<td></td>
				<td align='right'>".number_format($ang,2)."</td>
				<td align='right'>".number_format($dis,2)."</td>
				<td align='right'></td>
				<td></td>
				<td></td>
				<td></td>
 				<td></td>
				<td align='right'>".number_format($kon,2)."</td>
				<td align='right'>".number_format($bay,2)."</td>
 				<td></td>
				<td align='right'>".number_format($dis-$bay,2)."</td>
			</tr>";
*/			
	echo "</table>";
	
//	mysql_free_result($result);
//	mysql_close($link);	  
?>
</body>
</html>