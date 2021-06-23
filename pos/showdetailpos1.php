<?php
    session_start(); 
    require_once '../config/koneksi.php';
    $nip=$_SESSION['nip'];
  	$bidang=$_SESSION['bidang'];
    $pos=base64_decode($_GET['pos']);
	
	if(isset($_GET['del']))
    {
      $kdsubpos=base64_decode($_GET['del']);
	  
      
      $sql="delete 
            from posinduk4
            where kdindukpos like '$kdsubpos%'
            ";
      
      $hasil=mysql_query($sql);

      $sql="delete 
            from posinduk3
            where kdindukpos like '$kdsubpos%'
            ";
      
      $hasil=mysql_query($sql);    

      $sql="delete 
            from posinduk2
            where kdsubpos='$kdsubpos'
            and kdindukpos='$pos'
            ";
      
      $hasil=mysql_query($sql);   
    }
    else
    {
      
	 
      $sql="select namaindukpos 
                from posinduk
                where kdindukpos='$pos'
            ";
	  
	  $hasil = mysql_query($sql);
	  $row = mysql_fetch_array($hasil); 
 	  $namapos=$row['namaindukpos'];
	  
    }

?>
<link href="../css/screen.css" rel="stylesheet" type="text/css">
<h5>
  DAFTAR POS <?=$pos.' '.$namapos?>
</h5>
<a href="tambahsubpos1.php?pos=<?=$_GET['pos']?>">(+) Tambah subpos</a><br />
  <?php
 /* 
  $ses_userid=$_SESSION['p_userid'];
  $sqlsub="select subpos1 from `users-akses-pos` where pos='$pos' and iduser='$ses_userid'";
  $rssub=db_select($sqlsub);
  //echo "asda".$rssub[0][0]."asd".$sqlsub;
  if ($rssub[0]['subpos1']=='' or $_SESSION['p_akses']=='0')
  {
	  $sql="select kdsubpos, namasubpos
        from posanggota
        where kdindukpos='$pos'
        ";
  }
  else
  {
	   $sql="select kdsubpos, namasubpos
        from posanggota
        where kdindukpos='$pos' and kdsubpos in (select subpos1 from `users-akses-pos` where pos='$pos' and iduser='$ses_userid')
        ";
  }
  $rs=db_select($sql);
  */
  $sql="select kdsubpos, namasubpos
        from posinduk2
        where kdindukpos='$pos'
        ";
  
  $no=0;
  $hasil=mysql_query($sql);
  $rs = mysql_num_rows($hasil);
  if($rs==0)
  {
    echo "Data tidak ditemukan.<br>";
  }
  else
  {
    echo '
    <table>
      <tr>
        <th>No</th>
        <th>Sub Pos</th>
        <th>Nama</th>
        <th>Aksi</th>
      </tr>';
        
while ($row = mysql_fetch_array($hasil, MYSQL_ASSOC)) {
	//$row = mysql_fetch_array($hasil, MYSQL_ASSOC);
      $no++;
      echo '
      <tr>
        <td>'.$no.'</td>
        <td align="center">'.$row['kdsubpos'].'</td>   
        <td>'.$row['namasubpos'].'</td> 
        <td>
        <a href="showdetailpos2.php?pos='.base64_encode($row['kdsubpos']).'">lihat sub</a>&nbsp;&nbsp;&nbsp;          
        <a href="editsubpos1.php?pos='.base64_encode($pos).'&subpos='.base64_encode($row['kdsubpos']).'">edit</a>&nbsp;&nbsp;&nbsp;
        <a href="?pos='.base64_encode($pos).'&del='.base64_encode($row['kdsubpos']).'">hapus</a>      
        </td>                  
      </tr>';
    }  
  }
  ?>
</table>
<a href="index.php">Kembali ke daftar pos</a>