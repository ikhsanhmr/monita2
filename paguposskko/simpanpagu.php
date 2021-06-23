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
				$sql = "SELECT COUNT(*) jumlah FROM saldopos WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'";
				$result = mysql_query($sql);
				while ($row = mysql_fetch_array($result, MYSQL_BOTH)) {
					$jumlah = $row["jumlah"];
				}
				mysql_free_result($result);
				
				$rp = str_replace(".", "", str_replace(",", "", $param_val));
				$sub = substr($param_name, 1, strlen($param_name)-1);
				$sql = ($jumlah==0? 
					"INSERT INTO saldopos(tahun, kdsubpos, rppos) VALUES ($_REQUEST[prd], '$sub', $rp)": 
					"UPDATE saldopos SET rppos = $rp WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'");
				mysql_query($sql) or die(mysql_error());
			}
		}
	}
	mysql_close($link);	
	echo "<script>window.open('tambahpagu.php?prd=$_REQUEST[prd]','_self');</script>";
?>