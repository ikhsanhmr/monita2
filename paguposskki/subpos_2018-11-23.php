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

		echo "<form name='frm' id='frm' method='post' action='simpansubpagu.php'>";
		echo "<h2>Detail Pagu POS $_REQUEST[pos]</h2>";
		echo "<input type='hidden' name='pos' id='pos' value='$_REQUEST[pos]' size='4' readonly><br>";
		echo "Tahun <input type='text' name='prd' id='prd' value='$_REQUEST[prd]' size='4' readonly><br>";
		
		require_once "../config/control.inc.php";
		$link = new mysqli($srv, $usr, $pwd,$db);
		if (!$link) {
			echo "Failed to connect to MySQL: " . $mysqli -> connect_error; exit();
		}
		//mysqli_select_db($db);
		

//		$sql = "SELECT rppos FROM saldopos WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$_REQUEST[pos]'";
		$sql = "SELECT rppos FROM saldopos" . ($_REQUEST["ke"]>1?  $_REQUEST["ke"]: "") . 
			" WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$_REQUEST[pos]'";
//		echo "$sql<br>";
			
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {$tot = $row["rppos"]; }
		mysqli_free_result($result);		

		$sql = "SELECT p.*, rppos FROM posinduk2 p LEFT JOIN saldopos2 s ON p.kdsubpos = s.kdsubpos AND tahun = $_REQUEST[prd] WHERE kdindukpos = '$_REQUEST[pos]' ORDER BY p.kdsubpos";

		$sql = "SELECT p.*, rppos FROM posinduk" . ($_REQUEST["ke"]+1) . " p LEFT JOIN saldopos" . ($_REQUEST["ke"]+1) . 
			" s ON p.kdsubpos = s.kdsubpos AND tahun = $_REQUEST[prd] WHERE kdindukpos = '$_REQUEST[pos]' ORDER BY p.kdsubpos";
		//echo "$sql<br>";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		echo "
			<table border='1'>
				<tr>
					<th>No</th>
					<th>POS</th>
					<th>SUB POS</th>
					<th>NAMA</th>
					<th>PAGU</th>
					<th>AKI</th>
					<th>SALDO</th>
					<th>AKSI</th>
				</tr>";
	
		$no = 0;
		$sudah = 0;
		while ($row = mysqli_fetch_array($result)) {
			$dummy = str_replace('.', '_', $row['kdsubpos']);
			$sudah += $row['rppos'];
			$no++;
			echo "
				<tr>
					<td>$no</td>
					<td>$row[kdindukpos]</td>
					<td>$row[kdsubpos]</td>
					<td>$row[namasubpos]</td>
					<td align='right'>
						<input class='right' type='hidden' name='c$dummy' id='c$dummy' value='" . 
						(number_format($row['rppos'])==0? "": number_format($row['rppos'])) . "'>
						<input class='right' type='text' name='t$dummy' id='t$dummy' value='" . 
						(number_format($row['rppos'])==0? "": number_format($row['rppos'])) . 
						"' onchange=\"formatme(this); nilaisubpagu(this, '$tot')\">
					</td>
					<td align='right'>
						<input class='right' type='text' name='t$dummy' id='t$dummy'>
					</td>
					<td align='right'>" . number_format($row[""]) . "</td>
					<td>
						<a href='#' onclick=\"detailpagu('$row[kdsubpos]', $_REQUEST[prd])\">Detail</a>&nbsp;&nbsp;&nbsp; 
						<a href='#' onclick=\"hapuspagu('$row[kdsubpos]', $_REQUEST[prd])\">Hapus</a>       
					</td>
				</tr>";
		}
		mysqli_free_result($result);
		
		echo "
				<tr>
					<td align='center' colspan='8'>
					<input type='button' value='Kembali' 
						onclick=\"window.open('" . 
							($_REQUEST["ke"]==1? 
								"pagupos.php?prd=$_REQUEST[prd]": 
								"subpos.php?prd=$_REQUEST[prd]&pos=$back&ke=" . ($_REQUEST["ke"]-1)
							) . "', '_self')\">
					<input type='submit' value='Simpan'>
					</td>
				</tr>
			</table>";
		echo "</form>";
		
		$mysqli->close();($link);

		echo "Total pagu = Rp." . number_format($tot) . "<br>";
		echo "<div id='sudah'>Pagu yang sudah dirinci = Rp." . number_format($sudah) . "</div><br>";
		echo "<div id='belum'>Pagu yang belum dirinci = Rp." . number_format($total-$belum) . "</div><br>";
	}
?>
</body>

</html>