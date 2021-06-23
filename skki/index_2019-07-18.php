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
    //session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];

    if(isset($_GET['del'])) {
	/*echo "<script>window.alert('hapus : $_GET[del]')</script>";*/
//      $notadinas=base64_decode($_GET['del']);
      $noskk=base64_decode($_GET['del']);
      $sql="delete from skkiterbit where nomorskki='$noskk'";
      $hasil=mysql_query($sql);
      
	  $sql="update notadinas_detail set progress = null, noskk = null where noskk='$noskk'";
      $hasil=mysql_query($sql);     
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Penerbitan SKKI</h2>
<a href="tambahskko.php">(+) Tambah SKKI Terbit</a><br />
<table id='dataTables' class='display' cellspacing='0' width='100%'>
  <thead>
  <tr>
	<th>No</th>
    <th>Nomor PRK/SCORE</th>
    <th>No Nota</th>
    <th>No SKKI</th>
    <th>Uraian</th>
    <th>Tanggal SKKI</th>
    <th>Nomor Pos Anggaran</th>
    <th>
		Data Asset Manajemen<br>
    </th> 
    <th>Nilai Tunai</th>
    <th>Nilai Non Tunai</th>
    <th>Nilai Anggaran</th>
    <th>Nilai Disburse</th>
    <th>Nomor WBS</th>
    <th>Nilai WBS</th>
    <th>Pelaksana</th>
<!--    <th>Periode</th>
    <th>Jenis</th>-->
<!--    <th>Nomor Sub Pos 1</th>
    <th>Nomor Sub Pos 2</th>
    <th>Nomor Sub Pos 3</th>  -->
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
	"skkiterbit s LEFT JOIN (SELECT nd.*, namaunit FROM notadinas_detail nd LEFT JOIN bidang b ON nd.pelaksana = b.id) n 
	ON s.nomorskki = n.noskk where COALESCE(progress,0) >= 7 and Year(tanggalskki) = ".date('Y')." order by nomorskki, pos1, nomornota";
//echo $sql;

$dummyskk = "";
$no = 1;
$hasil=mysql_query($sql) or die (mysql_error());    
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
    
//     <td>'.$row['nomornota'].'</td>   
    echo '
    <tr>
      <td>'.($dummyskk != $row["nomorskki"]? $no: "").'</td>
      <td>'.($dummyskk != $row["nomorskki"]? $row['nomorprk']: "").'</td>
      <td>'.($dummyskk != $row["nomorskki"]? ($row["ps"]==0? "": $row['nota']): "").'</td>   
      <td>'.($dummyskk != $row["nomorskki"]? $row['nomorskki']: "").'</td>
	   <td>'.($dummyskk != $row["nomorskki"]? $row['uraian']: "").'</td>
	  <td>'.($dummyskk != $row["nomorskki"]? $row['tanggalskki']: "").'</td>
	   <td>'.($row["ps"]==0? "": $row['pos1']).'</td>' . 
	   '<td>'.($dummyskk != $row["nomorskki"]? (
			"-JTM= $row[jtm]. A= $row[nilaianggaranjtm]. D= $row[nilaidisbursejtm]<hr>" .
			"-GD= $row[gd]. A= $row[nilaianggarangd]. D= $row[nilaidisbursegd]<hr>" .
			"-JTR= $row[jtr]. A= $row[nilaianggaranjtr]. D= $row[nilaidisbursejtr]<hr>" .
			"-SL1= $row[sl1]. A= $row[nilaianggaransl1]. D= $row[nilaidisbursesl1]<hr>" .
			"-SL3= $row[sl3]. A= $row[nilaianggaransl3]. D= $row[nilaidisbursesl3]<hr>" .
			"-Key Point= $row[keypoint]. A= $row[nilaianggarankp]. D= $row[nilaidisbursekp]<hr>" 
		): "").'</td>' . 
	   '<td>'.($dummyskk != $row["nomorskki"]? ($row["ps"]==0? "": number_format($row['nilaitunai'])): "").'</td>
	   <td>'.($dummyskk != $row["nomorskki"]? ($row["ps"]==0? "": number_format($row['nilainontunai'])): "").'</td>
	   <td>'.($dummyskk != $row["nomorskki"]? ($row["ps"]==0? "": number_format($row['nilaianggaran'])): "").'</td>
	   <td>'.($dummyskk != $row["nomorskki"]? ($row["ps"]==0? "": number_format($row['nilaidisburse'])): "").'</td>
		<td>' . ($dummyskk != $row["nomorskki"]? $row['nomorwbs']: "") . '</td>
	   <td>'.($dummyskk != $row["nomorskki"]? number_format($row['nilaiwbs']): "").'</td> 
	   <td>'.($row["ps"]==0? "": $row['unit']).'</td>
      <td>'.($dummyskk != $row["nomorskki"]? '    
		<a href="#" onclick=\'edit("'.base64_encode($row['nomorskki']).'")\'>Edit</a>
		<a href="#" onclick=\'hapus("'.base64_encode($row['nomorskki']).'")\'>Hapus</a>
      ': '').'</td> 
    </tr>';
    
    if($dummyskk != $row["nomorskki"]) {
		$dummyskk = $row["nomorskki"];
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
		$('#dataTables').DataTable({
			"bSort" : false
		});
	} );
	</script>
</body>
</html>