<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	?>
	
	<script type="text/javascript">
		function simpan(me, num) {
			if(document.getElementById("n"+num).value=="" || parseFloat(document.getElementById("n"+num).value)==NaN) {
				alert("Nilai Pembayaran Harus Diisi!");
				return;
			}
			if(document.getElementById("t"+num).value=="") {
				alert("Tanggal Pembayaran Harus Diisi!");
				return;
			}
			//alert(me);
			
			var parm = "n="+document.getElementById("n"+num).value+"&t="+document.getElementById("t"+num).value+"&k="+me;

			var xmlhttp=new XMLHttpRequest();
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					//alert(xmlhttp.responseText);
					window.open("index.php", "_self");
				}
			}
			xmlhttp.open("GET","simpan.php?"+parm,true);
			xmlhttp.send();
		}
	</script>
</head>

<body>
<?php
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { 
		$bname = 'Internet Explorer'; 
		$ub = "MSIE"; 
	} 
	elseif(preg_match('/Firefox/i',$u_agent)) { 
		$bname = 'Mozilla Firefox'; 
		$ub = "Firefox"; 
	} 
	elseif(preg_match('/Chrome/i',$u_agent)) { 
		$bname = 'Google Chrome'; 
		$ub = "Chrome"; 
	} 
	elseif(preg_match('/Safari/i',$u_agent)) { 
		$bname = 'Apple Safari'; 
		$ub = "Safari"; 
	} 
	elseif(preg_match('/Opera/i',$u_agent)) { 
		$bname = 'Opera'; 
		$ub = "Opera"; 
	} 
	elseif(preg_match('/Netscape/i',$u_agent)) { 
		$bname = 'Netscape'; 
		$ub = "Netscape"; 
	} 
	$nice = (($ub=="Chrome" || $ub=="Opera" || $ub=="Chrome")? true: false);

    session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];
	
/*
	$sql = "
		SELECT k.*, ke, sudahbayar FROM kontrak k 
			LEFT JOIN (SELECT nokontrak, COUNT(*) ke, SUM(nilaibayar) sudahbayar FROM realisasibayar GROUP BY nokontrak) b ON k.nomorkontrak = b.nokontrak
			LEFT JOIN (SELECT nip, noskk FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota) nd ON k.nomorskkoi = nd.noskk and k.pos = nd.pos1" .
		($adm==1? " where nip='$nip'": "") . 	
		" ORDER BY nomorskkoi";
	echo $sql;
*/
	$sql = "
SELECT nip, nipuser, pelaksana, pos1, k.*, ke, sudahbayar 
FROM kontrak k 
	LEFT JOIN (SELECT nokontrak, COUNT(*) ke, SUM(nilaibayar) sudahbayar FROM realisasibayar GROUP BY nokontrak) b ON k.nomorkontrak = b.nokontrak 
	LEFT JOIN (SELECT nip, nipuser, pelaksana, noskk, pos1 FROM notadinas n LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota) nd ON k.nomorskkoi = nd.noskk AND k.pos = nd.pos1 ORDER BY nomorskkoi";
//	echo $sql;
	
	echo "
		<h2>Realisasi Bayar Kontrak</h2>
		<table border='1'>
			<tr>
				<th>No</th>
				<th>No SKKO/I</th>
				<th>No Kontrak</th>
				<th>Uraian</th>
				<th>Vendor</th>
				<th>Tgl Awal</th>
				<th>Tgl Akhir</th>
				<th>Nilai Kontrak</th>
				<th>Sisa Kontrak</th>
				<th>Pembayaran Ke-</th>
				<th>Tgl Bayar (MM/DD/YYYY) - Nilai</th>
				<th>Simpan</th>
			</tr>";

	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  
		$no++;
		$ke=$row["ke"];
		$ke=($ke==""||$ke==null? 0: $ke) + 1;
		
		echo "
			<tr>
				<td>$no</td>
				<td>$row[nomorskkoi]</td>
				<td>$row[nomorkontrak]</td>
				<td>$row[uraian]</td>
				<td>$row[vendor]</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
				<td>".number_format($row["nilaikontrak"],2)."</td>
				<td>".number_format(($row["nilaikontrak"]-$row["sudahbayar"]),2)."</td>
				<td align='right'>$ke</td>
				<td>
					<input required size='22' type='" . ($nice? "date": "text") . "' name='t$no' id='t$no' " . ($nice? "": "onChange='dateCheck(\"t$row[nomorkontrak]\")' ") . ">
					<input type='number' name='n$no' id='n$no'>
				</td>
				<td><input type='button' value='Simpan' onClick='simpan(\"$row[nomorkontrak]\", $no)'></td>
			</tr>
		";
	}
	echo "</table>";
	mysql_free_result($result);
	mysql_close($link);	  
?>
</body>
</html>