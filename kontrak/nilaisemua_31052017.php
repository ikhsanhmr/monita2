<?php ob_start(); 
error_reporting(0);  session_start(); ?>
<?php
	//error_reporting(0);  session_start(); 
	require_once '../config/koneksi.php';
	
	$result = "";
	$skk = $_REQUEST["s"];
	$pos = $_REQUEST["p"];

	$sql = "
		SELECT d.*, kontrak, namapos FROM notadinas_detail d
		LEFT JOIN (
			SELECT nomorskkoi, pos, SUM(nilaikontrak) kontrak FROM kontrak GROUP BY nomorskkoi, pos
		) k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos
		LEFT JOIN (
			SELECT kdindukpos kdpos, namaindukpos namapos FROM posinduk UNION ALL 
			SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk2 UNION ALL 
			SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk3 UNION ALL 
			SELECT kdsubpos kdpos, namasubpos namapos FROM posinduk4
		) p ON d.pos1 = p.kdpos
		WHERE 
			noskk = '$skk' 
			AND pos1 = '$pos'";
	
	$nilai = 0;
	$kontrak = 0;
	$sisa = 0;
	$nama = "";
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));	
	while ($row = mysqli_fetch_array($result)) {
		$nilai = $row["nilai1"];
		$kontrak = $row["kontrak"];
		$sisa = $nilai - $kontrak;
		$nama = $row["namapos"];
	}
	mysqli_free_result($result);
	$mysqli->close();($kon);
	
	echo "$nama <br>Disburse Anggaran = " . number_format($nilai) . " Terkontrak = " . number_format($kontrak) . " Sisa = " . number_format($sisa) . "<input type='hidden' name='sisanya' id='sisanya' value='$sisa'>";			
/*	
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
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));	
	while ($row = mysqli_fetch_array($result)) {
		$th = $row["th"];
		$nm = $row["nama"];
		$n1 = $row["nilai1"];
		$p1 = $row["pos1"];
	}
	mysqli_free_result($result);
	
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
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));	
	while ($row = mysqli_fetch_array($result)) { $pagu = $row["rppos"]; }
	mysqli_free_result($result);
	
	$sql = "	
SELECT SUM(nilai1) nilai1 FROM notadinas n
LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota 
WHERE YEAR(tanggal) = $th  AND NOT d.progress IN (1, 5) AND d.pos1 = '$p1'";
//	echo "$sql<br>";
	
	$pakai = 0;
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));	
	while ($row = mysqli_fetch_array($result)) { $pakai = $row["nilai1"]; }
	mysqli_free_result($result);
	$mysqli->close();($kon);	  		
		
	echo "$nm - Nilai = " . number_format($n1) . " Pagu = " . number_format($pagu) . " Sisa = " . number_format($pagu - $pakai) . "<input type='hidden' name='sisanya' id='sisanya' value='" . ($pagu-$pakai) . "'>";
*/
?>