<?php
	require_once "../config/control.inc.php";	
	$q = $_REQUEST["q"];
	$th = $_REQUEST["th"];

/*
	$query = "
		SELECT * FROM (
		SELECT * FROM saldopos WHERE tahun = " . date("Y") . "
		UNION 
		SELECT * FROM saldopos2 WHERE tahun = " . date("Y") . "
		UNION
		SELECT * FROM saldopos3 WHERE tahun = " . date("Y") . "
		UNION
		SELECT * FROM saldopos4 WHERE tahun = " . date("Y") . "
		) saldo
		WHERE kdsubpos = '$q' ORDER BY kdsubpos";
*/
	$query = "
		SELECT COALESCE(rppos,0)-COALESCE(nilai,0) rppos FROM 
		(
		SELECT * FROM (
		SELECT * FROM saldopos WHERE tahun = '$th'
		UNION 
		SELECT * FROM saldopos2 WHERE tahun = '$th'
		UNION
		SELECT * FROM saldopos3 WHERE tahun = '$th'
		UNION
		SELECT * FROM saldopos4 WHERE tahun = '$th'
		) saldo
		WHERE kdsubpos = '$q' ORDER BY kdsubpos
		) s LEFT JOIN
		(
		SELECT YEAR(tanggal) tahun, pos1, SUM(nilai1) nilai FROM notadinas n LEFT JOIN notadinas_detail d
		ON n.nomornota = d.nomornota
		WHERE YEAR(tanggal) = '$th' AND pos1 = '$q' and 
		(coalesce(d.progress,0)!=1 or coalesce(d.progress,0)!=5)
		GROUP BY YEAR(tanggal), pos1
		) n
		ON s.kdsubpos = n.pos1	
	";
//	echo $query;
	
	if ($result = mysqli_query($query)) {
		$rp = "";
		while ($row = mysqli_fetch_array($result)) {
			$rp = $row["rppos"];
		}
		mysqli_free_result($result);
	}
	$mysqli->close();($kon);
	
	echo $rp;
?>