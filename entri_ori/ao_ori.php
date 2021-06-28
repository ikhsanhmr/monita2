<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<script type="text/javascript">
		function filtermonita() {
			var u = document.getElementById("usr").value;
			var p = document.getElementById("bidang").value;
			var s = document.getElementById("status").value;
			var j = document.getElementById("jenis").value;
			var o = document.getElementById("pos").value;
			var parm = "index.php?" + "u=" + u + "&p=" + p + "&s=" + s + "&j=" + j + "&o=" + o;
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

	$sql = "
SELECT * FROM (
SELECT '' pos, nomorskko, s.uraian, tanggalskko, nilaianggaran, nilaidisburse, '' nilaiwbs, '' nomorkontrak, '' vendor, '' urai, '' tglawal, '' tglakhir, '' nilaikontrak, '' totalbayar, '' nipuser, '' pelaksana, '' namaunit
FROM skkoterbit s LEFT JOIN kontrak k ON s.nomorskko = k.nomorskkoi WHERE NOT nomorkontrak IS NULL
UNION
SELECT pos, nomorskko, s.uraian, tanggalskko, nilaianggaran, nilaidisburse, nilaiwbs, nomorkontrak, vendor, k.uraian urai, tglawal, tglakhir, nilaikontrak, totalbayar, nipuser, pelaksana, namaunit
FROM skkoterbit s
LEFT JOIN (
	SELECT n.nomornota, noskk, nipuser, pelaksana, namaunit FROM notadinas n 
	LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
	LEFT JOIN bidang b ON d.pelaksana = b.id
) nndb ON s.nomorskko = nndb.noskk
LEFT JOIN kontrak k ON s.nomorskko = k.nomorskkoi
LEFT JOIN (SELECT nokontrak, SUM(nilaibayar) totalbayar FROM realisasibayar GROUP BY nokontrak) b ON k.nomorkontrak = b.nokontrak
WHERE YEAR(tanggalskko) = " . date("Y") . " AND NOT nomorkontrak IS NULL) xy"; 
//	echo "sql : $sql<br>";
	
	$parm = "";
	$parm .= ($u==""? "": "id='$u'");
	$parm .= ($p==""? "": ($parm==""? "": " and ") . "pelaksanaid = '$p'");
	$parm .= ($s==""? "": ($parm==""? "": " and ") . "progressid= $s");
	$parm .= ($j==""? "": ($parm==""? "": " and ") . "skk = '$j'");
	$parm .= ($o==""? "": ($parm==""? "": " and ") . "pos1 = '$o'");
	//echo "parm : $parm<br>";

	$sql1 = "";
	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or pelaksana='$org')");
	
	$sql1 .= ($sql1==""? ($parm==""? "": " where $parm"): ($parm==""? "": " and $parm"));
	$sql .= $sql1 . " ORDER BY nomorskko, pos";
//	echo $sql;

	echo "
		<h2>Monitoring Pelaksanaan Anggaran Operasi Tahun " . date("Y") . "<br>Posisi Tanggal : " . date("d-m-Y") . "</h2>
		
		<table border='1'>
		<tr>
			<th rowspan='2' scope='col'>No</th>
			<th rowspan='2' scope='col'>No. SKK-O</th>
			<th rowspan='2' scope='col'>Uraian</th>
			<th rowspan='2' scope='col'>Tgl Terbit</th>
			<th rowspan='2' scope='col'>Pelaksana</th>
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
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {
		if($dummy!= $row["nomorskko"]) {
			if($no==1) {
				echo "<script>document.getElementById('n1').innerHTML = '". number_format($nkon,2) . "'</script>";
				echo "<script>document.getElementById('b1').innerHTML = '". number_format($nbay,2) . "'</script>";
				echo "<script>document.getElementById('s1').innerHTML = '". number_format($ndis-$nbay,2) . "'</script>"; 
			}
			$ndis = 0;
			$nkon = 0;
			$nbay = 0;
			$nsis = 0;

			echo "<script>document.getElementById('p$no').innerHTML = '". $nu . "'</script>";
			

			$no++;
			$dummy = $row["nomorskko"];
			$ang += $row["nilaianggaran"];
			$dis += $row["nilaidisburse"];			
			$ndis += $row["nilaidisburse"];

		}
		$kon += $row["nilaikontrak"];
		$bay += $row["totalbayar"];
		
		$nkon += $row["nilaikontrak"];
		$nbay += $row["totalbayar"];
		$nu = $row["namaunit"];
		

//				<td align='right'>".($row["nilaikontrak"]==""? "": number_format($row["nilaikontrak"],2))."</td>
//				<td align='right'>".($row["totalbayar"]==""? "": number_format($row["totalbayar"],2))."</td>

		
		echo "
			<tr>
				<td>" . ($row["pos"]==""? $no: "") . "</td>
				<td>" . ($row["pos"]==""? $row["nomorskko"]: "") . "</td>
				<td>" . ($row["pos"]==""? $row["uraian"]: "") . "</td>
				<td>" . ($row["pos"]==""? $row["tanggalskko"]: "") . "</td>
				<td align='right'>".($row["pos"]==""? "<div id='p$no'></div>": "")."</td>
				<td align='right'>" . ($row["pos"]==""? number_format($row["nilaianggaran"],2): "") ."</td>
				<td align='right'>" . ($row["pos"]==""? number_format($row["nilaidisburse"],2): "") ."</td>
				<td>$row[nomorkontrak]</td>
				<td>$row[vendor]</td>
				<td>$row[urai]</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
				<td align='right'>".($row["pos"]==""? "<div id='n$no'></div>": number_format($row["nilaikontrak"],2))."</td>
				<td align='right'>".($row["pos"]==""? "<div id='b$no'></div>": number_format($row["totalbayar"],2))."</td>
				<td></td>
				<td align='right'>".($row["pos"]==""? "<div id='s$no'></div>": "")."</td>
			</tr>";
	}
		echo "
			<tr>
				<td colspan='4'>PAGU ANGGARAN " . date("Y") . "</td>
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
	echo "</table>";
	echo "<script>document.getElementById('p$no').innerHTML = '". $nu . "'</script>";
	echo "<script>document.getElementById('n$no').innerHTML = '". number_format($nkon,2) . "'</script>";
	echo "<script>document.getElementById('b$no').innerHTML = '". number_format($nbay,2) . "'</script>";
	echo "<script>document.getElementById('s$no').innerHTML = '". number_format($ndis-$nbay,2) . "'</script>"; //number_format($ndis-$nbay,2)
	
	
	mysqli_free_result($result);
	$mysqli->close();($link);	  
?>
</body>
</html>