<html>
<head>
	<script type="text/javascript" src="js/methods.js"></script>
</head>
<body>
<?php
@session_start();
$nip=$_SESSION['nip'];
require_once "config/control.inc.php";
if ($_SESSION['adm']==2)
{

echo '<form name="frm" id="frm"  method="post" action="assign.php" onSubmit="return submitme()">';

	
	
	//mysql_select_db($db);

	$sql = "SELECT * FROM notadinas WHERE COALESCE(progress,0) < 8";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

	$nd = "<textarea hidden name='jobnd' id='jobnd'></textarea> &nbsp;<select name='nd' id='nd' size='10'>";
	while ($row = mysqli_fetch_array($result)) {
		$nd .= ($row["nip"]==null? "<option value='$row[nomornota]'>$row[perihal]</option>": "");
	}
	$nd .= "</select>&nbsp;";
	mysqli_free_result($result);
	
	$th = "";
	$td = "";
	$column = 0;
	$usr = "&nbsp;<select name='usr' id='usr' size='10'>";
	
	$sql = "SELECT * FROM USER WHERE adm = 1";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {
		$usr .= "<option value='$row[nip]'>$row[nama]</option>";

		$column++;
		$th .= "<th>$row[nama]<br><img src='./images/refresh16.png' onClick=\"refresh('s$row[nip]')\"></th>";
		$td .= "<td><textarea hidden name='job$row[nip]' id='job$row[nip]'></textarea> &nbsp;<select name='s$row[nip]' id='s$row[nip]' size='10'>";

		$insql = "SELECT * FROM notadinas WHERE COALESCE(progress,0) < 7 and nip='$row[nip]'";
		$inresult = mysqli_query($insql);
		while ($inrow = mysqli_fetch_array($inresult)) {
			$td .= "<option value='$inrow[nomornota]'>$inrow[perihal]</option>";
		}
		mysqli_free_result($inresult);

		$td .= "</select>&nbsp;</td>";
	}
	$usr .= "</select>&nbsp;";
	mysqli_free_result($result);
	$mysqli->close();($link);	

	echo "
		<div align='center'>
			<table border='1'>
				<tr><th colspan='3'>Disposisi Tugas</th></tr>
				<tr>
					<th>
						&nbsp;Nota Dinas&nbsp;<br><img src='./images/refresh16.png' onClick=\"refresh('nd')\">
					</th>
					<th>
						&nbsp;Process&nbsp;<br><img src='./images/refresh16.png' onClick=\"refresh('nd')\">
					</th>
					<th colspan='2'>
						ADM<br><img src='./images/refresh16.png' onClick=\"refresh('usr')\">
					</th>
				</tr>
				<tr>
					<td>$nd</td>
					<td align='center'>
						<img src='./images/r_arrow32k.png'/ id='r' width='32' height='32' 
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
						<img src='./images/u_arrow32k.png'/ id='u' width='32' height='32'
						onClick='assign(this.id)' onmouseover='abnormal(this.id)' onmouseout='normal(this.id)'>
						<input type='image' src='./images/MB_save32k.png'/ id='s' width='32' height='32'
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
}
else
{

echo '<form name="frm" id="frm" onSubmit="return submitme()">';

	
	//mysql_select_db($db);

	$sql = "SELECT * FROM notadinas n LEFT JOIN progress p 
ON COALESCE(n.progress, 0) = p.pid
WHERE nip = '$nip' AND COALESCE(progress,0) < 8
";
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
				<td>$row[pembuatskko]</td>
				<td>$row[noskkoi]</td>
				<td>$row[info]</td>
				<td><input type='button' onclick='proses(\"$row[nomornota]\")'></td>
			</tr>";
	}
	echo "</table>";

	mysqli_free_result($result);
	$mysqli->close();($link);	


echo '</form>';
 	
}
?>
</body>
</html>