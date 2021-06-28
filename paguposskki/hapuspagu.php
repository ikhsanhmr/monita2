<?php
	require_once "../config/control.inc.php";
	
	//mysql_select_db($db);
	
//	for($i=1; $i<=4; $i++) {
	$dummy = count(explode(".", $_REQUEST["pos"]));
	for($i=$dummy; $i<=4; $i++) {
		$sql = "delete from saldopos"  . ($i>1? $i: "") . " where kdsubpos like '$_REQUEST[pos]%' and tahun = $_REQUEST[prd]";
		mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
	}
	$mysqli->close();($link);	

	$pieces = explode(".", $_REQUEST["pos"]);
	$back = "";
	
	for($i=1; $i<=count($pieces); $i++) {
		$back .= ($i==count($pieces)? "": (($back==""? "": ".") . $pieces[$i-1]));
	}
	//echo "back : $back<br>";
	
	$url = ($dummy==1? "pagupos.php?prd=$_REQUEST[prd]": "subpos.php?prd=$_REQUEST[prd]&pos=$back&ke=".(count($pieces)-1));
//	echo $url;
	echo "<script>window.open('$url','_self');</script>";
?>