<?php
	require_once "../config/control.inc.php";
	
	//mysqli_select_db($db);
	foreach ($_REQUEST as $param_name => $param_val) {
		if(substr($param_name,0,1)=='t') {
			$sub = substr($param_name, 1, strlen($param_name)-1);
			$akipos = $_REQUEST["aki".$sub];
			if($_REQUEST["c".$sub] !== $param_val or $_REQUEST["oldaki".$sub] !== $akipos) {
				$sql = "SELECT COUNT(*) jumlah FROM saldopos WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'";
				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
				while ($row = mysqli_fetch_array($result)) {
					$jumlah = $row["jumlah"];
				}
				mysqli_free_result($result);
				
				$rp = str_replace(".", "", str_replace(",", "", $param_val));
				$aki = str_replace(".", "", str_replace(",", "", $akipos));
				$sub = substr($param_name, 1, strlen($param_name)-1);
				$sql = ($jumlah==0? 
					"INSERT INTO saldopos(tahun, kdsubpos, rppos, akipos) VALUES ($_REQUEST[prd], '$sub', $rp, $aki)": 
					"UPDATE saldopos SET rppos = $rp, akipos = $aki WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'");
				mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysqli_error());
			}
		}
	}
	$mysqli->close();($link);	
	echo "<script>window.open('tambahpagu.php?prd=$_REQUEST[prd]','_self');</script>";
?>