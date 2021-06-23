<?php
	session_start();
	if(!isset($_SESSION['nip'])) {
		echo "unauthorized user";
		echo "<script>window.open('../index.php', '_parent')</script>";
		exit;
	}

    require_once '../config/koneksi.php';
	$nip=$_SESSION['nip'];
	$bidang=$_SESSION['bidang'];
	$kdunit=$_SESSION['kdunit'];



    if(isset($_GET['pos']))
    {
      $pos=base64_decode($_GET['pos']);
      
      $sql="delete 
            from posinduk2
            where kdindukpos='$pos'
            ";
      $hasil=mysql_query($sql);
	  
	  $sql="delete 
            from posinduk3
            where kdindukpos like '%$pos%'
            ";
      $hasil=mysql_query($sql);
	  
	  $sql="delete 
            from posinduk4
            where kdindukpos like '%$pos%'
            ";
      $hasil=mysql_query($sql);
	  

      $sql="delete 
            from posinduk
            where kdindukpos='$pos'
            ";
      $hasil=mysql_query($sql);
           
    }    

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h2>Daftar Pos Anggaran</h2>
<a href="tambahpos.php">(+) Tambah pos</a><br />
<table>
  <tr>
    <th>No</th>
    <th>Pos</th>
    <th>Nama</th>
    <th>Aksi</th>
  </tr>
  <?php
	$no=0;
  if($bidang=='0' || $bidang=='1'|| $bidang=='2')
  {
  $sql="select kdindukpos, namaindukpos
        from posinduk";
  }
 
  else
  {
  //$sql="select kdindukpos, namaindukpos from posinduk";
   echo 'tes11';
  }
  
    	
	$hasil=mysql_query($sql) or die (mysql_error()); 

	while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
    
    $no++;
    echo '
    <tr>
      <td>'.$no.'</td>
      <td align="center">'.$row['kdindukpos'].'</td>   
      <td>'.$row['namaindukpos'].'</td> 
      <td> 
	  <a href="showdetailpos1.php?pos='.base64_encode($row['kdindukpos']).'">lihat sub</a>&nbsp;&nbsp;&nbsp;      
      <a href="editpos.php?pos='.base64_encode($row['kdindukpos']).'">edit</a>&nbsp;&nbsp;&nbsp;      
      <a href="?pos='.base64_encode($row['kdindukpos']).'">hapus</a>      
      </td>                  
    </tr>';
  }  
  
  ?>
</table>
<a href="" onclick="parent.isi.print()">Cetak</a>   