<? 
    require_once '../config/koneksi.php';
    
    $nonotadinas=$_POST['nonotadinas'];
	$noskko=trim($_POST['noskko']);
	$tgl_skko=trim($_POST['tgl_skko']);
	$nilaianggaran=trim(str_replace(',','',$_POST['nilaianggaran']));
	$nilaidisburse=trim(str_replace(',','',$_POST['nilaidisburse']));  
    $uraian=trim($_POST['uraian']);
	$periode=trim($_POST['periode']);
    $jenis=trim($_POST['jenis']);
	
	$kdindukpos=$_POST['posinduk'];

	if(!$_POST['posinduk2']==''){$kdpos2=$_POST['posinduk2'];}  
	if(!$_POST['posinduk3']==''){$kdpos3=$_POST['posinduk3'];}  
	if(!$_POST['posinduk4']==''){$kdpos4=$_POST['posinduk4'];}
	
	$nowbs=trim($_POST['nowbs']);
	$nocostcenter=trim($_POST['nocostcenter']);
	$nilaitunai=trim(str_replace(',','',$_POST['nilaitunai']));
	$nilainontunai=trim(str_replace(',','',$_POST['nilainontunai']));
	$nilaiwbs=trim(str_replace(',','',$_POST['nilaiwbs']));
	$unit1=trim($_POST['unit1']);
	$unit11=explode("-", $unit1);
	$unit111=$unit11[0];
	$unit112=$unit11[1];

$sql ="
	UPDATE skkoterbit SET
		nomorskko='$noskko',
		tanggalskko='$tgl_skko',
		nilaianggaran='$nilaianggaran',
		nilaidisburse='$nilaidisburse',
		uraian='$uraian',
		periode='$periode',
		jenis='$jenis',
		posinduk='$kdindukpos'," .
		(isset($kdpos2)?"posinduk2='$kdpos2',":"") .
		(isset($kdpos3)?"posinduk3='$kdpos3',":"") .
		(isset($kdpos4)?"posinduk4='$kdpos4',":"") .
		($kdindukpos=='53'?"nomorwbs='$nowbs',":"nomorcostcenter='$nocostcenter'") .
		",nilaitunai='$nilaitunai',
		nilainontunai='$nilainontunai',
		nilaiwbs='$nilaiwbs',
		unit='$unit112'
	WHERE nomornota='$nonotadinas'";
echo $sql;

$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)); 


echo '
	<script language="javascript">
		alert("Nota Dinas '.$nonotadinas.' . \r\nSKKO '.$noskko.' Berhasil Ditambah!\r\n");
		window.location.href="index.php";                   
	</script>';   

?>