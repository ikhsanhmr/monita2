<?php

    function generateLapPeta($p, $p0, $nick){
    
        $p1 = date('Y');

        $fileContent = "
            <h2>Peta Pagu</h2>
            <table>
                <tr>
                    <th>Periode</th>
                    <td>:</td>
                    <td>$p1</td>
                </tr>
                <tr>
                    <th>Pelaksana</th>
                    <td>:</td>
                    <td>$p</td>
                </tr>
                <tr>
                    <td colspan='3' align='right'>".
                    date("d-m-Y H:i:s")
                    ."</td>
                </tr>
            </table>		
        ";
        
        $fileContent .=  "
            <table border='1'>
                <tr>
                    <th rowspan='2' scope='col'>No</th>
                    <th rowspan='2' scope='col'>Kode Sub Pos</th>
                    <th rowspan='2' scope='col'>Uraian SUb Pos</th>
                    <th colspan='2' scope='col'>SKK</th>
                    <th colspan='2' scope='col'>Terkontrak</th>
                    <th colspan='2' scope='col'>Terbayar</th>
                    <th colspan='2' scope='col'>Sisa</th>
                </tr>
                <tr>
                    <td align='center' style='background-color:rgb(127,255,127)'></td>
                    <td align='center' style='background-color:rgb(127,255,127)'>Disburse</td>
                    <td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
                    <td align='center' style='background-color:rgb(127,255,127)'>%</td>
                    <td align='center' style='background-color:rgb(127,255,127)'>Nilai</td>
                    <td align='center' style='background-color:rgb(127,255,127)'>%</td>
                    <td align='center' style='background-color:rgb(127,255,127)'>Kontrak</td>
                    <td align='center' style='background-color:rgb(127,255,127)'>Bayar</td>
                </tr>
                <tr>
                    <td align='center'>a</td>
                    <td align='center'>b</td>
                    <td align='center'>c</td>
                    <td align='center'>d</td>
                    <td align='center'>e</td>
                    <td align='center'>f</td>
                    <td align='center'>g=f/e</td>
                    <td align='center'>h</td>
                    <td align='center'>i=h/f</td>
                    <td align='center'>j=e-f</td>
                    <td align='center'>k=f-h</td>
                </tr>";

        $sql = "
            SELECT  v.*, rppos, nilai, kontrak, bayar 
            FROM 	(
                        SELECT 	pos1, SUM(nilai1) nilai 
                        FROM 	notadinas_detail d LEFT JOIN 
                                notadinas n ON d.nomornota = n.nomornota 
                        WHERE YEAR(tanggal) = $p1 AND d.progress >= 7 and pelaksana = '$p0'
                        GROUP BY pos1
                    ) d	LEFT JOIN 
                    (
                        SELECT DISTINCT	akses, nama 
                        FROM 	v_pos 
                        UNION
                        SELECT DISTINCT	akses, namasubpos as nama 
                        FROM 	akses_pos ap inner join
                                (
                                    SELECT kdindukpos as kdsubpos, namaindukpos as namasubpos FROM posinduk
                                    UNION 
                                    SELECT kdsubpos, namasubpos FROM posinduk2
                                    UNION 
                                    SELECT kdsubpos, namasubpos FROM posinduk3
                                    UNION 
                                    SELECT kdsubpos, namasubpos FROM posinduk4
                                ) subpos ON ap.akses = subpos.kdsubpos 
                        ORDER BY akses
                    ) v ON d.pos1 = v.akses  LEFT JOIN 
                    (
                        SELECT kdsubpos, rppos FROM saldopos WHERE tahun = $p1
                        UNION 
                        SELECT kdsubpos, rppos FROM saldopos2 WHERE tahun = $p1
                        UNION 
                        SELECT kdsubpos, rppos FROM saldopos3 WHERE tahun = $p1
                        UNION 
                        SELECT kdsubpos, rppos FROM saldopos4 WHERE tahun = $p1
                    ) p ON v.akses = p.kdsubpos LEFT JOIN 
                    (
                        SELECT 	pos, SUM(nilaikontrak) kontrak
                        FROM 	(
                                    SELECT DISTINCT noskk 
                                    FROM 	notadinas_detail d LEFT JOIN 
                                            notadinas n ON d.nomornota = n.nomornota 
                                    WHERE YEAR(tanggal) = $p1 AND d.progress >= 7 and pelaksana = '$p0'
                                ) nd LEFT JOIN 
                                kontrak k ON nd.noskk = k.nomorskkoi
                        WHERE NOT nomorkontrak IS NULL
                        GROUP BY pos 
                    ) k ON v.akses = k.pos LEFT JOIN 
                    (
                        SELECT 	pos, SUM(nilaibayar) bayar
                        FROM 	(
                                    SELECT DISTINCT noskk 
                                    FROM 	notadinas_detail d LEFT JOIN 
                                            notadinas n ON d.nomornota = n.nomornota 
                                    WHERE YEAR(tanggal) = $p1 AND d.progress >= 7 and pelaksana = '$p0'
                                ) nd LEFT JOIN 
                                kontrak k ON nd.noskk = k.nomorskkoi LEFT JOIN 
                                realisasibayar b ON k.nomorkontrak = b.nokontrak 
                        WHERE NOT nomorkontrak IS NULL
                        GROUP BY pos 
                    ) b ON v.akses = b.pos
            order by akses
        ";
            
        // echo $sql;
        //echo $parm;
        
        $no = 0;
        $a = 0;
        $a1 = 0;
        $d = 0;
        $d1 = 0;
        $k = 0;
        $k1 = 0;
        $b = 0;
        $b1 = 0;
        $dummy = "";
        $result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
        while ($row = mysqli_fetch_array($result)) {
            if($dummy!=$row["akses"]) {
                $no++;
                $dummy = $row["akses"];
                
                if($no>1) {
                    $fileContent .=  "
                            <td align='right'>" . number_format($d1) . "</td>
                            <td align='right'>" . number_format($k1) . "</td>
                            <td align='right'>" . number_format(@($k1/$d1)*100,2) . "</td>
                            <td align='right'>" . number_format($b1) . "</td>
                            <td align='right'>" . number_format(@($b1/$k1)*100,2) ."</td>
                            <td align='right'>" . number_format($d1-$k1) . "</td>
                            <td align='right'>" . number_format($k1-$b1) . "</td>
                        </tr>
                    ";
                    
                    $d1 = 0;
                    $k1 = 0;
                    $b1 = 0;
                    $a += $a1;
                }
                
                $fileContent .=  "
                    <tr>
                        <td>$no</td>
                        <td>$row[akses]</td>
                        <td>$row[nama]</td>
                        <td align='right'></td>
                ";

            }
            
            $a1 = $row["rppos"];
            $d += $row["nilai"];
            $d1 += $row["nilai"];
            $k += $row["kontrak"];
            $k1 += $row["kontrak"];
            $b += $row["bayar"];
            $b1 += $row["bayar"];

        }
        mysqli_free_result($result);
            
        $a += $a1;
        $fileContent .=  "
                    <td align='right'>" . number_format($d1) . "</td>
                    <td align='right'>" . number_format($k1) . "</td>
                    <td align='right'>" . number_format(($d1 == 0 ? 0 : $k1/$d1*100),2) . "</td>
                    <td align='right'>" . number_format($b1) . "</td>
                    <td align='right'>" . number_format(($k1 == 0 ? 0 : $b1/$k1*100),2) . "</td>
                    <td align='right'>" . number_format($d1-$k1) . "</td>
                    <td align='right'>" . number_format($k1-$b1) . "</td>
                </tr>
                <tr>
                    <td colspan='3'>Total</td>
                    <td align='right'>" . "" /*number_format($a)*/ . "</td>
                    <td align='right'>" . number_format($d) . "</td>
                    <td align='right'>" . number_format($k) . "</td>
                    <td align='right'>" . number_format(@($k/$d*100),2) . "</td>
                    <td align='right'>" . number_format($b) . "</td>
                    <td align='right'>" . number_format(@($b/$k*100),2) . "</td>
                    <td align='right'>" . number_format($d-$k) . "</td>
                    <td align='right'>" . number_format($k-$b) . "</td>
                </tr>
            </table>
        ";

		$file_path = __DIR__ . "/../files/excel/pagu-".$nick."-".$p1.".xls";

        $fd = fopen ( $file_path, "w");

        fputs($fd, $fileContent);
        fclose($fd);

        return $file_path;
    }

    function generateLapAOPerJenisKontrak($p, $p0, $nick){

        $p1 = date('Y');

        $parm = ($p0==""? "": " and b.pelaksana = '$p0'");
                
        $fileContent = "
            <strong>Laporan AO Per Jenis Kontrak Wilayah Sumatera Utara</strong><br>
            <strong>Periode	: $p1</strong><br>
            <strong>Bidang : $b</strong><br>
            <strong>Pelaksana : $p</strong><br>
            Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB	
        ";

        $sql = "
            SELECT	k.id, k.nama, nilai_jan, nilai_feb, nilai_mar, nilai_apr, nilai_mei, nilai_jun, nilai_jul, 
                    nilai_ags, nilai_sep, nilai_okt, nilai_nov, nilai_des, nilai_utang, bayar_jan, bayar_feb, 
                    bayar_mar, bayar_apr, bayar_mei, bayar_jun, bayar_jul, bayar_ags, bayar_sep, bayar_okt, 
                    bayar_nov, bayar_des, bayar_utang
            FROM	(
                        SELECT	id, nama, SUM(nilai_jan) AS nilai_jan, SUM(nilai_feb) AS nilai_feb,
                                SUM(nilai_mar) AS nilai_mar, SUM(nilai_apr) AS nilai_apr, 
                                SUM(nilai_mei) AS nilai_mei, SUM(nilai_jun) AS nilai_jun, 
                                SUM(nilai_jul) AS nilai_jul, SUM(nilai_ags) AS nilai_ags, 
                                SUM(nilai_sep) AS nilai_sep, SUM(nilai_okt) AS nilai_okt, 
                                SUM(nilai_nov) AS nilai_nov, SUM(nilai_des) AS nilai_des, 
                                SUM(nilai_utang) AS nilai_utang
                        FROM 	(
                                    SELECT	kt.*, 
                                            (CASE WHEN MONTH(tgltagih) = 1 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jan,
                                            (CASE WHEN MONTH(tgltagih) = 2 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_feb, 
                                            (CASE WHEN MONTH(tgltagih) = 3 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_mar,
                                            (CASE WHEN MONTH(tgltagih) = 4 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_apr, 
                                            (CASE WHEN MONTH(tgltagih) = 5 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_mei,
                                            (CASE WHEN MONTH(tgltagih) = 6 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jun, 
                                            (CASE WHEN MONTH(tgltagih) = 7 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_jul,
                                            (CASE WHEN MONTH(tgltagih) = 8 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_ags, 
                                            (CASE WHEN MONTH(tgltagih) = 9 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_sep,
                                            (CASE WHEN MONTH(tgltagih) = 10 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_okt, 
                                            (CASE WHEN MONTH(tgltagih) = 11 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_nov,
                                            (CASE WHEN MONTH(tgltagih) = 12 AND YEAR(tgltagih) = $p1 THEN nilaikontrak ELSE 0 END) AS nilai_des,
                                            (CASE WHEN YEAR(tgltagih) = ".($p1 - 1)." THEN nilaikontrak ELSE 0 END) AS nilai_utang
                                    FROM	kontrak_type kt INNER JOIN 
                                            (
                                                SELECT	e.*
                                                FROM	notadinas a	LEFT JOIN 
                                                        notadinas_detail b ON a.nomornota = b.nomornota LEFT JOIN 
                                                        bidang c ON b.pelaksana = c.id LEFT JOIN
                                                        skkoterbit d ON b.noskk = d.nomorskko LEFT JOIN 
                                                        kontrak e ON b.noskk = e.nomorskkoi AND b.pos1 = e.pos
                                                WHERE	YEAR(d.tanggalskko) = $p1 and YEAR(e.tgltagih) IN (".($p1 - 1).", $p1) and a.skkoi = 'SKKO' $parm
                                            ) dk ON kt.id = dk.isrutin
                                ) AS kontrak 
                        GROUP BY id, nama
                    ) AS k left join 
                    (
                        SELECT	id, nama, SUM(bayar_jan) AS bayar_jan, SUM(bayar_feb) AS bayar_feb, 
                                SUM(bayar_mar) AS bayar_mar, SUM(bayar_apr) AS bayar_apr, 
                                SUM(bayar_mei) AS bayar_mei, SUM(bayar_jun) AS bayar_jun, 
                                SUM(bayar_jul) AS bayar_jul, SUM(bayar_ags) AS bayar_ags, 
                                SUM(bayar_sep) AS bayar_sep, SUM(bayar_okt) AS bayar_okt, 
                                SUM(bayar_nov) AS bayar_nov, SUM(bayar_des) AS bayar_des, 
                                SUM(bayar_utang) AS bayar_utang
                        FROM 	(
                                    SELECT	kt.*, 
                                            (CASE WHEN MONTH(tgltagih) = 1 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jan,
                                            (CASE WHEN MONTH(tgltagih) = 2 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_feb, 
                                            (CASE WHEN MONTH(tgltagih) = 3 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_mar,
                                            (CASE WHEN MONTH(tgltagih) = 4 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_apr, 
                                            (CASE WHEN MONTH(tgltagih) = 5 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_mei,
                                            (CASE WHEN MONTH(tgltagih) = 6 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jun, 
                                            (CASE WHEN MONTH(tgltagih) = 7 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_jul,
                                            (CASE WHEN MONTH(tgltagih) = 8 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_ags, 
                                            (CASE WHEN MONTH(tgltagih) = 9 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_sep,
                                            (CASE WHEN MONTH(tgltagih) = 10 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_okt, 
                                            (CASE WHEN MONTH(tgltagih) = 11 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_nov,
                                            (CASE WHEN MONTH(tgltagih) = 12 AND YEAR(tgltagih) = $p1 THEN nilaibayar ELSE 0 END) AS bayar_des,
                                            (CASE WHEN YEAR(tgltagih) = ".($p1 - 1)." THEN nilaibayar ELSE 0 END) AS bayar_utang
                                    FROM	kontrak_type kt INNER JOIN 
                                            (
                                                SELECT	e.*, f.nilaibayar
                                                FROM	notadinas a	LEFT JOIN 
                                                        notadinas_detail b ON a.nomornota = b.nomornota LEFT JOIN 
                                                        bidang c ON b.pelaksana = c.id LEFT JOIN
                                                        skkoterbit d ON b.noskk = d.nomorskko LEFT JOIN 
                                                        kontrak e ON b.noskk = e.nomorskkoi AND b.pos1 = e.pos LEFT JOIN
                                                        realisasibayar f ON e.nomorkontrak = f.nokontrak
                                                WHERE	YEAR(d.tanggalskko) = $p1 and YEAR(e.tgltagih) IN (".($p1 - 1).", $p1) and a.skkoi = 'SKKO' $parm
                                            ) dk ON kt.id = dk.isrutin
                                ) AS realisasibayar 
                        GROUP BY id, nama
                    ) AS r ON k.id = r.id
        ";
            
        // echo $sql;
        //echo $parm;
        
        $kontrak = 0;
		$bayar = 0;
		$ttl_nilai_utang = 0;
		$ttl_bayar_utang = 0;
		$ttl_nilai_jan = 0;
		$ttl_bayar_jan = 0;
		$ttl_nilai_feb = 0;
		$ttl_bayar_feb = 0;
		$ttl_nilai_mar = 0;
		$ttl_bayar_mar = 0;
		$ttl_nilai_apr = 0;
		$ttl_bayar_apr = 0;
		$ttl_nilai_mei = 0;
		$ttl_bayar_mei = 0;
		$ttl_nilai_jun = 0;
		$ttl_bayar_jun = 0;
		$ttl_nilai_jul = 0;
		$ttl_bayar_jul = 0;
		$ttl_nilai_ags = 0;
		$ttl_bayar_ags = 0;
		$ttl_nilai_sep = 0;
		$ttl_bayar_sep = 0;
		$ttl_nilai_okt = 0;
		$ttl_bayar_okt = 0;
		$ttl_nilai_nov = 0;
		$ttl_bayar_nov = 0;
		$ttl_nilai_des = 0;
		$ttl_bayar_des = 0;
		$ttl_nilai = 0;
		$ttl_bayar = 0;

		$no = 0;
		$parm = "";
        
        $result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
        while ($row = mysqli_fetch_array($result)) {
            $no++;
			$kontrak += $row["kontrak"];
			$bayar += $row["bayar"];

			$ttl_nilai_utang += $row["nilai_utang"];
			$ttl_bayar_utang += $row["bayar_utang"];
			$ttl_nilai_jan += $row["nilai_jan"];
			$ttl_bayar_jan += $row["bayar_jan"];
			$ttl_nilai_feb += $row["nilai_feb"];
			$ttl_bayar_feb += $row["bayar_feb"];
			$ttl_nilai_mar += $row["nilai_mar"];
			$ttl_bayar_mar += $row["bayar_mar"];
			$ttl_nilai_apr += $row["nilai_apr"];
			$ttl_bayar_apr += $row["bayar_apr"];
			$ttl_nilai_mei += $row["nilai_mei"];
			$ttl_bayar_mei += $row["bayar_mei"];
			$ttl_nilai_jun += $row["nilai_jun"];
			$ttl_bayar_jun += $row["bayar_jun"];
			$ttl_nilai_jul += $row["nilai_jul"];
			$ttl_bayar_jul += $row["bayar_jul"];
			$ttl_nilai_ags += $row["nilai_ags"];
			$ttl_bayar_ags += $row["bayar_ags"];
			$ttl_nilai_sep += $row["nilai_sep"];
			$ttl_bayar_sep += $row["bayar_sep"];
			$ttl_nilai_okt += $row["nilai_okt"];
			$ttl_bayar_okt += $row["bayar_okt"];
			$ttl_nilai_nov += $row["nilai_nov"];
			$ttl_bayar_nov += $row["bayar_nov"];
			$ttl_nilai_des += $row["nilai_des"];
			$ttl_bayar_des += $row["bayar_des"];

			$total_nilai = 	$row["nilai_utang"] + $row["nilai_jan"] + $row["nilai_feb"] + $row["nilai_mar"] + 
							$row["nilai_apr"] + $row["nilai_mei"] + $row["nilai_jun"] + $row["nilai_jul"] + 
							$row["nilai_ags"] + $row["nilai_sep"] + $row["nilai_okt"] + $row["nilai_nov"] + 
							$row["nilai_des"];

			$total_bayar = 	$row["bayar_utang"] + $row["bayar_jan"] + $row["bayar_feb"] + $row["bayar_mar"] + 
							$row["bayar_apr"] + $row["bayar_mei"] + $row["bayar_jun"] + $row["bayar_jul"] + 
							$row["bayar_ags"] + $row["bayar_sep"] + $row["bayar_okt"] + $row["bayar_nov"] + 
							$row["bayar_des"];

			$ttl_nilai += $total_nilai;

			$ttl_bayar += $total_bayar;
			
			$parm .= "
				<tr>
					<td>$no</td>
					<td>$row[nama]</td>
					<td align='right'>".number_format($row["nilai_utang"])."</td>
					<td align='right'>".number_format($row["bayar_utang"])."</td>
					<td align='right'>".number_format($row["nilai_jan"])."</td>
					<td align='right'>".number_format($row["bayar_jan"])."</td>
					<td align='right'>".number_format($row["nilai_feb"])."</td>
					<td align='right'>".number_format($row["bayar_feb"])."</td>
					<td align='right'>".number_format($row["nilai_mar"])."</td>
					<td align='right'>".number_format($row["bayar_mar"])."</td>
					<td align='right'>".number_format($row["nilai_apr"])."</td>
					<td align='right'>".number_format($row["bayar_apr"])."</td>
					<td align='right'>".number_format($row["nilai_mei"])."</td>
					<td align='right'>".number_format($row["bayar_mei"])."</td>
					<td align='right'>".number_format($row["nilai_jun"])."</td>
					<td align='right'>".number_format($row["bayar_jun"])."</td>
					<td align='right'>".number_format($row["nilai_jul"])."</td>
					<td align='right'>".number_format($row["bayar_jul"])."</td>
					<td align='right'>".number_format($row["nilai_ags"])."</td>
					<td align='right'>".number_format($row["bayar_ags"])."</td>
					<td align='right'>".number_format($row["nilai_sep"])."</td>
					<td align='right'>".number_format($row["bayar_sep"])."</td>
					<td align='right'>".number_format($row["nilai_okt"])."</td>
					<td align='right'>".number_format($row["bayar_okt"])."</td>
					<td align='right'>".number_format($row["nilai_nov"])."</td>
					<td align='right'>".number_format($row["bayar_nov"])."</td>
					<td align='right'>".number_format($row["nilai_des"])."</td>
					<td align='right'>".number_format($row["bayar_des"])."</td>
					<td align='right'>".number_format($total_nilai)."</td>
					<td align='right'>".number_format($total_bayar)."</td>
				</tr>";
        }
        mysqli_free_result($result);
            
        $a += $a1;
        $fileContent .=  "
            <table border='1'>
                <thead>
                    <tr>
                        <th rowspan='3'>No</th>
                        <th rowspan='3'>Jenis Tagihan</th>
                        <th colspan='26'>Bulan Tagih</th>
                        <th colspan='2'>Total</th>
                    </tr>
                    <tr>
                        <th colspan='2'>Utang</th>
                        <th colspan='2'>Januari</th>
                        <th colspan='2'>Februari</th>
                        <th colspan='2'>Maret</th>
                        <th colspan='2'>April</th>
                        <th colspan='2'>Mei</th>
                        <th colspan='2'>Juni</th>
                        <th colspan='2'>Juli</th>
                        <th colspan='2'>Agustus</th>
                        <th colspan='2'>September</th>
                        <th colspan='2'>Oktober</th>
                        <th colspan='2'>November</th>
                        <th colspan='2'>Desember</th>
                        <th rowspan='2'>Kontrak</th>
                        <th rowspan='2'>Bayar</th>
                    </tr>
                    <tr>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                        <th>Kontrak</th>
                        <th>Bayar</th>
                    </tr>
                </thead>
                $parm
                <tfoot>
                    <tr>
                        <td colspan='2'>Total</td>
                        <td align='right'>".number_format($ttl_nilai_utang)."</td>
                        <td align='right'>".number_format($ttl_bayar_utang)."</td>
                        <td align='right'>".number_format($ttl_nilai_jan)."</td>
                        <td align='right'>".number_format($ttl_bayar_jan)."</td>
                        <td align='right'>".number_format($ttl_nilai_feb)."</td>
                        <td align='right'>".number_format($ttl_bayar_feb)."</td>
                        <td align='right'>".number_format($ttl_nilai_mar)."</td>
                        <td align='right'>".number_format($ttl_bayar_mar)."</td>
                        <td align='right'>".number_format($ttl_nilai_apr)."</td>
                        <td align='right'>".number_format($ttl_bayar_apr)."</td>
                        <td align='right'>".number_format($ttl_nilai_mei)."</td>
                        <td align='right'>".number_format($ttl_bayar_mei)."</td>
                        <td align='right'>".number_format($ttl_nilai_jun)."</td>
                        <td align='right'>".number_format($ttl_bayar_jun)."</td>
                        <td align='right'>".number_format($ttl_nilai_jul)."</td>
                        <td align='right'>".number_format($ttl_bayar_jul)."</td>
                        <td align='right'>".number_format($ttl_nilai_ags)."</td>
                        <td align='right'>".number_format($ttl_bayar_ags)."</td>
                        <td align='right'>".number_format($ttl_nilai_sep)."</td>
                        <td align='right'>".number_format($ttl_bayar_sep)."</td>
                        <td align='right'>".number_format($ttl_nilai_okt)."</td>
                        <td align='right'>".number_format($ttl_bayar_okt)."</td>
                        <td align='right'>".number_format($ttl_nilai_nov)."</td>
                        <td align='right'>".number_format($ttl_bayar_nov)."</td>
                        <td align='right'>".number_format($ttl_nilai_des)."</td>
                        <td align='right'>".number_format($ttl_bayar_des)."</td>
                        <td align='right'>".number_format($ttl_nilai)."</td>
                        <td align='right'>".number_format($ttl_bayar)."</td>
                    </tr>
                </tfoot>
            </table>
        ";

		$file_path = __DIR__ . "/../files/excel/aorutin-".$nick."-".$p1.".xls";

        $fd = fopen ( $file_path, "w");

        // $fd = fopen ("../files/excel/pagu-".$nick."-".$p1.".xls", "w");
        fputs($fd, $fileContent);
        fclose($fd);

        return $file_path;
    }

?>