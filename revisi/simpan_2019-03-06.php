<?php
	session_start(); 
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	$nama=$_SESSION['nama'];
	$adm=$_SESSION['adm'];
	if($nip=="") {exit;}

	require_once '../config/koneksi.php';
/*	
	$n = $_REQUEST["n"];
	$t = $_REQUEST["t"];
	$t = (substr($t,2,1)=="/"? substr($t,-4)."/".substr($t,0,2)."/".substr($t,3,2): $t);
	$k = $_REQUEST["k"];
	
	$sql = "INSERT INTO realisasibayar(nokontrak, nilaibayar, tglbayar) VALUES('$k', '$n', '$t')";	
	//echo $sql;
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	$mysqli->close();($link);	  							
	echo $sukses;


noskk - 001/521/SKK.O/GM.WSU/2015-R
jenis - SKKO
tglskk - 2015-10-29
uraian - Biaya Administrasi Niaga Rutin 2015 (Tusbung 1 Th, cater Smt I) dan Hutang Biaya 2014 (revisi SKKO terbit Tgl 15 Januari 2015 dan REVISI I Tgl 25 Maret 2015 ) REVISI II Tgl 31-08-2015- Revisi III
nom - 6212240
nowbs - 
wbs - 14122069003
anggaran - 15385617337
disburse - 14122069003
tunai - 14122069003
nontunai - 1263548334
pos1 - 54.1.01
nilai1 - 222654150
pos2 - 54.1.02
nilai2 - 11584485390
pos3 - 54.1.06
nilai3 - 2314929463
*/
	$skk = $_POST["noskk"];
	$jenis = $_POST["jenis"];
	$tgl = $_POST["tglskk"];
	$tgl = (substr($tgl,2,1)=="/"? substr($tgl,-4)."/".substr($tgl,0,2)."/".substr($tgl,3,2): $tgl);
	$uraian = $_POST["uraian"];
	$nom = $_POST["nom"];
	$nowbs = $_POST["nowbs"];
	$wbs = $_POST["wbs"];
	$ang = $_POST["anggaran"];
	$dis = $_POST["disburse"];
	$tun = $_POST["tunai"];
	$non = $_POST["nontunai"];

	$sql = "
		update skk" . ($jenis=="SKKO"? "o": "i") . "terbit set 
			tanggalskk" . ($jenis=="SKKO"? "o": "i") . " = '$tgl',
			uraian = '$uraian',
			nomor". ($jenis=="SKKO"? "costcenter": "prk") . " = '$nom',
			nomorwbs = '$nowbs',
			nilaiwbs = '$wbs',
			nilaianggaran = '$ang',
			nilaidisburse = '$dis',
			nilaitunai = '$tun',
			nilainontunai = '$non'
		where nomorskk" . ($jenis=="SKKO"? "o": "i") . " = '$skk'";
	//echo "$sql<br>";
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	

	$n = "";
	$d = "";
	foreach($_POST as $name=>$value) {
		switch(substr($name,0,3)) {
			case "pos": $p = $value; break;
			case "nil": 
				$n = $value;
				if($n!="" && $p!="") {
					$sql = "update notadinas_detail set nilai1 = '$n' where noskk = '$skk' and pos1 = '$p'";
					//echo "$sql<br>";
					mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
					
					$n = "";
					$d = "";
				}
		}
		//echo "$name - $value<br>";
	}
	$mysqli->close();($kon);
	echo "<script>
		var url = encodeURI('index.php?k=$skk&v=');
		window.open(url, '_self')
	</script>";
?>