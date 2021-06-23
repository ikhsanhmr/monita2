<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	
	<script type="text/javascript">
		function viewk() {
			var k = document.getElementById("skk").value;
			var m = document.getElementById("mdu").value;
			var url = encodeURI("index.php?k="+k+"&m="+m+"&v=1");
			window.open(url, "_self");
		}
	</script>
	
	<?php
		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
	?>
</head>


<body>
	<?php
		require_once "../config/koneksi.php";
		$k = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
		$m = isset($_REQUEST["m"])? $_REQUEST["m"]: "";
		$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";

		$sql = "SELECT * FROM mdu ORDER BY mduid";
		$result = mysql_query($sql);
		
		$mdu = "<select name='mdu' id='mdu' onchange=''><option value=''></option>";
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			$mdu .= "<option value='$row[mduid]'" . ($row["mduid"]==$m? " selected": "") . ">$row[nama]</option>";
		}
		$mdu .= "</select>";		
		mysql_free_result($result);
		
		

		echo "
			<h2>JPROC MDU</h2>
			<table>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='49' value='$k'></td>
				</tr>
				<tr>
					<th>MDU</th>
					<td>:</td>
					<td>$mdu</td>
				</tr>
				<tr>
					<td colspan='3' align='right'><input type='button' value='Ok' onclick='viewk()'></td>
				</tr>
			</table>";
		
		if($v==1) {
			$sql = "
				SELECT *
				FROM (
					SELECT DISTINCT nomorskko noskk FROM skkoterbit 
					UNION
					SELECT DISTINCT nomorskki noskk FROM skkiterbit 
				) s WHERE noskk = '$k'";
			$result = mysql_query($sql);
			
			if(mysql_num_rows($result) > 0) {
				$sql = "
					SELECT noskk, butuh, alokasi, ms.* FROM mdudata md
					RIGHT JOIN (
						SELECT m.*, submduid, s.nama namasub FROM mdu m
						LEFT JOIN mdusub s ON m.mduid = s.mduid
					) ms ON md.mduid = ms.mduid AND md.submduid = ms.submduid AND noskk = '$k'
					WHERE ms.mduid = '$m'";
				$result = mysql_query($sql);
				
				echo "
					<table>
						<tr>
							<th>No</th>
							<th>Nama</th>
							<th>No</th>
							<th>No</th>
						</tr>
					</table>
				";
				
				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					echo 
				
				}
				mysql_free_result($result);
			} else {
				echo "<script>alert('SKK $k belum terbit!')</script>";
			}
		}
		mysql_close($kon);
	?>
</html>
