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
	SELECT 
		tanggalskko, nomorwbs, nomorcostcenter, nomorskko, uraian, nilaianggaran, nilaidisburse, nilaitunai, nilainontunai, nilaiwbs, 
		nipuser, pelaksana, unitpelaksana, pos1, nilai1
	FROM skkoterbit s
	LEFT JOIN (
		-- SELECT pelaksana, namaunit unitpelaksana, noskk, pos1, nilai1 FROM notadinas_detail d LEFT JOIN bidang b ON d.pelaksana = b.id
				SELECT nipuser, pelaksana, namaunit unitpelaksana, noskk, pos1, nilai1 FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN bidang b ON d.pelaksana = b.id 
	) db ON s.nomorskko = db.noskk
	WHERE YEAR(tanggalskko) = " . date("Y") . "
) sdb 
LEFT JOIN (
	SELECT noskk, pos posk, SUM(nilaikontrak) nilaikontrak, SUM(nilaibayar) nilaibayar FROM (
		SELECT k.*, nilaibayar FROM (
			SELECT nomorskkoi noskk, nomorkontrak, pos, SUM(nilaikontrak) nilaikontrak FROM kontrak GROUP BY nomorskkoi, nomorkontrak, pos
		) k LEFT JOIN (
			SELECT nokontrak, SUM(nilaibayar) nilaibayar FROM realisasibayar GROUP BY nokontrak 
		) b ON k.nomorkontrak = b.nokontrak 
	) kb GROUP BY noskk, pos
) kbg ON sdb.nomorskko = kbg.noskk AND sdb.pos1 = kbg.posk
LEFT JOIN (
	SELECT kdindukpos pos, namaindukpos namapos, rppos FROM posinduk p LEFT JOIN saldopos s ON p.kdindukpos = s.kdsubpos WHERE tahun = " . date("Y") . "
	UNION 
	SELECT p2.kdsubpos pos, namasubpos namapos, rppos FROM posinduk2 p2 LEFT JOIN saldopos2 s2 ON p2.kdsubpos = s2.kdsubpos WHERE tahun = " . date("Y") . "
	UNION 
	SELECT p3.kdsubpos pos, namasubpos namapos, rppos FROM posinduk3 p3 LEFT JOIN saldopos3 s3 ON p3.kdsubpos = s3.kdsubpos WHERE tahun = " . date("Y") . "
	UNION 
	SELECT p4.kdsubpos pos, namasubpos namapos, rppos FROM posinduk4 p4 LEFT JOIN saldopos4 s4 ON p4.kdsubpos = s4.kdsubpos WHERE tahun = " . date("Y") . "
) ps ON sdb.pos1 = ps.pos";

	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or pelaksana='$org')");
	$sql .= $sql1;
	
	if($o!="") {
		$sql = "select * from (" . $sql . ") vo where pelaksana = $o ";
	}
		
	$sql .= " ORDER BY nomorskko, pos";
	//where nomorskko = '049/KEU.01.02/SKKO/GM.WSU/2015-R' or nomorskko = '060/KEU.01.02/SKK.O/GM.WSU/2015-R' 

	//echo $sql;
	echo "
		<h2>Laporan Monitoring Penyerapaan Anggaran Operasi Tahun " . date("Y") . "<br>Posisi Tanggal : " . date("d-m-Y") . "</h2>
		
		<table border='1'>
			<tr>
				<th rowspan='3' scope='col'>No Urut</th>
				<th colspan='11' scope='col'>SKKO Terbit</th>
				<th rowspan='3' scope='col'>Nilai Kontrak (Rp.)</th>
				<th rowspan='3' scope='col'>% Kontrak</th>
				<th rowspan='3' scope='col'>Realisasi Pembayaran (Rp.)</th>
				<th rowspan='3' scope='col'>% Bayar</th>
				<th colspan='2' scope='col'>Sisa</th>
				<th rowspan='3' scope='col'>Keterangan</th>
			</tr>
			<tr>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Cost Center / WBS</td>
				<td align='center' colspan='3' style='background-color:rgb(127,255,127)'>POS</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Nomor</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Uraian</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Tgl Terbit</td>
				<td align='center' colspan='2' style='background-color:rgb(127,255,127)'>Nilai Ketetapan</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>WBS (Rp.)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Pelaksana" . /*($adm>=1 && $adm<=3? $vo: "")*/ $vo . "</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>SKKO (Disburse - Kontrak)</td>
				<td align='center' rowspan='2' style='background-color:rgb(127,255,127)'>Kontrak (Kontrak - Bayar)</td>
			</tr>
			<tr>
				<td style='background-color:rgb(127,255,127)' align='center'>Kode</td>
				<td style='background-color:rgb(127,255,127)' align='center'>Ket</td>
				<td style='background-color:rgb(127,255,127)' align='center'>Nilai</td>
				<td style='background-color:rgb(127,255,127)' align='center'>Anggaran (Rp.)</td>
				<td style='background-color:rgb(127,255,127)' align='center'>Disburse (Rp.)</td>
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
//		$ang += $row["nilaianggaran"];
//		$dis += $row["nilaidisburse"];
//		$wbs += $row["nilaiwbs"];
//		$kon += $row["nilaikontrak"];
//		$bay += $row["nilaibayar"];
		
		if($dummy!=$row["nomorskko"]) {
			$no++;
//			$diskon = $row["nilaidisburse"]; 

			echo "
			<tr>
				<td>$no</td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'><div id='pos$no'></div></td>
				<td>$row[nomorskko]</td>
				<td>$row[uraian]</td>
				<td>$row[tanggalskko]</td>
				<td align='right'>".number_format($row["nilaianggaran"])."</td>
				<td align='right'>".number_format($row["nilaidisburse"])."</td>
				<td align='right'>".number_format($row["nilaiwbs"])."</td>
				<td>$row[unitpelaksana]</td>
				<td align='right'><div id='kon$no'></div></td>
				<td align='right'><div id='konp$no'></div></td>
				<td align='right'><div id='bay$no'></div></td>
				<td align='right'><div id='bayp$no'></div></td>
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
				echo "<script>document.getElementById('pos$num').innerHTML = '". number_format($npos) . "'</script>";
				echo "<script>document.getElementById('kon$num').innerHTML = '". number_format($nkon) . "'</script>";
				echo "<script>document.getElementById('konp$num').innerHTML = '". number_format($nkon/$ndis*100,2) . "'</script>";
				echo "<script>document.getElementById('bay$num').innerHTML = '". number_format($nbay) . "'</script>";
				echo "<script>document.getElementById('bayp$num').innerHTML = '". number_format(0,2) . "'</script>";
			}

			$npos = 0;
			$nkon = 0;
			$nbay = 0;
			
			$ndis = 0;
			$ndis += $row["nilaidisburse"];
		}
		
		$pos += $row["nilai1"];
		$npos += $row["nilai1"];
		$kon += $row["nilaikontrak"];
		$nkon += $row["nilaikontrak"];
		$bay += $row["nilaibayar"];
		$nbay += $row["nilaibayar"];
		
		$ndis -= $row["nilaikontrak"];
		
		
		echo "
			<tr>
				<td></td>
				<td>$row[nomorcostcenter] / $row[nomorwbs]</td>
				<td>$row[pos1]</td>
				<td>$row[namapos]</td>
				<td align='right'>".number_format($row["nilai1"])."</td>
				<td></td>
				<td></td>
				<td></td>
				<td align='right'></td>
				<td align='right'></td>
				<td align='right'></td>
				<td></td>
				<td align='right'>".number_format($row["nilaikontrak"])."</td>
				<td align='right'></td>
				<td align='right'>".number_format($row["nilaibayar"])."</td>
				<td align='right'></td>
				<td align='right'>".number_format($ndis)."</td>
				<td align='right'>".number_format($row["nilaikontrak"]-$row["nilaibayar"])."</td>
				<td></td>
			</tr>";
	}
	
		if($no>1) {
			$num = $no;
			echo "<script>document.getElementById('pos$num').innerHTML = '". number_format($npos) . "'</script>";
			echo "<script>document.getElementById('kon$num').innerHTML = '". number_format($nkon) . "'</script>";
			echo "<script>document.getElementById('konp$num').innerHTML = '". number_format($nkon/$ndis*100,2) . "'</script>";
			echo "<script>document.getElementById('bay$num').innerHTML = '". number_format($nbay) . "'</script>";
			echo "<script>document.getElementById('bayp$num').innerHTML = '". number_format(0,2) . "'</script>";
		}

	
		echo "
			<tr>
				<td colspan='3'>Jumlah</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>$row[tanggalskko]</td>
				<td align='right'>".number_format($ang)."</td>
				<td align='right'>".number_format($dis)."</td>
				<td align='right'>".number_format($wbs)."</td>
				<td>$row[namaunit]</td>
				<td align='right'>".number_format($kon)."</td>
				<td align='right'>".number_format($kon/$dis*100,2)."</td>
				<td align='right'>".number_format($bay)."</td>
				<td align='right'>".number_format($bay/$kon*100,2)."</td>
				<td align='right'>".number_format($dis-$kon)."</td>
				<td align='right'>".number_format($kon-$bay)."</td>
				<td></td>
			</tr>";
	
	echo "</table>";
	mysql_free_result($result);
	mysql_close($link);	  
?>
</body>
</html>