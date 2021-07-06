<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/jquery-1.10.2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
	<script type="text/javascript" src="../js/jquery.datepick.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript">
		$(function() {
			$("#awal").datepick({dateFormat: 'yyyy-mm-dd'});
			$("#akhir").datepick({dateFormat: 'yyyy-mm-dd'});
		});
	</script>
</head>

<body>
	<?php
		error_reporting(0);  session_start(); 
		require_once '../config/koneksi.php';
		$nip=$_SESSION['nip'];
		$bidang=$_SESSION['bidang'];
		$kdunit=$_SESSION['kdunit'];
		$nama=$_SESSION['nama'];
		$adm=$_SESSION['adm'];
		if($nip=="") {exit;}
		
		$kon = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
		if($kon!="") {
			$sql = "select * from kontrak where nomorkontrak='$kon'";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			while ($row = mysqli_fetch_array($result)) {
				$skk = $row["nomorskkoi"];
				$pos = $row["pos"];
				$uraian = $row["uraian"];
				$vendor = $row["vendor"];
				$awal = $row["tglawal"];
				$akhir = $row["tglakhir"];
				$nilai = $row["nilaikontrak"];
				//echo "$skk - $pos - $uraian - $vendor - $awal - $akhir<br>";
			}
		
			mysqli_free_result($result);
			$mysqli->close();($link);	  			
		} else {
			$skk = $_REQUEST["skk"];
			$pos = $_REQUEST["pos"];		
		}
		
		echo "
			<h2>" . ($kon!=""? "EDIT ": "INPUT ") . "KONTRAK</h2>
			<form name='fkontrak' id='fkontrak' method='post' action='simpan.php'>
				<table border='1'>
					<tr>
						<td>Nomor SKKI/O</td>
						<td>&nbsp;:&nbsp;</td>
						<td>
							<input type='hidden' name='edit' id='edit' value='$kon'>
							<input size='52' readonly type='text' name='skk' id='skk' value='$skk'>
						</td>
					</tr>
					<tr>
						<td>POS</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' readonly type='text' name='pos' id='pos' value='$pos'></td>
					</tr>
					<tr>
						<td>Nomor Kontrak</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' type='text' name='kontrak' id='kontrak' value='" 
						. ($kon==""? "": $kon) . "'" . ($kon==""? "": " readonly") . "></td>
					</tr>
					<tr>
						<td>Uraian Kegitatan</td>
						<td>&nbsp;:&nbsp;</td>
						<td><textarea rows='3' cols='50' name='uraian' id='uraian'>" . ($kon==""? "": $uraian) . "</textarea></td>
					</tr>
					<tr>
						<td>Vendor</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' type='text' name='vendor' id='vendor' value='" . ($kon==""? "": $vendor) . "'></td>
					</tr>
					<tr>
						<td>Tanggal Awal</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' type='text' name='awal' id='awal' value='" . ($kon==""? "": $awal) . "'></td>
					</tr>
					<tr>
						<td>Tanggal Akhir</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' type='text' name='akhir' id='akhir' value='" . ($kon==""? "": $akhir) . "'></td>
					</tr>
					<tr>
						<td>Nilai Kontrak</td>
						<td>&nbsp;:&nbsp;</td>
						<td><input size='52' type='text' name='nilai' id='nilai' value='" . ($kon==""? "": $nilai) . "'></td>
					</tr>
					<tr>
						<td colspan='3'></td>
					</tr>
					<tr>
						<td colspan='3' align='right'>
							<input type='submit' value='Simpan'>&nbsp;
							<input type='button' value='Batal' onclick='window.open(\"index.php\", \"_self\")'>
						</td>
					</tr>
				</table>
			</form>
		";
	?>
</body>
</html>