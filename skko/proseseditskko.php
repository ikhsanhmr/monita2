<?php 
    require_once '../config/koneksi.php';
    
    session_start(); 
	$nip=$_SESSION['nip'];
	$noskko=trim($_POST['noskko']);
	$pos0 = trim($_POST['pos0']);
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
	
	$sql = "update skkoterbit set 
		tanggalskko = '$tgl_skko', 
		posinduk = '$pos0', 
		uraian = '$uraian', 
		periode = '$periode', 
		jenis = '$jenis', 
		nomorcostcenter = '$nocostcenter', 
		nomorwbs = '$nowbs', 
		nilaiwbs = '$wbs', 
		nilaitunai = '$tunai', 
		nilainontunai = '$nontunai', 
		nilaianggaran = '$anggaran', 
		nilaidisburse = '$disburse', 
		nip = '$nip'
	where nomorskko = '$noskko'";
	
	$sukses = mysql_query($sql);// or die(mysql_error());
	//echo "$sukses<br>";
// diremark karena notadinas_detail tidak mengalami perubahan
/*
	foreach($_REQUEST as $param_name => $param_val) {
		//echo "$param_name - $param_val<br>";
		if(substr($param_name, 0 ,3)=="not") {$not = $param_val;}
		if(substr($param_name, 0 ,3)=="pic") {$pic = $param_val;}
		if(substr($param_name, 0 ,3)=="pos") {$pos = $param_val;}
		if(substr($param_name, 0 ,3)=="nil") {
			$nil = $param_val;
			$sql = "update notadinas_detail set noskk = '$noskko', progress = 7 where nid = '$pic'";
			$sukses = mysql_query($sql);// or die(mysql_error());
		}
	}
*/	
	echo "<script>window.open('index.php', '_self')</script>";  
?>