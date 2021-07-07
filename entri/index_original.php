<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<script type="text/javascript">
		function filtermonita() {
			var u = document.getElementById("usr").value;
			var p = document.getElementById("bidang").value;
			var s = document.getElementById("status").value;
			var j = document.getElementById("jenis").value;
			var o = document.getElementById("pos").value;
			var parm = "index.php?" + "u=" + u + "&p=" + p + "&s=" + s + "&j=" + j + "&o=" + o;
			window.open(parm, "_self");
		}
	</script>
</head>

<body>
<?php
	error_reporting(0);  session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];
	
	$u = (isset($_REQUEST["u"])? $_REQUEST["u"]: "");
	$p = (isset($_REQUEST["p"])? $_REQUEST["p"]: "");
	$s = (isset($_REQUEST["s"])? $_REQUEST["s"]: "");
	$j = (isset($_REQUEST["j"])? $_REQUEST["j"]: "");
	$o = (isset($_REQUEST["o"])? $_REQUEST["o"]: "");
	
//	echo "u : $u - p : $p - s : $s - j : $j - o : $o";
	
	$sql = "select * from bidang";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$user = "<select name='usr' id='usr' onchange='filtermonita()'><option value=''></option>";
	$bidang = "<select name='bidang' id='bidang' onchange='filtermonita()'><option value=''></option>";
	while ($row = mysqli_fetch_array($result)) {  
		$user .= "<option value='$row[id]' " . ($row["id"]==$u? "selected": "") . ">$row[namaunit]</option>";
		$bidang .= "<option value='$row[id]' " . ($row["id"]==$p? "selected": "") . ">$row[namaunit]</option>";
	}
	$user .= "</select>";
	$bidang .= "</select>";
	mysqli_free_result($result);
	
	$sql = "select * from progress";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$status = "<select name='status' id='status' onchange='filtermonita()'><option value=''></option>";
	while ($row = mysqli_fetch_array($result)) {  
		$status .= "<option value='$row[pid]' " . ($row["pid"]==$s? "selected": "") . ">$row[info]</option>";
	}
	$status .= "</select>";
	mysqli_free_result($result);
	
	$jenis = "<select name='jenis' id='jenis' onchange='filtermonita()'><option value=''></option>";
	$jenis .= "<option value='SKKI' " . ("SKKI"==$j? "selected": "") . ">SKKI</option>";
	$jenis .= "<option value='SKKO' " . ("SKKO"==$j? "selected": "") . ">SKKO</option>";
	$jenis .= "</select>";
	
	$sql = "SELECT DISTINCT akses FROM v_pos ORDER BY akses";
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$pos = "<select name='pos' id='pos' onchange='filtermonita()'><option value=''></option>";
	while ($row = mysqli_fetch_array($result)) {  
		$pos .= "<option value='$row[akses]' " . ($row["akses"]==$o? "selected": "") . ">$row[akses]</option>";
	}
	$pos .= "</select>";
	mysqli_free_result($result);
	
	
/*
	$sql = "
	select * from (
		SELECT 
			nipuser, nama usr, pelaksana pelaksanaid, namaunit pelaksana, d.progress progressid, info, keterangan, n.nomornota, 
			n.tanggal tnd, skkoi skk, pos1, nilai1, perihal, nilaiusulan, noskk, nomorskki, nilaianggaran, nilaidisburse, 
			tanggalskki tglskk, nomorkontrak, tglawal, tglakhir
		FROM notadinas n 
		LEFT JOIN user u on n.nipuser = u.nip
		LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
		LEFT JOIN skkiterbit i ON d.noskk = i.nomorskki
		LEFT JOIN kontrak k ON i.nomorskki = k.nomorskkoi
		LEFT JOIN bidang b ON d.pelaksana = b.id
		LEFT JOIN progress p ON d.progress = p.pid
	) monita LEFT JOIN bidang b 
	ON monita.usr = b.namaunit";
*/
	$sql = "
	select * from (
		SELECT 
			nipuser, u.nama usr, pelaksana pelaksanaid, namaunit pelaksana, d.progress progressid, info, keterangan, n.nomornota, 
			n.tanggal tnd, skkoi skk, pos1, v.nama nsub, nilai1, perihal, nilaiusulan, noskk, nomorskk, nilaianggaran, nilaidisburse, 
			tglskk, nomorkontrak, nilaikontrak, tglawal, tglakhir
		FROM notadinas n 
		LEFT JOIN user u on n.nipuser = u.nip
		LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota
		LEFT JOIN (SELECT nomorskko nomorskk, tanggalskko tglskk, nilaianggaran, nilaidisburse FROM skkoterbit UNION SELECT nomorskki nomorskk, tanggalskki tglskk, nilaianggaran, nilaidisburse FROM skkiterbit) i ON d.noskk = i.nomorskk
		LEFT JOIN kontrak k ON i.nomorskk = k.nomorskkoi
		LEFT JOIN bidang b ON d.pelaksana = b.id
		LEFT JOIN progress p ON d.progress = p.pid
		LEFT JOIN v_pos v ON d.pos1 = v.akses
	) monita LEFT JOIN bidang b 
	ON monita.usr = b.namaunit";

	//echo "sql : $sql<br>";
	
	
	$parm = "";
	$parm .= ($u==""? "": "id='$u'");
	$parm .= ($p==""? "": ($parm==""? "": " and ") . "pelaksanaid = '$p'");
	$parm .= ($s==""? "": ($parm==""? "": " and ") . "progressid= $s");
	$parm .= ($j==""? "": ($parm==""? "": " and ") . "skk = '$j'");
	$parm .= ($o==""? "": ($parm==""? "": " and ") . "pos1 = '$o'");
	//echo "parm : $parm<br>";

	$sql1 = "";
	$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or pelaksanaid='$org')");
	
	$sql1 .= ($sql1==""? ($parm==""? "": " where $parm"): ($parm==""? "": " and $parm"));
	$sql .= $sql1;
	$sql .= " ORDER BY nomornota, noskk, pos1";
//	echo $sql;

	echo "
		<h2>Monitor SKKI/O</h2>
		<table border='1'>
			<tr>
				<th>No</th>
				<th>User<br>$user</th>
				<th>Pelaksana<br>$bidang</th>
				<th>Status<br>$status</th>
				<th>Nota Dinas</th>
				<th>Tgl ND</th>
				<th>Jenis<br>$jenis</th>
				<th colspan='2'>POS<br>$pos</th>
				<th>Perihal</th>
				<th>No SKK</th>
				<th>SKKO/I Terbit</th>
				<th>Anggaran</th>
				<th>Disburse</th>
				<th>No Kontrak</th>
				<th>Nilai Kontrak</th>				
				<th>Tgl Awal</th>
				<th>Tgl Akhir</th>
			</tr>";


	$no = 0;
	$dnota = "";
	$dskk = "";
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["usr"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row[pelaksana]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["info"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["nomornota"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["tnd"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["skk"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["pos1"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["nsub"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["perihal"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["noskk"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["tglskk"]) . "</td>
				<td>". ($dskk==$row["noskk"] && $dskk!=""? "": "Rp." . number_format($row["nilaianggaran"]) . ",-") . "</td>
				<td>". ($dskk==$row["noskk"] && $dskk!=""? "": "Rp." . number_format($row["nilaidisburse"]) . ",-") . "</td>
				<td>$row[nomorkontrak]</td>
				<td>Rp.".number_format($row["nilaikontrak"]).",-</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
			</tr>
		";
		
		$dnota = $row["nomornota"];
		$dskk = $row["noskk"];
	}
	echo "</table>";
	mysqli_free_result($result);
	$mysqli->close();($link);	  
?>
</body>
</html>