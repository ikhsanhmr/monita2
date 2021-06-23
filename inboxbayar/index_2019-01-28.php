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

		if ($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03){
			
			$userlvl = 3;

		} elseif($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05){

			$userlvl = 4;
			
		} elseif($_SESSION["roleid"] == 13){

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
		// $parm .= ($p1==""? "": " and SUBSTR(tglawal, 1, 7) >= '$p1'");
		// $parm .= ($p2==""? "": " and SUBSTR(tglakhir, 1, 7) <= '$p2'");
		$parm .= ($p1==""? "": " and YEAR(inputdt) = " . substr($p1,0,4) . " AND MONTH(inputdt) >= " . substr($p1,-2));
		$parm .= ($p2==""? "": " and YEAR(inputdt) = " . substr($p2,0,4) . " AND MONTH(inputdt) <= " . substr($p2,-2));
		$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
		
		if($_SESSION["roleid"] == 04 || $_SESSION["roleid"] == 05){
			
			$parm .= ($p0==""? " and pelaksana in (Select akses From akses_bidang Where nip = '".$_SESSION['cnip']."')": " and pelaksana = '$p0'");

		}else{

			if ($_SESSION["roleid"] > 3){
				$parm .= " and pelaksana = '$_SESSION[org]'";
			}
		}
		
		$parm .= ($k0==""? "": " and skk = '$k0'");
		$parm .= ($o==""? "": " and skkoi = '$o'");
		$parm .= ($c==""? "": " and TRIM(nomorkontrak) = '$c'");
		/*
			SignLevel #0 -> Laporan penyerapan AI
			SignLevel #1 -> Inbox Bayar Pelaksana UP3 / Bidang
			SignLevel #2 -> Inbox Bayar Manager Bagian
			SignLevel #3 -> Inbox Bayar User Anggaran
			SignLevel #4 -> Inbox Bayar User Keuangan
		*/
		// $parm .= ($_SESSION["org"] == '' || $_SESSION["org"] == 3 ? ($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03 ? ' AND (signlevel = 2 AND actiontype = 1)' : ' AND (signlevel = 3 AND actiontype = 1)') : ($_SESSION["roleid"] == 13 ? ' AND (signlevel = 1 AND actiontype = 1)' : ' AND ((signlevel = 0 AND actiontype = 1) OR (signlevel > 1 AND actiontype = 0))'));

		switch ($userlvl) {
		    case 1:
		        $parm .= " AND ((signlevel = 0 AND actiontype = 1) OR (signlevel > 1 AND actiontype = 0))";
		        break;
		    case 2:
		        $parm .= " AND (signlevel = 1 AND actiontype = 1)";
		        break;
		    case 3:
		        $parm .= " AND (signlevel = 2 AND actiontype = 1)";
		        break;
		    case 4:
		        $parm .= " AND (signlevel = 3 AND actiontype = 1)";
		        break;
		}

		if($_SESSION["org"] == '' && ($_SESSION["roleid"] == 02 || $_SESSION["roleid"] == 03)){
			$parm .= " AND d.pos1 IN (Select akses From akses_pos Where nip = '".$_SESSION["cnip"]."')";
		}
		
		// echo "<pre>";
		// echo print_r($_SESSION);
		// echo "</pre>";
		// return;
		echo "
			<h2>Inbox Bayar</h2>
		";
		// if($userlvl == 1){
		// 	echo "
		// 		<table>
		// 			<tr>
		// 				<th>Periode (yyyy-mm)</th>
		// 				<td>:</td>
		// 				<td><input type='month' name='p1' id='p1' value='$p1'> - <input type='month' name='p2' id='p2' value='$p2'></td>
		// 			</tr>
		// 			<tr>
		// 				<th>Jenis</th>
		// 				<td>:</td>
		// 				<td>
		// 					<select name='o' id='o'>
		// 						<option value=''></option>
		// 						<option value='SKKO'" . ($o=="SKKO"? "selected": "") . ">SKKO</option>
		// 						<option value='SKKI'" . ($o=="SKKI"? "selected": "") . ">SKKI</option>
		// 					</select>
		// 				</td>
		// 			</tr>
		// 			<tr>
		// 				<th>Bidang</th>
		// 				<td>:</td>
		// 				<td>$b</td>
		// 			</tr>
		// 			<tr>
		// 				<th>Pelaksana</th>
		// 				<td>:</td>
		// 				<td>$p</td>
		// 			</tr>
		// 			<tr>
		// 				<th>No SKK</th>
		// 				<td>:</td>
		// 				<td><input type='text' name='skk' id='skk' size='49' value='$k0'></td>
		// 			</tr>
		// 			<tr>
		// 				<th>No Kontrak</th>
		// 				<td>:</td>
		// 				<td><input type='text' name='kon' id='kon' size='49' value='$c'></td>
		// 			</tr>
		// 			<tr>
		// 				<td colspan='3' align='right'>
		// 					<input type='button' value='Cetak SPM' onclick='viewk(1)'>
		// 				</td>
		// 			</tr>
		// 		</table>
		// 	";
		// }
		echo ( $userlvl == 1 ? "<a href='#' onClick='upload()'>(+) Upload Bayar</a>" : "" );
		
		//if($v!="") {
			/*$sql = "
				SELECT 	n.nomornota, nipuser, g.id userid, pelaksana, b.namaunit, pos1, nilai1, 
						namapos, nomorwbs, nomorprk, nomorscore, nomorskki noskk, s.uraian uraians,
						tanggalskki, nilaianggaran anggaran, nilaidisburse disburse, nilaiwbs wbs,
						inputdt, nomorkontrak, k.nodokumen nodokumen, vendor, k.uraian uraiank, 
						tglawal, tglakhir, nilaikontrak kontrak, k.signed, bayar, k.kid , k.file_path
				FROM 	notadinas n	LEFT JOIN 
						bidang g ON n.nipuser = g.nick LEFT JOIN 
						notadinas_detail d ON n.nomornota = d.nomornota	LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						skkiterbit s ON d.noskk = s.nomorskki LEFT JOIN 
						(
							SELECT 	kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT 	kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos LEFT JOIN 
						kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
						(
							SELECT 	nokontrak, SUM(nilaibayar) bayar 
							FROM 	realisasibayar 
							GROUP BY nokontrak
						) r ON k.nomorkontrak = r.nokontrak INNER JOIN
						(
							SELECT t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, nilaitagihan, catatan, catatanreject
							FROM kontrak_approval t1
							WHERE t1.id = (SELECT t2.id
							                 FROM kontrak_approval t2
							                 WHERE TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
							                 ORDER BY t2.signdt DESC
							                 LIMIT 1)
						) ka  ON TRIM(k.nomorkontrak) = TRIM(ka.nmrkontrak)
				WHERE d.progress >= 7 AND NOT nomorskki IS NULL
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";*/
			/*$sql = "
				SELECT 	n.nomornota nd, skk, pos1, nomorkontrak, b.namaunit, vendor, oi.uraian oiuraian, nodokumen, nilaikontrak, bayar, ka.*
				FROM 	notadinas n LEFT JOIN 
						bidang g ON n.nipuser = g.nick LEFT JOIN 
						notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN 
						bidang b ON d.pelaksana = b.id LEFT JOIN 
						(
							SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
							SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
						) p ON d.pos1 = p.pos LEFT JOIN 
						(
							SELECT nomorskko skk, uraian FROM skkoterbit UNION
							SELECT nomorskki skk, uraian FROM skkiterbit
						) oi ON d.noskk = oi.skk LEFT JOIN 
						kontrak k ON d.noskk = k.nomorskkoi AND d.pos1 = k.pos LEFT JOIN 
						(
							SELECT 	nokontrak, SUM(nilaibayar) bayar 
							FROM 	realisasibayar 
							GROUP BY nokontrak
						) r ON k.nomorkontrak = r.nokontrak INNER JOIN
						(
							SELECT t1.nomorkontrak nmrkontrak, signed, signdt, signlevel, actiontype, nilaitagihan, catatan, catatanreject
							FROM kontrak_approval t1
							WHERE t1.id = (SELECT t2.id
							                 FROM kontrak_approval t2
							                 WHERE TRIM(t2.nomorkontrak) = TRIM(t1.nomorkontrak)
							                 ORDER BY t2.signdt DESC
							                 LIMIT 1)
						) ka  ON TRIM(k.nomorkontrak) = TRIM(ka.nmrkontrak)
				WHERE n.progress = 7 
					AND COALESCE(oi.skk, '') != '' AND NOT nomorkontrak IS NULL 
					$parm
				ORDER BY nd, skk, pos1, nomorkontrak";*/
			$sql = "
				SELECT	d.nomornota nd, skk, pos1, nomorkontrak, b.namaunit, vendor, oi.uraian oiuraian, 
						nodokumen, nilaikontrak, bayar, ka.*
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
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 LEFT JOIN 
						bidang b ON d.pelaksana = b.id  LEFT JOIN 
						(
							SELECT nomorskko skk, uraian FROM skkoterbit UNION
							SELECT nomorskki skk, uraian FROM skkiterbit
						) oi ON d.noskk = oi.skk LEFT JOIN 
						(
							SELECT 	nokontrak, SUM(nilaibayar) bayar 
							FROM 	realisasibayar 
							GROUP BY nokontrak
						) r ON ka.nmrkontrak = r.nokontrak
				WHERE 	COALESCE(nomorskkoi, '') != '' AND NOT nomorkontrak IS NULL
				$parm
				ORDER BY nd, nomorskkoi, pos1, nomorkontrak";
			/*echo $sql;
			return;*/
			
			$no = 0;
			$parm = "";
			//$dummy = 0;
			$result = mysql_query($sql);
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$no++;

				$nilaitagihan = $row["nilaitagihan"];

				if (empty($nilaitagihan)){

					$nilaitagihan = $row["nilaikontrak"] - $row["bayar"];
				}

				$parm .= "
					<tr>
						<td><input type='hidden' name='k$no' value='".$row[nomorkontrak]."'/></td>
						<td>$no</td>
						<td>$row[skk]</td>
						<td>$row[nomorkontrak]</td>
						<td>$row[namaunit]</td>
						<td>$row[vendor]</td>
						<td>$row[oiuraian]</td>
						".($userlvl == 1 ? "<td><input type='text' name='doc$no' value='".$row[nodokumen]."'/></td>" : "<td>$row[nodokumen]</td>" )."
						<td align='right'>".number_format($row["nilaikontrak"])."</td>
						<td align='right'>".number_format($row["bayar"])."<input type='hidden' name='s$no' value='".($row["nilaikontrak"] - $row["bayar"])."'/></td>
						<td align='right'>".number_format($row["nilaikontrak"] - $row["bayar"])."</td>
						<td align='right'>
							".($userlvl != 2 ? "<input type='number' name='t$no' value='".$nilaitagihan."'/>" : number_format($nilaitagihan)." <input type='hidden' name='t$no' value='".$nilaitagihan."'/>" )."
						</td>
						<td>
							".($userlvl == 1 ? "<input type='text' name='ctt$no' value='".$row[catatan]."'/>" : "$row[catatan]<input type='hidden' name='ctt$no' value='".$row[catatan]."'/>" )."
						</td>
						".($userlvl == 1 ? "<td>".(empty($row[catatanreject]) ? "-" : $row[catatanreject])."</td>" : "" )."
					</tr>";
					//min='0' max='$dummy' 
			}
			mysql_free_result($result);
			
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
							<th>No Dokumen</th>
							<th>Nilai Kontrak</th>
							<th>Nilai Bayar</th>
							<th>Sisa</th>
							<th>Nilai Tagihan</th>
							<th>Catatan</th>
							".($userlvl == 1 ? "<th>Alasan Ditolak</th>" : "" )."
						</thead>
						$parm
						<tfoot>

						</tfoot>
					</table>
					<input type='hidden' name='level' value='$userlvl'/>
					<button type='submit' data-flag='0ALL'>Reject All</button>
					<button type='submit' data-flag='0'>Reject</button>
					<button type='submit' data-flag='1'>Approve</button>
					<button type='submit' data-flag='1ALL'>Approve All</button>
					<input type='button' onclick='location.href=\"indexexcel.php?v=1\";' value='Export Excel' />
				</form>";
		//}
		mysql_close($kon);
	?>

	</body>
	
	<script src="../js/jquery-1.12.0.min.js"></script>
	<!-- <script src="../js/jquery.dataTables.min.js"></script> -->
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
	        order: [[ 1, 'asc' ]]
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