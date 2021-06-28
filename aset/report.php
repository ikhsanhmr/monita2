<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Laporan ASET</title>
	
	<script type="text/javascript">
		function viewrpt(x) {

			var p = document.getElementById("prd").value;
			var url = encodeURI(x==undefined? "report.php": "reportexcel.php");
			var parm = (p == ""? "": "?p=" + p);
			var url = encodeURI(url+parm);
			//alert(url);
			window.open(url, "_self");
		}
	</script>
</head>

<body>
<?php
    session_start(); 
    require_once '../config/koneksi.php';
/*    
    $sql = "
		SELECT DISTINCT prd FROM (
			SELECT DISTINCT(YEAR(tglawal)) prd FROM kontrak
			UNION 
			SELECT DISTINCT(YEAR(tglakhir)) prd FROM kontrak
		) k ORDER BY prd";
*/
    // $sql = "SELECT DISTINCT(YEAR(tglawal)) prd FROM kontrak ORDER BY tglawal";
    $sql = "SELECT DISTINCT(YEAR(inputdt)) prd FROM kontrak where COALESCE(inputdt,'') != '' ORDER BY inputdt";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
    $p = "<select name='prd' id='prd'><option value=''></option>";	
	while ($row = mysqli_fetch_array($result)) {
		$p .= "<option value='$row[prd]' " . (isset($_GET["p"])? ($row["prd"]==$_GET["p"]? " selected": ""): "") . ">$row[prd]</option>";
	}
	$p .= "</select>";
	mysqli_free_result($result);

	$sql = "
		SELECT 	namaunit, ap.* 
		FROM 	bidang b LEFT JOIN 
				(
					SELECT	SUM(jtmaset) jtmaset, SUM(gdaset) gdaset, SUM(jtraset) jtraset, SUM(sl1aset) sl1aset, 
							SUM(sl3aset) sl3aset, SUM(jtmrp) jtmrp, SUM(gdrp) gdrp, SUM(jtraset) jtrrp, SUM(sl1rp) sl1rp, 
							SUM(sl3rp) sl3rp, d.pelaksana 
					FROM 	asetpdp a LEFT JOIN 
							kontrak k ON a.nomorkontrak = k.nomorkontrak LEFT JOIN 
							notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1" . 
							// (isset($_GET["p"])? " WHERE YEAR(k.inputdt) = '$_GET[p]' OR YEAR(tglakhir) = '$_GET[p]'": "") . "
							(isset($_GET["p"])? " WHERE YEAR(k.inputdt) = '$_GET[p]'": "") . "
					GROUP BY pelaksana
				) ap ON b.id = ap.pelaksana
		WHERE id > 5 AND id <> 15 ORDER BY LPAD(id,2,'0')";

	// echo "$sql<br>";

	echo "
		<h2>LAPORAN ASET</h2>
		<table>
			<tr>
				<th>Periode</th>
				<td>:</td>
				<td>$p</td>
			</tr>
			<tr>
				<td colspan='3' align='right'>
					<input type='button' value='View' onclick='viewrpt()'>
					<input type='button' value='Excel' onclick='viewrpt(1)'>
				</td>
			</tr>
		</table>
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
				<td>" . number_format($row["jtmaset"],2) . "</td>
				<td>" . number_format($row["gdaset"],2) . "</td>
				<td>" . number_format($row["jtraset"],2) . "</td>
				<td>" . number_format($row["sl1aset"],2) . "</td>
				<td>" . number_format($row["sl3aset"],2) . "</td>
				<td>" . number_format($row["jtmrp"],2) . "</td>
				<td>" . number_format($row["gdrp"],2) . "</td>
				<td>" . number_format($row["jtrrp"],2) . "</td>
				<td>" . number_format($row["sl1rp"],2) . "</td>
				<td>" . number_format($row["sl3rp"],2) . "</td>
			</tr>";
	}
	echo "</table>";
	mysqli_free_result($result);
	$mysqli->close();($kon);	

?>
	<div id="showhere"></div>
</body>
</html>