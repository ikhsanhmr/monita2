<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript">
function hapus(x)
{
var r=confirm("Yakin Nota Dinas Dihapus?");
if (r==true)
  {
	  var url='index.php?del='+x;
  window.open(url,'_self');
  }

}
</script>

</head>
<body>
<?php
    session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];

    if(isset($_GET['del']))
    {
	/*echo "<script>window.alert('hapus : $_GET[del]')</script>";*/
//      $notadinas=base64_decode($_GET['del']);
      $notadinas=base64_decode($_GET['del']);
      $sql="delete 
            from skkoterbit
            where nomornota='$notadinas'
            ";
      $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
      
	  $sql="delete 
            from notadinas
            where nomornota='$notadinas'
            ";
      $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));     
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Penerbitan SKKO</h2>
<table>
  <tr>
    <th>No</th>
    <th>No Nota</th>
    <th>No SKKO</th>
    <th>Tanggal SKKO</th>
    <th>Nilai Anggaran</th>
    <th>Nilai Disburse</th>
    <th>Uraian</th>
    <th>Periode</th>
    <th>Jenis</th>
    <th>Nomor Pos Anggaran</th>
    <th>Nomor Sub Pos 1</th>
    <th>Nomor Sub Pos 2</th>
    <th>Nomor Sub Pos 3</th>
    <th>Nomor WBS/Cost Center</th>
    <th>Nilai Tunai</th>
    <th>Nilai Non Tunai</th>
    <th>Nilai WBS</th>
    <th>Pelaksana</th>
    <th>Aksi</th>
  </tr>
<?php

if($bidang=='0' || $bidang=='1' || $bidang=='2' )
  {
  $sql="SELECT s.*,pos.namaindukpos,pos2.namasubpos as namasubpos1,pos3.namasubpos as namasubpos2,pos4.namasubpos as namasubpos3,n.nip as nip2
FROM skkoterbit s 
LEFT JOIN posinduk pos ON s.posinduk = pos.kdindukpos
LEFT JOIN posinduk2 pos2 ON s.posinduk2 = pos2.kdsubpos
LEFT JOIN posinduk3 pos3 ON s.posinduk3 = pos3.kdsubpos
LEFT JOIN posinduk4 pos4 ON s.posinduk4 = pos4.kdsubpos
LEFT JOIN notadinas n ON s.nomornota = n.nomornota
where n.nip='$nip'
";
echo $sql;
  }
 
  else
  {
  //$sql="select kdindukpos, namaindukpos from posinduk";
   echo 'tes';
  }
  
$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysql_error());    
	while ($row = mysqli_fetch_array($hasil)) {
    
    $no++;
    echo '
    <tr>
      <td>'.$no.'</td>
      <td>'.$row['nomornota'].'</td>   
      <td>'.$row['nomorskko'].'</td>
	  <td>'.$row['tanggalskko'].'</td>
	   <td>'.number_format($row['nilaianggaran']).'</td>
	   <td>'.number_format($row['nilaidisburse']).'</td>
	   <td>'.$row['uraian'].'</td>
	   <td>'.$row['periode'].'</td>
	   <td>'.$row['jenis'].'</td>
	   <td>'.$row['posinduk'].'-'.$row['namaindukpos'].'</td>
	   <td>'.$row['posinduk2'].'-'.$row['namasubpos1'].'</td>
	   <td>'.$row['posinduk3'].'-'.$row['namasubpos2'].'</td>
	   <td>'.$row['posinduk4'].'-'.$row['namasubpos3'].'</td>
	   
	   <td>    
      ' . ($row['nomorwbs']!=null?$row['nomorwbs']:$row['nomorcostcenter']) . '</td>

	   <td>'.number_format($row['nilaitunai']).'</td>
	   <td>'.number_format($row['nilainontunai']).'</td>
	   <td>'.number_format($row['nilaiwbs']).'</td>
	   <td>'.$row['unit'].'</td>
      <td>    
      '.($row['nomorskko']==""?'<a href="tambahskko.php?notadinas='.base64_encode($row['nomornota']).'">Proses</a>':""). '
	  
      &nbsp;&nbsp;&nbsp;<a href="#" onclick=\'hapus("'.base64_encode($row['nomornota']).'")\'>Hapus</a>      
      </td>                  
    </tr>';
  }
  
?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>   
</body>
</html>