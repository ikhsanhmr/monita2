<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	error_reporting(0);  session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	$nip=$_SESSION['cnip'];
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="../js/methods.js"></script>
<title>Untitled Document</title>
</head>

<body>
<?php
require_once "../config/control.inc.php";
echo '<form name="frm" id="frm" onSubmit="return submitme()">';

	
	//mysqli_select_db($db);

	$sql = "SELECT n.nomornota as nomornota,n.tanggal,n.perihal,n.skkoi as skkoi,n.nilaiusulan,n.progress as progress,n.nip,n.assigndt,n.nipuser,p.pid,
 p.info,p.keterangan,u.nama,u.kdunit as unitanggaran,u.bidang,u.adm
FROM notadinas n 
LEFT JOIN progress p ON COALESCE(n.progress, 0) = p.pid 
LEFT JOIN user u on n.nip=u.nip 
WHERE n.nip = '$nip' 
AND COALESCE(progress,0) < 8 and COALESCE(progress,0) != 1 
order by tanggal
";
//AND COALESCE(progress,0) >= 2 and COALESCE(progress,0) < 8 
//echo $sql;
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	echo "
		<table border='1'>
			<tr>
				<th>No</th>
				<th>Nomor Nota Dinas</th>
				<th>Tanggal</th>
				<th>Unit</th>
				<th>Perihal</th>
				<th>SKKO/I</th>
				<th>Nilai Usulan</th>
				<th>Pembuat SKKO/O</th>
				<th>Nomor SKKO/I</th>
				<th>Progress</th>
				<th>Proses</th>
				</tr>";
	
	$no = 0;
	while ($row = mysqli_fetch_array($result)) {
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>$row[nomornota]</td>
				<td>$row[tanggal]</td>
				<td>$row[unit]</td>
				<td>$row[perihal]</td>
				<td>$row[skkoi]</td>
				<td>$row[nilaiusulan]</td>
				<td>$nip</td>	
				<td>$row[nomorskk]</td>
				<td>$row[info]</td>
				<td>$progress
				<input type='button' value='Evaluasi Usulan' onclick='window.open(\"prosesdisposisi.php?nd=$row[nomornota]&prg=3&skkoi=$row[skkoi]\",\"_self\")'".($row['progress']=='2'?"":" disabled").">
				<input type='button' value='Konsep' onclick='window.open(\"prosesdisposisi.php?nd=$row[nomornota]&prg=4&skkoi=$row[skkoi]\",\"_self\")'".($row['progress']=='3'?"":" disabled").">
				<input type='button' value='Kembali ke User' onclick='window.open(\"prosesdisposisi.php?nd=$row[nomornota]&prg=5&skkoi=$row[skkoi]\",\"_self\")'".($row['progress']=='4'?"":" disabled").">
				<input type='button' value='GM' onclick='window.open(\"prosesdisposisi.php?nd=$row[nomornota]&prg=6&skkoi=$row[skkoi]\",\"_self\")'".($row['progress']=='4'?"":" disabled").">
				<input type='button' value='Terbit' onclick='window.open(\"prosesdisposisi.php?nd=$row[nomornota]&prg=7&skkoi=$row[skkoi]\",\"_self\")'".($row['progress']=='6'?"":" disabled")."></td>
				 </tr>";
	}
	echo "</table>";

	mysqli_free_result($result);
	$mysqli->close();($link);	


echo '</form>';
?>
</body>
</html>