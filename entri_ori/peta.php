<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
</head>


<body>
<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];

	$sql = "
SELECT v.*, rppos, terbit, kontrak, bayar FROM 
(SELECT DISTINCT akses, nama FROM v_pos " . ($org>5 && $org<16? "":  ($org>="1"? " where nip='$nip'": "") ) . " ORDER BY akses) v
LEFT JOIN (
	SELECT * FROM 
		(SELECT DISTINCT akses FROM akses_pos " . ($org>5 && $org<16? "": ($org>="1"? " where nip='$nip'": "") ) . ") a 
		LEFT JOIN (	
			SELECT kdsubpos, rppos FROM saldopos WHERE tahun = YEAR(SYSDATE())
			UNION 
			SELECT kdsubpos, rppos FROM saldopos2 WHERE tahun = YEAR(SYSDATE())
			UNION 
			SELECT kdsubpos, rppos FROM saldopos3 WHERE tahun = YEAR(SYSDATE())
			UNION 
			SELECT kdsubpos, rppos FROM saldopos4 WHERE tahun = YEAR(SYSDATE())
		) s ON a.akses = s.kdsubpos 
) p ON v.akses = p.kdsubpos
LEFT JOIN (
	SELECT pos1, SUM(nilai1) terbit FROM notadinas_detail d LEFT JOIN notadinas n ON d.nomornota = n.nomornota WHERE YEAR(tanggal) = YEAR(SYSDATE()) AND d.progress >= 7 GROUP BY pos1
) d ON v.akses = d.pos1
LEFT JOIN (
	SELECT pos, SUM(nilaikontrak) kontrak FROM (
		SELECT DISTINCT noskk FROM notadinas_detail d LEFT JOIN notadinas n ON d.nomornota = n.nomornota WHERE YEAR(tanggal) = YEAR(SYSDATE()) AND d.progress >= 7 GROUP BY pos1
	) nd 
	LEFT JOIN kontrak k ON nd.noskk = k.nomorskkoi
	WHERE NOT nomorkontrak IS NULL
	GROUP BY pos 
) k ON v.akses = k.pos
LEFT JOIN (
	SELECT pos, SUM(nilaibayar) bayar FROM (
		SELECT DISTINCT noskk FROM notadinas_detail d LEFT JOIN notadinas n ON d.nomornota = n.nomornota WHERE YEAR(tanggal) = YEAR(SYSDATE()) AND d.progress >= 7 GROUP BY pos1
	) nd 
	LEFT JOIN kontrak k ON nd.noskk = k.nomorskkoi
	LEFT JOIN realisasibayar b ON k.nomorkontrak = b.nokontrak 
	WHERE NOT nokontrak IS NULL
	GROUP BY pos 
) b ON v.akses = b.pos
order by akses";
//echo $sql;

	echo "<table width='100%'>";
	echo "
		<th colspan='2'>POS</th>" .  ($org>5 && $org<16? "":
		"<th>PAGU</th>
		<th>SKK TERBIT</th>
		<th>SISA PAGU (PAGU - SKK TERBIT)</th>
		<th>TERKONTRAK</th>
		<th>SISA KONTRAK (SKK TERBIT - KONTRAK)</th>
		<th>TERBAYAR</th>
		<th>SISA BAYAR (KONTRAK - BAYAR)</th>");
		
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		echo "
			<tr>
				<td align='left'>$row[akses]</td>
				<td align='left'>$row[nama]</td>"  .  ($org>5 && $org<16? "":
				"<td align='right'>" . number_format($row["rppos"]) . "</td>
				<td align='right'>" . number_format($row["terbit"]) . "</td>
				<td align='right'>" . number_format($row["rppos"]-$row["terbit"]) . "</td>
				<td align='right'>" . number_format($row["kontrak"]) . "</td>
				<td align='right'>" . number_format($row["terbit"]-$row["kontrak"]) . "</td>
				<td align='right'>" . number_format($row["bayar"]) . "</td>
				<td align='right'>" . number_format($row["kontrak"]-$row["bayar"]) . "</td>") .
			"</tr>";
	}
	echo "</table>";
	
	
	mysql_free_result($result);
	mysql_close($link);	  
?>
</body>
</html>