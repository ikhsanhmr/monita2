<?php
	require_once '../config/koneksi.php';
    
	$nonotadinas= trim($_POST['nonotadinas']);
	$sql = "select * from notadinas where nomornota = '$nonotadinas'";
    $hasil=mysql_query($sql);
    $cek_notadinas = mysql_num_rows($hasil);
    mysql_free_result($hasil);
    
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
					$nilairekom += $nilai;
					$sql = "INSERT INTO notadinas_detail(nomornota, pelaksana, pos1, nilai1)
						VALUES('$nonotadinas', '$pic', '$pos', '$nilai')";
					mysql_query($sql) or die(mysql_error());
				} 
			}
		}
			


        $sql = "INSERT INTO notadinas(nomornota, tanggal, perihal, skkoi, nilaiusulan, nipuser) VALUES
			('$nonotadinas', '$tgl_nota', '$perihal', '$jenis', '$nilairekom', '$nip')";
		//echo $sql;
		mysql_query($sql) or die(mysql_error());
		
		mysql_close($kon);
    } else { echo "<script>alert('No Nota Dinas Sudah Ada!')</script>"; }
    
	echo "
		<script type='text/javascript'>
			window.location.href='javascript:history.back(0)';
		</script>";
?>