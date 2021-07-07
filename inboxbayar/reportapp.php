<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.min.css">
	
	<script type="text/javascript">
		function viewk(me) {
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
			var url = encodeURI((me==undefined? "reportapp.php": "reportappxl.php") + "?p1="+p1+"&p2="+p2+"&b="+b+"&p="+p+"&k="+k+"&o="+o+"&c="+c+"&v=1");
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
		
		$b = "<select name='bidang' id='bidang'>" . ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5? "<option value=''></option>": "");
		$p = "<select name='pelaksana' id='pelaksana'>" . ($_SESSION["org"]>5? "": "<option value=''></option>");

		while ($row = mysqli_fetch_array($result)) {
			if($row["id"]<6) {
				$b .= ($_SESSION["org"]=="" || $_SESSION["org"]==1 || $_SESSION["org"]==3 || $_SESSION["org"]>5)?
					"<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>":
					($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$b0? "selected": "") . ">$row[namaunit]</option>": "");
			}
			$p .= ($_SESSION["org"]=="" || $_SESSION["org"]<=5)? 
				"<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>":
				($row["id"]==$_SESSION["org"]? "<option value='$row[id]' " . ($row["id"]==$p0? "selected": "") . ">$row[namaunit]</option>": "");
		}
		mysqli_free_result($result);
		$b .= "</select>";
		$p .= "</select>";
	?>
</head>


<body>
	<?php
		$parm = "";
		$parm .= ($p1==""? "": " and DATE(ka.signdt) >= '$p1'");
		$parm .= ($p2==""? "": " and DATE(ka.signdt) <= '$p2'");
		$parm .= ($b0==""? "": " and (g.id = '$b0' or d.pelaksana = '$b0')");
		$parm .= ($p0==""? "": " and d.pelaksana = '$p0'");
		$parm .= ($k0==""? "": " and k.nomorskkoi = '$k0'");
		$parm .= ($o==""? "": " and n.skkoi = '$o'");
		$parm .= ($c==""? "": " and k.nomorkontrak = '$c'");
		
		echo "
			<h2>Data Approval Anggaran</h2>
			<table>
				<tr>
					<th>Periode (yyyy-mm-dd)</th>
					<td>:</td>
					<td><input type='date' name='p1' id='p1' value='$p1'> - <input type='date' name='p2' id='p2' value='$p2'></td>
				</tr>
				<tr>
					<th>Jenis</th>
					<td>:</td>
					<td>
						<select name='o' id='o'>
							<option value=''></option>
							<option value='SKKO'" . ($o=="SKKO"? "selected": "") . ">SKKO</option>
							<option value='SKKI'" . ($o=="SKKI"? "selected": "") . ">SKKI</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Bidang</th>
					<td>:</td>
					<td>$b</td>
				</tr>
				<tr>
					<th>Pelaksana</th>
					<td>:</td>
					<td>$p</td>
				</tr>
				<tr>
					<th>No SKK</th>
					<td>:</td>
					<td><input type='text' name='skk' id='skk' size='49' value='$k0'></td>
				</tr>
				<tr>
					<th>No Kontrak</th>
					<td>:</td>
					<td><input type='text' name='kon' id='kon' size='49' value='$c'></td>
				</tr>
				<tr>
					<td colspan='3' align='right'>
						<input type='button' value='Ok' onclick='viewk()'>
						<input type='button' value='Excel' onclick='viewk(1)'>
					</td>
				</tr>
			</table>		
		";
		
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
			// echo $sql;
			// return;
			//echo $parm;
			
			$kontrak = 0;
			$bayar = 0;
			$no = 0;
			$body = "";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				$kontrak += $row["nilaikontrak"];
				$bayar += $row["bayar"];
				
				$body .= "
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
				<table id='dataTables' class='display' cellspacing='0' width='100%'>
					<thead>	
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
					</thead>
					<tbody>
						$body
					</tbody>
					<tfoot>
						<tr>
							<td colspan='9' style='tet-align: right;'>Total</td>
							<td align='right'>".number_format($kontrak)."</td>
							<td align='right'>".number_format($bayar)."</td>
							<td align='right'>".number_format($kontrak-$bayar)."</td>
							<td align='right'>".number_format(@($bayar/$kontrak)*100,2)."</td>
							<td colspan='5'></td>
						</tr>
					</tfoot>
				</table>
			";
		}
		$mysqli->close();($kon);
	?>
	<script src="../js/jquery-1.12.0.min.js"></script>
	<script src="../js/jquery.dataTables.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#dataTables').DataTable();
	} );
	</script>
	</body>
</html>