<?php
	session_start();
	$nip = $_SESSION['nip'];
	if($nip=="") {
		exit;
	}

	$t = trim($_REQUEST["t"]);
	$i = trim($_REQUEST["i"]);
	require_once "../config/control.inc.php";	
	
	$nota = "<select name='pic$t' id='pic$t' onchange='ndcheck($t)'>";
	$query = "SELECT * FROM notadinas_detail n LEFT JOIN bidang b
		ON n.pelaksana = b.id
		WHERE nomornota = '$i' AND COALESCE(progress,0) = 0";
//	echo "$query<br>";
	
	if ($result = mysqli_query($query)) {
//		echo "result : $result<br>";
		$nota .= "<option value=''>Pilih Pelaksana</option>";
		while ($row = mysqli_fetch_array($result)) {
//			echo "$row[nid] - $row[namaunit]<br>";
			$nota .= "<option value='$row[nid]'>$row[namaunit]</option>";
		}
		mysqli_free_result($result);
	}
	$mysqli->close();($kon);
	$nota .= "</select>&nbsp;";
	echo $nota;
?>