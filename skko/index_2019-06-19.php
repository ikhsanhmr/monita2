<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../jquery.dataTables.min.css">
<title>Untitled Document</title>
        <?php
		session_start();
		if(!isset($_SESSION['nip'])) {
			echo "unauthorized user";
			echo "<script>window.open('../index.php', '_parent')</script>";
			exit;
		}
		?>

<script type="text/javascript">
	function edit(x) {
		var url='editskko.php?s='+x;
		window.open(url,'_self');
	}
	
	function hapus(x) {
		var r = confirm("Yakin Nota Dinas Dihapus?") 
		if (r) {
			var url='index.php?del='+x;
			window.open(url,'_self');
		}
	}
</script>

</head>
<body>
<?php
//    session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];

    if(isset($_GET['del'])) {
	/*echo "<script>window.alert('hapus : $_GET[del]')</script>";*/
//      $notadinas=base64_decode($_GET['del']);
      $noskk=base64_decode($_GET['del']);
      $sql="delete from skkoterbit where nomorskko='$noskk'";
      $hasil=mysql_query($sql);
      
	  $sql="update notadinas_detail set progress = null, noskk = null where noskk='$noskk'";
      $hasil=mysql_query($sql);     
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Penerbitan SKKO</h2>
<a href="tambahskko.php">(+) Tambah SKKO Terbit</a><br />
<table id='dataTables' class='display' cellspacing='0' width='100%'>
  <thead>
  <tr>
    <th>No</th>
    <th>No SKKO</th>
    <th>Tanggal SKKO</th>
    <th>No Nota</th>
    <th>Nilai Anggaran</th>
    <th>Nilai Disburse</th>
    <th>Uraian</th>
    <th>Periode</th>
    <th>Jenis</th>
    <th>Nomor Pos Anggaran</th>
<!--    <th>Nomor Sub Pos 1</th>
    <th>Nomor Sub Pos 2</th>
    <th>Nomor Sub Pos 3</th>  -->
    <th>Nomor WBS/Cost Center</th>
    <th>Nilai Tunai</th>
    <th>Nilai Non Tunai</th>
    <th>Nilai WBS</th>
    <th>Pelaksana</th>
    <th>Aksi</th>
  </tr>
  </thead>
<?php
/*
if($bidang=='0' || $bidang=='1' || $bidang=='2' )
  {
  $sql="select * from skkoterbit " . (($nip == "admin" || $nip== "6793235Z")? "": "where nip='$nip'");
echo $sql;
  }
 
  else
  {
  //$sql="select kdindukpos, namaindukpos from posinduk";
   echo 'tes';
  }
 */ 
//$sql="select * from skkoterbit " . (($nip == "admin" || $nip== "6793235Z")? "": "where nip='$nip'");
$sql = "SELECT s.*, n.nomornota nota, pelaksana, pos1, nilai1, coalesce(progress,0) ps, sisa1, namaunit unit FROM " . 
	"skkoterbit s LEFT JOIN (SELECT nd.*, namaunit FROM notadinas_detail nd LEFT JOIN bidang b ON nd.pelaksana = b.id) n 
	ON s.nomorskko = n.noskk where COALESCE(progress,0) = 7" . 
	(($nip == "admin" || $nip== "6793235Z")? "": " and nip='$nip'") . 
	" order by nomorskko, pos1, nomornota limit 100";
//echo $sql;

$dummyskk = "";
$no = 1;
$hasil=mysql_query($sql) or die (mysql_error());    
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
    
//     <td>'.$row['nomornota'].'</td>   
    echo '
    <tr>
      <td>'.($dummyskk != $row["nomorskko"]? $no: "").'</td>
      <td>'.($dummyskk != $row["nomorskko"]? $row['nomorskko']: "").'</td>
	  <td>'.($dummyskk != $row["nomorskko"]? $row['tanggalskko']: "").'</td>
      <td>'.($dummyskk != $row["nomorskko"]? ($row["ps"]==0? "": $row['nota']): "").'</td>   
	   <td>'.($dummyskk != $row["nomorskko"]? ($row["ps"]==0? "": number_format($row['nilaianggaran'])): "").'</td>
	   <td>'.($dummyskk != $row["nomorskko"]? ($row["ps"]==0? "": number_format($row['nilaidisburse'])): "").'</td>
	   <td>'.($dummyskk != $row["nomorskko"]? $row['uraian']: "").'</td>
	   <td>'.($dummyskk != $row["nomorskko"]? $row['periode']: "").'</td>
	   <td>'.($dummyskk != $row["nomorskko"]? $row['jenis']: "").'</td>' . 
	   '<td>'.($row["ps"]==0? "": $row['pos1']).'</td>' . 
/*	   <td>'.$row['posinduk'].'-'.$row['namaindukpos']. '</td>
	   <td>'.$row['posinduk2'].'-'.$row['namasubpos1'].'</td>
	   <td>'.$row['posinduk3'].'-'.$row['namasubpos2'].'</td>
	   <td>'.$row['posinduk4'].'-'.$row['namasubpos3'].'</td> */ '
	   <td>' . ($dummyskk != $row["nomorskko"]? ($row['nomorwbs']!=null?$row['nomorwbs']:$row['nomorcostcenter']): "") . '</td>

	   <td>'.($dummyskk != $row["nomorskko"]? ($dummyskk != $row["nomorskko"]? ($row["ps"]==0? "": number_format($row['nilaitunai'])): ""): "").'</td>
	   <td>'.($dummyskk != $row["nomorskko"]? ($row["ps"]==0? "": number_format($row['nilainontunai'])): "").'</td>
	   <td>'.($dummyskk != $row["nomorskko"]? number_format($row['nilaiwbs']): "").'</td> 
	   <td>'.($row["ps"]==0? "": $row['unit']).'</td>
      <td>'.($dummyskk != $row["nomorskko"]? '    
		<a href="#" onclick=\'edit("'.base64_encode($row['nomorskko']).'")\'>Edit</a>
		<a href="#" onclick=\'hapus("'.base64_encode($row['nomorskko']).'")\'>Hapus</a>
      ': '').'</td> 
    </tr>';
    
    if($dummyskk != $row["nomorskko"]) {
		$dummyskk = $row["nomorskko"];
		$no++;
    }
  }
  
?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>  
<script src="../js/jquery-1.12.0.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#dataTables').DataTable();
	} );
	</script>
</body>
</html>