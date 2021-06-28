<?php
	require_once '../config/koneksi.php';
	$nonotadinas= trim($_POST['nonotadinas']);
	
	$sql = "INSERT INTO notadinas_detail_history SELECT * FROM notadinas_detail WHERE nomornota = '$nonotadinas'";
//	echo $sql;
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	$sql = "INSERT INTO notadinas_history SELECT * FROM notadinas WHERE nomornota = '$nonotadinas'";
//	echo $sql;
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die (mysql_error());
//	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	
	$sql = "delete from notadinas_detail WHERE nomornota = '$nonotadinas'";
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	$sql = "delete FROM notadinas WHERE nomornota = '$nonotadinas'";
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die (mysql_error());
//	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());
	
    $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	$sql = "select * from notadinas where nomornota = '$nonotadinas'";
    $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
	
	$sql = "select * from notadinas where nomornota = '$nonotadinas'";
    $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    $cek_notadinas = mysql_num_rows($hasil);
    mysqli_free_result($hasil);
    
	$nip=$_POST['nip'];
	$tgl_nota=$_POST['tgl_nota'];
	$jenis= trim($_POST['jenis']);
	$perihal= trim($_POST['perihal']);
	$nilairekom = 0;

	foreach($_REQUEST as $param_name => $param_val) {
//			echo "$param_name - $param_val<br>";
		
		if(substr($param_name, 0 ,3)=="pic") {$pic = $param_val;}
		if(substr($param_name, 0 ,3)=="pos") {$pos = $param_val;}
		if(substr($param_name, 0 ,5)=="nilai" && $$param_name!="nilairekom") {$nilai = $param_val;}
		if(substr($param_name, 0 ,4)=="sisa") {
			$sisa = $param_val;
			$sql = "INSERT INTO notadinas_detail(nomornota, pelaksana, pos1, nilai1, sisa1)
				VALUES('$nonotadinas', '$pic', '$pos', '$nilai', $sisa)";
			$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
			if($sukses) $nilairekom += $nilai;
//					mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
//					echo "$sql<br>";
		}
	}
		
	$sql = "INSERT INTO notadinas(nomornota, tanggal, perihal, skkoi, nilaiusulan, nipuser) VALUES
		('$nonotadinas', '$tgl_nota', '$perihal', '$jenis', '$nilairekom', '$nip')";
//	echo $sql;
	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
//	mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
	$mysqli->close();($kon);
    
	echo "
		<script type='text/javascript'>
			window.open('editrekomendasiskk.php', '_self');
		</script>";
?>