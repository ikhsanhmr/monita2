<?php
	error_reporting(0);  session_start(); 
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
*/
	$url = $_POST["url"];
	foreach($_POST as $name=>$value) {
		switch(substr($name,0,1)) {
			case "k": $k = $value; break;
			case "d": $d = (substr($value,2,1)=="/"? substr($value,-4)."/".substr($value,0,2)."/".substr($value,3,2): $value); break;
			case "n": 
				$n = $value;
				if($n!="" && $d!="") {
					$sql = "INSERT INTO realisasibayar(nokontrak, nilaibayar, tglbayar) VALUES('$k', '$n', '$d')";	
					//echo $sql;
					mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
				}
		}
		//echo "$name - $value<br>";
	}
	$mysqli->close();($kon);
	echo "<script>
		var url = encodeURI('$url');
		window.open(url, '_self')
	</script>";
?>