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
      $hasil=mysql_query($sql);
      
	  $sql="delete 
            from skkoterbit
            where nomornota='$notadinas'
            ";
      $hasil=mysql_query($sql);
	  
	  $sql="delete 
            from skkiterbit
            where nomornota='$notadinas'
            ";
      $hasil=mysql_query($sql);       
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Kontrak</h2>
<a href="tambahkontrak.php">(+) Tambah Kontrak</a><br />
<table>
  <tr>
    <th>No</th>
    <th>No SKKO/I</th>
    <th>No Kontrak</th>
    <th>Uraian</th>
    <th>Vendor</th>
    <th>Tanggal Awal Kontrak</th>
    <th>Tanggal Akhir Kontrak</th>
    <th>Nilai Kontrak</th>
    <th>Aksi</th>
  </tr>
<?php

  $sql="SELECT * FROM (
                SELECT k.*, nomornota FROM kontrak k
                LEFT JOIN skkoterbit t ON k.nomorskkoi = t.nomorskko
                UNION 
                SELECT k.*, nomornota FROM kontrak k
                LEFT JOIN skkiterbit t ON k.nomorskkoi = t.nomorskki
				) skk LEFT JOIN notadinas n ON skk.nomornota = n.nomornota 
		WHERE nipuser = '$nip'";

  
$hasil=mysql_query($sql) or die (mysql_error());    
	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
	
    $no++;
    echo '
    <tr>
      <td>'.$no.'</td>
      <td>'.$row['nomorskkoi'].'</td>   
      <td><input type="text" name="kontrak$no" id="kontrak$no" value="'.$row["nomorkontrak"].'"></td>
	   <td><input type="text" name="uraian$no" id="uraian$no" value="'.$row["uraian"].'"></td>
	   <td><input type="text" name="vendor$no" id="vendor$no" value="'.$row["vendor"].'"></td>
	   <td><input type="text" name="tglawal$no" id="tglawal$no" value="'.$row["tglawal"].'"></td>
	   <td><input type="text" name="tglakhir$no" id="tglakhir$no" value="'.$row["tglakhir"].'"></td>
	   <td><input type="text" name="nilai$no" id="nilai$no" value="'.$row["nilaikontrak"].'"></td>
      <td>        
      <a href="?del='.base64_encode($row['nomornota']).'">Hapus</a>      
      </td>                  
    </tr>';
  }  
  echo '
  <tr>
  <td align="right" colspan="9"><input type="submit" value="Simpan">
  </td> 
   </tr>';
?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>   
