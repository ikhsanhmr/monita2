<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	
	$sql = "SELECT DISTINCT pos1 FROM notadinas_detail WHERE noskk='$skk' /* AND progress = 7 */ ORDER BY pos1";
	$result = mysql_query($sql);
	
	$skk = "<select name='pos' id='pos' required onchange='viewnilai(\"infopos\")'><option value=''></option>";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$skk .= "<option value='$row[pos1]'>$row[pos1]</option>";
	}
	$skk .= "</select>";
	
	mysql_free_result($result);
	mysql_close($kon);	  			
	echo $skk;
?>