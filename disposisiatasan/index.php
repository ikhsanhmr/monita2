<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link href="../css/screen2.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/methods.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Untitled Document</title>
</head>
<body>
<?php
	error_reporting(0);  session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	$nip=$_SESSION['nip'];
	require_once "../config/control.inc.php";
	
	echo '<form name="frm" id="frm"  method="post" action="assign.php" onSubmit="return submitme()">';

	
	

	$sql = "SELECT * FROM notadinas WHERE COALESCE(progress,0) <= 2";
//	echo $sql;
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

	$nd = "<textarea hidden name='jobnd' id='jobnd'></textarea> &nbsp;<select name='nd' id='nd' size='10'>";
	while ($row = mysqli_fetch_array($result)) {
		$nd .= ($row["nip"]==null? "<option value='$row[nomornota]'>$row[nipuser]-$row[perihal]</option>": "");
	}
	$nd .= "</select>&nbsp;";
	mysqli_free_result($result);
	
	$th = "";
	$td = "";
	$column = 0;
	$usr = "&nbsp;<select name='usr' id='usr' size='10'>";
	
	$sql = "SELECT * FROM USER WHERE adm = 1 and nama != 'GM'";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {
		$usr .= "<option value='$row[nip]'>$row[nama]</option>";

		$column++;
		$th .= "<th>$row[nama]<br><img src='../images/refresh16.png' onClick=\"refresh('s$row[nip]')\"></th>";
		$td .= "<td><textarea hidden name='job$row[nip]' id='job$row[nip]'></textarea> &nbsp;<select name='s$row[nip]' id='s$row[nip]' size='10'>";

		$insql = "SELECT * FROM notadinas WHERE COALESCE(progress,0) <= 2 and nip='$row[nip]'";
		$inresult = mysqli_query($mysqli, $insql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($inrow = mysqli_fetch_array($inresult)) {
			$td .= "<option value='$inrow[nomornota]'>$inrow[nipuser]-$inrow[perihal]</option>";
		}
		mysqli_free_result($inresult);

		$td .= "</select>&nbsp;</td>";
	}
	$usr .= "</select>&nbsp;";
	mysqli_free_result($result);
		

	echo "
		<div align='center'>
			<table border='1'>
				<tr><th colspan='3'>Disposisi Tugas</th></tr>
				<tr>
					<th>
						&nbsp;Nota Dinas&nbsp;<br><img src='../images/refresh16.png' onClick=\"refresh('nd')\">
					</th>
					<th>
						&nbsp;Process&nbsp;<br><img src='../images/refresh16.png' onClick=\"refresh('nd')\">
					</th>
					<th colspan='2'>
						ADM<br><img src='../images/refresh16.png' onClick=\"refresh('usr')\">
					</th>
				</tr>
				<tr>
					<td>$nd</td>
					<td align='center'>
						<img src='../images/r_arrow32k.png'/ id='r' width='32' height='32' 
						onClick='assign(this.id)' onmouseover='abnormal(this.id)' onmouseout='normal(this.id)'>
					</td>
					<td>$usr</td>
				</tr>
			</table>
		</div><br>";

	echo "
		<div align='center'>
			<table border='1'>
				<tr>
					<th colspan='$column'>
						Tugas<br>
						<img src='../images/u_arrow32k.png'/ id='u' width='32' height='32'
						onClick='assign(this.id)' onmouseover='abnormal(this.id)' onmouseout='normal(this.id)'>
						<input type='image' src='../images/MB_save32k.png'/ id='s' width='32' height='32'
						 onmouseover='abnormal(this.id)' onmouseout='normal(this.id)'>
					</th>
				</tr>
				<tr>$th</tr>
				<tr>$td</tr>
			</table>
		</div>";
//						<img src='./images/MB_save32k.png'/ id='s' width='32' height='32'
//						onClick='submitme()' onmouseover='abnormal(this.id)' onmouseout='normal(this.id)'>
echo '</form>';
?>
</body>
</html>