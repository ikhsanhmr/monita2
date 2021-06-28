<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript">
</script>

</head>
<body>
<?php
	require_once "../config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);

	$skk = $_REQUEST["skk"];
	$sql = "";
	
	foreach ($_REQUEST as $param_name => $param_val) {
		if(substr($param_name,0,1)=="k") {$k = $param_val;}
		if(substr($param_name,0,1)=="u") {$u = $param_val;}
		if(substr($param_name,0,1)=="v") {$v = $param_val;}
		if(substr($param_name,0,1)=="a") {$a = $param_val;}
		if(substr($param_name,0,1)=="r") {$r = $param_val;}
		if(substr($param_name,0,1)=="n") {
			$n = $param_val;
			
			$sql = "SELECT count(*) jumlah from kontrak where nomorskkoi='$skk' and nomorkontrak='$k'";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));			
			while ($row = mysqli_fetch_array($result)) {  
				$jumlah = $row["jumlah"];
			}
			mysqli_free_result($result);
			
			$sql = ($jumlah==0? "INSERT INTO kontrak(nomorskkoi, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak) values('$skk', '$k', '$u', '$v', '$a', '$r', '$n')"
				: "update kontrak set uraian='$u', vendor='$v', tglawal='$a', tglakhir='$r', nilaikontrak='$n' where nomorskkoi='$skk' and nomorkontrak='$k'");
			echo $sql;
			mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		}
	}
	$mysqli->close();($link);	
//	echo "<script>window.open('kontrak.php?skk=$skk','_self')</script>";
?>
</body>
</html>