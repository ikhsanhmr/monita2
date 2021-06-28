<?php
	require_once "../config/control.inc.php";
	
	//mysql_select_db($db);
	foreach ($_REQUEST as $param_name => $param_val) {
		//echo "$param_name => $param_val<br>";

// 		if(substr($param_name,0,1)=='t') {
// 			$sub = substr($param_name, 1, strlen($param_name)-1);
// 			if($_REQUEST["c".$sub] !== $param_val) {
// 				$sub = trim(str_replace("_", ".", $sub));
// 				$idx = count(explode(".", $sub));
				
// //				$sql = "SELECT COUNT(*) jumlah FROM saldopos2 WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'";
// 				$sql = "SELECT COUNT(*) jumlah FROM saldopos$idx WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'";
// 				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
// 				while ($row = mysqli_fetch_array($result)) {
// 					$jumlah = $row["jumlah"];
// 				}
// 				mysqli_free_result($result);
				
// 				$rp = str_replace(".", "", str_replace(",", "", (empty($param_val) ? 0 : $param_val)));
// 				$sql = ($jumlah==0? 
// 					"INSERT INTO saldopos$idx(tahun, kdsubpos, rppos) VALUES ($_REQUEST[prd], '$sub', $rp)": 
// 					"UPDATE saldopos$idx SET rppos = $rp WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'");
// 				mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
// //				echo "$sql<br>";
// 			}
// 		}

		$sub = substr($param_name, 1, strlen($param_name)-1);
		$sub = trim(str_replace("_", ".", $sub));
		$idx = count(explode(".", $sub));

		$sql = "SELECT COUNT(*) jumlah FROM saldopos$idx WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {
			$jumlah = $row["jumlah"];
		}
		mysqli_free_result($result);

		switch(substr($param_name,0,1)) {
			case "t" : //echo "$param_name - $param_val<br>"; 
				
				if($_REQUEST["c".$sub] !== $param_val) {

					$rp = str_replace(".", "", str_replace(",", "", (empty($param_val) ? 0 : $param_val)));

				}else{

					$rp = str_replace(".", "", str_replace(",", "", (empty($_REQUEST["c".$sub]) ? 0 : $_REQUEST["c".$sub])));
				}

				break;
			case "b" : 
				
				if($_REQUEST["a".$sub] !== $param_val) {

					$aki = str_replace(".", "", str_replace(",", "", (empty($param_val) ? 0 : $param_val)));
				}else{

					$aki = str_replace(".", "", str_replace(",", "", (empty($_REQUEST["a".$sub]) ? 0 : $_REQUEST["a".$sub])));
				}

				$sql = ($jumlah==0? 
					"INSERT INTO saldopos$idx(tahun, kdsubpos, rppos, rpaki) 
					VALUES ($_REQUEST[prd], '$sub', $rp, $aki)": 
					"UPDATE saldopos$idx SET rppos = $rp, rpaki = $aki WHERE tahun = $_REQUEST[prd] AND kdsubpos = '$sub'");
				mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
//				echo "$sql<br>";
			
				break;
			
		}

	}
	$mysqli->close();($link);	
	
	$ke = count(explode(".", $_REQUEST["pos"]));
	//echo $_REQUEST['pos'];
	$url = "subpos.php?prd=$_REQUEST[prd]&pos=$_REQUEST[pos]&ke=$ke";
	//echo $url;
	echo "<script>window.open('$url','_self');</script>";
?>