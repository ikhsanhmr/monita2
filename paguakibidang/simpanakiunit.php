<?php
	require_once "../config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);
	foreach ($_REQUEST as $param_name => $param_val) {

		if(substr($param_name,0,1)=='t') {
			$sub = substr($param_name, 1, strlen($param_name)-1);
			if($_REQUEST["c".$sub] !== $param_val) {
				$sub = trim(str_replace("_", ".", $sub));
				$idx = count(explode(".", $sub));
				
				$sql = "SELECT COUNT(*) jumlah FROM saldoakibidang WHERE tahun = $_REQUEST[prd] AND kdbidang = '$sub'";
				$result = mysql_query($sql);
				while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
					$jumlah = $row["jumlah"];
				}
				mysql_free_result($result);
				
				$rp = str_replace(".", "", str_replace(",", "", $param_val));
				$sql = ($jumlah==0? 
					"INSERT INTO saldoakibidang(tahun, kdbidang, rpaki) VALUES ($_REQUEST[prd], '$sub', $rp)": 
					"UPDATE saldoakibidang SET rpaki = $rp WHERE tahun = $_REQUEST[prd] AND kdbidang = '$sub'");
				mysql_query($sql) or die(mysql_error());
			}
		}

	}
	mysql_close($link);	
	
	$ke = count(explode(".", $_REQUEST["pos"]));
	//echo $_REQUEST['pos'];
	$url = "subaki.php?prd=$_REQUEST[prd]&pos=$_REQUEST[pos]&ke=$ke";
	//echo $url;
	echo "<script>window.open('$url','_self');</script>";
?>