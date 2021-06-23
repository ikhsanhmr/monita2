<?php
	require_once "../config/control.inc.php";
	$link = mysql_connect($srv, $usr, $pwd);
	if (!$link) {
		die('Could not connect: ' . mysql_error());
	}
	mysql_select_db($db);

	foreach ($_REQUEST as $param_name => $param_val) {
		if(substr($param_name,0,3)=="job") {
			//echo "$param_name - $param_val <br>";
			$usr = substr($param_name,3,strlen($param_name)-3);
			//echo $param_name.'</br>';
			$dummy = explode("[splitme]", $param_val);			
			for($i=0; $i<sizeof($dummy); $i++) {
				if($dummy[$i]!=="") {
					$sql = "update notadinas set nip = " . 
						($usr=="nd"? "null": "'$usr'" . ", assigndt = SYSDATE() ") . 
						",progress=".($usr=="nd"? "null": ($param_name=="jobuser"? "1": "2"))." WHERE nomornota = '$dummy[$i]'";
					//echo $sql .'</br>';
					mysql_query($sql) or die(mysql_error());
				}
			}
		}
	}
	mysql_close($link);	
	echo "<script>window.open('index.php','_self');</script>";
?>