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
	$org=$_SESSION['org'];
	
	$sql = "
SELECT * FROM (
	SELECT noskk, nipuser, unituser, namaunit pelaksana, kodeorg FROM 
	(SELECT nomornota, nipuser, tanggal, namaunit unituser FROM notadinas n LEFT JOIN (SELECT * FROM USER u LEFT JOIN bidang b ON u.kodeorg = b.id) ub ON n.nipuser = ub.nip) nub
	LEFT JOIN (
		SELECT nomornota, noskk, namaunit, pelaksana kodeorg FROM notadinas_detail d LEFT JOIN bidang b ON d.pelaksana = b.id 
	) db ON nub.nomornota = db.nomornota WHERE YEAR(tanggal) = " . date("Y") . " AND NOT noskk IS NULL
	GROUP BY noskk, unituser, namaunit, kodeorg
) dbn 
RIGHT JOIN (
	SELECT s.nomorskko, uraian, tanggalskko, nilaianggaran, nilaidisburse, nilaikontrak, nilaibayar FROM skkoterbit s LEFT JOIN (
		SELECT nomorskkoi, SUM(nilaikontrak) nilaikontrak, nilaibayar FROM (
			SELECT nomorskkoi, nomorkontrak, nilaikontrak FROM kontrak 
		) k LEFT JOIN (
			SELECT nokontrak, COALESCE(SUM(nilaibayar),0) nilaibayar FROM realisasibayar GROUP BY nokontrak
		) t ON k.nomorkontrak = t.nokontrak
		GROUP BY nomorskkoi
	) kt ON s.nomorskko = kt.nomorskkoi
) skb ON dbn.noskk = skb.nomorskko
";

	$sql1 = "";
	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or kodeorg='$org')");
	
	$sql1 .= ($sql1==""? ($parm==""? "": " where $parm"): ($parm==""? "": " and $parm"));
	$sql .= $sql1;
	$sql .= " ORDER BY nomorskko";
	
	
//	echo $sql;
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
			<th rowspan='2' scope='col'>Nilai Kontrak</th>
			<th rowspan='2' scope='col'>Realisasi Pembayaran (Rp.)</th>
			<th rowspan='2' scope='col'>Tanggal Terima di Anggaran</th>
			<th rowspan='2' scope='col'>Sisa SKKO</th>
		</tr>
		<tr>
			<td>Anggaran</td>
			<td>Disburse</td>
		</tr>";


	$dummy = "";
	$no = 0;
	$ang = 0;
	$dis = 0;
	$wbs = 0;
	$kon = 0;
	$bay = 0;

	$dummyskk = "";
	$dummykon = "";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {
		$no++;
		$kon += $row["nilaikontrak"];
		$bay += $row["totalbayar"];
		$ang += ($dummyskk==$row["nomorskko"]? 0: $row["nilaianggaran"]);
		$dis += ($dummyskk==$row["nomorskko"]? 0: $row["nilaidisburse"]);
		
		echo "
			<tr>
				<td>$no</td>
				<td>$row[unituser]</td>
				<td>$row[pelaksana]</td>
				<td>$row[nomorskko]</td>
				<td>$row[uraian]</td>
				<td>$row[tanggalskko]</td>
				<td align='right'>" . number_format($row["nilaianggaran"],2) ."</td>
				<td align='right'>" . number_format($row["nilaidisburse"],2) ."</td>
				<td align='right'>" . number_format($row["nilaikontrak"],2) ."</td>
				<td align='right'>" . number_format($row["totalbayar"],2) ."</td>
				<td></td>
				<td align='right'>".number_format($row["nilaidisburse"]-$row["totalbayar"],2)."</td>
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
				<td align='right'>".number_format($kon,2)."</td>
				<td align='right'>".number_format($bay,2)."</td>
 				<td></td>
				<td align='right'>".number_format($dis-$kon,2)."</td>
			</tr>";
	echo "</table>";
	
	mysqli_free_result($result);
	$mysqli->close();($link);	  
?>
</body>
</html>