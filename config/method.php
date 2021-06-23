<?php
	function myIp() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) { //check ip from share internet
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { //to check ip is pass from proxy
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else { 
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
	
	
	function dbConn() {
		require_once("inc.php");

		$mysql = new mysql($svr, $usr, $pwd, $db);
		/*
		if($db==0) { $mysql = new mysql($svr, $usr, $pwd, $db); } 
		else { $mysql = new mysql($svr1, $usr1, $pwd1, $db1); }
		*/
		if (mysql_connect_errno()) {
			printf("Connection failed: %s\n", mysql_connect_error());
			exit();
		}
		return $mysql;
	}
	
	
	function db($mysql, $query) {
		$result = $mysql->query($query);
		while($row = $result->fetch_array()) { $rows[] = $row; }
		$result->close();
		
		return (isset($rows)? $rows: null);
	}
	
	
	function dbExec($mysql, $query) {
		$result = $mysql->query($query);
		return $result;
	}


	function dbClose($mysql) {
		$mysql->close();
	}
/*
// 	see below to see how it works
	$db = dbConn();
	echo "query 2: " . dbExec($db, "INSERT INTO userlog(userid, timein, timeout) VALUES(3,SYSDATE(), SYSDATE())");
	echo "query 1: " . dbExec($db, "INSERT INTO userlog VALUES(1,SYSDATE(), SYSDATE())") . "<br>";
/*
	$db = dbConn();

	$rows = db($db, "select * from akses");
	foreach($rows as $row) { echo "$row[kdakses] - $row[keterangan]<br>"; }

	$rows = db($db, "select * from header");
	foreach($rows as $row) { echo "$row[kdheader] - $row[keterangan]<br>"; }
	
	dbClose($db);
	
	$rows = db($db, "select * from bpkpi");
	foreach($rows as $row) { echo "$row[kdbpkpi] - $row[keterangan]<br>"; }
*/
?>\