<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<style type="text/css">
			input.right { text-align:right; }
		</style>
		<script type="text/javascript" src="../js/methods.js"></script>
        <link href="../css/screen.css" rel="stylesheet" type="text/css">
	</head>

<body>

<?php
	if(isset($_REQUEST['prd'])) {
		$pieces = explode(".", $_REQUEST["pos"]);
		$back = "";
//		echo $pieces . " " . count($pieces) . "<br>";
		
		for($i=1; $i<=count($pieces); $i++) {
			//echo "$i - " . $pieces[$i-1] . "<br>";
			$back .= ($i==count($pieces)? "": (($back==""? "": ".") . $pieces[$i-1]));
		}
		//echo "back : $back<br>";

		echo "<form name='frm' id='frm' method='post' action='simpanakiunit.php'>";
		echo "<h2>Detail Pagu AKI</h2>";
		echo "<input type='hidden' name='pos' id='pos' value='$_REQUEST[pos]' size='4' readonly><br>";
		echo "Tahun <input type='text' name='prd' id='prd' value='$_REQUEST[prd]' size='4' readonly><br>";
		
		require_once "../config/control.inc.php";
		$link = new mysqli($srv, $usr, $pwd,$db);
		if (!$link) {
			echo "Failed to connect to MySQL: " . $mysqli -> connect_error; exit();
		}
		//mysql_select_db($db);
		

//		$sql = "SELECT rppos FROM saldopos WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$_REQUEST[pos]'";
		$sql = "SELECT sum(akipos) as rppos FROM saldopos WHERE tahun = $_REQUEST[prd] AND kdsubpos = 62";
//		echo "$sql<br>";
			
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {$tot = $row["rppos"]; }
		mysqli_free_result($result);		

		$sql = "SELECT *, IFNULL( b.rpaki, 0 ) AS nilaiaki FROM bidang a LEFT JOIN saldoakibidang b ON a.id = b.kdbidang AND b.tahun = $_REQUEST[prd] WHERE a.id <> 3 Order By a.namaunit";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		echo "
			<table border='1'>
				<tr>
					<th>No</th>
					<th>Kode</th>
					<th>NAMA</th>
					<th>AKI</th>
					<th>AKSI</th>
				</tr>";
	
		$no = 0;
		$sudah = 0;
		while ($row = mysqli_fetch_array($result)) {
			$dummy = $row['id'];
			$sudah += $row['nilaiaki'];
			$no++;
			echo "
				<tr>
					<td>$no</td>
					<td>$row[nick]</td>
					<td>$row[namaunit]</td>
					<td align='right'>
						<input class='right' type='hidden' name='c$dummy' id='c$dummy' value='" . 
						(number_format($row['nilaiaki'])==0? "": number_format($row['nilaiaki'])) . "'>
						<input class='right' type='text' name='t$dummy' id='t$dummy' value='" . 
						(number_format($row['nilaiaki'])==0? "": number_format($row['nilaiaki'])) . 
						"' onchange=\"formatme(this); nilaisubpagu(this, '$tot')\">
					</td>
					<td>
						<a href='#' onclick=\"hapuspaguaki('$row[id]', $_REQUEST[prd], '$row[namaunit]')\">Hapus</a>       
					</td>
				</tr>";
		}
		mysqli_free_result($result);
		
		echo "
				<tr>
					<td align='center' colspan='8'>
					<input type='button' value='Kembali' 
						onclick=\"window.open('paguaki.php?prd=$_REQUEST[prd]', '_self')\">
					<input type='submit' value='Simpan'>
					</td>
				</tr>
			</table>";
		echo "</form>";
		
		$mysqli->close();($link);

		echo "Total pagu = Rp." . number_format($tot) . "<br>";
		echo "<div id='sudah'>Pagu yang sudah dirinci = Rp." . number_format($sudah) . "</div><br>";
		echo "<div id='belum'>Pagu yang belum dirinci = Rp." . number_format($tot-$sudah) . "</div><br>";
	}
?>
</body>

</html>