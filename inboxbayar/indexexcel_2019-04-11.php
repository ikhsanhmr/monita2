<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=inboxbayar.xls");

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
	$kdpos0 = isset($_REQUEST["kpos"])? $_REQUEST["kpos"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	require_once "../config/koneksi.php";
	
	//user level control
	$userlvl = 0;

	if ($_SESSION["roleid"] <= 03){
		
		$userlvl = 3;

	} else if($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05){

		$userlvl = 4;
		
	} else if($_SESSION["roleid"] == 13){

		$userlvl = 2;
		
	} else {

		$userlvl = 1;
	}

	switch ($userlvl) {
	    case 1:
	    	/* 
	    		1. user level 1 kontrak yang diklik bayar di laporan penyerapan AI/AO, maka masuk kesini.
	    		2. jika user Manager, Anggaran dan Keuangan meng-reject tagihan, masuk juga kesini.
	    		3. khusus subpos '54.2.04', akan muncul di user Bu Heva.
	    		4. jika user ini meng-reject tagihan maka akan hilang dari inbox bayar ini.
	    		5. khusus user Wahyuni akan muncul tagihan dari bidangnya sendiri dan bidang REN.
	    	*/

	        $parm .= " AND ((signlevel = 0 AND actiontype = 1) OR (signlevel > 1 AND actiontype = 0))"; // case 1, 2, dan 4

	        if($_SESSION["cnip"] == "8910061A"){ // case 3
				
				$parm .= " AND (d.pos1 = '54.2.04' OR (d.pos1 != '54.2.04' and pelaksana = '$_SESSION[org]'))";

			}else if($_SESSION["cnip"] == "94171330ZY"){ // case 5

				$parm .= " and pelaksana IN ('$_SESSION[org]','1') ";

			}else{

				$parm .= " and pelaksana = '$_SESSION[org]'";
			}

	        break;

	    case 2:
	    	/* 
	    		1. user level 1 tagihan di approve, maka masuk kesini.
	    		2. khusus tagihan SKKO di wilayah, maka akan masuk ke manajer tertentu sesuai dengan wewenang subposnya. sedangkan tagihan SKKI di wilayah dan SKKI/SKKO di UP3 akan masuk ke manager UP3 masing masing.
	    		3. kecuali subpos '54.2.04', akan muncul di user Pak Bram.
	    		4. jika user ini meng-reject tagihan maka akan kembali ke inbox bayar level 1.
	    		5. khusus user Darma Wijaya - 8610292Z akan muncul tagihan dari bidangnya sendiri dan bidang REN.
	    	*/

	        $parm .= " AND (signlevel = 1 AND actiontype = 1)"; // case 1 dan 4

			if($_SESSION["cnip"] == "8308307Z"){ // case 3
				
				$parm .= " 
							AND (
									(n.skkoi = 'SKKI' and pelaksana = '$_SESSION[org]') OR 
									(n.skkoi = 'SKKO' and ((pelaksana < 6 and d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')) OR d.pos1 = '54.2.04'))
							) 
						";

			}else if($_SESSION["cnip"] == "8610292Z"){ // case 5

				$parm .= " and pelaksana IN ('$_SESSION[org]','1') ";

			}else if($_SESSION["roleid"] == 13 && $_SESSION["org"] < 6){ // case 2

				$parm .= " 
							AND (
									(n.skkoi = 'SKKI' and pelaksana = '$_SESSION[org]') OR 
									(n.skkoi = 'SKKO' and pelaksana < 6 and d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."'))
							)
						";

			}else{ // case 2

				$parm .= " and pelaksana = '$_SESSION[org]'";
			}

	        break;
	    case 3:
			/* 
	    		1. user level 2 tagihan di approve, maka masuk kesini.
	    		2. masing masing user memiliki wewenang subpos sendiri.
	    		3. jika user ini meng-reject tagihan maka akan kembali ke inbox bayar level 1.
	    	*/

	        $parm .= " AND (signlevel = 2 AND actiontype = 1) "; //case 1 dan 3

	        if($_SESSION["cnip"] != '6793235Z'){ // case 2

	        	$parm .= " AND d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."') ";
	        }
	       	

	        break;
	    case 4:
	        /* 
	    		1. user level 3 tagihan di approve, maka masuk kesini.
	    		2. masing masing user memiliki wewenang berdasarkan bidang/area sendiri. kecuali untuk pak Ricky muncul semua tagihan.
	    		3. jika user ini meng-reject tagihan maka akan kembali ke inbox bayar level 1.
	    	*/

			$parm .= " AND (signlevel = 3 AND actiontype = 1)"; // case 1 dan 3

	        if($_SESSION["cnip"] != '7602006A'){ // case 2

	        	$parm .= " and pelaksana in (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."')";
	        }

	        break;
	}

	$otherjoin = "";

	if($_SESSION["cnip"] == '7602006A'){
		$otherjoin .= " LEFT JOIN
					(
						SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
						FROM 	akses_bidang ap INNER JOIN 
								user u On ap.nip = u.nip 
						where 	roleid IN (4,5)
						GROUP BY akses
					) ap ON pelaksana = ap.akses";
	}

	if($_SESSION["cnip"] == '6793235Z'){
		$otherjoin .= " LEFT JOIN
					(
						SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
						FROM 	akses_pos ap LEFT JOIN
								user u On ap.nip = u.nip
						where 	roleid IN (2,3)
						GROUP BY akses
					) ap ON d.pos1 = ap.akses";
	}

	if($v!="") {
			$sql = "
				SELECT	nomorskkoi, pos1, nomorkontrak, b.namaunit, vendor, k.uraian uraian, 
						nodokumen, nilaikontrak, bayar, ka.signed, ka.signdt, ka.signlevel, ka.actiontype, 
						ka.nilaitagihan, ka.catatan, ka.catatanreject, namapos ".( ($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A') ? ', ap.nama namauser' : '' )."
				FROM 	(
							SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
									nilaitagihan, catatan, catatanreject
							FROM	kontrak_approval t1
							WHERE	t1.id = (	SELECT	t2.id
												FROM	kontrak_approval t2
												WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
												ORDER BY t2.signdt DESC
												LIMIT 1
											)
						) ka INNER JOIN 
						kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN
						notadinas n ON d.nomornota = n.nomornota LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						(
							SELECT 	nokontrak, SUM(nilaibayar) bayar 
							FROM 	realisasibayar 
							GROUP BY nokontrak
						) r ON ka.nmrkontrak = r.nokontrak LEFT JOIN (
							SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos
						$otherjoin
				WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
				$parm
				ORDER BY d.pos1 ASC, k.inputdt DESC";
			//echo $sql;
			
			//$hasil = "
			echo "
			<strong>LAPORAN INBOX BAYAR</strong><br>
			<strong>Periode	: $p1  -  $p2</strong><br>
			<strong>Bidang : $b</strong><br>
			<strong>Pelaksana : $p</strong><br>
			<strong>No SKK : $k0</strong><br>
			<strong>POS : $kdpos</strong><br>
			Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB
			
			<table border='1'>
				<tr>
					<th>No</th>
					<th>SKK</th>
					<th>Kontrak</th>
					<th>Pelaksana</th>
					<th>Vendor</th>
					<th>Uraian</th>
					<th>POS</th>
					<th>No Dokumen</th>
					<th>Nilai Kontrak</th>
					<th>Nilai Bayar</th>
					<th>Sisa</th>
					<th>Nilai Tagihan</th>
					".($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A' ? "<th>PIC</th>" : "" )."
					<th>Catatan</th>
				</tr>
			";
			$no = 0;
			//$parm = "";
			//$dummy = 0;
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

			while ($row = mysqli_fetch_array($result)) {
				$no++;

				$nilaitagihan = $row["nilaitagihan"];

				if (empty($nilaitagihan)){

					$nilaitagihan = $row["nilaikontrak"] - $row["bayar"];
				}

				echo "
					<tr>
						<td>$no</td>
						<td>$row[nomorskkoi]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[namaunit]</td>
						<td>$row[vendor]</td>
						<td>$row[uraian]</td>
						<td>$row[namapos]</td>
						<td>$row[nodokumen]</td>
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."</td>
						<td align='right'>".number_format($row["nilaikontrak"] - $row["bayar"])."</td>
						<td align='right'>".number_format($nilaitagihan)."</td>
						".($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A' ? "<td>$row[namauser]</td>" : "" )."
						<td>$row[catatan]</td>
					</tr>";
			}
			echo "</table>";
			mysqli_free_result($result);

			if($_SESSION["cnip"] == '6793235Z'){

				$sql = "
					SELECT	ap.nama, p.namapos, COUNT(k.nomorkontrak) as jml, SUM(ka.nilaitagihan) as nilai
					FROM 	(
								SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
										nilaitagihan, catatan, catatanreject
								FROM	kontrak_approval t1
								WHERE	t1.id = (	SELECT	t2.id
													FROM	kontrak_approval t2
													WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
													ORDER BY t2.signdt DESC
													LIMIT 1
												)
							) ka INNER JOIN 
							kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
							notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN
							notadinas n ON d.nomornota = n.nomornota LEFT JOIN 
							bidang b ON d.pelaksana = b.id LEFT JOIN 
							(
								SELECT 	nokontrak, SUM(nilaibayar) bayar 
								FROM 	realisasibayar 
								GROUP BY nokontrak
							) r ON ka.nmrkontrak = r.nokontrak LEFT JOIN (
								SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
							) p ON d.pos1 = p.pos LEFT JOIN
							(
								SELECT 	akses, nama 
								FROM 	akses_pos ap LEFT JOIN
										user u On ap.nip = u.nip
								where 	roleid IN (2,3)
							) ap ON d.pos1 = ap.akses
					WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
					$parm
					GROUP BY ap.nama, p.namapos
					ORDER BY ap.nama";
				//echo $sql;

				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

				echo "<table id='dataTables2' class='display' cellspacing='0' width='100%'>
						<thead>
							<tr>
								<th colspan='4'>REKAP INBOX ANGGARAN</th>
							</tr>
							<tr>
								<th>Nama Sub Pos</th>
								<th>Jml</th>
								<th>Nilai</th>
								<th>PIC</th>
							</tr>
						</thead>
					";

				$totaljml = 0;
				$totalnilai = 0;

				while ($row = mysqli_fetch_array($result)) {
					
					$totaljml += $row["jml"];
					$totalnilai += $row["nilai"];

					if (!empty($row[nama])){
						echo "
							<tr>
								<td>$row[namapos]</td>
								<td>".number_format($row["jml"])."</td>
								<td>".number_format($row["nilai"])."</td>
								<td>$row[nama]</td>
							</tr>";
					}
				}

				echo "	<tfoot>
							<th>Total</th>
							<th>".number_format($totaljml)."</th>
							<th>".number_format($totalnilai)."</th>
							<th></th>
						</tfoot>
					</table>
				";

				mysqli_free_result($result);
			}
			
			if($_SESSION["cnip"] == '7602006A'){

				$sql = "
					SELECT	ap.nama, b.namaunit, SUM(CASE WHEN n.skkoi = 'SKKI' THEN 1 ELSE 0 END) as jmlskki, 
							SUM(CASE WHEN n.skkoi = 'SKKO' THEN 1 ELSE 0 END) as jmlskko, 
							SUM(CASE WHEN n.skkoi = 'SKKI' THEN ka.nilaitagihan ELSE 0 END) as nilaiskki, 
							SUM(CASE WHEN n.skkoi = 'SKKO' THEN ka.nilaitagihan ELSE 0 END) as nilaiskko
					FROM 	(
								SELECT	t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
										nilaitagihan, catatan, catatanreject
								FROM	kontrak_approval t1
								WHERE	t1.id = (	SELECT	t2.id
													FROM	kontrak_approval t2
													WHERE	TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
													ORDER BY t2.signdt DESC
													LIMIT 1
												)
							) ka INNER JOIN 
							kontrak k ON TRIM(ka.nmrkontrak) = TRIM(k.nomorkontrak) INNER JOIN
							notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN
							notadinas n ON d.nomornota = n.nomornota LEFT JOIN 
							bidang b ON d.pelaksana = b.id LEFT JOIN 
							(
								SELECT 	nokontrak, SUM(nilaibayar) bayar 
								FROM 	realisasibayar 
								GROUP BY nokontrak
							) r ON ka.nmrkontrak = r.nokontrak LEFT JOIN (
								SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
								SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
							) p ON d.pos1 = p.pos LEFT JOIN
							(
								SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
								FROM 	akses_bidang ap INNER JOIN 
										user u On ap.nip = u.nip 
								where 	roleid IN (4,5)
								GROUP BY akses
							) ap ON pelaksana = ap.akses
					WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
					$parm
					GROUP BY ap.nama, b.namaunit
					ORDER BY b.namaunit, ap.nama";
				//echo $sql;

				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

				echo "<table id='dataTables2' class='display' cellspacing='0' width='100%'>
						<thead>
							<tr>
								<th colspan='6'>REKAP INBOX KEUANGAN</th>
							</tr>
							<tr>
								<th rowspan='2'>Pelaksana SKK</th>
								<th colspan='2'>Investasi</th>
								<th colspan='2'>Operasi</th>
								<th rowspan='2'>PIC</th>
							</tr>
							<tr>
								<th>JML</th>
								<th>Nilai</th>
								<th>JML</th>
								<th>Nilai</th>
							</tr>
						</thead>
					";

				$totaljmlskki = 0;
				$totalnilaiskki = 0;
				$totaljmlskko = 0;
				$totalnilaiskko = 0;

				while ($row = mysqli_fetch_array($result)) {
					
					$totaljmlskki += $row["jmlskki"];
					$totalnilaiskki += $row["nilaiskki"];
					$totaljmlskko += $row["jmlskko"];
					$totalnilaiskko += $row["nilaiskko"];

					if (!empty($row[nama])){
						echo "
							<tr>
								<td>$row[namaunit]</td>
								<td>".number_format($row["jmlskki"])."</td>
								<td>".number_format($row["nilaiskki"])."</td>
								<td>".number_format($row["jmlskko"])."</td>
								<td>".number_format($row["nilaiskko"])."</td>
								<td>$row[nama]</td>
							</tr>";
					}
				}

				echo "	<tfoot>
							<th>Total</th>
							<th>".number_format($totaljmlskki)."</th>
							<th>".number_format($totalnilaiskki)."</th>
							<th>".number_format($totaljmlskko)."</th>
							<th>".number_format($totalnilaiskko)."</th>
							<th></th>
						</tfoot>
					</table>
				";

				mysqli_free_result($result);
			}
	}
	$mysqli->close();($kon);
	
	//echo $hasil;
?>