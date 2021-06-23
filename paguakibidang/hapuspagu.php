<?php
	require_once "../config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);
	
//	for($i=1; $i<=4; $i++) {
	$dummy = count(explode(".", $_REQUEST["pos"]));
	for($i=$dummy; $i<=4; $i++) {
		$sql = "delete from saldoakibidang where kdbidang = '$_REQUEST[pos]' and tahun = $_REQUEST[prd]";
		mysql_query($sql) or die(mysql_error());
	}
	mysql_close($link);	

	$pieces = explode(".", $_REQUEST["pos"]);
	$back = "";
	
	for($i=1; $i<=count($pieces); $i++) {
		$back .= ($i==count($pieces)? "": (($back==""? "": ".") . $pieces[$i-1]));
	}
	//echo "back : $back<br>";
	
	$url = ($dummy==1? "paguaki.php?prd=$_REQUEST[prd]": "subaki.php?prd=$_REQUEST[prd]&pos=$back&ke=".(count($pieces)-1));
//	echo $url;
	echo "<script>window.open('$url','_self');</script>";
?>