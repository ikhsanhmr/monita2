<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>
	
	<script type="text/javascript">
		function onlyme() {
			var j = document.getElementById("j").value;
			var o = document.getElementById("o").value;
			
			var parm = (j==""? "": "j=" + j);
			parm = (o==""? parm: ((parm==""? "": parm + "&") + "o=" + o));
			parm = encodeURI("kontrak.php" + (parm==""? "": "?" + parm));
			
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
	$j = (isset($_REQUEST["j"])? $_REQUEST["j"]: "");
	
	$parm = ($o==""? "": "pelaksana='$o'");
	$parm = ($j==""? $parm: (($parm==""? "": $parm . " and ") . " jenis = '$j'"));
	$parm = ($parm==""? "": " where $parm");
	
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
	$vj = "<select name='j' id='j' onchange='onlyme()'><option values=''></option><option values='SKKO'" . ($j=="SKKO"? " selected": "") .">SKKO</option><option values='SKKI'" . ($j=="SKKI"? " selected": "") .">SKKI</option></select>";

	$sql = "
SELECT * FROM (
	SELECT nipuser, db.* FROM notadinas n LEFT JOIN (
		SELECT nomornota, noskk, pos1, pelaksana, nilai1, namaunit
		FROM notadinas_detail d LEFT JOIN bidang b ON d.pelaksana = b.id
	) db ON n.nomornota = db.nomornota WHERE YEAR(tanggal) = " . date("Y") . "
) nd
LEFT JOIN (
	SELECT s.*, pos, nomorkontrak, vendor, k.uraian, tglawal, tglakhir, nilaikontrak, nilaibayar FROM kontrak k
	LEFT JOIN (
		SELECT 'SKKO' jenis, nomorskko nomorskk, tanggalskko tglskk, nilaianggaran, nilaidisburse FROM skkoterbit UNION SELECT 'SKKI' jenis, nomorskki nomorskk, tanggalskki tglskk, nilaianggaran, nilaidisburse FROM skkiterbit
	) s ON k.nomorskkoi = s.nomorskk
	LEFT JOIN (SELECT nokontrak, uraian, COALESCE(SUM(nilaibayar),0) nilaibayar FROM realisasibayar GROUP BY nokontrak, uraian) r ON k.nomorkontrak = r.nokontrak
) sk ON nd.noskk = sk.nomorskk AND nd.pos1 = sk.pos
WHERE NOT nomorkontrak IS NULL";
//ORDER BY nomornota, nomorskk";

	$sql1 = "";
	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " and (nipuser='$nip' or pelaksana='$org')");	
	$sql .= $sql1;
	
	if($parm!="") {
		$sql = "select * from (" . $sql . ") vall $parm";
	}
	
	$sql .= " ORDER BY noskk, nomorkontrak";


//	echo $sql;
//NO SKKO/SKKI	UNIT PELAKSANA	No. Kontrak	Nama Vendor	Nama Kontrak	Tanggal Awal	Tanggal Akhir	 Nilai Kontrak 	 Total Bayar 	 Sisa Bayar 	Persentasi (%)

	echo "
		<h2>Data Kontrak 6200 - Wilayah Sumatera Utara<br>" . date("d-m-Y") . "<br>Posisi Tanggal : " . date("d-m-Y H:i:s") . "</h2>
		
		<table border='1'>
		<tr>
			<th scope='col'>No</th>
			<th scope='col'>Jenis<br>$vj</th>
			<th scope='col'>NO SKKO/I</th>
			<th scope='col'>Unit Pelaksana" . /*($adm>=1 && $adm<=3? $vo: "")*/ $vo . "</th>
			<th scope='col'>No.Kontrak</th>
			<th scope='col'>Nama Vendor</th>
			<th scope='col'>Nama Kontrak</th>
			<th scope='col'>Tanggal Awal</th>
			<th scope='col'>Tanggal Akhir</th>
			<th scope='col'>Nilai Kontrak</th>
			<th scope='col'>Total Bayar</th>
			<th scope='col'>sisa Bayar</th>
			<th scope='col'>Persentasi (%)</th>
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
				<td>$row[jenis]</td>
				<td>$row[noskk]</td>
				<td>$row[namaunit]</td>
				<td>$row[nomorkontrak]</td>
				<td>$row[vendor]</td>
				<td>$row[uraian]</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
				<td>".number_format($row["nilaikontrak"])."</td>
				<td>".number_format($row["nilaibayar"])."</td>
				<td>".number_format($row["nilaikontrak"]-$row["nilaibayar"])."</td>
				<td>".number_format($row["nilaibayar"]/$row["nilaikontrak"]*100)."</td>
			</tr>";
			
		$dummyskk = $row["nomorskko"];
		$dummykon = $row["dummykon"];
	}
	
	mysql_free_result($result);
	mysql_close($link);	  
?>
</body>
</html>