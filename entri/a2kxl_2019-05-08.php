<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=a2k.xls");

	session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}

	echo "<style> .str{ mso-number-format:\@; } </style>";
	
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
	$parm .= ($p1==""? "": " and YEAR(tanggalskki) = " . substr($p1,0,4) . " AND MONTH(tanggalskki) >= " . substr($p1,-2));
	$parm .= ($p2==""? "": " and YEAR(tanggalskki) = " . substr($p2,0,4) . " AND MONTH(tanggalskki) <= " . substr($p2,-2));
	$parm .= ($b0==""? "": " and (g.id = '$b0' or pelaksana = '$b0')");
	$parm .= ($p0==""? "": " and pelaksana = '$p0'");
	$parm .= ($k0==""? "": " and skk = '$k0'");
	$parm .= ($o==""? "": " and skkoi = '$o'");
	$parm .= ($c==""? "": " and nomorkontrak = '$c'");
	
	if($v!="") {

			$sql = "
				SELECT 	nomorprk, nomorkontrak, nilaikontrak, vendor, k.uraian, tglawal, tglakhir, namaunit, nick2, 
						inputdt
				FROM	kontrak k INNER JOIN
						skkiterbit skk ON k.nomorskkoi = skk.nomorskki INNER JOIN
						notadinas_detail d ON k.nomorskkoi = d.noskk AND k.pos = d.pos1 INNER JOIN 
						bidang b ON d.pelaksana = b.id
				Where	1=1
				$parm
				ORDER BY nomorskki, LPAD(pelaksana, 2, '0'), k.pos, nomorkontrak";
			// echo $sql;
			// return;
			//echo $parm;
			
			
			$no = 0;
			$parm = "";
			$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
			while ($row = mysqli_fetch_array($result)) {
				$no++;
				
				$kdfungsi = substr($row['nomorprk'],23,5);
				$nokontrak = $row['nick2'].".".$kdfungsi.".".$row['nomorkontrak'];
				$noski = substr($row['nomorprk'],0,12);
				$noproyek = substr($row['nomorprk'],13,15);

				$parm .= "
					<tr>
						<td></td>
						<td>$nokontrak</td>
						<td>$noski</td>
						<td>$noproyek</td>
						<td>$row[nilaikontrak]</td>
						<td>$row[vendor]</td>
						<td>$row[uraian]</td>
						<td class='str'>".date("d-m-Y", strtotime($row["tglawal"]))."</td>
						<td class='str'>".date("d-m-Y", strtotime($row["tglakhir"]))."</td>
						<td>$row[nilaikontrak]</td>
						<td>$row[inputdt]</td>
					</tr>";
					//min='0' max='$dummy' 
			}
			mysqli_free_result($result);
			
			echo "
				<table border='1'>
					<tr>
						<th>ID KONTRAK</th>
						<th>NO. KONTRAK</th>
						<th>NO. SKI</th>
						<th>NO. PROYEK</th>
						<th>NILAI KONTRAK YANG DIBIAYAI OLEH PROYEK (RP)</th>
						<th>NAMA VENDOR</th>
						<th>NAMA KONTRAK</th>
						<th>TGL. AWAL</th>
						<th>TGL. AKHIR</th>
						<th>NILAI KONTRAK (RP)</th>
						<th>TGL. ENTRY</th>
					</tr>
					$parm
				</table>";
		}
	$mysqli->close();($kon);
?>