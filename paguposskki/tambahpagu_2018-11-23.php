<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../css/screen.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="../js/methods.js"></script>
        <link href="../css/screen.css" rel="stylesheet" type="text/css">
		<style type="text/css">
			input.right { text-align:right; }
		</style>
	</head>

<body>

<?php
	echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />';

	require_once "../config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);
	
	$sql = "SELECT * FROM posinduk p LEFT JOIN saldopos s ON kdindukpos = kdsubpos AND tahun = '$_REQUEST[prd]' where kdindukpos = '62'";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	echo "<h2>FORM TAMBAH PAGU POS<br></h2>";
	//echo "Tahun $_REQUEST[prd]<br><br>";
	echo "Tahun <input type='text' value='$_REQUEST[prd]' size='4' readonly><br>";
	
	echo "
		<form name='frm' id='frm' method='post' action='simpanpagu.php'>
		<input type='hidden' name='prd' id='prd' value='$_REQUEST[prd]'>
		<table border='1'>
			<tr>
				<th>POS</th>
				<th>NAMA</th>
				<th>NILAI</th>
			</tr>";
	
	$tot = 0;
	while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
		$tot += $row['rppos'];
		echo "
			<tr>
				<td>$row[kdindukpos]</td>
				<td>$row[namaindukpos]</td>
				<td align='right'>
					<input class='right' type='hidden' name='c$row[kdindukpos]' id='c$row[kdindukpos]' value='" . 
				(number_format($row['rppos'])==0? "": number_format($row['rppos'])) . "'>
					<input class='right' type='text' name='t$row[kdindukpos]' id='t$row[kdindukpos]' value='" . 
				(number_format($row['rppos'])==0? "": number_format($row['rppos'])) . "' onchange='formatme(this); addme();'>
				</td>
			</tr>";
	}	
	mysqli_free_result($result);
	$mysqli->close();($link);	
	
	echo "
			<tr>
				<td colspan='2' align='center'>Total</td>
				<td><input class='right' type='text' name='jumlah' id='jumlah' value='" . number_format($tot) . "'></td>
			</tr>
			<tr>
				<td colspan='3' align='center'>
					<input type='button' value='Kembali' 
						onclick=\"window.open('pagupos.php?prd=$_REQUEST[prd]', '_self')\">
					<input type='submit' value='Simpan'>
				</td>
			</tr>
		</table>
		</form>";
?>

</body>

</html>
