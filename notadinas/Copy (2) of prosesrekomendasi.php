<?php
	require_once '../config/koneksi.php';
    
	$nonotadinas= trim($_POST['nonotadinas']);
	$sql = "select * from notadinas where nomornota = '$nonotadinas'";
    $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
    $cek_notadinas = mysql_num_rows($hasil);
    mysqli_free_result($hasil);
    
    if($cek_notadinas==0) {
		$nip=$_POST['nip'];
		$tgl_nota=$_POST['tgl_nota'];
		$jenis= trim($_POST['jenis']);
		$perihal= trim($_POST['perihal']);
		$nilairekom = 0;
    
		foreach($_REQUEST as $param_name => $param_val) {
//			echo "$param_name - $param_val<br>";
			
			if(substr($param_name, 0 ,3)=="pic") {$pic = $param_val;}
			if(substr($param_name, 0 ,3)=="pos") {$pos = $param_val;}
			if(substr($param_name, 0 ,5)=="nilai" && $$param_name!="nilairekom") {
				$nilai = $param_val;
				if($nilai!=="") {
					$sql = "INSERT INTO notadinas_detail(nomornota, pelaksana, pos1, nilai1)
						VALUES('$nonotadinas', '$pic', '$pos', '$nilai')";
					$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
					if($sukses) $nilairekom += $nilai;
//					mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
//					echo "$sql<br>";
				} 
			}
		}
			
        $sql = "INSERT INTO notadinas(nomornota, tanggal, perihal, skkoi, nilaiusulan, nipuser) VALUES
			('$nonotadinas', '$tgl_nota', '$perihal', '$jenis', '$nilairekom', '$nip')";
//		echo "$sql<br>";
		mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
//		mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die(mysql_error());
		
		$mysqli->close();($kon);
    } else { echo "<script>alert('No Nota Dinas Sudah Ada!')</script>"; }

	echo "
		<script type='text/javascript'>
			window.open('tambahrekomendasiskk.php', '_self');
		</script>";
?>