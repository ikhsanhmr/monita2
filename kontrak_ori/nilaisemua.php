<?php
	session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	$pos = $_REQUEST["p"];
//	echo "$skk<br>";
	
	$sql = "
SELECT d.*, YEAR(tanggal) th, nama FROM notadinas_detail d 
LEFT JOIN notadinas n ON d.nomornota = n.nomornota 
LEFT JOIN v_pos v ON d.pos1 = v.akses
WHERE noskk = '$skk' AND pos1 = '$pos'";
//	echo "$sql<br>";

	$th = 0;
	$nm = "";
	$n1 = 0;
	$p1 = "";
	$result = mysql_query($sql);	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$th = $row["th"];
		$nm = $row["nama"];
		$n1 = $row["nilai1"];
		$p1 = $row["pos1"];
	}
	mysql_free_result($result);
	
	$sql = "
		SELECT rppos FROM saldopos WHERE tahun = $th AND kdsubpos = '$p1'
		UNION 
		SELECT rppos FROM saldopos2 WHERE tahun = $th AND kdsubpos = '$p1'
		UNION 
		SELECT rppos FROM saldopos3 WHERE tahun = $th AND kdsubpos = '$p1'
		UNION 
		SELECT rppos FROM saldopos4 WHERE tahun = $th AND kdsubpos = '$p1'";
//	echo "$sql<br>";

	$pagu = 0;
	$result = mysql_query($sql);	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { $pagu = $row["rppos"]; }
	mysql_free_result($result);
	
	$sql = "	
SELECT SUM(nilai1) nilai1 FROM notadinas n
LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
WHERE YEAR(tanggal) = $th  AND NOT d.progress IN (1, 5) AND d.pos1 = '$p1'";
//	echo "$sql<br>";
	
	$pakai = 0;
	$result = mysql_query($sql);	
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) { $pakai = $row["nilai1"]; }
	mysql_free_result($result);
	mysql_close($link);	  		
		
	echo "$nm - Nilai = " . number_format($n1) . " Pagu = " . number_format($pagu) . " Sisa = " . number_format($pagu - $pakai);
?>