<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<script type="text/javascript">
		function onlyme() {
			var o = document.getElementById("o").value;
			var parm = "rekapao.php?" + "o=" +  o;
			//alert(parm);
			window.open(parm, "_self");
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
	if($adm>=1 && $adm<=3) {
		$vo = "<select name='o' id='o' onchange='onlyme()'><option value=''></option>";

		$sql = "SELECT * FROM bidang WHERE NOT namaunit LIKE '%wilayah%'";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$vo .= "<option value='$row[id]'" . ($row["id"]==$o? " selected": "") .">$row[namaunit]</option>";
		}
		mysql_free_result($result);

		$vo .= "</select>";
	}

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
	SELECT s.nomorskko, nomorwbs, nomorcostcenter, uraian, tanggalskko, nilaianggaran, nilaidisburse, nilaiwbs, nilaikontrak, nilaibayar FROM skkoterbit s LEFT JOIN (
		SELECT nomorskkoi, SUM(nilaikontrak) nilaikontrak, sum(nilaibayar) nilaibayar FROM (
			SELECT nomorskkoi, nomorkontrak, nilaikontrak FROM kontrak 
		) k LEFT JOIN (
			SELECT nokontrak, COALESCE(SUM(nilaibayar),0) nilaibayar FROM realisasibayar GROUP BY nokontrak
		) t ON k.nomorkontrak = t.nokontrak
		GROUP BY nomorskkoi
	) kt ON s.nomorskko = kt.nomorskkoi
) skb ON dbn.noskk = skb.nomorskko
";

	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or pelaksana='$org')");
	$sql .= $sql1;
	
	if($o!="") {
		$sql = "select * from (" . $sql . ") vo where kodeorg = $o ";
	}
		
	$sql .= " ORDER BY nomorskko";

//	echo $sql;
	echo "
		<h2>Laporan Monitoring Penyerapaan Anggaran Operasi Tahun " . date("Y") . "<br>Posisi Tanggal : " . date("d-m-Y") . "</h2>
		
		<table border='1'>
			<tr>
				<th rowspan='3' scope='col'>No Urut</th>
				<th colspan='8' scope='col'>SKKO Terbit</th>
				<th rowspan='3' scope='col'>Nilai Kontrak (Rp.)</th>
				<th rowspan='3' scope='col'>% Kontrak</th>
				<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
				<th rowspan='3' scope='col'>% Bayar</th>
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
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana" . ($adm>=1 && $adm<=3? $vo: "") . "</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKO (Disburse - Kontrak)</td>
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
	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$ang += $row["nilaianggaran"];
		$dis += $row["nilaidisburse"];
		$wbs += $row["nilaiwbs"];
		$kon += $row["nilaikontrak"];
		$bay += $row["nilaibayar"];
		
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>$row[nomorprk]</td>
				<td>$row[nomorskko]</td>
				<td>$row[uraian]</td>
				<td>$row[tanggalskko]</td>
				<td align='right'>".number_format($row["nilaianggaran"],2)."</td>
				<td align='right'>".number_format($row["nilaidisburse"],2)."</td>
				<td align='right'>".number_format($row["nilaiwbs"],2)."</td>
				<td>$row[pelaksana]</td>
				<td align='right'>".number_format($row["nilaikontrak"],2)."</td>
				<td align='right'>".number_format($row["nilaikontrak"]/$row["nilaidisburse"]*100,2)."</td>
				<td align='right'>".number_format($row["nilaibayar"],2)."</td>
				<td align='right'>".number_format($row["nilaibayar"]/$row["nilaikontrak"]*100,2)."</td>
				<td align='right'>".number_format($row["nilaidisburse"]-$row["nilaikontrak"],2)."</td>
				<td align='right'>".number_format($row["nilaikontrak"]-$row["nilaibayar"],2)."</td>
				<td></td>
			</tr>";
	}
		echo "
			<tr>
				<td colspan='3'>Jumlah</td>
				<td></td>
				<td>$row[tanggalskko]</td>
				<td align='right'>".number_format($ang,2)."</td>
				<td align='right'>".number_format($dis,2)."</td>
				<td align='right'>".number_format($wbs,2)."</td>
				<td>$row[namaunit]</td>
				<td align='right'>".number_format($kon,2)."</td>
				<td align='right'>".number_format($kon/$dis*100,2)."</td>
				<td align='right'>".number_format($bay,2)."</td>
				<td align='right'>".number_format($bay/$kon*100,2)."</td>
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