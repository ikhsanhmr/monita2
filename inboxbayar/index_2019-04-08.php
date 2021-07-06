<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<!-- <link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css"> -->
	<!-- <link href="../css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" > -->
	<link href="../css/datatables.v1.10.16.min.css" rel="stylesheet" type="text/css" >
	<link href="../css/dataTables.checkboxes.css" rel="stylesheet" type="text/css" >
	
	<script type="text/javascript">
		/*function viewk(x) {
			var p1 = document.getElementById("p1").value;
			var p2 = document.getElementById("p2").value;
			var b = document.getElementById("bidang").value;
			var p = document.getElementById("pelaksana").value;
			var k = document.getElementById("skk").value;
			var o = document.getElementById("o").value;
			var c = document.getElementById("kon").value;
			
			if(p1>p2 || p1=="") {
				if(p2!="") {
					var dummy = p1;
					p1 = p2;
					p2 = dummy;
				}
			}
			
			var url = encodeURI((x==undefined? "index.php": "indexexcel.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&o="+o+"&c="+c+"&v=1");
			window.open(url, "_self"); 
		}*/

		function upload(p) {
			url = (p==undefined? encodeURI("upload.php"): encodeURI("upload.php"));
			//alert(url);
			window.open(url, "_self");
		}
		
		
	</script>
	
	<?php
		error_reporting(0);  session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
			$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";

			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			
			$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
			$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
			$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
			$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
			$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
			$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
			$c = isset($_REQUEST["c"])? $_REQUEST["c"]: "";
			$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
		
		$url = "index.php?p1=$p1&p2=$p2&b=$b0&p=$p0&k=$k0&o=$o&c=$c&v=1";

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
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysqli_fetch_array($result)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}

			if ($userlvl != 4){

				$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
					"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
			}
		}
		mysqli_free_result($result);

		if ($userlvl == 4){
			$sql = "SELECT * FROM bidang b INNER JOIN akses_bidang ab ON b.id = ab.akses WHERE ab.nip = '$_SESSION[cnip]' ORDER BY LPAD(id, 2, '0')";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

			while ($row = mysqli_fetch_array($result)) {
				
				$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
						"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
						($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			mysqli_free_result($result);
		}

		$b .= "</select>";
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
		// $parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4) . " AND MONTH(inputdt) >= " . substr($p1,-2));
		// $parm .= ($p2==""? "": " and YEAR(inputdt) = " . substr($p2,0,4) . " AND MONTH(inputdt) <= " . substr($p2,-2));
		// $parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		// $parm .= ($k0==""? "": " and skk = '$k0'");
		// $parm .= ($o==""? "": " and skkoi = '$o'");
		// $parm .= ($c==""? "": " and TRIM(nomorkontrak) = '$c'");

		/*
			SignLevel #0 -> Laporan penyerapan AI
			SignLevel #1 -> Inbox Bayar Pelaksana UP3 / Bidang
			SignLevel #2 -> Inbox Bayar Manager Bagian
			SignLevel #3 -> Inbox Bayar User Anggaran
			SignLevel #4 -> Inbox Bayar User Keuangan
		*/

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

				$parm .= " AND (signlevel = 3 AND actiontype = 1) "; // case 1 dan 3

				if($_SESSION["cnip"] != '7602006A'){ // case 2
					
					$sisip = "";

					if ($_SESSION["cnip"] == '8106282Z' || $_SESSION["cnip"] == '8509035A'){
						$sisip = " OR d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."') ";
					}else{
						$sisip = " AND d.pos1 NOT IN (Select akses From akses_pos Where nip IN ('8106282Z','8509035A')) ";
					}

		        	$parm .= " and (pelaksana in (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."') $sisip )";
		        }

		        break;
		}

		/*
		if(($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05) && $_SESSION["cnip"] != '7602006A'){
			
			$parm .= " and pelaksana in (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."')";

		}else{

			if ($_SESSION["roleid"] > 3){
				$parm .= " and pelaksana = '$_SESSION[org]'";
			}

			if ($_SESSION["roleid"] > 5){

				if($_SESSION["cnip"] == "8610292Z" || $_SESSION["cnip"] == "94171330ZY"){
					
					$parm .= " and pelaksana IN ('$_SESSION[org]','1')";	

				}elseif($_SESSION["roleid"] == 13 && $_SESSION["org"] < 6){

					//$parm .= " and pelaksana < 6";

				}else{

					$parm .= " and pelaksana = '$_SESSION[org]'";
				}
			}
		}

		if(($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03)){
			$parm .= " AND d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')";
		}

		if($_SESSION["cnip"] == "8308307Z"){
			
			$parm .= " AND ((n.skkoi = 'SKKI' and pelaksana = '$_SESSION[org]') OR (n.skkoi = 'SKKO' and ((pelaksana < 6 and d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')) OR d.pos1 = '54.2.04'))) ";

		}else if($_SESSION["roleid"] == 13 && $_SESSION["org"] < 6){

			$parm .= " AND ( (n.skkoi = 'SKKI' and pelaksana = '$_SESSION[org]') OR (n.skkoi = 'SKKO' and pelaksana < 6 and d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')))";
		}
		*/

		$otherjoin = "";

		if($_SESSION["cnip"] == '7602006A'){
			$otherjoin .= " LEFT JOIN
						(
							SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
							FROM 	akses_bidang ap INNER JOIN 
									user u On ap.nip = u.nip 
							where 	roleid IN (4,5)
							GROUP BY akses
							UNION
							SELECT 	akses, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
							FROM 	akses_pos ap LEFT JOIN
									user u On ap.nip = u.nip
							where 	roleid IN (4,5)
							GROUP BY akses
						) ap ON (pelaksana = ap.akses and d.pos1 NOT IN ('52.3.04','54.2.04')) or d.pos1 = ap.akses";
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
		
		// echo "<pre>";
		// echo print_r($_SESSION);
		// echo "</pre>";
		// return;
		echo "
			<h2>Inbox Bayar</h2>
		";
		
		echo ( $userlvl == 1 ? "<a href='#' onClick='upload()'>(+) Upload Bayar</a>" : "" );
		

			$sql = "
				SELECT	nomorskkoi, pos1, nomorkontrak, b.namaunit, vendor, k.uraian uraian, 
						nodokumen, nilaikontrak, bayar, ka.signed, ka.signdt, ka.signlevel, ka.actiontype, 
						ka.nilaitagihan, ka.catatan, ka.catatanreject, namapos ".( ($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A') ? ', ap.nama namauser' : '' )."
				FROM 	(
							SELECT	t.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
									nilaitagihan, catatan, catatanreject
							FROM	kontrak_approval t INNER JOIN 
									(
										SELECT nomorkontrak, MAX( id ) AS lastid
										FROM kontrak_approval
										GROUP BY nomorkontrak
									)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
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
			// echo $sql;
			// return;
			
			$no = 0;
			$body = "";
			$infopic = "";
			//$dummy = 0;
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;

				$nilaitagihan = $row["nilaitagihan"];
				$sisa = $row["nilaikontrak"] - $row["bayar"];

				if (empty($nilaitagihan)){

					$nilaitagihan = $sisa;
				}

				$body .= "
					<tr data-verifikasi='1'>
						<td></td>
						<td>$no</td>
						<td>$row[nomorskkoi]</td>
						<td>
							$row[nomorkontrak]
							<input type='hidden' name='k$no' value='".$row[nomorkontrak]."'/>
						</td>
						<td>$row[namaunit]</td>
						<td>$row[vendor]</td>
						<td>$row[uraian]</td>
						<td>$row[namapos]</td>
						".($userlvl == 1 ? "<td><input type='text' name='doc$no' value='".$row[nodokumen]."'/></td>" : "<td>$row[nodokumen]</td>" )."
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."
							<input type='hidden' name='s$no' value='".$sisa."'/>
						</td>
						<td align='right'>".number_format($sisa)."</td>
						<td align='right'>
							".($userlvl != 2 ? "<input type='number' name='t$no' value='".$nilaitagihan."'/>" : number_format($nilaitagihan)." <input type='hidden' name='t$no' value='".$nilaitagihan."'/>" )."
						</td>
						".($_SESSION["cnip"] == '6793235Z' || $_SESSION["cnip"] == '7602006A' ? "<td>$row[namauser]</td>" : "" )."
						<td>
							".($userlvl == 1 ? "<input type='text' name='ctt$no' value='".$row[catatan]."'/>" : "$row[catatan]<input type='hidden' name='ctt$no' value='".$row[catatan]."'/>" )."
						</td>
						".($userlvl == 1 ? "<td>".(empty($row[catatanreject]) ? "-" : $row[catatanreject])."</td>" : "" )."
					</tr>";
					//min='0' max='$dummy' 
			}
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
					WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL AND (signlevel = 2 AND actiontype = 1) 
					GROUP BY ap.nama, p.namapos
					ORDER BY ap.nama";
				//echo $sql;

				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

				$infopic .= "<table id='dataTables2' class='display' cellspacing='0' width='100%'>
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
						$infopic .= "
							<tr>
								<td>$row[namapos]</td>
								<td style='text-align: right;'>".number_format($row["jml"])."</td>
								<td style='text-align: right;'>".number_format($row["nilai"])."</td>
								<td>$row[nama]</td>
							</tr>";
					}
				}

				$infopic .= "	<tfoot>
									<th>Total</th>
									<th style='text-align: right;'>".number_format($totaljml)."</th>
									<th style='text-align: right;'>".number_format($totalnilai)."</th>
									<th></th>
								</tfoot>
							</table>";

				mysqli_free_result($result);
			}
			
			if($_SESSION["cnip"] == '7602006A' || $_SESSION["cnip"] == '6793235Z'){

				$sql = "
					SELECT	ap.nama, b.namaunit, SUM(CASE WHEN n.skkoi = 'SKKI' THEN 1 ELSE 0 END) as jmlskki, 
							SUM(CASE WHEN n.skkoi = 'SKKO' THEN 1 ELSE 0 END) as jmlskko, 
							SUM(CASE WHEN n.skkoi = 'SKKI' THEN ka.nilaitagihan ELSE 0 END) as nilaiskki, 
							SUM(CASE WHEN n.skkoi = 'SKKO' THEN ka.nilaitagihan ELSE 0 END) as nilaiskko,
							(CASE WHEN ap.grouping = 'KHUSUS' THEN CONCAT(b.namaunit, ' - SPPD Diklat & Non Diklat') ELSE b.namaunit END) as namapelaksana
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
								SELECT 	akses, 'UMUM' as grouping, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
								FROM 	akses_bidang ap INNER JOIN 
										user u On ap.nip = u.nip 
								where 	roleid IN (4,5)
								GROUP BY akses
								UNION
								SELECT 	akses, 'KHUSUS' as grouping, GROUP_CONCAT(nama SEPARATOR ', ') AS nama 
								FROM 	akses_pos ap LEFT JOIN
										user u On ap.nip = u.nip
								where 	roleid IN (4,5)
								GROUP BY akses
							) ap ON (pelaksana = ap.akses and d.pos1 NOT IN ('52.3.04','54.2.04')) or d.pos1 = ap.akses
					WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL AND (signlevel = 3 AND actiontype = 1)
					GROUP BY ap.nama, b.namaunit, ap.grouping
					ORDER BY b.namaunit, ap.nama";
				//echo $sql;

				$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));

				$infopic .= "<table id='dataTables3' class='display' cellspacing='0' width='100%'>
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
						$infopic .= "
							<tr>
								<td>$row[namapelaksana]</td>
								<td style='text-align: right;'>".number_format($row["jmlskki"])."</td>
								<td style='text-align: right;'>".number_format($row["nilaiskki"])."</td>
								<td style='text-align: right;'>".number_format($row["jmlskko"])."</td>
								<td style='text-align: right;'>".number_format($row["nilaiskko"])."</td>
								<td>$row[nama]</td>
							</tr>";
					}
				}

				$infopic .= "	<tfoot>
									<th>Total</th>
									<th style='text-align: right;'>".number_format($totaljmlskki)."</th>
									<th style='text-align: right;'>".number_format($totalnilaiskki)."</th>
									<th style='text-align: right;'>".number_format($totaljmlskko)."</th>
									<th style='text-align: right;'>".number_format($totalnilaiskko)."</th>
									<th></th>
								</tfoot>
							</table>";

				mysqli_free_result($result);
			}
			
			echo "
				<form name='frm' id='frm' method='post' action='simpan.php'>
					<table id='dataTables' class='display' cellspacing='0' width='100%'>
						<thead>
							<th style='width: 10px;'></th>
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
							".($userlvl == 1 ? "<th>Alasan Ditolak</th>" : "" )."
						</thead>
						$body
						<tfoot>

						</tfoot>
					</table>
					
					$infopic
					
					<input type='hidden' name='level' value='$userlvl'/>
					<button type='submit' data-flag='0'>Reject</button>
					<button type='submit' data-flag='1'>Approve</button>
					<input type='button' onclick='location.href=\"indexexcel.php?v=1\";' value='Export Excel' />
				</form>";
		//}
		$mysqli->close();($kon);
	?>

	</body>
	
	<script src="../js/jquery-1.12.0.min.js"></script>
	<!-- <script src="../js/jquery.dataTables.min.js"></script> -->
	<script src="../js/jquery.dataTables.v1.10.16.min.js"></script>
	<script src="../js/dataTables.select.min.js"></script>
	<script>
	$(document).ready(function() {
		var table = $('#dataTables').DataTable({
			// 'initComplete': function(settings){
			// 	var api = this.api();

				
			// },
			columnDefs: [ {
	            orderable: false,
	            className: 'select-checkbox',
	            targets:   0
	        } ],
			// columnDefs: [ {
			// 	targets: 0,
			// 	//orderable: false,
			// 	checkboxes: {
			// 		selectRow: true
			// 	}
			// } ],
	        select: {
	            style:    'multi',
	            selector: 'td:first-child'
	        },
	        order: [[ 1, 'asc' ]],
	        "bPaginate": false,
	   	});

	   	

	   	$('#frm').on('submit', function(e){
			var form = this;
			var userlvl = <?php echo $userlvl ?>;

			var btn = $(this).find("button[type=submit]:focus" );
			var btnName = btn.data('flag');

			if (btnName == '1ALL'){

				var data = table.rows( { filter : 'applied'} ).data();
				$.each(data, function(id, data){
					// Create a hidden element 
					
					$(form).append(
						$('<input>').attr('type', 'hidden').attr('name', 'id[]').val(data[1])
					);
				});

				btnName = 1;

			} else if (btnName == '0ALL'){

				var data = table.rows( { filter : 'applied'} ).data();
				$.each(data, function(id, data){
					// Create a hidden element 
					
					$(form).append(
						$('<input>').attr('type', 'hidden').attr('name', 'id[]').val(data[1])
					);
				});

				btnName = 0;

			}else{
				//var rows_selected = table.column(0).checkboxes.selected();
				var rows_selected = table.rows( { selected: true } ).data();
				
				// Iterate over all selected checkboxes
				$.each(rows_selected, function(index, data){
					// Create a hidden element

					$(form).append(
						$('<input>').attr('type', 'hidden').attr('name', 'id[]').val(data[1])
					);
				});
			}
			
			input = $("<input>").attr('type', 'hidden').attr("name", "actiontype").val(btnName);
			$(this).append(input);

			if(btnName == 0 && userlvl != 1){
				var reason = prompt("Silahkan masukan alasan kontrak ditolak:", "");
				if (reason == null || reason == "") {
					
				} else {
					$(form).append(
						$('<input>').attr('type', 'hidden').attr('name', 'reason').val(reason)
					);
				}
			}

			// Prevent actual form submission
			//e.preventDefault();
	   });

	   	
	} );
	</script>
</html>