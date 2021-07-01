<?php 
    require_once '../config/koneksi.php';
    
    session_start(); 
	$nip=$_SESSION['nip'];
	$noskko=trim($_POST['noskko']);

	$sql = "select * from skkoterbit where nomorskko = '$noskko'";
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	$num_rows = mysqli_num_rows($sukses);
	if($num_rows==0) {
		$tgl_skko=trim($_POST['tgl_skko']);
		$uraian=trim($_POST['uraian']);
		$periode=trim($_POST['periode']);
		$jenis=trim($_POST['jenis']);
		$nocostcenter=trim($_POST['nocostcenter']);
		$nowbs=trim($_POST['nowbs']);
		$wbs=trim(str_replace(',','',$_POST['wbs']));
		$tunai=trim(str_replace(',','',$_POST['tunai']));
		$nontunai=trim(str_replace(',','',$_POST['nontunai']));
		$anggaran=trim(str_replace(',','',$_POST['anggaran']));
		$disburse=trim(str_replace(',','',$_POST['disburse']));  
		$pos0 = trim($_POST['pos0']);
		
		$sql = "INSERT INTO skkoterbit (nomorskko, posinduk, tanggalskko, uraian, periode, jenis, nomorcostcenter, nomorwbs, nilaiwbs, nilaitunai, nilainontunai, nilaianggaran, nilaidisburse, nip) VALUES ('$noskko', '$pos0', '$tgl_skko', '$uraian', '$periode', '$jenis', '$nocostcenter', '$nowbs', '$wbs', '$tunai', '$nontunai', '$anggaran', '$disburse', '$nip')";
//		echo "$sql<br>";
		$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
		//echo "$sukses<br>";
	
		echo "<br>";
		foreach($_REQUEST as $param_name => $param_val) {
			//echo "$param_name - $param_val<br>";
			if(substr($param_name, 0 ,3)=="not") {$not = $param_val;}
			if(substr($param_name, 0 ,3)=="pic") {$pic = $param_val;}
			if(substr($param_name, 0 ,3)=="pos") {$pos = $param_val;}
			if(substr($param_name, 0 ,3)=="nil") {
				$nil = $param_val;
				$sql = "update notadinas_detail set noskk = '$noskko', progress = 7 where nid = '$pic'";
//				echo "$sql<br>";
				$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	//			echo "$sql  $sukses<br>";
			}
		}
		
		echo '
		<script>
			alert("SKKO '.$noskko.' Berhasil Ditambah!\r\n");
			window.open("index.php", "_self");
		</script>'; 
	} else {
		echo "<script>
				alert('Gagal membuat SKKO. SKKO $noskko sudah ada!');
				window.open('index.php', '_self');
			</script>";
	}

?>