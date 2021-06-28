<?php 
    require_once '../config/koneksi.php';
    
    session_start(); 
	$nip=$_SESSION['nip'];

	$noprk = trim($_POST['noprk']);
	$basket = trim($_POST['basket']);
	//$nota0 = trim($_POST['nota0']);
	//$pic0 = trim($_POST['pic0']);
	$pos0 = trim($_POST['pos0']);
	$nilai0 = trim(str_replace(',','',$_POST['nilai0']));
	$noskki = trim($_POST['noskko']);
	$uraian = trim($_POST['uraian']);
	$tgl_skki = trim($_POST['tgl_skko']);
	$fungsi = trim($_POST['fungsi']);
	$fungsi_val = trim($_POST['fungsi_val']);
//	$aset = trim($_POST['aset']);
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
	$kp=trim($_POST['kp']);
	$kpa=trim(str_replace(',','',$_POST['kpa']));
	$kpd=trim(str_replace(',','',$_POST['kpd']));  
	

//	aset = '$aset', 

	$sql = "
		update skkiterbit
			set nomorprk = '$noprk', 
			nomorscore = '$basket',
			posinduk = '$pos0', 
			uraian = '$uraian', 
			tanggalskki = '$tgl_skki', 
			nilaitunai = '$tunai', 
			nilainontunai = '$nontunai', 
			nilaianggaran = '$anggaran', 
			nilaidisburse = '$disburse', 
			nomorwbs = '$nowbs', 
			nilaiwbs = '$wbs',
			jtm = '$jtm', 
			nilaianggaranjtm = '$jtma', 
			nilaidisbursejtm = '$jtmd', 
			gd = '$gd', 
			nilaianggarangd = '$gda', 
			nilaidisbursegd = '$gdd', 
			jtr = '$jtr', 
			nilaianggaranjtr =  '$jtra', 
			nilaidisbursejtr = '$jtrd', 
			sl1 = '$sl1', 
			nilaianggaransl1 = '$sl1a', 
			nilaidisbursesl1 = '$sl1d', 
			sl3 = '$sl3', 
			nilaianggaransl3 = '$sl3a', 
			nilaidisbursesl3 = '$sl3d',
			keypoint = '$kp', 
			nilaianggarankp = '$kpa', 
			nilaidisbursekp = '$kpd', 
			nip = '$nip',
			fungsi = '$fungsi', 
			fungsi_val = '$fungsi_val'
		where nomorskki = '$noskki'";
		
	//echo "$sql<br>";
	$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
	//echo "$sukses<br>";
// disable karena notadinas_detail tidak mengalami perubahan
/*
	//echo "<br>";
	foreach($_REQUEST as $param_name => $param_val) {
		//echo "$param_name - $param_val<br>";
		if(substr($param_name, 0 ,3)=="not") {$not = $param_val;}
		if(substr($param_name, 0 ,3)=="pic") {$pic = $param_val;}
		if(substr($param_name, 0 ,3)=="pos") {$pos = $param_val;}
		if(substr($param_name, 0 ,3)=="nil") {
			$nil = $param_val;
			$sql = "update notadinas_detail set noskk = '$noskki', progress = 7 where nid = '$pic'";
			echo "$sql<br>";
			$sukses = mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));// or die(mysql_error());
			//echo "$sukses<br>";
		}
	}
*/	
	echo "<script>window.open('index.php', '_self')</script>";  
?>