<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=data_approval_anggaran.xls");

	session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	
	$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
	$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
	$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
	$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
	$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
	$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
	$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	require_once "../config/koneksi.php";
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$p = "";
	$b = "";
	while ($row = mysqli_fetch_array($result)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysqli_free_result($result);

	$parm = "";
	$parm .= ($p1==""? "": " and DATE(ka.signdt) >= '$p1'");
    $parm .= ($p2==""? "": " and DATE(ka.signdt) <= '$p2'");
    $parm .= ($b0==""? "": " and (g.id = '$b0' or d.pelaksana = '$b0')");
    $parm .= ($p0==""? "": " and d.pelaksana = '$p0'");
    $parm .= ($k0==""? "": " and k.nomorskkoi = '$k0'");
    $parm .= ($o==""? "": " and n.skkoi = '$o'");
    $parm .= ($c==""? "": " and k.nomorkontrak = '$c'");
	
	echo "
		<strong>Data Approval Anggaran Wilayah Sumatera Utara</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Jenis : $o</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
	
	if($v!="") {

		$sql = "
            SELECT  n.skkoi, b.namaunit, k.nomorskkoi, k.nomorkontrak, k.vendor, k.uraian, k.tglawal, k.tglakhir, 
                    k.nilaikontrak, k.inputdt, kapel.signdt as app_pel, kaang.signdt as app_ang, 
                    kakeu.signdt as app_keu, bayar
            FROM    (
                        SELECT 	nomorkontrak, MIN(signdt) as signdt
                        FROM 	kontrak_approval
                        GROUP BY nomorkontrak
                    ) ka INNER JOIN
                    kontrak k ON trim(ka.nomorkontrak) = trim(k.nomorkontrak) LEFT JOIN
                    notadinas_detail nd ON k.nomorskkoi = nd.noskk LEFT JOIN
                    (
                        SELECT 	nokontrak, SUM(nilaibayar) bayar 
                        FROM 	realisasibayar 
                        GROUP BY nokontrak
                    ) r ON trim(ka.nomorkontrak) = trim(r.nokontrak) LEFT JOIN
                    bidang b ON nd.pelaksana = b.id LEFT JOIN
                    notadinas n ON nd.nomornota = n.nomornota LEFT JOIN
                    (
                        SELECT nomorkontrak, MAX( signdt ) AS signdt
                        FROM kontrak_approval
                        Where signlevel = 2 and actiontype = 1
                        GROUP BY nomorkontrak
                    ) kapel ON TRIM(ka.nomorkontrak) = TRIM(kapel.nomorkontrak)  LEFT JOIN
                    (
                        SELECT nomorkontrak, MAX( signdt ) AS signdt
                        FROM kontrak_approval
                        Where signlevel = 3 and actiontype = 1
                        GROUP BY nomorkontrak
                    ) kaang ON TRIM(ka.nomorkontrak) = TRIM(kaang.nomorkontrak) LEFT JOIN
                    (
                        SELECT nomorkontrak, MAX( signdt ) AS signdt
                        FROM kontrak_approval
                        Where signlevel = 4 and actiontype = 1
                        GROUP BY nomorkontrak
                    ) kakeu ON TRIM(ka.nomorkontrak) = TRIM(kakeu.nomorkontrak)
            WHERE 	1=1
            $parm
            ORDER BY nomorskkoi, LPAD(pelaksana, 2, '0'), k.pos, k.inputdt DESC, nomorkontrak
        ";
		//echo $sql;
		//echo $parm;
		
		$kontrak = 0;
		$bayar = 0;
		$no = 0;
		$parm = "";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {
			$no++;
			$kontrak += $row["nilaikontrak"];
			$bayar += $row["bayar"];
			
			$parm .= "
                <tr>
                    <td>$no</td>
                    <td>$row[skkoi]</td>
                    <td>$row[namaunit]</td>
                    <td>$row[nomorskkoi]</td>
                    <td>$row[nomorkontrak]</td>
                    <td>$row[vendor]</td>
                    <td>$row[uraian]</td>
                    <td>$row[tglawal]</td>
                    <td>$row[tglakhir]</td>
                    <td align='right'>".number_format($row["nilaikontrak"])."</td>
                    <td align='right'>".number_format($row["bayar"])."</td>
                    <td align='right'>".number_format($row["nilaikontrak"]-$row["bayar"])."</td>
                    <td align='right'>".number_format(@($row["bayar"]/$row["nilaikontrak"]*100),2)."</td>
                    <td></td>
                    <td>$row[inputdt]</td>
                    <td>$row[app_pel]</td>
                    <td>$row[app_ang]</td>
                    <td>$row[app_keu]</td>
                </tr>    
            ";
				//min='0' max='$dummy' 
		}
		mysqli_free_result($result);
		
		echo "
            <table border='1'>
                <tr>
                    <th rowspan='2'>No</th>
                    <th rowspan='2'>Jenis</th>
                    <th rowspan='2'>Pelaksana</th>
                    <th rowspan='2'>No SKK</th>
                    <th colspan='10'>Kontrak</th>
                    <th rowspan='2'>Tgl Entry</th>
                    <th rowspan='2'>Tgl Approve Bidang/UP3</th>
                    <th rowspan='2'>Tgl Approve Anggaran</th>
                    <th rowspan='2'>Tgl Approve Keuangan</th>
                </tr>
                <tr>
                    <th>Nomor</th>
                    <th>Vendor</th>
                    <th>Uraian</th>
                    <th>Tgl Awal</th>
                    <th>Tgl Akhir</th>
                    <th>Nilai</th>
                    <th>Total Bayar</th>
                    <th>Sisa</th>
                    <th>Prosentase (%)</th>
                    <th>Keterangan</th>
                </tr>
                $parm
                <tr>
                    <td colspan='9' style='tet-align: right;'>Total</td>
                    <td align='right'>".number_format($kontrak)."</td>
                    <td align='right'>".number_format($bayar)."</td>
                    <td align='right'>".number_format($kontrak-$bayar)."</td>
                    <td align='right'>".number_format(@($bayar/$kontrak)*100,2)."</td>
                    <td colspan='5'></td>
                </tr>
            </table>
        ";
	}
	$mysqli->close();($kon);
?>