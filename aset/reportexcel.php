<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=assetpdp.xls");

	error_reporting(0);  session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	
    require_once "../config/koneksi.php";
    
	$p = isset($_GET["p"])? $_GET["p"]: "";
	
    $sql = "
        SELECT namaunit, ap.* FROM bidang b 
        LEFT JOIN (
            SELECT 
                SUM(jtmaset) jtmaset, SUM(gdaset) gdaset, SUM(jtraset) jtraset, SUM(sl1aset) sl1aset, SUM(sl3aset) sl3aset, 
                SUM(jtmrp) jtmrp, SUM(gdrp) gdrp, SUM(jtraset) jtrrp, SUM(sl1rp) sl1rp, SUM(sl3rp) sl3rp, d.pelaksana 
            FROM asetpdp a 
            LEFT JOIN kontrak k ON a.nomorkontrak = k.nomorkontrak
            LEFT JOIN notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1" . 
            // (isset($_GET["p"])? " WHERE YEAR(tglawal) = '$_GET[p]' OR YEAR(tglakhir) = '$_GET[p]'": "") . "
            (isset($_GET["p"])? " WHERE YEAR(k.inputdt) = '$_GET[p]'": "") . "
            GROUP BY pelaksana
        ) ap ON b.id = ap.pelaksana
        WHERE id > 5 AND id <> 15 ORDER BY LPAD(id,2,'0')
    ";

    //echo $sql;
    
    //$hasil = "
    echo "
    <strong>LAPORAN MONITORING ASSET - PDP</strong><br>
    <strong>Periode	: $p</strong><br>
    Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB
    <table border='1'>
        <tr>
            <th rowspan='2'>No</th>
            <th rowspan='2'>Unit</th>
            <th colspan='5'>Aset</th>
            <th colspan='5'>Nilai Aset (Rp)</th>
        </tr>
        <tr>
            <th>JTM (Kms)</th>
            <th>Gardu (Unit)</th>
            <th>JTR (Kms)</th>
            <th>SL 1 Phasa (Plgn)</th>
            <th>SL 3 Phasa (Plgn)</th>
            <th>JTM</th>
            <th>Gardu</th>
            <th>JTR</th>
            <th>SL 1 Phasa</th>
            <th>SL 3 Phasa</th>
        </tr>
    ";
    
    $dummynota = "";
	$dummyskk = "";
    $dummypos = "";
    
    $result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    while ($row = mysqli_fetch_array($result)) {

        $no++;

        echo "
			<tr>
				<td>$no</td>
				<td>$row[namaunit]</td>
				<td>" . number_format($row["jtmaset"],2) . "</td>
				<td>" . number_format($row["gdaset"],2) . "</td>
				<td>" . number_format($row["jtraset"],2) . "</td>
				<td>" . number_format($row["sl1aset"],2) . "</td>
				<td>" . number_format($row["sl3aset"],2) . "</td>
				<td>" . number_format($row["jtmrp"],2) . "</td>
				<td>" . number_format($row["gdrp"],2) . "</td>
				<td>" . number_format($row["jtrrp"],2) . "</td>
				<td>" . number_format($row["sl1rp"],2) . "</td>
				<td>" . number_format($row["sl3rp"],2) . "</td>
			</tr>";
    }
	echo "</table>";
    mysqli_free_result($result);
	$mysqli->close();($kon);
	
	//echo $hasil;
?>
