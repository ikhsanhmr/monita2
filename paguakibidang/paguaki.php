<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="../js/methods.js?version=<?php echo rand(10,100)?>"></script>
        <link href="../css/screen.css" rel="stylesheet" type="text/css">
        <?php
		session_start();
		if(!isset($_SESSION['nip'])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		?>
	</head>

<body>

<?php
	$prd = "";
	echo "<form name='frm' method='post' action='paguaki.php'>";
	for($i=2013; $i<=date('Y')+1; $i++) { $prd .= "<option value='$i' " . 
		(isset($_REQUEST['prd'])? ($_REQUEST['prd']==$i? " selected": "") : ($i==date('Y')? " selected": "") ) . ">$i</option>"; }
	
	echo "<h2>Pagu AKI Unit / Bidang<br></h2>";
	echo "
		<table border='1'>
			<tr>
				<td>&nbsp;Periode&nbsp;</td>
				<td>&nbsp;<select name='prd' id='prd'>$prd</select>&nbsp;</td>
			</tr>
			<tr><td colspan='2' align='center'><input type='submit' value='Lihat'></td></tr>
		</table><br>";
	echo "</form>";
	
	if(isset($_REQUEST['prd'])) {
		require_once "../config/control.inc.php";
		$link = new mysqli($srv, $usr, $pwd,$db);
		if (!$link) {
			echo "Failed to connect to MySQL: " . $mysqli -> connect_error; exit();
		}
		//mysqli_select_db($db);
		
		// $sql = "SELECT *
				// FROM posinduk p
  				// INNER JOIN saldopos s
    			// ON kdindukpos = kdsubpos
      			// AND tahun = '$_REQUEST[prd]'
// LEFT JOIN (SELECT posinduk, SUM(nilaitunai) nt FROM skkoterbit GROUP BY posinduk) t
				// ON p.kdindukpos = t.posinduk
				// WHERE kdindukpos = '62' 
				// ORDER BY kdindukpos
				// ";
		$sql = "Select * From bidang a Inner Join saldoakibidang b On a.id = b.kdbidang AND tahun = '$_REQUEST[prd]' WHERE a.id <> 3 Order By a.namaunit";
		
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		
		echo "Tahun <input type='text' name='tprd' id='tprd' value='$_REQUEST[prd]' size='4' readonly><br><br>";
		echo "<a href='#' onclick='tambahaki($_REQUEST[prd])'>(+) Tambah/Edit Pagu Aki</a><br><br>";
		echo "
			<table border='1'>
				<tr>
					<th>No</th>
					<th>KODE BIDANG</th>
					<th>NAMA UNIT / BIDANG</th>
					<th>TOTAL PAGU</th>
				</tr>";
	
		$no = 0;
		while ($row = mysqli_fetch_array($result)) {
			$no++;
			echo "
				<tr>
					<td>$no</td>
					<td>$row[nick]</td>
					<td>$row[namaunit]</td>
					<td align='right'>" . number_format($row["rpaki"]) . "</td>
				</tr>";
		}
		mysqli_free_result($result);
		$mysqli->close();($link);	
		echo "</table>";
	}
?>
</body>

</html>