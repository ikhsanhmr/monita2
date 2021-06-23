<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link href="../css/screen.css" rel="stylesheet" type="text/css">
	<title>Untitled Document</title>

	<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}
	?>
	
	<script type="text/javascript">
		function edit(k, p) {
			var url = "tindex.php";
			var parm= encodeURI("k=" + k.trim() + "&p=" + p);
			window.open(url+"?"+parm, "_self");
		}
	
		function hapus(k, p) {
			if(confirm("Hapus Data?")) {
				var url = "hapus.php";
				var parm= encodeURI("k=" + k.trim() + "&p=" + p);
				//alert(url + " -  " + parm);
	
				var xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp=new XMLHttpRequest();
				} else {
					xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				}
				
				xmlhttp.onreadystatechange=function() {
					if (xmlhttp.readyState==4 && xmlhttp.status==200) {
						if(xmlhttp.responseText=="1") {
							window.open(".", "_self");
						}
						//document.getElementById("showhere").innerHTML=xmlhttp.responseText;
					}
				}
				
				xmlhttp.open("POST", url, true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				xmlhttp.send(parm);	
			}
		}
	</script>	
</head>

<body>
<?php
    session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	$org=$_SESSION['org'];
/*
	$kon = (isset($_REQUEST["kon"])? $_REQUEST["kon"]: "");
	if($kon!="") {
		$sql = "delete from asetpdp where nomorkontrak='$kon'";
		$result = mysql_query($sql);
//		echo "$sql<br>";		
	}
*/
	$sql = "SELECT a.*, nilaikontrak, uraian, tglawal, tglakhir, pos FROM asetpdp a LEFT JOIN (
		SELECT noskk, pelaksana, nomorkontrak, uraian, vendor, tglawal, tglakhir, nilaikontrak, pos FROM notadinas_detail n 
		LEFT JOIN kontrak k ON n.noskk = k.nomorskkoi AND k.pos = n.pos1 
		WHERE progress = 9 " . ($adm==""? " AND pelaksana = '$org'": "") . "
	) nk ON a.nomorkontrak = nk.nomorkontrak";

//	echo "$sql";

	echo "
		<h2>ASET PDP</h2>
		<a href='tindex.php'>(+) Tambah Aset</a>
		<table border='1'>
			<tr>
				<th>No</th>
				<th>No Kontrak</th>
				<th>Uraian</th>
				<th>Tgl Awal</th>
				<th>Tgl Akhir</th>
				<th>Nilai Kontrak</th>
				<th>Proses</th>
			</tr>";
			
	$dummynota = "";
	$dummyskk = "";
	$dummypos = "";
	
	$result = mysql_query($sql);
	while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  
		$no++;
		echo "
			<tr>
				<td>$no</td>
				<td>$row[nomorkontrak]</td>
				<td>$row[uraian]</td>
				<td>$row[tglawal]</td>
				<td>$row[tglakhir]</td>
				<td>". number_format($row["nilaikontrak"],0) . "</td>
				<td>" . (
					"<a href='#' onclick='edit(\"$row[nomorkontrak]\", \"$row[pdpid]\")'>Edit</a>
					<a href='#' onclick='hapus(\"$row[nomorkontrak]\", \"$row[pdpid]\")'>Hapus</a>") . 
				"</td>
			</tr>";
			
		$dummynota = ($dummynota==""? $row["nomornota"]: ($dummynota==$row["nomornota"]? $dummynota: $row["nomornota"]));
		$dummyskk = ($dummyskk==""? $row["noskk"]: ($dummyskk==$row["noskk"]? $dummyskk: $row["noskk"]));
		$dummypos = ($dummypos==""? $row["pos1"]: ($dummypos==$row["pos1"]? $dummypos: $row["pos1"]));
	}
	echo "</table>";
	mysql_free_result($result);
	mysql_close($link);	

?>
	<div id="showhere"></div>
</body>
</html>