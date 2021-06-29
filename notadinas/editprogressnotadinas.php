<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<?php
	session_start();
	if (!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	$nip = $_SESSION['cnip'];
	?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../js/methods.js"></script>
	<title>Untitled Document</title>

	<script type="text/javascript">
		function viewk(x) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			//alert(kpos);

			if (p1 > p2 || p1 == "") {
				if (p2 != "") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			var url = encodeURI("editprogressnotadinas.php?p1=" + p1 + "&p2=" + p2 + "&v=1");
			//alert(url);
			window.open(url, "_self");
		}
	</script>
	<?php
	$p1 = isset($_REQUEST["p1"]) ? $_REQUEST["p1"] : "";
	$p2 = isset($_REQUEST["p2"]) ? $_REQUEST["p2"] : "";
	$v = isset($_REQUEST["v"]) ? $_REQUEST["v"] : "";
	?>
</head>

<body>
	<?php

	$parm = "";
	$parm .= ($p1 == "" ? "" : " and YEAR(tanggal) = " . substr($p1, 0, 4) . " AND MONTH(tanggal) >= " . substr($p1, -2));
	$parm .= ($p2 == "" ? "" : " and YEAR(tanggal) = " . substr($p2, 0, 4) . " AND MONTH(tanggal) <= " . substr($p2, -2));
	echo "
			<table>
				<tr>
					<th>Periode (yyyy-mm)</th>
					<td>:</td>
					<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='View' onclick='viewk()'>
					</td>
				</tr>
			</table>";

	require_once "../config/control.inc.php";
	if ($v != "") {
		echo "<form name='frm' id='frm' method='post' action='editprognd.php'>";


		//mysql_select_db($db);

		$sql = "SELECT 	n.nomornota as nomornota, n.tanggal, n.perihal, n.skkoi as skkoi, n.nilaiusulan, 
					n.progress as progress, n.nip, n.assigndt, n.nipuser, p.pid, p.info, p.keterangan, u.nama, 
					u.kdunit as unitanggaran, u.bidang, u.adm
			FROM 	notadinas n LEFT JOIN 
					progress p ON COALESCE(n.progress, 0) = p.pid LEFT JOIN 
					user u on n.nip=u.nip 
			WHERE 1=1 
			$parm
			order by tanggal DESC
	";
		//AND COALESCE(progress,0) >= 2 and COALESCE(progress,0) < 8 
		//echo $sql;
		$result = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli));
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
				<th>Pembuat SKKO/I</th>
				<th>Nomor SKKO/I</th>
				<th>Progress</th>
				<th>Proses</th>
			</tr>
	";

		$no = 0;
		while ($row = mysqli_fetch_array($result)) {
			$no++;

			echo "
				<tr>
					<td>$no</td>
					<td>
						$row[nomornota]
						<input type='hidden' name='id$no' value='$row[nomornota]'>
					</td>
					<td>$row[tanggal]</td>
					<td>$row[unit]</td>
					<td>$row[perihal]</td>
					<td>$row[skkoi]</td>
					<td>$row[nilaiusulan]</td>
					<td>$row[nama]</td>	
					<td>$row[nomorskk]</td>
					<td>$row[info]</td>
					<td><input type='button' value='Ubah Progress' onclick='window.open(\"editprognd.php?nd=$row[nomornota]\",\"_self\")'></td>
				 </tr>
		";
		}
		echo "</table>";

		mysqli_free_result($result);
		$mysqli->close();
		($link);


		echo '</form>';
	}
	?>
</body>

</html>