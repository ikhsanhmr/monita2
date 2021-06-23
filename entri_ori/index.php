<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<script type="text/javascript">
		function onlyme() {
			var u = document.getElementById("u").value;
			var o = document.getElementById("o").value;
			var s = document.getElementById("s").value;
			var j = document.getElementById("j").value;
			var p = document.getElementById("p").value;
			
			var parm = (u==""? "": "u=" + u);
			parm = (o==""? parm: ((parm==""? "": parm + "&") + "o=" + o));
			parm = (s==""? parm: ((parm==""? "": parm + "&") + "s=" + s));
			parm = (j==""? parm: ((parm==""? "": parm + "&") + "j=" + j));
			parm = (p==""? parm: ((parm==""? "": parm + "&") + "p=" + p));
			parm = encodeURI("index.php" + (parm==""? "": "?" + parm));
			
			//alert(parm);
			window.open(parm, "_self");
		}
	</script>
</head>

<body>
<?php
	session_start();
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
	$o = (isset($_REQUEST["o"])? $_REQUEST["o"]: "");
	$s = (isset($_REQUEST["s"])? $_REQUEST["s"]: "");
	$j = (isset($_REQUEST["j"])? $_REQUEST["j"]: "");
	$p = (isset($_REQUEST["p"])? $_REQUEST["p"]: "");
	
	$parm = ($u==""? "": "nama='$u'");
	$parm = ($o==""? $parm: (($parm==""? "": $parm . " and ") . " pelaksana = '$o'"));
	$parm = ($s==""? $parm: (($parm==""? "": $parm . " and ") . " progress = '$s'"));
	$parm = ($j==""? $parm: (($parm==""? "": $parm . " and ") . " jenis = '$j'"));
	$parm = ($p==""? $parm: (($parm==""? "": $parm . " and ") . " pos = '$p'"));

	$parm = ($parm==""? "": " where $parm");

	$sql = "select * from bidang";
	$result = mysql_query($sql);
	$usr = "<select name='u' id='u' onchange='onlyme()'><option value=''></option>";
	$bid = "<select name='o' id='o' onchange='onlyme()'><option value=''></option>";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  
		$usr .= "<option value='$row[namaunit]' " . ($row["namaunit"]==$u? "selected": "") . ">$row[namaunit]</option>";
		$bid .= "<option value='$row[id]' " . ($row["id"]==$o? "selected": "") . ">$row[namaunit]</option>";
	}
	$usr .= "</select>";
	$bid .= "</select>";
	mysql_free_result($result);

	$sql = "select * from progress";
	$result = mysql_query($sql);
	$status = "<select name='s' id='s' onchange='onlyme()'><option value=''></option>";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  
		$status .= "<option value='$row[pid]' " . ($row["pid"]==$s? "selected": "") . ">$row[info]</option>";
	}
	$status .= "</select>";
	mysql_free_result($result);
	
	$jenis = "<select name='j' id='j' onchange='onlyme()'><option value=''></option>";
	$jenis .= "<option value='SKKI' " . ("SKKI"==$j? "selected": "") . ">SKKI</option>";
	$jenis .= "<option value='SKKO' " . ("SKKO"==$j? "selected": "") . ">SKKO</option>";
	$jenis .= "</select>";

	$sql = "SELECT DISTINCT akses FROM v_pos ORDER BY akses";
	$result = mysql_query($sql);
	$pos = "<select name='p' id='p' onchange='onlyme()'><option value=''></option>";
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  
		$pos .= "<option value='$row[akses]' " . ($row["akses"]==$p? "selected": "") . ">$row[akses]</option>";
	}
	$pos .= "</select>";
	mysql_free_result($result);

	
	$sql = "
SELECT * FROM (
	SELECT * FROM (
		SELECT nipuser, u.nama, v.nama nsub, pelaksana, namaunit, n.nomornota, tanggal tglnota, perihal, skkoi jenis, pos1 pos, CASE WHEN d.progress IS NULL THEN n.progress ELSE d.progress END progress, noskk nosk 
		FROM notadinas n 
		LEFT JOIN USER u ON n.nipuser = u.nip
		LEFT JOIN notadinas_detail d ON n.nomornota = d.nomornota LEFT JOIN bidang b ON d.pelaksana = b.id
		LEFT JOIN (SELECT DISTINCT akses, nama FROM v_pos) v ON d.pos1 = v.akses
		WHERE YEAR(tanggal) = " . date("Y") . "
	) nd LEFT JOIN progress p ON nd.progress = pid
) ndbp 
LEFT JOIN (
	SELECT * FROM (
		SELECT nomorskko noskk, tanggalskko tglskk, nilaianggaran, nilaidisburse, uraian uraians FROM skkoterbit 
		UNION 
		SELECT nomorskki noskk, tanggalskki tglskk, nilaianggaran, nilaidisburse, uraian uraians FROM skkiterbit 
	) s  LEFT JOIN (
		SELECT nomorskkoi noskkk, nomorkontrak, pos posk, uraian uraiank, tglawal, tglakhir, nilaikontrak FROM kontrak
	) k ON s.noskk = k.noskkk ORDER BY noskk, nomorkontrak 
) sk ON ndbp.nosk = sk.noskk AND ndbp.pos = sk.posk";

$sql1 = ((($adm=="1") || ($adm=="2") || ($adm=="3") || ($nama=="GM"))? "": " where (nipuser='$nip' or pelaksana='$nama' or pelaksana='$org')");
$sql .= $sql1;


if($parm!="") {
	$sql = "select * from (" . $sql . ") vall $parm";
}

$sql .= " ORDER BY noskk, nomorkontrak";
	
	//echo "$sql";
	echo "
		<h2>Monitor SKKI/O</h2>
		<table border='1'>
			<tr>
				<th>No</th>
				<th>User<br>$usr</th>
				<th>Pelaksana<br>$bid</th>
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
	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["nama"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["namaunit"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["info"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["nomornota"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["tglnota"]) . "</td>
				<td>" . ($dnota==$row["nomornota"]? "": $row["jenis"]) . "</td>
				<td>" . ($dskk==$row["noskk"] && $dskk!=""? "": $row["pos"]) . "</td>
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
	mysql_free_result($result);
	mysql_close($link);	  
?>
</body>
</html>