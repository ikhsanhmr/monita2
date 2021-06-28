<?php
session_start();
if (!isset($_SESSION['nip'])) {
  echo "unauthorized user";
  echo "<script>window.open('../index.php', '_parent')</script>";
  exit;
}

require_once '../config/koneksi.php';
$nip = $_SESSION['nip'];
$bidang = $_SESSION['bidang'];
$kdunit = $_SESSION['kdunit'];

if (isset($_GET['del'])) {
  $nocostcenter = base64_decode($_GET['del']);
  $sql = "delete 
            from costcenter
            where nocostcenter='$nocostcenter'
            ";
  $hasil = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli));
}

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Cost Center</h2>
<a href="tambahcostcenter.php">(+) Tambah Cost Center</a><br />
<table>
  <tr>
    <th>No</th>
    <th>Hierarchy Area</th>
    <th>Uraian</th>
    <th>Cost Center</th>
    <th>Description</th>
    <th>Business Area</th>
    <th>Aksi</th>
  </tr>
  <?php

  if ($bidang == '0' || $bidang == '1' || $bidang == '2') {
    $sql = "select * from costcenter";
  } else {
    //$sql="select kdindukpos, namaindukpos from posinduk";
    echo 'tes';
    echo $bidang;
  }

  $hasil = mysqli_query($mysqli, $sql) or die('Unable to execute query. ' . mysqli_error($mysqli)) or die(mysqli_error($mysqli));
  $no = 1;
  while ($row = mysqli_fetch_array($hasil)) {
    echo '
    <tr>
      <td>' . $no++ . '</td>
      <td>' . $row['hierarkiarea'] . '</td>   
      <td>' . $row['uraian'] . '</td>
	   <td>' . $row['nocostcenter'] . '</td>
	   <td>' . $row['descriptionbisnis'] . '</td>
	   <td>' . $row['bisnisarea'] . '</td>
      <td>    
      <a href="editcostcenter.php?nocostcenter=' . base64_encode($row['nocostcenter']) . '">Edit</a>&nbsp;&nbsp;&nbsp;      
      <a href="?del=' . base64_encode($row['nocostcenter']) . '">Hapus</a>      
      </td>   
      </tr>';

    // 
  }
  ?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>