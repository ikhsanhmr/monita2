<?php
	require_once "../config/control.inc.php";	
	$q = $_REQUEST["q"];
	$th = $_REQUEST["th"];
	$td = $_REQUEST["td"];

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
//		SELECT COALESCE(rppos,0)-COALESCE(nilai,0) rppos FROM 
	$query = "
		SELECT saldo.*, posx, nilaix, COALESCE(rppos,0)-COALESCE(nilaix,0) sisax FROM (
			SELECT * FROM saldopos WHERE tahun = $th
			UNION 
			SELECT * FROM saldopos2 WHERE tahun = $th
			UNION
			SELECT * FROM saldopos3 WHERE tahun = $th
			UNION
			SELECT * FROM saldopos4 WHERE tahun = $th
		) saldo
		LEFT JOIN (
			SELECT pos1 posx, SUM(COALESCE(nilai1,0)) nilaix 
			FROM notadinas n 
			LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
			WHERE YEAR(tanggal) = $th AND (COALESCE(n.progress,0) != 1 OR COALESCE(n.progress,0) != 5) AND n.nomornota != '$td'
			GROUP BY pos1
		) pakai ON  kdsubpos = posx
		WHERE kdsubpos = '$q' ORDER BY kdsubpos
	";
//	echo $query;
	
	if ($result = mysqli_query($query)) {
		$rp = "";
		while ($row = mysqli_fetch_array($result, MYSQL_BOTH)) {
			$pagu = $row["rppos"];
			$pakai = $row["nilaix"];
			$sisa = $row["sisax"];
			echo "$pagu<data>$pakai<data>$sisa<data>";
		}
		mysqli_free_result($result);
	}
	$mysqli->close();($kon);
//	echo $rp;
	echo "Tahun $th - Pagu POS $q : " . number_format($pagu,0) . " -- Nilai Yang Telah diusulkan : " . number_format($pakai,0) . " -- Sisa pagu : " . number_format($sisa,0);
?>