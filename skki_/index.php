<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>
<?
    session_start(); 
    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];
	
    if(isset($_GET['del']))
    {
      $notadinas=base64_decode($_GET['del']);
      $sql="delete 
            from notadinas
            where nomornota='$notadinas'
            ";
      $hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli));
           
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Penerbitan SKKI</h2>
<a href="tambahskki.php">(+) Tambah SKKO/ I Terbit</a><br />
<table>
  <tr>
    <th>No</th>
    <th>No Nota</th>
    <th>No SKKI</th>
    <th>Tanggal SKKI</th>
    <th>Nomor Score</th>
    <th>Nomor PRK</th>
    <th>Nilai Anggaran</th>
    <th>Nilai Disburse</th>
    <th>Uraian</th>
    <th>Nomor Pos Anggaran</th>
    <th>Nomor Sub Pos 1</th>
    <th>Nomor WBS</th>
    <th>Nilai Tunai</th>
    <th>Nilai Non Tunai</th>
    <th>Nilai WBS</th>
    <th>JTM</th>
    <th>GD</th>
    <th>JTR</th>
    <th>SL1Fasa</th>
    <th>SL3Fasa</th>     
    <th>Pelaksana</th>
    <th>Aksi</th>
  </tr>
<?php

if($bidang=='0' || $bidang=='1' || $bidang=='2' )
  {
  $sql="SELECT s.*,pos.namaindukpos,pos2.namasubpos as namasubpos1 FROM skkiterbit s 
LEFT JOIN posinduk pos ON s.posinduk = pos.kdindukpos
LEFT JOIN posinduk2 pos2 ON s.posinduk2 = pos2.kdsubpos
LEFT JOIN notadinas n ON s.nomornota = n.nomornota where n.nip='$nip'
";
  }
 
  else
  {
  //$sql="select kdindukpos, namaindukpos from posinduk";
   echo 'tes';
  }
  
$hasil=mysqli_query($mysqli, $sql) or die ('Unable to execute query. '. mysqli_error($mysqli)) or die (mysqli_error());    
	while ($row = mysqli_fetch_array($hasil)) {
    
    $no++;
    echo '
    <tr>
      <td>'.$no.'</td>
      <td>'.$row['nomornota'].'</td>   
	  <td>'.$row['nomorskki'].'</td>
	  <td>'.$row['tanggalskki'].'</td>
      <td>'.$row['nomorscore'].'</td>
	  <td>'.$row['nomorprk'].'</td>
	  <td>'.number_format($row['nilaianggaran']).'</td>
	  <td>'.number_format($row['nilaidisburse']).'</td>
	  <td>'.$row['uraian'].'</td>
	   <td>'.$row['posinduk'].'</td>
	   <td>'.$row['posinduk2'].'</td>
	   <td>'.$row['nomorwbs'].'</td>
	   <td>'.number_format($row['nilaitunai']).'</td>
	   <td>'.number_format($row['nilainontunai']).'</td>
	   <td>'.number_format($row['nilaiwbs']).'</td>
	   <td>'.$row['jtm'].'</td>
	   <td>'.$row['gd'].'</td>
	   <td>'.$row['jtr'].'</td>
	   <td>'.$row['sl1'].'</td>
	   <td>'.$row['sl3'].'</td>
	   <td>'.$row['unit'].'</td>
      <td>    
      <a href="tambahskki.php?notadinas='.base64_encode($row['nomornota']).'">Proses</a>
	   &nbsp;&nbsp;&nbsp;<a href="?del='.base64_encode($row['nomornota']).'">Hapus</a>
      </td>                  
    </tr>';
  }
  
?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>   
</body>
</html>