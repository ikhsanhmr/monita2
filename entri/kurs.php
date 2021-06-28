<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="../css/screen.css" rel="stylesheet" type="text/css">
        <?php
		session_start();
		if(!isset($_SESSION['nip'])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		?>

		<script type="text/javascript">
			function edit(x) {
				var url='tambahkurs.php?id='+x;
				window.open(url,'_self');
			}
			
			function hapus(no, id) {
				var r = confirm("Yakin anda ingin menghapus kurs tanggal " + no + "?") 
				if (r) {
					var url='kurs.php?del='+id;
					window.open(url,'_self');
				}
			}
		</script>
	</head>

<body>

<?php
	require_once "../config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);

	if(isset($_GET['del'])) {
		$noskk=$_GET['del'];
		$sql="delete from kurs_dollar where id=$noskk";

		$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	}

	$prd = "";
	echo "<form name='frm' method='post' action='kurs.php'>";
	for($i=2013; $i<=date('Y')+1; $i++) { $prd .= "<option value='$i' " . 
		(isset($_REQUEST['prd'])? ($_REQUEST['prd']==$i? " selected": "") : ($i==date('Y')? " selected": "") ) . ">$i</option>"; }
	
	echo "<h2>Kurs Dollar<br></h2>";
	echo "
		<table border='1'>
			<tr>
				<td>&nbsp;Periode&nbsp;</td>
				<td>&nbsp;<select name='prd' id='prd'>$prd</select>&nbsp;</td>
			</tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Lihat'></td></tr>
		</table><br>";
	echo "</form>";
	echo "<a href='tambahkurs.php'>(+) Tambah Nilai Kurs Dollar</a><br><br>";
	
	if(isset($_REQUEST['prd'])) {
		
		$sql = "Select * From kurs_dollar WHERE Year(tanggal) = '$_REQUEST[prd]' Order By tanggal";
		
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		echo "Tahun <input type='text' name='tprd' id='tprd' value='$_REQUEST[prd]' size='4' readonly><br><br>";
		
		echo "
			<table border='1'>
				<tr>
					<th>No</th>
					<th>Tanggal</th>
					<th>Nilai Kurs</th>
					<th>Action</th>
				</tr>";
	
		$no = 0;
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$no++;
			echo "
				<tr>
					<td>$no</td>
					<td>$row[tanggal]</td>
					<td align='right'>" . number_format($row["nilaitengah"]) . "</td>
					<td><a href='#' onClick='edit(\"$row[id]\")'>Edit</a><br><a href='#' onClick='hapus(\"$row[tanggal]\", \"$row[id]\")'>Hapus</a></td>
				</tr>";
		}
		mysqli_free_result($result);
		$mysqli->close();($link);	
		echo "</table>";
	}
?>
</body>

</html>