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
		echo "Tahun <input type='text' name='prd' id='prd' value='$_REQUEST[prd]' size='4' readonly><br>";
		
		require_once "../config/control.inc.php";
		$link = mysql_connect($srv, $usr, $pwd);
		if (!$link) {
			die('Could not connect: ' . mysql_error());
		}
		mysql_select_db($db);
		

//		$sql = "SELECT rppos FROM saldopos WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$_REQUEST[pos]'";
		$sql = "SELECT sum(akipos) as akipos FROM saldoakibidang WHERE tahun = $_REQUEST[prd]";
//		echo "$sql<br>";
			
		$result = mysql_query($sql);
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {$tot = $row["akipos"]; }
		mysql_free_result($result);		

		$bidangselect = "";
		
		$sql = "SELECT * FROM bidang";
		$result = mysql_query($sql);
		
		while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
			$bidangselect .= "<option value='$row[id]' >$row[namaunit]</option>"; 
		}
		
		echo "
			<table border='1'>
				<tr>
					<td>&nbsp;Pelaksana &nbsp;</td>
					<td>&nbsp;<select name='bidangselect' id='bidangselect' onchange='ambildataaki()'>$bidangselect</select>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;Nilai AKI &nbsp;</td>
					<td>&nbsp;
						<input type='hidden' name='nilaioldaki' id='nilaiaki' value=''>
						<input type='text' name='nilaiaki' id='nilaiaki' value=''>&nbsp;</td>
				</tr>
				<tr>
					<td align='center' colspan='8'>
					<input type='button' value='Kembali' 
						onclick=\"window.open('paguaki.php?prd=$_REQUEST[prd]', '_self')\">
					<input type='submit' value='Simpan'>
					</td>
				</tr>
			</table><br>
		";
		
		echo "</form>";
		
		mysql_close($link);

		echo "Total pagu = Rp." . number_format($tot) . "<br>";
		echo "<div id='sudah'>Pagu yang sudah dirinci = Rp.<span id='akiterpakai'>0</span></div><br>";
		echo "<div id='belum'>Pagu yang belum dirinci = Rp.<span id='akibelumterpakai'>0</span></div><br>";
	}
?>
	<script>
		$(document).ready(function() {
			
		});
	</script>

</body>

</html>