<? 
    require_once '../config/koneksi.php';

    
    $nonotadinas=$_POST['nonotadinas'];
	$noskki=trim($_POST['noskki']);
	$tgl_skki=trim($_POST['tgl_skki']);
	//echo $tgl_skki;
	$nomorscore=trim($_POST['nomorscore']);
	$nomorprk=trim($_POST['nomorprk']);
	$nilaianggaran=trim(str_replace(',','',$_POST['nilaianggaran']));
	$nilaidisburse=trim(str_replace(',','',$_POST['nilaidisburse']));  
	$uraian=trim($_POST['uraian']);
	$kdindukpos=$_POST['posinduk'];
	if(!$_POST['posinduk2']==''){$kdpos2=$_POST['posinduk2'];}  
	$nomorwbs=trim($_POST['nomorwbs']);
	$nilaitunai=trim(str_replace(',','',$_POST['nilaitunai']));
	$nilainontunai=trim(str_replace(',','',$_POST['nilainontunai']));
	$nilaiwbs=trim(str_replace(',','',$_POST['nilaiwbs']));
	$unit1=trim($_POST['unit1']);
	/*
	if(!$_POST['jumlahjtm']==''){$jumlahjtm=$_POST['jumlahjtm']; echo "oki";}
	if(!$_POST['anggaranjtm']==''){$anggaranjtm=$_POST['anggaranjtm'];}
	if(!$_POST['disburmentjtm']==''){$disburmentjtm=$_POST['disburmentjtm'];}
	if(!$_POST['jumlahgd']==''){$jumlahgd=$_POST['jumlahgd'];}
	if(!$_POST['anggarangd']==''){$anggarangd=$_POST['anggarangd'];}
	if(!$_POST['disburmentgd']==''){$disburmentgd=$_POST['disburmentgd'];}
	if(!$_POST['jumlahjtr']==''){$jumlahjtr=$_POST['jumlahjtr'];} 
	if(!$_POST['anggaranjtr']==''){$anggaranjtr=$_POST['anggaranjtr'];} 
	if(!$_POST['disburmentjtr']==''){$disburmentjtr=$_POST['disburmentjtr'];} 
	if(!$_POST['jumlah1fasa']==''){$jumlah1fasa=$_POST['jumlah1fasa'];} 
	if(!$_POST['anggaran1fasa']==''){$anggaran1fasa=$_POST['anggaran1fasa'];} 
	if(!$_POST['disburment1fasa']==''){$disburment1fasa=$_POST['disburment1fasa'];}
	if(!$_POST['jumlah3fasa']==''){$jumlah3fasa=$_POST['jumlah3fasa'];}
	if(!$_POST['anggaran3fasa']==''){$anggaran3fasa=$_POST['anggaran3fasa'];}
	if(!$_POST['disburment3fasa']==''){$disburment3fasa=$_POST['disburment3fasa'];}
	echo $_POST['jumlahjtm']!='';
*/
$sql ="
	UPDATE skkiterbit SET
		nomorskki='$noskki',
		tanggalskki='$tgl_skki',
		nilaianggaran='$nilaianggaran',
		nilaidisburse='$nilaidisburse',
		uraian='$uraian',
		posinduk='$kdindukpos'," .
		(isset($kdpos2)?"posinduk2='$kdpos2',":"") .
		(!$_POST['jumlahjtm']==''?"jtm='$_POST[jumlahjtm]',":"") .
		(!$_POST['anggaranjtm']==''?"nilaianggaranjtm='$_POST[anggaranjtm]',":"") .
		(!$_POST['disburmentjtm']==''?"nilaidisbursejtm='$_POST[disburmentjtm]',":"") .
		(!$_POST['jumlahgd']==''?"gd='$_POST[jumlahgd]',":"") .
		(!$_POST['anggarangd']==''?"nilaianggarangd='$_POST[anggarangd]',":"") .
		(!$_POST['disburmentgd']==''?"nilaidisbursegd='$_POST[disburmentgd]',":"") .
		(!$_POST['jumlahjtr']==''?"jtr='$_POST[jumlahjtr]',":"") .
		(!$_POST['anggaranjtr']==''?"nilaianggaranjtr='$_POST[anggaranjtr]',":"") .
		(!$_POST['disburmentjtr']==''?"nilaidisbursejtr='$_POST[disburmentjtr]',":"") .
		(!$_POST['jumlah1fasa']==''?"sl1='$_POST[jumlah1fasa]',":"") .
		(!$_POST['anggaran1fasa']==''?"nilaianggaransl1='$_POST[anggaran1fasa]',":"") .
		(!$_POST['disburment1fasa']==''?"nilaidisbursesl1='$_POST[disburment1fasa]',":"") .
		(!$_POST['jumlah3fasa']==''?"sl3='$_POST[jumlah3fasa]',":"") .
		(!$_POST['anggaran3fasa']==''?"nilaianggaransl3='$_POST[anggaran3fasa]',":"") .
		(!$_POST['disburment3fasa']==''?"nilaidisbursesl3='$_POST[disburment3fasa]',":"") .
		"
		nomorscore=$nomorscore,
		nomorprk=$nomorprk,
		nomorwbs=$nomorwbs,
		nilaitunai='$nilaitunai',
		nilainontunai='$nilainontunai',
		nilaiwbs='$nilaiwbs',
		unit='$unit1'
	WHERE nomornota='$nonotadinas'";

echo $sql;
$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 


echo '
	<script language="javascript">
		alert("Nota Dinas '.$nomornota.' . \r\nSKKO '.$noskko.' Berhasil Ditambah!\r\n");
		window.location.href="index.php";                   
	</script>';   

?>