<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	?>	
	
	<script type="text/javascript">
		function showme(me) {
			var url = "tambah.php";
			var parm= encodeURI("k=" + document.getElementById(me).value.trim());

			var xmlhttp;
			if (window.XMLHttpRequest) {
				xmlhttp=new XMLHttpRequest();
			} else {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange=function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					document.getElementById("showhere").innerHTML=xmlhttp.responseText;
				}
			}
			
			xmlhttp.open("POST", url, true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlhttp.send(parm);		
		}
	</script>
</head>

<body <?php echo (isset($_GET["k"])? "onload='showme(\"nk\")'": "");  ?>  >
<?php

	require_once '../config/koneksi.php';
		
	$sql = "SELECT * FROM kontrak WHERE Year(inputdt) = ".date("Y")." order by nomorkontrak ASC";

	// echo $sql;
	// return;
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

	$th = "<select name='nk' id='nk'><option value=''></option>";
	while ($row = mysqli_fetch_array($result)) {
		$th .= "<option value='$row[nomorkontrak]'>$row[nomorkontrak]</option>";
	}
	$th .= "</select>";
	mysqli_free_result($result);

	$k = $_GET["k"];

	echo "
		<h2>Aset PDP</h2>
		<form name='frm' id='frm' method='post' action='simpan.php'>
			<input type='hidden' name='isedit' id='isedit' value='$k'>
			<table border='0'>
				<tr>
					<th>Nomor Kontrak</th>
					<td>:</td>
					" . (isset($k)? "<td><input type='text' name='nk' id='nk' size='70' value='$k' readonly></td>": "<td>$th</td>") . "
					" . (isset($k)? "": "<td><input value='>>' type='button' onclick=\"showme('nk')\"></td>") . "
				</tr>
			</table>
			<div id='showhere'></div>
		</form>";
?>
</body>
</html>