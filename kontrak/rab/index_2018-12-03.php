<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../jquery.dataTables.min.css">
<title>Untitled Document</title>

        <?php
		session_start();
		if(!isset($_SESSION['nip'])) {
			echo "unauthorized user";
			echo "<script>window.open('../../index.php', '_parent')</script>";
			exit;
		}
		?>

<script type="text/javascript">
	function edit(x) {
		var url='editrab.php?s='+x;
		window.open(url,'_self');
	}
	
	function hapus(x) {
		var r = confirm("Yakin RAB Dihapus?") 
		if (r) {
			var url='index.php?del='+x;
			window.open(url,'_self');
		}
	}
</script>

</head>
<body>
<?php
    //session_start(); 
    require_once '../../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];

    if(isset($_GET['del'])) {
	/*echo "<script>window.alert('hapus : $_GET[del]')</script>";*/
//      $notadinas=base64_decode($_GET['del']);
      $noskk=base64_decode($_GET['del']);
      $sql="delete from rab where id='$noskk'";
      $hasil=mysql_query($sql);     
    }    

?>
<link href="../../css/screen.css" rel="stylesheet" type="text/css">
<h2>Rencana Anggaran Biaya</h2>
<a href="tambah.php">(+) Tambah RAB</a><br />
<table id='dataTables' class='display' cellspacing='0' width='100%'>
  <thead>
  <tr>
	<th>No</th>
    <th>Nomor SKK</th>
    <th>Nomor RAB</th>
    <th>Nilai RP</th>
    <th>Tanggal RAB</th>
    <th>Uraian Kegiatan</th>
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
$sql = "SELECT * FROM rab where nip='$_SESSION[nip]'";
//echo $sql;

$dummyskk = "";
$no = 1;
$hasil=mysql_query($sql) or die (mysql_error());    
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
    
//     <td>'.$row['nomornota'].'</td>   
    echo '
    <tr>
	  <td>'.$no.'</td>
      <td>'.$row['skk'].'</td>
      <td>'.$row['no_rab'].'</td>
      <td>'.$row['nilai_rp'].'</td>
      <td>'.$row['tgl_rab'].'</td>   
      <td>'.$row['uraian_kegiatan'].'</td>
    </tr>';
    $no++;
    
  }
  
?>
</table>
<script src="../../js/jquery-1.12.0.min.js"></script>
<script src="../../js/jquery.dataTables.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#dataTables').DataTable();
	} );
	</script>
</body>
</html>