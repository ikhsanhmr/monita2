<?php 
    require_once '../config/koneksi.php';
    
    session_start(); 
	$nip=$_SESSION['nip'];
	$noskki=trim($_POST['noskko']);

	$sql = "select * from skkiterbit where nomorskki = '$noskki'";
	//echo "$sql<br>";
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
	$num_rows = mysqli_num_rows($sukses);

	if($num_rows==0) {
		$noprk = trim($_POST['noprk']);
		$basket = trim($_POST['basket']);
		$nota0 = trim($_POST['nota0']);
		$pic0 = trim($_POST['pic0']);
		$pos0 = trim($_POST['pos0']);
		$nilai0 = trim(str_replace(',','',$_POST['nilai0']));
		$noskki = trim($_POST['noskko']);
		$uraian = trim($_POST['uraian']);
		$tgl_skki = trim($_POST['tgl_skko']);
//		$aset = trim($_POST['aset']);
		$tunai = trim(str_replace(',','',$_POST['tunai']));
		$nontunai = trim(str_replace(',','',$_POST['nontunai']));
		$anggaran = trim(str_replace(',','',$_POST['anggaran']));
		$disburse = trim(str_replace(',','',$_POST['disburse']));
		$nowbs = trim($_POST['nowbs']);
		$wbs = trim(str_replace(',','',$_POST['wbs']));

				
		$jtm=trim($_POST['jtm']);
		$jtma=trim(str_replace(',','',$_POST['jtma']));
		$jtmd=trim(str_replace(',','',$_POST['jtmd']));  
		$gd=trim($_POST['gd']);
		$gda=trim(str_replace(',','',$_POST['gda']));
		$gdd=trim(str_replace(',','',$_POST['gdd']));  
		$jtr=trim($_POST['jtr']);
		$jtra=trim(str_replace(',','',$_POST['jtra']));
		$jtrd=trim(str_replace(',','',$_POST['jtrd']));  
		$sl1=trim($_POST['sl1']);
		$sl1a=trim(str_replace(',','',$_POST['sl1a']));
		$sl1d=trim(str_replace(',','',$_POST['sl1d']));  		
		$sl3=trim($_POST['sl3']);
		$sl3a=trim(str_replace(',','',$_POST['sl3a']));
		$sl3d=trim(str_replace(',','',$_POST['sl3d']));  
		
		// nomornota, unit, 
		// '$nota0', '$pic0', 
		$sql = "
			INSERT INTO skkiterbit(
				nomorprk, nomorscore, posinduk, nomorskki, uraian, tanggalskki, 
				nilaitunai, nilainontunai, nilaianggaran, nilaidisburse, nomorwbs, nilaiwbs, 
				jtm, nilaianggaranjtm, nilaidisbursejtm, gd, nilaianggarangd, nilaidisbursegd, 
				jtr, nilaianggaranjtr, nilaidisbursejtr, sl1, nilaianggaransl1, 
				nilaidisbursesl1, sl3, nilaianggaransl3, nilaidisbursesl3, nip)
			VALUES (
				'$noprk', '$basket', '$pos0', '$noskki', '$uraian', '$tgl_skki', 
				'$tunai', '$nontunai', '$anggaran', '$disburse', '$nowbs', '$wbs', 
				'$jtm', '$jtma', '$jtmd', '$gd', '$gda', '$gdd', 
				'$jtr', '$jtra', '$jtrd', '$sl1', '$sl1a', '$sl1d', 
				'$sl3', '$sl3a', '$sl3d', '$nip')";
		//echo "$sql<br>";
		$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
		//echo "$sukses<br>";
	
		//echo "<br>";
		foreach($_REQUEST as $param_name => $param_val) {
			//echo "$param_name - $param_val<br>";
			if(substr($param_name, 0 ,3)=="not") {$not = $param_val;}
			if(substr($param_name, 0 ,3)=="pic") {$pic = $param_val;}
			if(substr($param_name, 0 ,3)=="pos") {$pos = $param_val;}
			if(substr($param_name, 0 ,3)=="nil") {
				$nil = $param_val;
				$sql = "update notadinas_detail set noskk = '$noskki', progress = 7 where nid = '$pic'";
				//echo "$sql<br>";
				$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysqli_error());
				//echo "$sukses<br>";
			}
		}
		
		echo '
		<script>
			alert("SKKI '.$noskki.' Telah disimpan!\r\n");
			window.open("index.php", "_self");
		</script>'; 
	} else {
		echo "<script>
				alert('Gagal membuat SKKI. SKKI $noskki sudah ada!');
				window.open('index.php', '_self');
			</script>";
	}
?>