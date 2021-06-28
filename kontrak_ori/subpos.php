<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	
	$sql = "SELECT DISTINCT pos1 FROM notadinas_detail WHERE noskk='$skk' AND progress = 7 ORDER BY pos1";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$skk = "<select name='pos' id='pos' required onchange='viewnilai(\"infopos\")'><option value=''></option>";
	while ($row = mysqli_fetch_array($result)) {
		$skk .= "<option value='$row[pos1]'>$row[pos1]</option>";
	}
	$skk .= "</select>";
	
	mysqli_free_result($result);
	$mysqli->close();($link);	  			
	echo $skk;
?>