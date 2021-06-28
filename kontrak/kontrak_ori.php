<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Untitled Document</title>
    <link href="../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/methods.js"></script>
	</script>
</head>

<body>
<?php
	function cetakbaris($no, $k, $u, $v, $a, $r, $n) {
		echo "<tr>
					<td>$no</td>.
					<td><input type='text' name='k$no' id='k$no' value='$k'" . ($k==""? "": " readonly") . "></td>
					<td><input type='text' name='u$no' id='u$no' value='$u'></td>
					<td><input type='text' name='v$no' id='v$no' value='$v'></td>
					<td><input type='text' name='a$no' id='a$no' value='$a'></td>
					<td><input type='text' name='r$no' id='r$no' value='$r'></td>
					<td><input type='text' name='n$no' id='n$no' value='$n'></td>
				</tr>";
	}
	
	require_once "../config/koneksi.php";
	
	//mysql_select_db($db);

	echo "<a href='index.php'>Kembali</a><br><br>
			<form name='myForm' id='myForm' method='POST' action='simpan.php' onSubmit='return validateContract(\"$_REQUEST[skk]\")'>
				Nomor SKK : <input type='text' name='skk' id='skk' value='$_REQUEST[skk]' readonly>
				<table border='1'>
					<tr><th>No</th><th>No Kontrak</th><th>Uraian</th><th>Vendor</th><th>Tgl Awal</th><th>Tgl Akhir</th><th>Nilai</th></tr>";

	$sql="SELECT * FROM kontrak WHERE nomorskkoi = '$_REQUEST[skk]'";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

	$no = 1;
	while ($row = mysqli_fetch_array($result)) {  
		cetakbaris($no, $row["nomorkontrak"], $row["uraian"], $row["vendor"], $row["tglawal"], $row["tglakhir"], $row["nilaikontrak"]);
		$no++;
	}
	mysqli_free_result($result);
	$mysqli->close();($link);	
	
	cetakbaris($no);
	echo 
					"<tr>
						<td align='right' colspan='8'>
							<input type='submit' value='Tambah'>
							<input type='submit' value='simpan'>
						</td>
					</tr>
				</table>
			</form>	";
?>
</body>

</html>