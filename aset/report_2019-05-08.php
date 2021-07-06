<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Laporan ASET</title>
	
	<script type="text/javascript">
		function viewrpt(me) {
			var url = "report.php";
			var parm = (me.value==""? "": "?p=" + me.value);
			var url = encodeURI(url+parm);
			//alert(url);
			window.open(url, "_self");
		}
	</script>
</head>

<body>
<?php
    error_reporting(0);  session_start(); 
    require_once '../config/koneksi.php';
/*    
    $sql = "
		SELECT DISTINCT prd FROM (
			SELECT DISTINCT(YEAR(tglawal)) prd FROM kontrak
			UNION 
			SELECT DISTINCT(YEAR(tglakhir)) prd FROM kontrak
		) k ORDER BY prd";
*/
    $sql = "SELECT DISTINCT(YEAR(tglawal)) prd FROM kontrak ORDER BY tglawal";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
    $p = "<select name='prd' id='prd' onchange='viewrpt(this)'><option value=''></option>";	
	while ($row = mysqli_fetch_array($result)) {
		$p .= "<option value='$row[prd]' " . (isset($_GET["p"])? ($row["prd"]==$_GET["p"]? " selected": ""): "") . ">$row[prd]</option>";
	}
	$p .= "</select>";
	mysqli_free_result($result);

	$sql = "
		SELECT namaunit, ap.* FROM bidang b 
		LEFT JOIN (
			SELECT 
				SUM(jtmaset) jtmaset, SUM(gdaset) gdaset, SUM(jtraset) jtraset, SUM(sl1aset) sl1aset, SUM(sl3aset) sl3aset, 
				SUM(jtmrp) jtmrp, SUM(gdrp) gdrp, SUM(jtraset) jtrrp, SUM(sl1rp) sl1rp, SUM(sl3rp) sl3rp, d.pelaksana 
			FROM asetpdp a 
			LEFT JOIN kontrak k ON a.nomorkontrak = k.nomorkontrak
			LEFT JOIN notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1" . 
			(isset($_GET["p"])? " WHERE YEAR(tglawal) = '$_GET[p]' OR YEAR(tglakhir) = '$_GET[p]'": "") . "
			GROUP BY pelaksana
		) ap ON b.id = ap.pelaksana
		WHERE id > 5 AND id <> 15 ORDER BY LPAD(id,2,'0')";

	//echo "$sql<br>";

	echo "
		<h2>LAPORAN ASET</h2>
		Periode: $p
		<table border='1'>
			<tr>
				<th rowspan='2'>No</th>
				<th rowspan='2'>Unit</th>
				<th colspan='5'>Aset</th>
				<th colspan='5'>Nilai Aset (Rp)</th>
			</tr>
			<tr>
				<th>JTM (Kms)</th>
				<th>Gardu (Unit)</th>
				<th>JTR (Kms)</th>
				<th>SL 1 Phasa (Plgn)</th>
				<th>SL 3 Phasa (Plgn)</th>
				<th>JTM</th>
				<th>Gardu</th>
				<th>JTR</th>
				<th>SL 1 Phasa</th>
				<th>SL 3 Phasa</th>
			</tr>
			";
			
	$dummynota = "";
	$dummyskk = "";
	$dummypos = "";
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {  
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>$row[namaunit]</td>
				<td>$row[jtmaset]</td>
				<td>$row[gdaset]</td>
				<td>$row[jtraset]</td>
				<td>$row[sl1aset]</td>
				<td>$row[sl3aset]</td>
				<td>$row[jtmrp]</td>
				<td>$row[gdrp]</td>
				<td>$row[jtrrp]</td>
				<td>$row[sl1rp]</td>
				<td>$row[sl3rp]</td>
			</tr>";
	}
	echo "</table>";
	mysqli_free_result($result);
	$mysqli->close();($link);	

?>
	<div id="showhere"></div>
</body>
</html>