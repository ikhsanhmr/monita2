<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<?php
	error_reporting(0);  session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	?>
	
	<script type="text/javascript">
		function hapus(x, y) {
			var r = confirm("Kontrak akan Dihapus?");
			if (r) {
				var url="index.php?kon="+x+"&nid="+y;
				//alert(url);
				window.open(url,"_self");
			}
		}
	</script>
</head>

<body>
<?php
    error_reporting(0);  session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];

	$kon = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
	if($kon!="") {
		$sql = "update notadinas_detail set progress = 7 where (noskk,pos1) = 
			(SELECT nomorskkoi, pos FROM kontrak  WHERE nomorkontrak = '$kon')";
//		echo $sql;
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));		

		$sql = "delete from kontrak where nomorkontrak='$kon'";
		$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
//		echo "$sql<br>";		
	}

/*
	$sql = "SELECT nip, nipuser, namaunit, perihal, skkoi, nilaiusulan, progress, dsk.* 
FROM notadinas n LEFT JOIN (
	SELECT nomornota, pelaksana, pos1, nilai1, sk.* 
	FROM notadinas_detail d LEFT JOIN (
		SELECT 'SKKO' skk, nomorskko noskk, nilaianggaran anggaran, nilaidisburse disburse, nilaitunai, nilainontunai, nilaiwbs, 
			s.uraian uraians, nomorkontrak, k.uraian uraiank, vendor, tglawal, tglakhir, k.pos posk, nilaikontrak nilaik 
		FROM skkoterbit s LEFT JOIN kontrak k ON s.nomorskko = k.nomorskkoi
		UNION 
		SELECT 'SKKI' skk, nomorskki noskk, nilaianggaran anggaran, nilaidisburse disburse, nilaitunai, nilainontunai, nilaiwbs, 
			s.uraian uraians, nomorkontrak, k.uraian uraiank, vendor, tglawal, tglakhir, k.pos posk, nilaikontrak nilaik 
		FROM skkiterbit s LEFT JOIN kontrak k ON s.nomorskki = k.nomorskkoi
	) sk ON d.noskk = sk.noskk AND d.pos1 = sk.posk
) dsk ON n.nomornota = dsk.nomornota 
LEFT JOIN bidang b ON dsk.pelaksana = b.id 
WHERE NOT noskk IS NULL " . ($adm==1? " where nip = '$nip'": ($bidang==2? " where namaunit = '$nama' or nipuser = '$nip'": "")) . 
"ORDER BY nomornota, noskk, nipuser, pelaksana";
*/

	$sql = "SELECT nip, nipuser, namaunit, perihal, skkoi, nilaiusulan, progress, dsk.*, nama
FROM notadinas n LEFT JOIN (
	SELECT nomornota, pelaksana, pos1, nilai1, sk.* 
	FROM notadinas_detail d LEFT JOIN (
		SELECT skk, noskk, anggaran, disburse, nilaitunai, nilainontunai, nilaiwbs, 
			s.uraian uraians, nomorkontrak, k.uraian uraiank, vendor, tglawal, tglakhir, k.pos posk, nilaikontrak nilaik
		 FROM (
			SELECT 'SKKO' skk, nomorskko noskk, nilaianggaran anggaran, nilaidisburse disburse, nilaitunai, nilainontunai, nilaiwbs, uraian FROM skkoterbit
			UNION 
			SELECT 'SKKI' skk, nomorskki noskk, nilaianggaran anggaran, nilaidisburse disburse, nilaitunai, nilainontunai, nilaiwbs, uraian FROM skkiterbit
		) s RIGHT JOIN kontrak k ON s.noskk = k.nomorskkoi 
	) sk ON d.noskk = sk.noskk AND d.pos1 = sk.posk
) dsk ON n.nomornota = dsk.nomornota 
LEFT JOIN bidang b ON dsk.pelaksana = b.id 
LEFT JOIN (SELECT DISTINCT akses, nama FROM v_pos) v ON dsk.pos1 = v.akses
WHERE NOT noskk IS NULL " . ($adm==1? " and nip = '$nip'": ($bidang==2? " and (namaunit = '$nama' or nipuser = '$nip' or pelaksana='$org')": "")) . 
"ORDER BY nomornota, noskk, nipuser, pelaksana, nomorkontrak";

	//echo "$sql";

	echo "
		<h2>Kontrak</h2>
		<a href='kontrak0.php'>(+) Tambah Kontrak</a>
		<table border='1'>
			<tr>
				<th>No</th>
				<th>SKKO/I</th>
				<th>Nota Dinas</th>
				<th>No SKK</th>
				<th>Uraian</th>
				<th colspan='2'>POS</th>
				<th>No Kontrak</th>
				<th>Uraian</th>
				<th>Vendor</th>
				<th>Tgl Awal</th>
				<th>Tgl Akhir</th>
				<th>Nilai Kontrak</th>
				<th>Proses</th>
			</tr>";
			
	$dummynota = "";
	$dummyskk = "";
	$dummypos = "";
	
	$result = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	while ($row = mysqli_fetch_array($result)) {  
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>$row[skk]</td>" . 
				($dummynota==$row["nomornota"]? "<td></td>": "<td>$row[nomornota]</td>") .
				($dummyskk==$row["noskk"]? "<td></td>": "<td>$row[noskk]</td>") .
				($dummyskk==$row["noskk"]? "<td></td>": "<td>$row[uraians]</td>") .
				($dummypos==$row["pos1"]? "<td></td>": "<td>$row[pos1]</td>") ."
				<td>$row[nama]</td>
				<td>$row[nomorkontrak]</td>
				<td>$row[uraiank]</td>
				<td>$row[vendor]</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
				<td>". number_format($row["nilaik"],0) . "</td>
				<td>" . ($row["nomorkontrak"]==""? 
					"<a href='kontrak.php?skk=$row[noskk]&pos=$row[pos1]'>Kontrak</a>": 
					"<a href='kontrak.php?kon=$row[nomorkontrak]'>Edit</a>
					<a href='#' onclick='hapus(\"$row[nomorkontrak]\", \"$row[nid]\")'>Hapus</a>") . 
				"</td>
			</tr>";
			
		$dummynota = ($dummynota==""? $row["nomornota"]: ($dummynota==$row["nomornota"]? $dummynota: $row["nomornota"]));
		$dummyskk = ($dummyskk==""? $row["noskk"]: ($dummyskk==$row["noskk"]? $dummyskk: $row["noskk"]));
		$dummypos = ($dummypos==""? $row["pos1"]: ($dummypos==$row["pos1"]? $dummypos: $row["pos1"]));
	}
	echo "</table>";
	mysqli_free_result($result);
	$mysqli->close();($link);	  
?>
</body>
</html>