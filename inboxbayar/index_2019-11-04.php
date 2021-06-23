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
		session_start();
		if(!isset($_SESSION["nip"])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		
		require_once "../config/koneksi.php";
			$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";

			$result = mysql_query($sql);
			
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

		} else if($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05 || $_SESSION["roleid"] == 21){

			$userlvl = 4;
			
		} else if($_SESSION["roleid"] == 13){

			$userlvl = 2;
			
		} else {

			$userlvl = 1;
		}
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
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
		mysql_free_result($result);

		if ($userlvl == 4){
			$sql = "SELECT * FROM bidang b INNER JOIN akses_bidang ab ON b.id = ab.akses WHERE ab.nip = '$_SESSION[cnip]' ORDER BY LPAD(id, 2, '0')";
			$result = mysql_query($sql);

			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				
				$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
						"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
						($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			mysql_free_result($result);
		}

		$b .= "</select>";
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
		$column = "";
		$table = "";

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
				$column = "";
				$table = "";

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
				$column = "";
				$table = "";

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

				$parm .= " AND ((signlevel = 2 AND actiontype = 1) or (d.pelaksana <= 5 and (signlevel = 1 AND actiontype = 1))) "; //case 1 dan 3
				$column = ", kapel.signdt as app_pel";
				$table = "
					LEFT JOIN
					(
						SELECT	t.nomorkontrak as nmrkontrak, signdt
						FROM		kontrak_approval t INNER JOIN 
										(
											SELECT nomorkontrak, MAX( id ) AS lastid
											FROM kontrak_approval
											Where signlevel = 2 and actiontype = 1
											GROUP BY nomorkontrak
										)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
					) kapel  ON k.nomorkontrak = kapel.nmrkontrak
				";

		        if($_SESSION["roleid"] > 1){ // case 2

					$parm .=" and 
						(
							(
								pelaksana <= 5 and 
								d.pos1 IN (
									Select 	akses 
									From 	akses_pos 
									Where 	nip = '".$_SESSION["cnip"]."'
								)
							)
							or 
							(
								pelaksana > 5 and (
									pelaksana IN (
										Select 	akses 
										From 	akses_bidang 
										Where 	nip = '".$_SESSION["cnip"]."'
									) and 
									d.pos1 NOT IN (
										Select 	akses 
										From 	akses_pos ap inner join 
												user u ON ap.nip = u.nip 
										Where 	roleid IN (2,3) and is_all_unit = 1
									)
									or
									d.pos1 IN (
										Select 	akses 
										From 	akses_pos 
										Where 	nip = '".$_SESSION["cnip"]."' and is_all_unit = 1
									)
								)
							)
						)
					";
					
		        }
		       	
		        break;
		    case 4:
		        /* 
		    		1. user level 3 tagihan di approve, maka masuk kesini.
		    		2. masing masing user memiliki wewenang berdasarkan bidang/area sendiri. kecuali untuk pak Ricky muncul semua tagihan.
		    		3. jika user ini meng-reject tagihan maka akan kembali ke inbox bayar level 1.
		    	*/

				$parm .= " AND (signlevel = 3 AND actiontype = 1) "; // case 1 dan 3
				$column = ", kapel.signdt as app_pel, kaang.signdt as app_ang";
				$table = "
					LEFT JOIN
					(
						SELECT	t.nomorkontrak as nmrkontrak, signdt
						FROM		kontrak_approval t INNER JOIN 
										(
											SELECT nomorkontrak, MAX( id ) AS lastid
											FROM kontrak_approval
											Where signlevel = 2 and actiontype = 1
											GROUP BY nomorkontrak
										)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
					) kapel  ON k.nomorkontrak = kapel.nmrkontrak LEFT JOIN
					(
						SELECT	t.nomorkontrak as nmrkontrak, signdt
						FROM		kontrak_approval t INNER JOIN 
										(
											SELECT nomorkontrak, MAX( id ) AS lastid
											FROM kontrak_approval
											Where signlevel = 3 and actiontype = 1
											GROUP BY nomorkontrak
										)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
					) kaang  ON k.nomorkontrak = kaang.nmrkontrak
				";

				if($_SESSION["roleid"] != 21){ // case 2
					
					$parm .=" and 
						(
							(
								pelaksana <= 5 and 
								d.pos1 IN (
									Select 	akses 
									From 	akses_pos 
									Where 	nip = '".$_SESSION['cnip']."'
								)
							) 
							or 
							(
								pelaksana > 5 and 
								(
									(
										pelaksana IN (
											Select 	akses 
											From 	akses_bidang 
											Where nip = '".$_SESSION['cnip']."'
										) and 
										d.pos1 NOT IN (
											Select 	akses 
											From 	akses_pos ap inner join 
													user u ON ap.nip = u.nip 
											Where 	roleid IN (4,5) and is_all_unit = 1
										)
									) or
									d.pos1 IN (
										Select 	akses 
										From 	akses_pos 
										Where 	nip = '".$_SESSION['cnip']."' and is_all_unit = 1
									)
								)
							)
						)
					";
					
		        }

		        break;
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
				SELECT	nomorskkoi, pos1, nomorkontrak, b.namaunit, vendor, k.uraian uraian, nodokumen, 
						nilaikontrak, bayar, ka.signed, ka.signdt, ka.signlevel, ka.actiontype, ka.nilaitagihan, 
						ka.catatan, ka.catatanreject, namapos, k.inputdt $column
				FROM 	(
							SELECT	t.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, 
									nilaitagihan, catatan, catatanreject
							FROM	kontrak_approval t INNER JOIN 
									(
										SELECT nomorkontrak, MAX( id ) AS lastid
										FROM kontrak_approval
										WHERE year(signdt) = ".date("Y")."
										GROUP BY nomorkontrak
									)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
						) ka INNER JOIN 
						kontrak k ON ka.nmrkontrak = k.nomorkontrak INNER JOIN
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 LEFT JOIN
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
						$table
				WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
				$parm
				ORDER BY d.pos1 ASC, k.inputdt DESC";

			if ($_SESSION["roleid"] == 1 || $_SESSION["roleid"] == 21){

				$otherjoin = "";

				if($_SESSION["roleid"] == 21){
					$otherjoin = " 	LEFT JOIN
									(
										SELECT 	akses, ap.nip, u.nama, 0 as is_all_unit
										FROM 	akses_bidang ap INNER JOIN 
												user u On ap.nip = u.nip 
										where 	roleid IN (4,5)
										GROUP BY akses
										UNION
										SELECT 	ap.akses, ap.nip, u.nama, ap.is_all_unit
										FROM 	akses_pos ap LEFT JOIN
												user u On ap.nip = u.nip
										where 	roleid IN (4,5)
										GROUP BY akses
									) ap ON (pelaksana <= 5 and (d.pos1 = ap.akses)) OR (pelaksana > 5 and (pelaksana = ap.akses and d.pos1 NOT IN (Select akses From akses_pos ap inner join user u ON ap.nip = u.nip Where roleid IN (4,5) and is_all_unit = 1) or (d.pos1 = ap.akses and ap.is_all_unit = 1)))";
								//(pelaksana = ap.akses and d.pos1 NOT IN ('52.3.04','54.2.04')) or d.pos1 = ap.akses";
				}

				if($_SESSION["roleid"] == 1){
					$otherjoin = " 	LEFT JOIN
									(
										SELECT 	akses, ap.nip, u.nama, 0 as is_all_unit
										FROM 	akses_bidang ap INNER JOIN 
												user u On ap.nip = u.nip 
										where 	roleid IN (2,3)
										GROUP BY akses
										UNION
										SELECT 	ap.akses, ap.nip, u.nama, ap.is_all_unit
										FROM 	akses_pos ap LEFT JOIN
												user u On ap.nip = u.nip
										where 	roleid IN (2,3)
										GROUP BY akses
									) ap ON (pelaksana <= 5 and (d.pos1 = ap.akses)) OR 
											(pelaksana > 5 and 
												(
													(pelaksana = ap.akses and d.pos1 NOT IN (Select akses From akses_pos ap inner join user u ON ap.nip = u.nip Where roleid IN (4,5) and is_all_unit = 1)) 
													or 
													(d.pos1 = ap.akses and ap.is_all_unit = 1)
												)
											)
					";
				}
				
				$sql = "
					SELECT 	nomorskkoi, pos1, nomorkontrak, namaunit, vendor, uraian, nodokumen, nilaikontrak, 
							bayar, signed, signdt, signlevel, actiontype, nilaitagihan, catatan, catatanreject, 
							namapos, inputdt, app_pel, app_ang, GROUP_CONCAT(namauser SEPARATOR ', ') AS namauser
					FROM	(
								SELECT	nomorskkoi, pos1, nomorkontrak, b.namaunit, vendor, k.uraian uraian, 
										nodokumen, nilaikontrak, bayar, ka.signed, ka.signdt, ka.signlevel, 
										ka.actiontype, ka.nilaitagihan, ka.catatan, ka.catatanreject, namapos, 
										k.inputdt, kapel.signdt as app_pel, kaang.signdt as app_ang, 
										ap.nama namauser
								FROM 	(
											SELECT	t.nomorkontrak nmrkontrak, signed, signdt, signlevel, 
													actiontype, nilaitagihan, catatan, catatanreject
											FROM	kontrak_approval t INNER JOIN 
													(
														SELECT nomorkontrak, MAX( id ) AS lastid
														FROM kontrak_approval
														WHERE year(signdt) = ".date("Y")."
														GROUP BY nomorkontrak
													)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
										) ka INNER JOIN 
										kontrak k ON ka.nmrkontrak = k.nomorkontrak INNER JOIN
										notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 LEFT JOIN
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
											SELECT	t.nomorkontrak as nmrkontrak, signdt
											FROM		kontrak_approval t INNER JOIN 
															(
																SELECT nomorkontrak, MAX( id ) AS lastid
																FROM kontrak_approval
																Where signlevel = 2 and actiontype = 1
																GROUP BY nomorkontrak
															)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
										) kapel  ON k.nomorkontrak = kapel.nmrkontrak LEFT JOIN
										(
											SELECT	t.nomorkontrak as nmrkontrak, signdt
											FROM		kontrak_approval t INNER JOIN 
															(
																SELECT nomorkontrak, MAX( id ) AS lastid
																FROM kontrak_approval
																Where signlevel = 3 and actiontype = 1
																GROUP BY nomorkontrak
															)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
										) kaang  ON k.nomorkontrak = kaang.nmrkontrak
										$otherjoin
								WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
								$parm
								ORDER BY d.pos1 ASC, k.inputdt DESC
							) as data
					GROUP BY nomorskkoi, pos1, nomorkontrak, namaunit, vendor, uraian, nodokumen, nilaikontrak, bayar, 
							signed, signdt, signlevel, actiontype, nilaitagihan, catatan, catatanreject, namapos, inputdt, 
							app_pel, app_ang
					ORDER BY pos1 ASC, inputdt DESC
				";
			}
			// echo $sql;
			// return;
			
			$no = 0;
			$body = "";
			$infopic = "";
			//$dummy = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
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
						<td>$row[namapos] <br /> ($row[pos1])</td>
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
						<td>$row[inputdt]</td>
						".($userlvl >= 3 ? "<td>$row[app_pel]</td>" : "" )."
						".($userlvl == 4 ? "<td>$row[app_ang]</td>" : "" )."
					</tr>";
					//min='0' max='$dummy' 
			}
			mysql_free_result($result);

			if($_SESSION["roleid"] == 1){

				$sql = "
					SELECT 	judul, namauser, SUM(jmlskki) as jmlskki, SUM(jmlskko) as jmlskko, 
							SUM(nilaiskki) as nilaiskki, SUM(nilaiskko) as nilaiskko
					FROM	(
								SELECT 	nomorkontrak, jmlskki, jmlskko, nilaiskko, nilaiskki, judul, 
										GROUP_CONCAT(DISTINCT namauser SEPARATOR ', ') AS namauser
								FROM	(
											SELECT	nomorkontrak, (CASE WHEN n.skkoi = 'SKKI' THEN 1 ELSE 0 END) as jmlskki, 
													(CASE WHEN n.skkoi = 'SKKO' THEN 1 ELSE 0 END) as jmlskko, 
													(CASE WHEN n.skkoi = 'SKKI' THEN ka.nilaitagihan ELSE 0 END) as nilaiskki, 
													(CASE WHEN n.skkoi = 'SKKO' THEN ka.nilaitagihan ELSE 0 END) as nilaiskko, 
													(CASE 	WHEN pelaksana <= 5 THEN CONCAT('WILSU - ', p.namapos) 
															WHEN pelaksana > 5 and d.pos1 IN (Select akses From akses_pos ap inner join user u ON ap.nip = u.nip Where roleid IN (4,5) and is_all_unit = 1) THEN CONCAT(b.namaunit, ' - ', p.namapos)
															ELSE b.namaunit 
													END) as judul, ap.nama namauser
											FROM 	(
														SELECT	t.nomorkontrak nmrkontrak, signed, signdt, signlevel, 
																actiontype, nilaitagihan, catatan, catatanreject
														FROM	kontrak_approval t INNER JOIN 
																(
																	SELECT nomorkontrak, MAX( id ) AS lastid
																	FROM kontrak_approval
																	GROUP BY nomorkontrak
																)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
													) ka INNER JOIN 
													kontrak k ON ka.nmrkontrak = k.nomorkontrak INNER JOIN
													notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 LEFT JOIN
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
														SELECT 	akses, ap.nip, u.nama, 0 as is_all_unit
														FROM 	akses_bidang ap INNER JOIN 
																user u On ap.nip = u.nip 
														where 	roleid IN (2,3)
														UNION
														SELECT 	ap.akses, ap.nip, u.nama, ap.is_all_unit
														FROM 	akses_pos ap LEFT JOIN
																user u On ap.nip = u.nip
														where 	roleid IN (2,3)
													) ap ON (pelaksana <= 5 and (d.pos1 = ap.akses)) OR 
															(pelaksana > 5 and 
																(
																	(pelaksana = ap.akses and d.pos1 NOT IN (Select akses From akses_pos ap inner join user u ON ap.nip = u.nip Where roleid IN (4,5) and is_all_unit = 1)) 
																	or 
																	(d.pos1 = ap.akses and ap.is_all_unit = 1)
																)
															)
											WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL AND (signlevel = 2 AND actiontype = 1)
											ORDER BY nomorkontrak ASC
										) as data
								GROUP BY nomorkontrak, jmlskki, jmlskko, nilaiskko, nilaiskki, judul
							) as datagroup
					GROUP BY datagroup.judul, datagroup.namauser
					ORDER BY datagroup.judul ASC
				";
				//echo $sql;

				$result = mysql_query($sql);

				$infopic .= "<table id='dataTables2' class='display' cellspacing='0' width='100%'>
								<thead>
									<tr>
										<th colspan='6'>REKAP INBOX ANGGARAN</th>
									</tr>
									<tr>
										<th rowspan='2'>Nama Sub Pos / Pelaksana</th>
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

				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					
					$totaljmlskki += $row["jmlskki"];
					$totalnilaiskki += $row["nilaiskki"];
					$totaljmlskko += $row["jmlskko"];
					$totalnilaiskko += $row["nilaiskko"];

					if (!empty($row["judul"])){
						$infopic .= "
							<tr>
								<td>$row[judul]</td>
								<td style='text-align: right;'>".number_format($row["jmlskki"])."</td>
								<td style='text-align: right;'>".number_format($row["nilaiskki"])."</td>
								<td style='text-align: right;'>".number_format($row["jmlskko"])."</td>
								<td style='text-align: right;'>".number_format($row["nilaiskko"])."</td>
								<td>$row[namauser]</td>
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

				mysql_free_result($result);
			}
			
			if($_SESSION["roleid"] == 1 || $_SESSION["roleid"] == 21){

				$sql = "
					SELECT 	judul, namauser, SUM(jmlskki) as jmlskki, SUM(jmlskko) as jmlskko, 
							SUM(nilaiskki) as nilaiskki, SUM(nilaiskko) as nilaiskko
					FROM	(
								SELECT 	nomorkontrak, jmlskki, jmlskko, nilaiskko, nilaiskki, judul, 
										GROUP_CONCAT(DISTINCT namauser SEPARATOR ', ') AS namauser
								FROM	(
											SELECT	nomorkontrak, (CASE WHEN n.skkoi = 'SKKI' THEN 1 ELSE 0 END) as jmlskki, 
													(CASE WHEN n.skkoi = 'SKKO' THEN 1 ELSE 0 END) as jmlskko, 
													(CASE WHEN n.skkoi = 'SKKI' THEN ka.nilaitagihan ELSE 0 END) as nilaiskki, 
													(CASE WHEN n.skkoi = 'SKKO' THEN ka.nilaitagihan ELSE 0 END) as nilaiskko, 
													(CASE 	WHEN pelaksana <= 5 THEN CONCAT('WILSU - ', p.namapos) 
															WHEN pelaksana > 5 and ap.is_all_unit = 1 THEN CONCAT(b.namaunit, ' - ', p.namapos)
															ELSE b.namaunit 
													END) as judul, ap.nama namauser
											FROM 	(
														SELECT	t.nomorkontrak nmrkontrak, signed, signdt, signlevel, 
																actiontype, nilaitagihan, catatan, catatanreject
														FROM	kontrak_approval t INNER JOIN 
																(
																	SELECT nomorkontrak, MAX( id ) AS lastid
																	FROM kontrak_approval
																	GROUP BY nomorkontrak
																)tm ON t.nomorkontrak = tm.nomorkontrak AND t.id = tm.lastid
													) ka INNER JOIN 
													kontrak k ON ka.nmrkontrak = k.nomorkontrak INNER JOIN
													notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 LEFT JOIN
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
														SELECT 	akses, ap.nip, u.nama, 0 as is_all_unit
														FROM 	akses_bidang ap INNER JOIN 
																user u On ap.nip = u.nip 
														where 	roleid IN (4,5)
														UNION
														SELECT 	ap.akses, ap.nip, u.nama, ap.is_all_unit
														FROM 	akses_pos ap LEFT JOIN
																user u On ap.nip = u.nip
														where 	roleid IN (4,5)
													) ap ON (pelaksana <= 5 and (d.pos1 = ap.akses)) OR 
															(pelaksana > 5 and 
																(
																	(pelaksana = ap.akses and d.pos1 NOT IN (Select akses From akses_pos ap inner join user u ON ap.nip = u.nip Where roleid IN (4,5) and is_all_unit = 1)) 
																	or 
																	(d.pos1 = ap.akses and ap.is_all_unit = 1)
																)
															)
											WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL AND (signlevel = 3 AND actiontype = 1)
											ORDER BY nomorkontrak ASC
										) as data
								GROUP BY nomorkontrak, jmlskki, jmlskko, nilaiskko, nilaiskki, judul
							) as datagroup
					GROUP BY datagroup.judul, datagroup.namauser
					ORDER BY datagroup.judul ASC
				";
				//echo $sql;

				$result = mysql_query($sql);

				$infopic .= "<table id='dataTables3' class='display' cellspacing='0' width='100%'>
								<thead>
									<tr>
										<th colspan='6'>REKAP INBOX KEUANGAN</th>
									</tr>
									<tr>
										<th rowspan='2'>Nama Sub Pos / Pelaksana</th>
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

				while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
					
					$totaljmlskki += $row["jmlskki"];
					$totalnilaiskki += $row["nilaiskki"];
					$totaljmlskko += $row["jmlskko"];
					$totalnilaiskko += $row["nilaiskko"];

					if (!empty($row["judul"])){
						$infopic .= "
							<tr>
								<td>$row[judul]</td>
								<td style='text-align: right;'>".number_format($row["jmlskki"])."</td>
								<td style='text-align: right;'>".number_format($row["nilaiskki"])."</td>
								<td style='text-align: right;'>".number_format($row["jmlskko"])."</td>
								<td style='text-align: right;'>".number_format($row["nilaiskko"])."</td>
								<td>$row[namauser]</td>
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

				mysql_free_result($result);
			}
			
			echo "
				<form name='frm' id='frm' method='post' action='simpan.php'>
					<input type='hidden' id='level' name='level' value='".($userlvl == 1 && $org <= 5 ? 2 : $userlvl)."' />
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
							".($_SESSION["roleid"] == 1 || $_SESSION["roleid"] == 21 ? "<th>PIC</th>" : "" )."
							<th>Catatan</th>
							".($userlvl == 1 ? "<th>Alasan Ditolak</th>" : "" )."
							<th>Tgl Entry Kontrak</th>
							".($userlvl >= 3 ? "<th>Tgl Approve Bidang/UP3</th>" : "" )."
							".($userlvl == 4 ? "<th>Tgl Approve Anggaran</th>" : "" )."
						</thead>
						$body
						<tfoot>

						</tfoot>
					</table>
					
					<button type='submit' data-flag='0'>Reject</button>
					<button type='submit' data-flag='1'>Approve</button>
					<input type='button' onclick='location.href=\"indexexcel.php?v=1\";' value='Export Excel' />
				</form>

				$infopic
			";
		//}
		mysql_close($kon);
	?>

	</body>
	
	<script src="../js/jquery-1.12.0.min.js"></script>
	<script src="../js/jquery.dataTables.v1.10.16.min.js"></script>
	<script src="../js/dataTables.select.min.js"></script>
	<script>
	$(document).ready(function() {
		var table = $('#dataTables').DataTable({
			columnDefs: [ {
	            orderable: false,
	            className: 'select-checkbox',
	            targets:   0
	        } ],
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
					
					$(form).prepend(
						$('<input>').attr('type', 'hidden').attr('name', 'id[]').val(data[1])
					);
				});

				btnName = 1;

			} else if (btnName == '0ALL'){

				var data = table.rows( { filter : 'applied'} ).data();
				$.each(data, function(id, data){
					// Create a hidden element 
					
					$(form).prepend(
						$('<input>').attr('type', 'hidden').attr('name', 'id[]').val(data[1])
					);
				});

				btnName = 0;

			}else{
				var rows_selected = table.rows( { selected: true } ).data();
				
				// Iterate over all selected checkboxes
				$.each(rows_selected, function(index, data){
					// Create a hidden element

					$(form).prepend(
						$('<input>').attr('type', 'hidden').attr('name', 'id[]').val(data[1])
					);
				});
			}
			
			input = $("<input>").attr('type', 'hidden').attr("name", "actiontype").val(btnName);
			$(this).prepend(input);

			if(btnName == 0 && userlvl == 1){
				$("#level").val(userlvl);
			}

			if(btnName == 0 && userlvl != 1){
				var reason = prompt("Silahkan masukan alasan kontrak ditolak:", "");
				console.log(reason);
				if (reason == "") {

					alert("Alasan kontrak ditolak wajib diisi.", "Warning");
					e.preventDefault();
					return false;

				} else if (reason == null) {
					e.preventDefault();
					return false;
					
				} else{
					$(form).prepend(
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