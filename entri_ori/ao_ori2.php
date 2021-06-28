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
FROM skkoterbit s LEFT JOIN kontrak k ON s.nomorskko = k.nomorskkoi WHERE NOT /*nomorkontrak*/ nomorskko IS NULL
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
WHERE YEAR(tanggalskko) = " . date("Y") . " AND NOT /*nomorkontrak*/ nomorskko IS NULL) xy"; 
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
	$sql .= $sql1 . " ORDER BY nomorskko, nomorkontrak";
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
		if($dummy!=$row["nomorskko"]) {
			$no++;
			$diskon = $row["nilaidisburse"]; 

			echo "
				<tr>
					<td>" . ($dummy==$row["nomorskko"]? "": $no) . "</td>
					<td>" . ($dummy==$row["nomorskko"]? "": $row["nomorskko"]) . "</td>
					<td>" . ($dummy==$row["nomorskko"]? "": $row["uraian"]) . "</td>
					<td>" . ($dummy==$row["nomorskko"]? "": $row["tanggalskko"]) . "</td>
					<td>$row[namaunit]</td>
					<td align='right'>" . ($dummy==$row["nomorskko"]? "": number_format($row["nilaianggaran"],2)) . "</td>
					<td align='right'>" . ($dummy==$row["nomorskko"]? "": number_format($row["nilaidisburse"],2)) . "</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td align='right'><div id='kon$no'></div></td>
					<td align='right'></td>
					<td align='right'></td>
					<td></td>
				</tr>";
				
			$dummy = $row["nomorskko"];
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
		if($row["nomorkontrak"]!="") {
		echo "
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'></td>
				<td align='right'></td>
				<td align='right'></td>
				<td></td>
				<td align='right'>". $row["nomorkontrak"] ."</td>
				<td align='right'>". $row["vendor"] ."</td>
				<td align='right'>". $row["urai"] ."</td>
				<td align='right'>". $row["tglawal"] ."</td>
				<td align='right'>". $row["tglakhir"] ."</td>
				<td align='right'>".number_format($row["nilaikontrak"],2)."</td>
				<td align='right'>".number_format($row["totalbayar"],2)."</td>
				<td align='right'></td>
				<td align='right'>".number_format($row["nilaidisburse"]-$nkon,2)."</td>
			</tr>";
		}
		$diskon -= $row["nilaikontrak"];	
	}

		$num = $no;
		echo "<script>document.getElementById('kon$num').innerHTML = '". number_format($nkon,2) . "'</script>";
		echo "<script>document.getElementById('bay$num').innerHTML = '". number_format($nbay,2) . "'</script>";
		echo "<script>document.getElementById('diskon$num').innerHTML = '". number_format($ndis-$nbay,2) . "'</script>"; 
		echo "<script>document.getElementById('konbay$num').innerHTML = '". number_format($ndis-$nbay,2) . "'</script>"; 	
	
		echo "
			<tr>
				<td colspan='3'>Jumlah</td>
				<td></td>
				<td>$row[tanggalskko]</td>
				<td align='right'>".number_format($ang,2)."</td>
				<td align='right'>".number_format($dis,2)."</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'>".number_format($kon,2)."</td>
				<td align='right'>".number_format($bay,2)."</td>
				<td align='right'></td>
				<td align='right'>".number_format($dis-$kon,2)."</td>
			</tr>";
	
	echo "</table>";
	
	
	mysqli_free_result($result);
	$mysqli->close();($link);	  
?>
</body>
</html>