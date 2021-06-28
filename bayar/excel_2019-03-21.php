<?php
	header("Content-type: application/vnd.ms-excell");
	header("Content-Disposition: attachment; Filename=bayar.xls");

	session_start();
	if(!isset($_SESSION["nip"])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	$editor = (($_SESSION["roleid"]=="01" || $_SESSION["roleid"]=="04" || $_SESSION["roleid"]=="05" || $_SESSION["roleid"]=="10")? true: false);
	
	require_once "../config/koneksi.php";
	$sql = "SELECT * FROM bidang ORDER BY LPAD(id, 2, '0')";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$p1 = isset($_REQUEST["p1"])? $_REQUEST["p1"]: "";
	$p2 = isset($_REQUEST["p2"])? $_REQUEST["p2"]: "";
	$p2 = ($p2==""? $p1: $p2);
	$b0 = isset($_REQUEST["b"])? $_REQUEST["b"]: "";
	$p0 = isset($_REQUEST["p"])? $_REQUEST["p"]: "";
	$s0 = isset($_REQUEST["s"])? $_REQUEST["s"]: "";
	$k0 = isset($_REQUEST["k"])? $_REQUEST["k"]: "";
	$o = isset($_REQUEST["o"])? $_REQUEST["o"]: "";
	$v = isset($_REQUEST["v"])? $_REQUEST["v"]: "";
	
	while ($row = mysqli_fetch_array($result)) {
		$b = ($row["id"]==$b0? $row["namaunit"]: $b);
		$p = ($row["id"]==$p0? $row["namaunit"]: $p);
	}
	mysqli_free_result($result);

		$parm = "";
//		$parm .= ($p1==""? "": " and SUBSTR(tglbayar, 1, 7) >= '$p1'"); //
//		$parm .= ($p2==""? "": " and SUBSTR(tglbayar, 1, 7) <= '$p2'"); //
		$parm .= ($p1==""? "": " and YEAR(tglbayar) = " . substr($p1,0,4) . " AND MONTH(tglbayar) >= " . substr($p1,-2));		
		$parm .= ($p1==""? "": " and YEAR(tglbayar) = " . substr($p2,0,4) . " AND MONTH(tglbayar) <= " . substr($p2,-2));		
		$parm .= ($b0==""? "": " and g.id = '$b0'");  //
		$parm .= ($p0==""? "": " and pelaksana = '$p0'"); //
		$parm .= ($s0==""? "": " and skk = '$s0'"); // 
		$parm .= ($k0==""? "": " and k.nomorkontrak = '$k0'"); //
		$parm .= ($o==""? "": " and skkoi = '$o'"); //
	//echo $parm;
	
	echo "
		<strong>LAPORAN REALISASI BAYAR</strong><br>
		<strong>Periode	: $p1  -  $p2</strong><br>
		<strong>Bidang : $b</strong><br>
		<strong>Pelaksana : $p</strong><br>
		<strong>No SKK : $s0</strong><br>
		<strong>No Kontrak : $k0</strong><br>
		Posisi Tanggal : " . date("d-m-Y  H:m:i") . " WIB";
	
	if($v!="") {
		if(isset($_REQUEST["e"])) {
			$sql = (
				$_REQUEST["e"]==1? 
				"update realisasibayar set nilaibayar = '$_REQUEST[r]', tglbayar = '$_REQUEST[d]' where bayarid = '$_REQUEST[n]'": 
				"delete from realisasibayar where bayarid = '$_REQUEST[n]'"
			);
			mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		}
		
		$sql = "
			SELECT 
				n.skkoi skkoi, nipuser, g.id iduser, pelaksana, b.namaunit, skk, pos1, namapos, k.nomorkontrak, 
				k.uraian uraiank, k.tglawal tglmulai, k.tglakhir tglakhir, k.nilaikontrak nilaikontrak, k.vendor vendor, 
				k.nodokumen nodokumen, nilaibayar, tglbayar, bayarid, aset.jtmaset jtma, aset.jtraset jtra, aset.gdaset gda, 
				o.nomorprk prk, o.nomorscore score, o.nilaitunai nilaiskk, o.tglskk tanggalterbit, o.posinduk posinduk, 
				unit.namaunit nmunit, r.pmn pmn, r.nodokrep, r.keterangan
			FROM (
				SELECT nomorskko skk, '' AS nomorscore, nilaitunai, tanggalskko as tglskk, posinduk, '' AS nomorprk, nip FROM skkoterbit 
				UNION 
				SELECT nomorskki skk, nomorscore, nilaitunai, tanggalskki as tglskk, posinduk, nomorprk, nip FROM skkiterbit
			) o
			 
			LEFT JOIN notadinas_detail d ON o.skk = d.noskk 
			LEFT JOIN user u ON o.nip = u.nip
			LEFT JOIN unit unit ON u.kdunit = unit.kdunit
			LEFT JOIN notadinas n ON d.nomornota = n.nomornota 
			LEFT JOIN bidang g ON n.nipuser = g.nick 
			LEFT JOIN bidang b ON d.pelaksana = b.id 
			LEFT JOIN (
				SELECT kdindukpos pos, namaindukpos namapos FROM posinduk UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk2 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk3 UNION
				SELECT kdsubpos pos, namasubpos namapos FROM posinduk4
			) p ON d.pos1 = p.pos
			LEFT JOIN kontrak k ON d.noskk = k.nomorskkoi and d.pos1 = k.pos
			LEFT JOIN realisasibayar r ON k.nomorkontrak = r.nokontrak
			LEFT JOIN asetpdp aset ON r.nokontrak = aset.nomorkontrak
			WHERE NOT tglbayar IS NULL
			$parm
			ORDER BY skkoi, skk, pos1, pelaksana, k.nomorkontrak, tglbayar";
		//echo $sql;
		
		$no = 0;
		$parm = "";
		$dummy = "";
		$total = 0;
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
		while ($row = mysqli_fetch_array($result)) {
			$no++;
			$parm .= "
				<tr>
					<td>$no</td>
					<td>" . ($dummy==$row["skk"]? "": $row["prk"]) . "</td>
					<td>" . (empty($row["score"])? $row["namapos"]: $row["score"]) . "</td>
					<td>" . ($dummy==$row["skk"]? "": $row["skk"]) . "</td>
					<td>" . ($dummy==$row["skk"]? "": number_format($row["nilaiskk"])) . "</td>
					<td>" . ($dummy==$row["skk"]? "": $row['tanggalterbit']) . "</td>
					<td>" . ($dummy==$row["skk"]? "": $row['nmunit']) . "</td>
					<td>" . ($dummy==$row["skk"]? "": ($row['posinduk']=="62.1"?"Lanjutan":"Murni")) . "</td>
					<td>$row[nomorkontrak]</td>
					<td>$row[uraiank]</td>
					<td>$row[tglmulai]</td>
					<td>$row[tglakhir]</td>
					<td>".number_format($row['nilaikontrak'])."</td>
					<td>$row[tglbayar]</td>
					<td align='right'>" . number_format($row["nilaibayar"],0) . "</td>
				</tr>";
				
			$dummy = $row["skk"];
			$total += $row["nilaibayar"];
		}
		mysqli_free_result($result);
		
		echo "
			<table>
					<tr>
						<th rowspan='2'>No</th>
						<th colspan='2'>Program Rencana Kerja</th>
						<th rowspan='2'>No SKK</th>
						<th rowspan='2'>Nilai SKK</th>
						<th rowspan='2'>Tanggal Terbit</th>
						<th rowspan='2'>Unit/Pelaksana</th>
						<th rowspan='2'>Jenis</th>
						<th colspan='5'>Kontrak</th>
						<th colspan='2'>Realisasi Bayar</th>". ($editor? "<th colspan='2' rowspan='2'>Action</th>": "") ."
					</tr>
					<tr>
						<th>No. PRK</th>
						<th>Sasaran/Basket</th>
						<th>Nomor</th>
						<th>Uraian Kegiatan</th>
						<th>Tgl. Mulai</th>
						<th>Tgl. Akhir</th>
						<th>Nilai Kontrak</th>
						<th>Tanggal</th>
						<th>Nilai</th>
					</tr>
					$parm
					<tr>
						<td colspan='14'>Total</td>
						<td align='right'>" . number_format($total) ."</td>". ($editor? "<td colspan='2'></td>": "") ."
					</tr>
			</table>";
	}
	$mysqli->close();($kon);
?>